<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherDetail;
use App\Models\TransactionTypeAccount;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherService
{

    public function generateVoucherNumber()
    {
        $last = Voucher::orderBy('id', 'desc')->first();
        $next = $last ? intval(substr($last->voucher_no, 4)) + 1 : 1;
        return 'USV-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public function generateVoucher($source, $referenceType, $referenceId, $date, $description, array $entries, $approved = true)
    {
        // Validate entries have required keys
        foreach ($entries as $index => $entry) {
            if (!isset($entry['account_id'])) {
                throw new \Exception("Entry at index {$index} is missing 'account_id'");
            }
            if (!isset($entry['debit'])) {
                $entries[$index]['debit'] = 0;
            }
            if (!isset($entry['credit'])) {
                $entries[$index]['credit'] = 0;
            }
        }

        // Validate entries are balanced
        $totalDebit = array_sum(array_column($entries, 'debit'));
        $totalCredit = array_sum(array_column($entries, 'credit'));

        if (round($totalDebit, 2) != round($totalCredit, 2)) {
            Log::warning('Voucher entries not balanced', [
                'debit' => $totalDebit,
                'credit' => $totalCredit,
                'difference' => $totalDebit - $totalCredit
            ]);
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
            ]);

            foreach ($entries as $entry) {
                $debit = isset($entry['debit']) ? (float) $entry['debit'] : 0;
                $credit = isset($entry['credit']) ? (float) $entry['credit'] : 0;

                if ($debit > 0 || $credit > 0) {
                    VoucherDetail::create([
                        'voucher_id' => $voucher->id,
                        'account_id' => $entry['account_id'],
                        'debit' => $debit,
                        'credit' => $credit,
                        'description' => $entry['description'] ?? null,
                    ]);
                }
            }

            return $voucher;
        });
    }

    private function getAccountId($name)
    {
        return Account::where('name', $name)->value('id');
    }

    public function generateShipmentVouchers($shipment)
    {
        $total = ($shipment->item_value_pkr ?? 0) + ($shipment->company_charges ?? 0);
        $received = $shipment->received_amount ?? 0;
        $date = $shipment->created_at->toDateString();


        $debtorAccountId = $this->getAccountId('Debtors');
        $cashAccountId = $this->getAccountId('Cash Account');
        $revenueShippingAccountId = $this->getAccountId('Revenue - Shipping');
        $costOfSalesAccountId = $this->getAccountId('Cost of Sales - Items');
        $inventoryAccountId = $this->getAccountId('Inventory');

        $vouchers = [];


        if ($total > 0 && $debtorAccountId && $revenueShippingAccountId) {
            $entries1 = [
                [
                    'account_id' => $debtorAccountId,
                    'debit' => $total,
                    'credit' => 0,
                    'description' => "Revenue from shipment {$shipment->shipment_code}"
                ],
                [
                    'account_id' => $revenueShippingAccountId,
                    'debit' => 0,
                    'credit' => $total,
                    'description' => "Revenue from shipment {$shipment->shipment_code}"
                ]
            ];

            $vouchers[] = $this->generateVoucher(
                'system',
                'shipment_revenue',
                $shipment->id,
                $date,
                "Shipment Created - Revenue Recognition for {$shipment->shipment_code}",
                $entries1
            );
        }


        if ($received > 0 && $cashAccountId && $debtorAccountId) {
            $entries2 = [
                [
                    'account_id' => $cashAccountId,
                    'debit' => $received,
                    'credit' => 0,
                    'description' => "Customer advance for shipment {$shipment->shipment_code}"
                ],
                [
                    'account_id' => $debtorAccountId,
                    'debit' => 0,
                    'credit' => $received,
                    'description' => "Customer advance for shipment {$shipment->shipment_code}"
                ]
            ];

            $vouchers[] = $this->generateVoucher(
                'system',
                'shipment_advance',
                $shipment->id,
                $date,
                "Customer Advance Received for {$shipment->shipment_code}",
                $entries2
            );
        }


        $itemValue = $shipment->item_value_pkr ?? 0;
        if ($itemValue > 0 && $costOfSalesAccountId && $inventoryAccountId) {
            $entries3 = [
                [
                    'account_id' => $costOfSalesAccountId,
                    'debit' => $itemValue,
                    'credit' => 0,
                    'description' => "Cost of items for shipment {$shipment->shipment_code}"
                ],
                [
                    'account_id' => $inventoryAccountId,
                    'debit' => 0,
                    'credit' => $itemValue,
                    'description' => "Inventory reduction for shipment {$shipment->shipment_code}"
                ]
            ];

            $vouchers[] = $this->generateVoucher(
                'system',
                'shipment_cost',
                $shipment->id,
                $date,
                "Company Purchased Items - Cost Recognition for {$shipment->shipment_code}",
                $entries3
            );
        }


        return $vouchers;
    }


    /**
     * Generate COD Received Voucher (کوریئر چارج سمیت - صرف ایک بار)
     */
    public function generateCODVoucher($shipment, $codAmount, $date)
    {
        $debtorAccountId = $this->getAccountId('Debtors');
        $cashAccountId = $this->getAccountId('Cash Account');
        $courierExpenseAccountId = $this->getAccountId('Courier Charges Expense');

        if (!$debtorAccountId || !$cashAccountId) {
            Log::warning('Missing required accounts for COD voucher');
            return null;
        }

        $courierCharges = $shipment->delivery_charges ?? 0;
        $netAmount = $codAmount - $courierCharges;

        Log::info('Generating COD voucher', [
            'shipment' => $shipment->shipment_code,
            'cod_amount' => $codAmount,
            'courier_charges' => $courierCharges,
            'net_amount' => $netAmount
        ]);

        $entries = [];

        // Dr: Cash Account (کوریئر سے موصول ہونے والی خالص رقم)
        if ($netAmount > 0) {
            $entries[] = [
                'account_id' => $cashAccountId,
                'debit' => $netAmount,
                'credit' => 0,
                'description' => "COD Net amount received from courier for {$shipment->shipment_code}"
            ];
        }

        // Dr: Courier Charges Expense (کوریئر کی کٹوتی - صرف ایک بار)
        if ($courierCharges > 0 && $courierExpenseAccountId) {
            $entries[] = [
                'account_id' => $courierExpenseAccountId,
                'debit' => $courierCharges,
                'credit' => 0,
                'description' => "Courier charges deducted from COD for {$shipment->shipment_code}"
            ];
        }

        // Cr: Debtors (کل COD - کسٹمر کا قرض کم ہوا)
        if ($codAmount > 0) {
            $entries[] = [
                'account_id' => $debtorAccountId,
                'debit' => 0,
                'credit' => $codAmount,
                'description' => "COD received for {$shipment->shipment_code}"
            ];
        }

        if (empty($entries)) {
            Log::warning('No entries for COD voucher', ['shipment' => $shipment->id]);
            return null;
        }

        // Check if entries are balanced
        $totalDebit = array_sum(array_column($entries, 'debit'));
        $totalCredit = array_sum(array_column($entries, 'credit'));

        if (round($totalDebit, 2) != round($totalCredit, 2)) {
            Log::warning('COD voucher entries not balanced, adjusting', [
                'debit' => $totalDebit,
                'credit' => $totalCredit,
                'difference' => $totalDebit - $totalCredit
            ]);

            // Add balancing entry
            if ($totalDebit > $totalCredit) {
                $entries[] = [
                    'account_id' => $debtorAccountId,
                    'debit' => 0,
                    'credit' => $totalDebit - $totalCredit,
                    'description' => 'Balancing entry'
                ];
            } else {
                $entries[] = [
                    'account_id' => $debtorAccountId,
                    'debit' => $totalCredit - $totalDebit,
                    'credit' => 0,
                    'description' => 'Balancing entry'
                ];
            }
        }

        return $this->generateVoucher(
            'system',
            'shipment_cod',
            $shipment->id,
            $date,
            "COD Received - Customer paid courier, courier deducted {$courierCharges} and paid net {$netAmount} for {$shipment->shipment_code}",
            $entries
        );
    }
    public function generateSettlementVoucher($shipment, $balance, $date)
    {
        if ($balance == 0) {
            return null;
        }

        $debtorAccountId = $this->getAccountId('Debtors');
        $cashAccountId = $this->getAccountId('Cash Account');

        if (!$debtorAccountId || !$cashAccountId) {
            Log::warning('Missing required accounts for settlement voucher');
            return null;
        }

        $entries = [];

        if ($balance > 0) {

            // Dr: Cash Account, Cr: Debtors
            $entries = [
                [
                    'account_id' => $cashAccountId,
                    'debit' => $balance,
                    'credit' => 0,
                    'description' => "Final payment received for {$shipment->shipment_code}"
                ],
                [
                    'account_id' => $debtorAccountId,
                    'debit' => 0,
                    'credit' => $balance,
                    'description' => "Final payment received for {$shipment->shipment_code}"
                ]
            ];
        } else {

            // Dr: Debtors, Cr: Cash Account
            $refundAmount = abs($balance);
            $entries = [
                [
                    'account_id' => $debtorAccountId,
                    'debit' => $refundAmount,
                    'credit' => 0,
                    'description' => "Refund to customer for overpayment on {$shipment->shipment_code}"
                ],
                [
                    'account_id' => $cashAccountId,
                    'debit' => 0,
                    'credit' => $refundAmount,
                    'description' => "Refund to customer for overpayment on {$shipment->shipment_code}"
                ]
            ];
        }

        return $this->generateVoucher(
            'system',
            'shipment_settlement',
            $shipment->id,
            $date,
            "Shipment Closed - Final Settlement for {$shipment->shipment_code}",
            $entries
        );
    }

    /**
     * Sync shipment vouchers - creates or updates the linked vouchers
     */
    public function syncShipmentVouchers($shipment)
    {
        // Delete existing vouchers for this shipment
        $voucherTypes = [
            'shipment_revenue',
            'shipment_advance',
            'shipment_cost',
            'shipment_courier',
            'shipment_cod',
            'shipment_settlement'
        ];

        foreach ($voucherTypes as $type) {
            $vouchers = Voucher::where('reference_type', $type)
                ->where('reference_id', $shipment->id)
                ->get();

            foreach ($vouchers as $voucher) {
                $voucher->details()->delete();
                $voucher->delete();
            }
        }

        // Generate new vouchers
        return $this->generateShipmentVouchers($shipment);
    }

    /**
     * Generate voucher for consolidation costs
     */
    /**
     * Generate voucher for consolidation costs
     * ❌ Removed courier charges - they are already recorded in shipment COD vouchers
     */
    public function generateConsolidationCostVoucher($consolidation)
    {
        $mapping = TransactionTypeAccount::where('transaction_type', 'consolidation_cost')->first();
        if (!$mapping) {
            throw new \Exception('No mapping for consolidation_cost');
        }

        $entries = [];

        // Get account IDs
        $expenseAccountId = $mapping->debit_account_id;
        $creditAccountId = $mapping->credit_account_id; // usually Creditors

        // ✅ Only include consolidation costs, NOT courier charges
        // Courier charges are already recorded in shipment COD vouchers
        $totalCost = $consolidation->ware_house_charges
            + $consolidation->customs_duty
            + $consolidation->sales_tax
            + $consolidation->income_tax
            + $consolidation->caa_charges;

        Log::info('Generating consolidation cost voucher', [
            'consolidation' => $consolidation->consol_id,
            'ware_house_charges' => $consolidation->ware_house_charges,
            'customs_duty' => $consolidation->customs_duty,
            'sales_tax' => $consolidation->sales_tax,
            'income_tax' => $consolidation->income_tax,
            'caa_charges' => $consolidation->caa_charges,
            'total_cost' => $totalCost
        ]);

        if ($totalCost > 0 && $expenseAccountId && $creditAccountId) {
            // Debit: Expense account (Customs Duty, etc.)
            $entries[] = [
                'account_id' => $expenseAccountId,
                'debit' => $totalCost,
                'credit' => 0,
                'description' => "Consolidation costs for {$consolidation->consol_id}"
            ];
            // Credit: Creditors
            $entries[] = [
                'account_id' => $creditAccountId,
                'debit' => 0,
                'credit' => $totalCost,
                'description' => "Consolidation costs payable for {$consolidation->consol_id}"
            ];
        }

        // ❌ REMOVED: Courier charges section - they are already recorded in shipment COD vouchers
        // The courier charges are recorded when the shipment COD is cleared

        if (empty($entries)) {
            Log::info('No consolidation costs to record for ' . $consolidation->consol_id);
            return null;
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

    /**
     * Generate voucher for manual expense
     */
    public function generateExpenseVoucher($expense)
    {
        $category = $expense->category;
        if (!$category || !$category->account_id) {
            throw new \Exception('Expense category has no account mapping');
        }

        $cashAccountId = $this->getAccountId('Cash Account');
        if (!$cashAccountId) {
            throw new \Exception('Cash Account not found');
        }

        $entries = [
            [
                'account_id' => $category->account_id,
                'debit' => $expense->amount,
                'credit' => 0,
                'description' => $expense->description ?? 'Expense voucher'
            ],
            [
                'account_id' => $cashAccountId,
                'debit' => 0,
                'credit' => $expense->amount,
                'description' => $expense->description ?? 'Expense voucher'
            ]
        ];

        return $this->generateVoucher(
            'system',
            'expense',
            $expense->id,
            $expense->date,
            $expense->description ?? 'Expense voucher',
            $entries
        );
    }

    public function generatePaymentVoucher($shipment, $payment)
    {
        $cashAccountId = $this->getAccountId('Cash Account');
        $debtorAccountId = $this->getAccountId('Debtors');

        if (!$cashAccountId || !$debtorAccountId) {
            Log::warning('Missing required accounts for payment voucher');
            return null;
        }

        $entries = [
            [
                'account_id' => $cashAccountId,
                'debit' => $payment->amount,
                'credit' => 0,
                'description' => "Payment received for shipment {$shipment->shipment_code}"
            ],
            [
                'account_id' => $debtorAccountId,
                'debit' => 0,
                'credit' => $payment->amount,
                'description' => "Payment received for shipment {$shipment->shipment_code}"
            ]
        ];

        return $this->generateVoucher(
            'system',
            'payment',
            $payment->id,
            $payment->payment_date,
            "Payment of {$payment->amount} for shipment {$shipment->shipment_code}",
            $entries
        );
    }

    /**
     * Delete a payment voucher
     */
    public function deletePaymentVoucher($payment)
    {
        $voucher = Voucher::where('reference_type', 'payment')
            ->where('reference_id', $payment->id)
            ->first();

        if ($voucher) {
            $voucher->details()->delete();
            $voucher->delete();
            return true;
        }
        return false;
    }
}
