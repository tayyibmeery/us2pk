<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherDetail;
use App\Models\Account;
use App\Models\TransactionTypeAccount;
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

            Log::info('Voucher generated', [
                'voucher_no' => $voucher->voucher_no,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'entries_count' => count($entries)
            ]);

            return $voucher;
        });
    }

    private function getAccountId($name)
    {
        return Account::where('name', $name)->value('id');
    }

    // private function getAccountForPaymentMethod($paymentMethodName)
    // {
    //     $accountMap = [
    //         'Bank Transfer' => 'Cash Account',
    //         'Cash' => 'Cash Account',
    //         'JazzCash' => 'Cash Account',
    //         'EasyPaisa' => 'Cash Account',
    //         'Credit Card' => 'Cash Account',
    //         'Bank Transfer (IBFT)' => 'Cash Account',
    //         'Cheque' => 'Cash Account',
    //     ];
    //     return $accountMap[$paymentMethodName] ?? 'Cash Account';
    // }
    /**
     * Get account ID for a payment method
     * First tries to get from payment_method->account_id mapping
     * If not found, falls back to Cash Account
     */
    private function getAccountForPaymentMethod($paymentMethodId, $paymentMethodName = null)
    {
        // If payment_method_id is provided, try to get the mapped account
        if ($paymentMethodId) {
            $paymentMethod = \App\Models\PaymentMethod::with('account')
                ->where('id', $paymentMethodId)
                ->first();

            if ($paymentMethod && $paymentMethod->account_id) {
                Log::info('Using mapped account for payment method', [
                    'payment_method' => $paymentMethod->name,
                    'account' => $paymentMethod->account?->name ?? 'Not found'
                ]);
                return $paymentMethod->account_id;
            }
        }

        // Fallback: Use the hardcoded mapping
        $fallbackMap = [
            'Bank Transfer' => 'Cash Account',
            'Cash' => 'Cash Account',
            'JazzCash' => 'Cash Account',
            'EasyPaisa' => 'Cash Account',
            'Credit Card' => 'Cash Account',
            'Bank Transfer (IBFT)' => 'Cash Account',
            'Cheque' => 'Cash Account',
            'Cash on Delivery' => 'Cash Account',
        ];

        $accountName = $fallbackMap[$paymentMethodName] ?? 'Cash Account';
        return $this->getAccountId($accountName);
    }

    // public function generateShipmentVouchers($shipment)
    // {
    //     $total = ($shipment->item_value_pkr ?? 0) + ($shipment->company_charges ?? 0);
    //     $received = $shipment->received_amount ?? 0;
    //     $courierCharges = $shipment->delivery_charges ?? 0;

    //     $grossCod = max(0, $total - $received);
    //     $netReceivable = max(0, $grossCod - $courierCharges);

    //     $date = $shipment->created_at->toDateString();

    //     $debtorAccountId = $this->getAccountId('Debtors');
    //     $revenueShippingAccountId = $this->getAccountId('Revenue - Shipping');
    //     $costOfSalesAccountId = $this->getAccountId('Cost of Sales - Items');
    //     $inventoryAccountId = $this->getAccountId('Inventory');
    //     $courierExpenseAccountId = $this->getAccountId('Courier Charges Expense');

    //     $paymentMethodName = $shipment->paymentMethod?->name ?? 'Cash';
    //     $paymentAccountName = $this->getAccountForPaymentMethod($paymentMethodName);
    //     // $paymentAccountId = $this->getAccountId($paymentAccountName);
    //     $paymentAccountId = $this->getAccountForPaymentMethod($shipment->payment_method_id, $paymentMethodName);

    //     $vouchers = [];

    //     // ============================================================
    //     // 1. REVENUE RECOGNITION VOUCHER - SIMPLE AND CLEAN
    //     // ============================================================
    //     if ($total > 0 && $debtorAccountId && $revenueShippingAccountId) {
    //         Log::info('Generating Revenue Recognition', [
    //             'shipment' => $shipment->shipment_code,
    //             'bought_by' => $shipment->bought_by,
    //             'total' => $total,
    //             'received' => $received,
    //             'gross_cod' => $grossCod,
    //             'courier_charges' => $courierCharges,
    //             'net_receivable' => $netReceivable
    //         ]);

    //         // SIMPLE APPROACH: Recognize revenue at gross amount
    //         // Dr: Debtors = Total amount
    //         // Cr: Revenue - Shipping = Total amount
    //         $entries1 = [
    //             [
    //                 'account_id' => $debtorAccountId,
    //                 'debit' => $total,
    //                 'credit' => 0,
    //                 'description' => "Revenue from shipment {$shipment->shipment_code}"
    //             ],
    //             [
    //                 'account_id' => $revenueShippingAccountId,
    //                 'debit' => 0,
    //                 'credit' => $total,
    //                 'description' => "Revenue from shipment {$shipment->shipment_code}"
    //             ]
    //         ];

    //         $vouchers[] = $this->generateVoucher(
    //             'system',
    //             'shipment_revenue',
    //             $shipment->id,
    //             $date,
    //             "Shipment Created - Revenue Recognition for {$shipment->shipment_code}",
    //             $entries1
    //         );
    //     }

    //     // ============================================================
    //     // 2. COURIER CHARGES VOUCHER (ONLY if courier charges exist)
    //     // ============================================================
    //     // This reduces the Debtors by the courier charges
    //     if ($courierCharges > 0 && $courierExpenseAccountId && $debtorAccountId) {
    //         Log::info('Creating courier charges voucher', [
    //             'shipment' => $shipment->shipment_code,
    //             'courier_charges' => $courierCharges
    //         ]);

    //         // Dr: Courier Charges Expense = 200
    //         // Cr: Debtors = 200 (Reduce the receivable)
    //         $entriesCourier = [
    //             [
    //                 'account_id' => $courierExpenseAccountId,
    //                 'debit' => $courierCharges,
    //                 'credit' => 0,
    //                 'description' => "Courier charges for {$shipment->shipment_code}"
    //             ],
    //             [
    //                 'account_id' => $debtorAccountId,
    //                 'debit' => 0,
    //                 'credit' => $courierCharges,
    //                 'description' => "Courier charges reducing debtor for {$shipment->shipment_code}"
    //             ]
    //         ];

    //         $vouchers[] = $this->generateVoucher(
    //             'system',
    //             'shipment_courier',
    //             $shipment->id,
    //             $date,
    //             "Courier Charges Deduction for {$shipment->shipment_code}",
    //             $entriesCourier
    //         );
    //     }

    //     // ============================================================
    //     // 3. ADVANCE PAYMENT VOUCHER
    //     // ============================================================
    //     if ($received > 0 && $paymentAccountId && $debtorAccountId) {
    //         Log::info('Creating advance payment voucher', [
    //             'shipment' => $shipment->shipment_code,
    //             'amount' => $received,
    //             'payment_account' => $paymentAccountName,
    //         ]);

    //         $entries2 = [
    //             [
    //                 'account_id' => $paymentAccountId,
    //                 'debit' => $received,
    //                 'credit' => 0,
    //                 'description' => "Customer advance for shipment {$shipment->shipment_code}"
    //             ],
    //             [
    //                 'account_id' => $debtorAccountId,
    //                 'debit' => 0,
    //                 'credit' => $received,
    //                 'description' => "Customer advance for shipment {$shipment->shipment_code}"
    //             ]
    //         ];

    //         $vouchers[] = $this->generateVoucher(
    //             'system',
    //             'shipment_advance',
    //             $shipment->id,
    //             $date,
    //             "Customer Advance Received for {$shipment->shipment_code}",
    //             $entries2
    //         );
    //     }

    //     // ============================================================
    //     // 4. COST RECOGNITION VOUCHER (ONLY if Bought By Company AND item_value > 0)
    //     // ============================================================
    //     $itemValue = $shipment->item_value_pkr ?? 0;
    //     if ($itemValue > 0 && $costOfSalesAccountId && $inventoryAccountId && $shipment->bought_by === 'By Company') {
    //         $entries3 = [
    //             [
    //                 'account_id' => $costOfSalesAccountId,
    //                 'debit' => $itemValue,
    //                 'credit' => 0,
    //                 'description' => "Cost of items for shipment {$shipment->shipment_code}"
    //             ],
    //             [
    //                 'account_id' => $inventoryAccountId,
    //                 'debit' => 0,
    //                 'credit' => $itemValue,
    //                 'description' => "Inventory reduction for shipment {$shipment->shipment_code}"
    //             ]
    //         ];

    //         $vouchers[] = $this->generateVoucher(
    //             'system',
    //             'shipment_cost',
    //             $shipment->id,
    //             $date,
    //             "Company Purchased Items - Cost Recognition for {$shipment->shipment_code}",
    //             $entries3
    //         );
    //     }

    //     Log::info('Shipment vouchers generated', [
    //         'shipment' => $shipment->shipment_code,
    //         'bought_by' => $shipment->bought_by,
    //         'voucher_count' => count($vouchers),
    //         'received_amount' => $received,
    //         'courier_charges' => $courierCharges
    //     ]);

    //     return $vouchers;
    // }

    public function generateShipmentVouchers($shipment)
    {
        $total = ($shipment->item_value_pkr ?? 0) + ($shipment->company_charges ?? 0);
        $received = $shipment->received_amount ?? 0;
        $courierCharges = $shipment->delivery_charges ?? 0;

        $grossCod = max(0, $total - $received);
        $netReceivable = max(0, $grossCod - $courierCharges);

        $date = $shipment->created_at->toDateString();

        $debtorAccountId = $this->getAccountId('Debtors');
        $revenueShippingAccountId = $this->getAccountId('Revenue - Shipping');
        $costOfSalesAccountId = $this->getAccountId('Cost of Sales - Items');
        $inventoryAccountId = $this->getAccountId('Inventory');
        $courierExpenseAccountId = $this->getAccountId('Courier Charges Expense');

        $paymentMethodName = $shipment->paymentMethod?->name ?? 'Cash';

        // Get the payment account ID using the new method
        $paymentAccountId = $this->getAccountForPaymentMethod(
            $shipment->payment_method_id,
            $paymentMethodName
        );

        $vouchers = [];

        // 1. REVENUE RECOGNITION
        if ($total > 0 && $debtorAccountId && $revenueShippingAccountId) {
            Log::info('Generating Revenue Recognition', [
                'shipment' => $shipment->shipment_code,
                'bought_by' => $shipment->bought_by,
                'total' => $total,
                'received' => $received,
                'gross_cod' => $grossCod,
                'courier_charges' => $courierCharges,
                'net_receivable' => $netReceivable
            ]);

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

        // 2. COURIER CHARGES VOUCHER
        if ($courierCharges > 0 && $courierExpenseAccountId && $debtorAccountId) {
            Log::info('Creating courier charges voucher', [
                'shipment' => $shipment->shipment_code,
                'courier_charges' => $courierCharges
            ]);

            $entriesCourier = [
                [
                    'account_id' => $courierExpenseAccountId,
                    'debit' => $courierCharges,
                    'credit' => 0,
                    'description' => "Courier charges for {$shipment->shipment_code}"
                ],
                [
                    'account_id' => $debtorAccountId,
                    'debit' => 0,
                    'credit' => $courierCharges,
                    'description' => "Courier charges reducing debtor for {$shipment->shipment_code}"
                ]
            ];

            $vouchers[] = $this->generateVoucher(
                'system',
                'shipment_courier',
                $shipment->id,
                $date,
                "Courier Charges Deduction for {$shipment->shipment_code}",
                $entriesCourier
            );
        }

        // 3. ADVANCE PAYMENT VOUCHER - USING MAPPED ACCOUNT
        if ($received > 0 && $paymentAccountId && $debtorAccountId) {
            Log::info('Creating advance payment voucher', [
                'shipment' => $shipment->shipment_code,
                'amount' => $received,
                'payment_account_id' => $paymentAccountId,
                'payment_method' => $paymentMethodName
            ]);

            $entries2 = [
                [
                    'account_id' => $paymentAccountId,  // Now using mapped account
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

        // 4. COST RECOGNITION VOUCHER
        $itemValue = $shipment->item_value_pkr ?? 0;
        if ($itemValue > 0 && $costOfSalesAccountId && $inventoryAccountId && $shipment->bought_by === 'By Company') {
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

        Log::info('Shipment vouchers generated', [
            'shipment' => $shipment->shipment_code,
            'bought_by' => $shipment->bought_by,
            'voucher_count' => count($vouchers),
            'received_amount' => $received,
            'courier_charges' => $courierCharges,
            'payment_method' => $paymentMethodName,
            'payment_account_id' => $paymentAccountId
        ]);

        return $vouchers;
    }

    public function syncShipmentVouchers($shipment)
    {
        $voucherTypes = [
            'shipment_revenue',
            'shipment_advance',
            'shipment_cost',
            'shipment_courier',
            'shipment_cod',
            'shipment_settlement'
        ];

        $deletedCount = 0;
        foreach ($voucherTypes as $type) {
            $vouchers = Voucher::where('reference_type', $type)
                ->where('reference_id', $shipment->id)
                ->get();

            foreach ($vouchers as $voucher) {
                $voucher->details()->delete();
                $voucher->delete();
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            Log::info('Deleted old vouchers', [
                'shipment' => $shipment->shipment_code,
                'deleted_count' => $deletedCount
            ]);
        }

        $newVouchers = $this->generateShipmentVouchers($shipment);

        Log::info('Generated new vouchers', [
            'shipment' => $shipment->shipment_code,
            'new_voucher_count' => count($newVouchers)
        ]);

        return $newVouchers;
    }

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
            'net_amount' => $netAmount,
            'bought_by' => $shipment->bought_by
        ]);

        $entries = [];

        // Dr: Cash Account (net amount received from courier)
        if ($netAmount > 0) {
            $entries[] = [
                'account_id' => $cashAccountId,
                'debit' => $netAmount,
                'credit' => 0,
                'description' => "COD Net amount received from courier for {$shipment->shipment_code}"
            ];
        }

        // Dr: Courier Charges Expense (courier deduction) - ONLY if NOT already recorded
        // Since we already have a separate courier charges voucher, we don't need to record it again
        // But for COD, the courier charges are already deducted from the amount
        // So we just record the net amount received

        // Cr: Debtors (reduce by the net amount received)
        if ($netAmount > 0) {
            $entries[] = [
                'account_id' => $debtorAccountId,
                'debit' => 0,
                'credit' => $netAmount,
                'description' => "COD received - Net amount after courier deduction for {$shipment->shipment_code}"
            ];
        }

        if (empty($entries)) {
            Log::warning('No entries for COD voucher', ['shipment' => $shipment->id]);
            return null;
        }

        $totalDebit = array_sum(array_column($entries, 'debit'));
        $totalCredit = array_sum(array_column($entries, 'credit'));

        if (round($totalDebit, 2) != round($totalCredit, 2)) {
            Log::warning('COD voucher entries not balanced, adjusting', [
                'debit' => $totalDebit,
                'credit' => $totalCredit,
                'difference' => $totalDebit - $totalCredit
            ]);

            if ($totalDebit > $totalCredit) {
                $entries[] = [
                    'account_id' => $debtorAccountId,
                    'debit' => 0,
                    'credit' => round($totalDebit - $totalCredit, 2),
                    'description' => 'Balancing entry'
                ];
            } else {
                $entries[] = [
                    'account_id' => $debtorAccountId,
                    'debit' => round($totalCredit - $totalDebit, 2),
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
     * Generate voucher for consolidation costs
     * ✅ Creates voucher for Warehouse Charges + Import Taxes only
     * ❌ Excludes courier charges (they are recorded in shipment COD vouchers)
     */
    public function generateConsolidationCostVoucher($consolidation)
    {
        // Delete existing voucher if any
        $existingVoucher = $consolidation->voucher;
        if ($existingVoucher) {
            $existingVoucher->details()->delete();
            $existingVoucher->delete();
            Log::info('Deleted existing consolidation voucher', [
                'consolidation' => $consolidation->consol_id,
                'voucher_no' => $existingVoucher->voucher_no
            ]);
        }

        $mapping = TransactionTypeAccount::where('transaction_type', 'consolidation_cost')->first();
        if (!$mapping) {
            throw new \Exception('No mapping for consolidation_cost');
        }

        $entries = [];
        $expenseAccountId = $mapping->debit_account_id;
        $creditAccountId = $mapping->credit_account_id;

        // ✅ Only include consolidation costs (Warehouse + Import Taxes)
        // ❌ EXCLUDING courier charges (they are recorded in shipment COD vouchers)
        $totalCost = ($consolidation->ware_house_charges ?? 0)
            + ($consolidation->customs_duty ?? 0)
            + ($consolidation->sales_tax ?? 0)
            + ($consolidation->income_tax ?? 0)
            + ($consolidation->caa_charges ?? 0);

        Log::info('Generating consolidation cost voucher', [
            'consolidation' => $consolidation->consol_id,
            'ware_house_charges' => $consolidation->ware_house_charges ?? 0,
            'customs_duty' => $consolidation->customs_duty ?? 0,
            'sales_tax' => $consolidation->sales_tax ?? 0,
            'income_tax' => $consolidation->income_tax ?? 0,
            'caa_charges' => $consolidation->caa_charges ?? 0,
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
