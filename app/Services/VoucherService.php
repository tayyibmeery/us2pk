<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherDetail;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherService
{
    public function generateVoucherNumber()
    {
        $last = Voucher::withTrashed()
            ->where('voucher_no', 'like', 'VR-%')
            ->orderByRaw('CAST(SUBSTRING(voucher_no, 4) AS UNSIGNED) DESC')
            ->first();
        $nextId = $last ? intval(substr($last->voucher_no, 3)) + 1 : 1;
        return 'VR-' . $nextId;
    }

    public function generateVoucher($source, $referenceType, $referenceId, $date, $description, array $entries, $approved = true)
    {
        foreach ($entries as $i => $entry) {
            if (!isset($entry['account_id'])) throw new \Exception("Entry at index {$i} is missing 'account_id'");
            $entries[$i]['debit'] = $entry['debit'] ?? 0;
            $entries[$i]['credit'] = $entry['credit'] ?? 0;
        }

        $totalDebit = array_sum(array_column($entries, 'debit'));
        $totalCredit = array_sum(array_column($entries, 'credit'));

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new \Exception("Voucher not balanced: Dr {$totalDebit} / Cr {$totalCredit}");
        }

        return DB::transaction(function () use ($source, $referenceType, $referenceId, $date, $description, $entries, $approved) {
            $voucher = Voucher::create([
                'voucher_no' => $this->generateVoucherNumber(),
                'date' => $date,
                'description' => $description,
                'is_active' => true,
                'approved' => $approved,
                'is_deleted' => false,
                'source' => $source,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'created_by' => auth()->id(),
                'approved_by' => $approved ? auth()->id() : null,
            ]);

            foreach ($entries as $entry) {
                if (($entry['debit'] ?? 0) == 0 && ($entry['credit'] ?? 0) == 0) continue;
                VoucherDetail::create([
                    'voucher_id' => $voucher->id,
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                    'description' => $entry['description'] ?? null,
                ]);
            }
            return $voucher;
        });
    }

    private function getAccountId(string $name): ?int
    {
        return Account::where('name', $name)->value('id');
    }

    private function voucherExists(string $refType, $refId): bool
    {
        return Voucher::where('reference_type', $refType)->where('reference_id', $refId)->where('is_deleted', false)->exists();
    }

    private function getAccountForPaymentMethod(?int $paymentMethodId, ?string $paymentMethodName): ?int
    {
        if ($paymentMethodId) {
            $pm = \App\Models\PaymentMethod::find($paymentMethodId);
            if ($pm && $pm->account_id) return $pm->account_id;
        }

        $map = [
            'Bank Transfer' => 'Bank Account',
            'Bank Transfer (IBFT)' => 'Bank Account',
            'Cheque' => 'Bank Account',
            'Credit Card' => 'Bank Account',
            'Cash' => 'Cash Account',
            'JazzCash' => 'JazzCash Account',
            'EasyPaisa' => 'EasyPaisa Account',
            'Cash on Delivery' => 'COD Receivable - BlueEx',
        ];

        $name = $map[$paymentMethodName ?? ''] ?? 'Bank Account';
        return $this->getAccountId($name) ?? $this->getAccountId('Bank Account');
    }

    public function syncShipmentVouchers($shipment)
    {
        $created = [];

        $v = $this->generateShipmentPurchaseVoucher($shipment);
        if ($v) $created[] = $v;

        if ($shipment->date_delivered) {
            $v = $this->generateDeliveryRevenueVoucher($shipment);
            if ($v) $created[] = $v;
            $v = $this->generateDeliveryCOGSVoucher($shipment);
            if ($v) $created[] = $v;
            // Delivery charge voucher is generated when COD is remitted.
        }
        return $created;
    }

    public function generateShipmentPurchaseVoucher($shipment): ?Voucher
    {
        if ($shipment->bought_by !== 'By Company' || (float) $shipment->item_value_pkr <= 0) return null;
        if ($this->voucherExists('shipment_purchase', $shipment->id)) return null;

        $inventory = $this->getAccountId('Inventory');
        $bank = $this->getAccountId('Bank Account');
        if (!$inventory || !$bank) return null;

        $amount = (float) $shipment->item_value_pkr;

        return $this->generateVoucher('system', 'shipment_purchase', $shipment->id, $shipment->created_at->toDateString(), "Company Purchased Items - Purchase for {$shipment->shipment_code}", [
            ['account_id' => $inventory, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $bank, 'debit' => 0, 'credit' => $amount],
        ]);
    }

    public function generateDeliveryRevenueVoucher($shipment): ?Voucher
    {
        if (!$shipment->date_delivered) return null;

        $itemValue = (float) $shipment->item_value_pkr;
        $shippingRevenue = (float) $shipment->company_charges;
        $total = $itemValue + $shippingRevenue;

        $advance = (float) $shipment->payments()
            ->where('payment_method', '!=', 'Cash on Delivery')
            ->sum('amount');

        $codAmount = max(0, $total - $advance);

        $itemSales = $this->getAccountId('Item Sales');
        $shippingRev = $this->getAccountId('Revenue - Shipping');
        $customerAdvance = $this->getAccountId('Customer Advances');
        $codReceivable = $this->getAccountId('COD Receivable - BlueEx');

        if (!$itemSales || !$shippingRev) return null;

        $entries = [];
        if ($advance > 0 && $customerAdvance) {
            $entries[] = ['account_id' => $customerAdvance, 'debit' => $advance, 'credit' => 0, 'description' => "Advance applied for {$shipment->shipment_code}"];
        }
        if ($codAmount > 0 && $codReceivable) {
            $entries[] = ['account_id' => $codReceivable, 'debit' => $codAmount, 'credit' => 0, 'description' => "COD to collect for {$shipment->shipment_code}"];
        }
        if ($itemValue > 0) {
            $entries[] = ['account_id' => $itemSales, 'debit' => 0, 'credit' => $itemValue, 'description' => "Item sales for {$shipment->shipment_code}"];
        }
        if ($shippingRevenue > 0) {
            $entries[] = ['account_id' => $shippingRev, 'debit' => 0, 'credit' => $shippingRevenue, 'description' => "Shipping revenue for {$shipment->shipment_code}"];
        }

        $existingVoucher = Voucher::where('reference_type', 'shipment_delivery_revenue')
            ->where('reference_id', $shipment->id)
            ->where('is_deleted', false)
            ->first();

        if ($existingVoucher) {
            $currentAdvanceDebit = $existingVoucher->details()->where('account_id', $customerAdvance)->sum('debit');
            $currentCodDebit = $existingVoucher->details()->where('account_id', $codReceivable)->sum('debit');

            if (round($currentAdvanceDebit, 2) === round($advance, 2) && round($currentCodDebit, 2) === round($codAmount, 2)) {
                return $existingVoucher;
            }

            $existingVoucher->details()->delete();
            foreach ($entries as $entry) {
                VoucherDetail::create([
                    'voucher_id' => $existingVoucher->id,
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                    'description' => $entry['description'] ?? null,
                ]);
            }
            $existingVoucher->save();
            return $existingVoucher;
        }

        return $this->generateVoucher('system', 'shipment_delivery_revenue', $shipment->id, $shipment->date_delivered->toDateString(), "Revenue Recognition on Delivery for {$shipment->shipment_code}", $entries);
    }

    public function generateDeliveryCOGSVoucher($shipment): ?Voucher
    {
        if (!$shipment->date_delivered || $shipment->bought_by !== 'By Company' || (float) $shipment->item_value_pkr <= 0) return null;
        if ($this->voucherExists('shipment_delivery_cost', $shipment->id)) return null;

        $cogs = $this->getAccountId('Cost of Sales - Items');
        $inventory = $this->getAccountId('Inventory');
        if (!$cogs || !$inventory) return null;

        $amount = (float) $shipment->item_value_pkr;

        return $this->generateVoucher('system', 'shipment_delivery_cost', $shipment->id, $shipment->date_delivered->toDateString(), "Company Purchased Items - Cost Recognition for {$shipment->shipment_code}", [
            ['account_id' => $cogs, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $inventory, 'debit' => 0, 'credit' => $amount],
        ]);
    }

    public function generateDeliveryChargeVoucher($shipment): ?Voucher
    {
        if ((float) $shipment->delivery_charges <= 0) return null;

        $expense = $this->getAccountId('Local Delivery Expense');
        $codReceivable = $this->getAccountId('COD Receivable - BlueEx');
        if (!$expense || !$codReceivable) return null;

        $amount = (float) $shipment->delivery_charges;

        // ✅ Get ALL active courier vouchers for this shipment
        $activeVouchers = Voucher::where('reference_type', 'shipment_delivery_charge')
            ->where('reference_id', $shipment->id)
            ->where('is_deleted', false)
            ->get();

        foreach ($activeVouchers as $v) {
            $currentDebit = $v->details()->where('account_id', $expense)->sum('debit');

            // If an active voucher already exists with the exact amount, do nothing
            if (round($currentDebit, 2) === round($amount, 2)) {
                return $v;
            }

            // Otherwise, reverse it
            $entries = [
                ['account_id' => $expense, 'debit' => 0, 'credit' => $currentDebit],
                ['account_id' => $codReceivable, 'debit' => $currentDebit, 'credit' => 0],
            ];
            $this->generateVoucher('system', 'reversal_shipment_delivery_charge', $shipment->id, now()->toDateString(), "REVERSAL of {$v->voucher_no}", $entries);

            // Mark it as deleted so it's no longer considered active
            $v->is_deleted = true;
            $v->save();
        }

        // Create the new active voucher
        $date = now()->toDateString();
        return $this->generateVoucher('system', 'shipment_delivery_charge', $shipment->id, $date, "Local Delivery Charge for {$shipment->shipment_code}", [
            ['account_id' => $expense, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $codReceivable, 'debit' => 0, 'credit' => $amount],
        ]);
    }

    public function generatePaymentVoucher($shipment, $payment): ?Voucher
    {
        if ($this->voucherExists('shipment_payment', $payment->id)) return null;

        if (($payment->payment_method ?? '') === 'Cash on Delivery') {
            return $this->generateCODRemittanceVoucher($shipment, $payment);
        }

        $debitAccount = $this->getAccountForPaymentMethod(null, $payment->payment_method);

        if ($shipment->date_delivered) {
            $creditAccount = $this->getAccountId('COD Receivable - BlueEx');
            $desc = "Post-delivery payment of {$payment->amount} for {$shipment->shipment_code}";
        } else {
            $creditAccount = $this->getAccountId('Customer Advances');
            $desc = "Advance of {$payment->amount} received for {$shipment->shipment_code}";
        }

        if (!$debitAccount || !$creditAccount) return null;

        return $this->generateVoucher('system', 'shipment_payment', $payment->id, $payment->payment_date, $desc, [
            ['account_id' => $debitAccount, 'debit' => $payment->amount, 'credit' => 0],
            ['account_id' => $creditAccount, 'debit' => 0, 'credit' => $payment->amount],
        ]);
    }

    public function generateCODRemittanceVoucher($shipment, $payment): ?Voucher
    {
        if ($this->voucherExists('shipment_cod_remittance', $payment->id)) return null;

        $bank = $this->getAccountId('Bank Account');
        $codReceivable = $this->getAccountId('COD Receivable - BlueEx');

        if (!$bank || !$codReceivable) return null;

        // ✅ Calculate the net amount deposited in the bank.
        // For partial payments, the courier fee is only deducted on the FIRST remittance.
        $grossAmount = $payment->amount;

        // Check if a courier fee has already been deducted for this shipment
        $courierFeeAlreadyApplied = Voucher::where('reference_type', 'shipment_cod_remittance')
            ->whereIn('reference_id', function ($q) use ($shipment) {
                $q->select('id')->from('shipment_payments')->where('shipment_id', $shipment->id);
            })
            ->where('reference_id', '!=', $payment->id)
            ->exists();

        $courierFee = $courierFeeAlreadyApplied ? 0 : (float) $shipment->delivery_charges;
        $remittedAmount = max(0, $grossAmount - $courierFee);

        $entries = [
            ['account_id' => $bank, 'debit' => $remittedAmount, 'credit' => 0, 'description' => "Net remittance for {$shipment->shipment_code}"],
            ['account_id' => $codReceivable, 'debit' => 0, 'credit' => $remittedAmount, 'description' => "COD settled for {$shipment->shipment_code}"]
        ];

        return $this->generateVoucher(
            'system',
            'shipment_cod_remittance',
            $payment->id,
            $payment->payment_date,
            "COD Remittance of {$remittedAmount} for {$shipment->shipment_code}",
            $entries
        );
    }

    public function reversePaymentVoucher($payment): void
    {
        $vouchers = Voucher::whereIn('reference_type', ['shipment_payment', 'shipment_cod_remittance'])
            ->where('reference_id', $payment->id)->get();

        // ONLY reverse the delivery charge if the payment being deleted is a COD payment
        if (($payment->payment_method ?? '') === 'Cash on Delivery') {
            $courierVouchers = Voucher::where('reference_type', 'shipment_delivery_charge')
                ->where('reference_id', $payment->shipment_id)
                ->where('is_deleted', false)
                ->get();

            foreach ($courierVouchers as $courierVoucher) {
                // Prevent double reversal
                $alreadyReversed = Voucher::where('reference_type', 'reversal_shipment_delivery_charge')
                    ->where('reference_id', $payment->shipment_id)
                    ->where('description', 'LIKE', "%REVERSAL of {$courierVoucher->voucher_no}%")
                    ->exists();

                if (!$alreadyReversed) {
                    $vouchers->push($courierVoucher);
                }
            }
        }

        foreach ($vouchers as $v) {
            $entries = [];
            foreach ($v->details as $d) {
                if ($d->debit > 0) $entries[] = ['account_id' => $d->account_id, 'debit' => 0, 'credit' => $d->debit];
                if ($d->credit > 0) $entries[] = ['account_id' => $d->account_id, 'debit' => $d->credit, 'credit' => 0];
            }
            if (!empty($entries)) {
                $this->generateVoucher('system', 'reversal_' . $v->reference_type, $v->reference_id, now()->toDateString(), "REVERSAL of {$v->voucher_no}", $entries);
                // Mark original as deleted
                $v->is_deleted = true;
                $v->save();
            }
        }

        // Revenue voucher update is handled by the controller AFTER payment deletion
    }

    public function generateConsolidationCostVoucher($consolidation, $existingVoucher = null): ?Voucher
    {
        $entries = [];
        $totalCost = 0;

        $customsDuty = (float) ($consolidation->customs_duty ?? 0);
        if ($customsDuty > 0) {
            $accountId = $this->getAccountId('Customs Duty');
            if ($accountId) {
                $entries[] = [
                    'account_id' => $accountId,
                    'debit' => $customsDuty,
                    'credit' => 0,
                    'description' => "Customs Duty for {$consolidation->consol_id}"
                ];
                $totalCost += $customsDuty;
            }
        }

        $otherCharges = (float) ($consolidation->ware_house_charges ?? 0)
            + (float) ($consolidation->sales_tax ?? 0)
            + (float) ($consolidation->income_tax ?? 0)
            + (float) ($consolidation->caa_charges ?? 0);

        if ($otherCharges > 0) {
            $accountId = $this->getAccountId('Warehouse Charges');
            if ($accountId) {
                $entries[] = [
                    'account_id' => $accountId,
                    'debit' => $otherCharges,
                    'credit' => 0,
                    'description' => "Merged Other Charges for {$consolidation->consol_id}"
                ];
                $totalCost += $otherCharges;
            }
        }

        if ($totalCost <= 0) {
            if ($existingVoucher) {
                $existingVoucher->details()->delete();
                $existingVoucher->delete();
            }
            return null;
        }

        $creditor = $this->getAccountId('Consolidation Creditors') ?? $this->getAccountId('Creditors');
        if (!$creditor) return null;

        $entries[] = [
            'account_id' => $creditor,
            'debit' => 0,
            'credit' => $totalCost,
            'description' => "Consolidation costs payable for {$consolidation->consol_id}"
        ];

        if ($existingVoucher) {
            if (method_exists($existingVoucher, 'trashed') && $existingVoucher->trashed()) {
                $existingVoucher->restore();
            }

            $existingVoucher->details()->delete();
            foreach ($entries as $entry) {
                VoucherDetail::create([
                    'voucher_id' => $existingVoucher->id,
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                    'description' => $entry['description'] ?? null,
                ]);
            }
            $existingVoucher->description = "Costs for consolidation {$consolidation->consol_id}";
            $existingVoucher->date = $consolidation->date_reached ?? now()->toDateString();
            $existingVoucher->is_deleted = false;
            $existingVoucher->save();
            return $existingVoucher;
        }

        return $this->generateVoucher(
            'system',
            'consolidation',
            $consolidation->id,
            $consolidation->date_reached ?? now()->toDateString(),
            "Costs for consolidation {$consolidation->consol_id}",
            $entries
        );
    }

    public function generateDamagedShipmentVouchers($shipment, $wasDelivered = false): array
    {
        $created = [];

        if ($wasDelivered) {
            if (!$this->voucherExists('shipment_damage_revenue_reversal', $shipment->id)) {
                $itemSales = $this->getAccountId('Item Sales');
                $shippingRev = $this->getAccountId('Revenue - Shipping');
                $bank = $this->getAccountId('Bank Account');

                if ($itemSales && $shippingRev && $bank) {
                    $itemValue = (float) $shipment->item_value_pkr;
                    $shippingRevenue = (float) $shipment->company_charges;

                    $entries = [
                        ['account_id' => $itemSales, 'debit' => $itemValue, 'credit' => 0, 'description' => "Revenue reversal for damage {$shipment->shipment_code}"],
                        ['account_id' => $shippingRev, 'debit' => $shippingRevenue, 'credit' => 0, 'description' => "Shipping reversal for damage {$shipment->shipment_code}"],
                        ['account_id' => $bank, 'debit' => 0, 'credit' => $itemValue + $shippingRevenue, 'description' => "Refund to customer for damaged {$shipment->shipment_code}"],
                    ];

                    $v = $this->generateVoucher('system', 'shipment_damage_revenue_reversal', $shipment->id, now()->toDateString(), "Sales Return & Refund for {$shipment->shipment_code}", $entries);
                    if ($v) $created[] = $v;
                }
            }

            if (!$this->voucherExists('shipment_damage_cogs_reversal', $shipment->id)) {
                $damageExpense = $this->getAccountId('Damaged Goods Expense');
                $cogs = $this->getAccountId('Cost of Sales - Items');

                if ($damageExpense && $cogs) {
                    $itemValue = (float) $shipment->item_value_pkr;

                    $entries = [
                        ['account_id' => $damageExpense, 'debit' => $itemValue, 'credit' => 0, 'description' => "Damaged goods loss for {$shipment->shipment_code}"],
                        ['account_id' => $cogs, 'debit' => 0, 'credit' => $itemValue, 'description' => "Reversal of COGS for returned {$shipment->shipment_code}"],
                    ];

                    $v = $this->generateVoucher('system', 'shipment_damage_cogs_reversal', $shipment->id, now()->toDateString(), "COGS Reversal & Damage Loss for {$shipment->shipment_code}", $entries);
                    if ($v) $created[] = $v;
                }
            }
        } else {
            $itemValue = (float) ($shipment->item_value_pkr ?? 0);
            if ($shipment->bought_by === 'By Company' && $itemValue > 0) {
                if (!$this->voucherExists('shipment_damage_writeoff', $shipment->id)) {
                    $inventory = $this->getAccountId('Inventory');
                    $damageExpense = $this->getAccountId('Damaged Goods Expense');

                    if ($inventory && $damageExpense) {
                        $v = $this->generateVoucher(
                            'system',
                            'shipment_damage_writeoff',
                            $shipment->id,
                            now()->toDateString(),
                            "Damaged inventory write-off for {$shipment->shipment_code}",
                            [
                                ['account_id' => $damageExpense, 'debit' => $itemValue, 'credit' => 0, 'description' => "Damaged goods loss for {$shipment->shipment_code}"],
                                ['account_id' => $inventory, 'debit' => 0, 'credit' => $itemValue, 'description' => "Inventory write-off for {$shipment->shipment_code}"],
                            ]
                        );
                        if ($v) $created[] = $v;
                    }
                }
            }

            $advance = (float) ($shipment->received_amount ?? 0);
            if ($advance > 0) {
                if (!$this->voucherExists('shipment_refund', $shipment->id)) {
                    $customerAdvance = $this->getAccountId('Customer Advances');
                    $paymentAccountId = $this->getAccountForPaymentMethod($shipment->payment_method_id, $shipment->paymentMethod?->name) ?? $this->getAccountId('Bank Account');

                    if ($customerAdvance && $paymentAccountId) {
                        $v = $this->generateVoucher(
                            'system',
                            'shipment_refund',
                            $shipment->id,
                            now()->toDateString(),
                            "Advance refund for damaged shipment {$shipment->shipment_code}",
                            [
                                ['account_id' => $customerAdvance, 'debit' => $advance, 'credit' => 0, 'description' => "Refund to customer for {$shipment->shipment_code}"],
                                ['account_id' => $paymentAccountId, 'debit' => 0, 'credit' => $advance, 'description' => "Advance refund to customer for {$shipment->shipment_code}"],
                            ]
                        );
                        if ($v) $created[] = $v;
                    }
                }
            }
        }

        if (!$shipment->payments()->where('notes', 'Refund - Damaged shipment')->exists()) {
            $refundAmount = $wasDelivered ? $shipment->total : $shipment->received_amount;

            if ($refundAmount > 0) {
                $shipment->payments()->create([
                    'amount'           => -$refundAmount,
                    'payment_date'     => now()->toDateString(),
                    'payment_method'   => $shipment->paymentMethod?->name,
                    'reference_number' => null,
                    'notes'            => 'Refund - Damaged shipment',
                ]);
            }
        }

        $shipment->refresh();
        $shipment->recalcReceivedAmount();

        return $created;
    }

    public function reverseDamagedShipmentVouchers($shipment): void
    {
        $shipment->payments()
            ->where('notes', 'Refund - Damaged shipment')
            ->forceDelete();

        Voucher::where('reference_id', $shipment->id)
            ->whereIn('reference_type', [
                'shipment_damage_writeoff',
                'shipment_refund',
                'shipment_damage_revenue_reversal',
                'shipment_damage_cogs_reversal'
            ])
            ->get()
            ->each(function ($v) {
                $v->details()->delete();
                $v->forceDelete();
            });

        $shipment->refresh();
        $shipment->recalcReceivedAmount();
    }

    public function softDeleteShipmentVouchers($shipment)
    {
        $paymentIds = $shipment->payments()->withTrashed()->pluck('id')->toArray();

        Voucher::where(function ($q) use ($shipment) {
            $q->where('reference_type', 'like', 'shipment%')
                ->where('reference_id', $shipment->id);
        })->orWhere(function ($q) use ($paymentIds) {
            $q->whereIn('reference_type', ['shipment_payment', 'shipment_cod_remittance'])
                ->whereIn('reference_id', $paymentIds);
        })->delete();
    }

    public function restoreShipmentVouchers($shipment)
    {
        $paymentIds = $shipment->payments()->withTrashed()->pluck('id')->toArray();

        Voucher::onlyTrashed()->where(function ($q) use ($shipment) {
            $q->where('reference_type', 'like', 'shipment%')
                ->where('reference_id', $shipment->id);
        })->orWhere(function ($q) use ($paymentIds) {
            $q->whereIn('reference_type', ['shipment_payment', 'shipment_cod_remittance'])
                ->whereIn('reference_id', $paymentIds);
        })->restore();
    }
}
