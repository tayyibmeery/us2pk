<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherDetail;
use App\Models\TransactionTypeAccount;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class VoucherService
{
    public function generateVoucherNumber()
    {
        $last = Voucher::orderBy('id', 'desc')->first();
        $next = $last ? intval(substr($last->voucher_no, 4)) + 1 : 1;
        return 'USV-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new voucher with entries.
     */
    public function generateVoucher($source, $referenceType, $referenceId, $date, $description, array $entries, $approved = false)
    {
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

    /**
     * Update an existing voucher: clear old details and add new ones.
     */
    public function updateVoucher(Voucher $voucher, $date, $description, array $entries)
    {
        return DB::transaction(function () use ($voucher, $date, $description, $entries) {
            $voucher->update([
                'date' => $date,
                'description' => $description,
            ]);

            // Delete old details
            $voucher->details()->delete();

            foreach ($entries as $entry) {
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

    /**
     * Generate shipment revenue voucher – balanced double entry.
     */
    public function generateShipmentVoucher($shipment)
    {
        $mapping = TransactionTypeAccount::where('transaction_type', 'shipment_revenue')->first();
        if (!$mapping) {
            throw new \Exception('No mapping for shipment_revenue');
        }

        $revenueAccountId = $mapping->credit_account_id;
        $taxPayableAccountId = Account::where('name', 'Sales Tax Payable')->value('id');
        $cashAccountId = Account::where('name', 'Cash Account')->value('id');
        $debtorAccountId = Account::where('name', 'Debtors')->value('id');

        // Revenue and tax
        $revenue = $shipment->company_charges;
        $tax = $shipment->output_tax ?? 0;
        $totalCredit = $revenue + $tax;

        // Already received amount (could be partial)
        $received = $shipment->received_amount ?? 0;

        // Debit Cash up to received, but not exceeding totalCredit
        $cashDebit = min($received, $totalCredit);
        // Remaining goes to Debtors (if any)
        $debtorDebit = $totalCredit - $cashDebit;

        $entries = [];
        if ($cashDebit > 0) {
            $entries[] = ['account_id' => $cashAccountId, 'debit' => $cashDebit];
        }
        if ($debtorDebit > 0) {
            $entries[] = ['account_id' => $debtorAccountId, 'debit' => $debtorDebit];
        }
        $entries[] = ['account_id' => $revenueAccountId, 'credit' => $revenue];
        if ($tax > 0) {
            $entries[] = ['account_id' => $taxPayableAccountId, 'credit' => $tax];
        }

        // If for some reason entries are empty, skip
        if (empty($entries)) {
            return null;
        }

        // Ensure balanced (should be)
        $totalDebit = array_sum(array_column($entries, 'debit'));
        $totalCreditCalculated = array_sum(array_column($entries, 'credit'));
        if ($totalDebit != $totalCreditCalculated) {
            // Adjust: add difference to Debtors or Cash (should not happen)
            $diff = $totalCreditCalculated - $totalDebit;
            if ($diff > 0) {
                $entries[] = ['account_id' => $debtorAccountId, 'debit' => $diff];
            } elseif ($diff < 0) {
                $entries[] = ['account_id' => $debtorAccountId, 'credit' => -$diff];
            }
        }

        return $this->generateVoucher(
            'system',
            'shipment',
            $shipment->id,
            $shipment->created_at->toDateString(),
            "Revenue from shipment {$shipment->shipment_code}",
            $entries
        );
    }


    public function generateConsolidationCostVoucher($consolidation)
    {
        $mapping = TransactionTypeAccount::where('transaction_type', 'consolidation_cost')->first();
        if (!$mapping) {
            throw new \Exception('No mapping for consolidation_cost');
        }

        $entries = [];

        // Debit: Cost of Sales or Operating Expenses? We'll use mapping.
        $expenseAccountId = $mapping->debit_account_id;
        $creditAccountId = $mapping->credit_account_id; // usually Cash/Bank or Creditors

        $totalCost = $consolidation->ware_house_charges + $consolidation->customs_duty + $consolidation->sales_tax
            + $consolidation->income_tax + $consolidation->caa_charges;

        if ($totalCost > 0) {
            $entries[] = ['account_id' => $expenseAccountId, 'debit' => $totalCost];
            $entries[] = ['account_id' => $creditAccountId, 'credit' => $totalCost];
        }

        // Also handle courier charges? They might be separate.
        if ($consolidation->courier_charges > 0) {
            $courierMapping = TransactionTypeAccount::where('transaction_type', 'local_courier_charges')->first();
            if ($courierMapping) {
                $entries[] = ['account_id' => $courierMapping->debit_account_id, 'debit' => $consolidation->courier_charges];
                $entries[] = ['account_id' => $courierMapping->credit_account_id, 'credit' => $consolidation->courier_charges];
            }
        }

        if (empty($entries)) return null;

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

        $entries = [
            ['account_id' => $category->account_id, 'debit' => $expense->amount],
            ['account_id' => Account::where('name', 'Cash Account')->value('id'), 'credit' => $expense->amount],
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

    /**
     * Sync shipment voucher – creates or updates the linked voucher.
     */
    public function syncShipmentVoucher($shipment)
    {
        $entries = $this->buildShipmentEntries($shipment);
        if (empty($entries)) {
            return null;
        }

        $description = "Revenue from shipment {$shipment->shipment_code}";
        $date = $shipment->created_at->toDateString();

        if ($shipment->voucher) {
            return $this->updateVoucher(
                $shipment->voucher,
                $date,
                $description,
                $entries
            );
        } else {
            return $this->generateVoucher(
                'system',
                'shipment',
                $shipment->id,
                $date,
                $description,
                $entries
            );
        }
    }

    /**
     * Build balanced entries for a shipment.
     */
    private function buildShipmentEntries($shipment)
    {
        $mapping = TransactionTypeAccount::where('transaction_type', 'shipment_revenue')->first();
        if (!$mapping) {
            throw new \Exception('No mapping for shipment_revenue');
        }

        $revenueAccountId = $mapping->credit_account_id;
        $taxPayableAccountId = Account::where('name', 'Sales Tax Payable')->value('id');
        $cashAccountId = Account::where('name', 'Cash Account')->value('id');
        $debtorAccountId = Account::where('name', 'Debtors')->value('id');

        $revenue = $shipment->company_charges;
        $tax = $shipment->output_tax ?? 0;
        $totalCredit = $revenue + $tax;

        $received = $shipment->received_amount ?? 0;
        $cashDebit = min($received, $totalCredit);
        $debtorDebit = $totalCredit - $cashDebit;

        $entries = [];
        if ($cashDebit > 0) {
            $entries[] = ['account_id' => $cashAccountId, 'debit' => $cashDebit];
        }
        if ($debtorDebit > 0) {
            $entries[] = ['account_id' => $debtorAccountId, 'debit' => $debtorDebit];
        }
        $entries[] = ['account_id' => $revenueAccountId, 'credit' => $revenue];
        if ($tax > 0) {
            $entries[] = ['account_id' => $taxPayableAccountId, 'credit' => $tax];
        }

        // Ensure balanced – add a balancing entry if needed
        $totalDebit = array_sum(array_column($entries, 'debit'));
        $totalCreditCalc = array_sum(array_column($entries, 'credit'));
        if ($totalDebit != $totalCreditCalc) {
            $diff = $totalCreditCalc - $totalDebit;
            if ($diff > 0) {
                $entries[] = ['account_id' => $debtorAccountId, 'debit' => $diff];
            } elseif ($diff < 0) {
                $entries[] = ['account_id' => $debtorAccountId, 'credit' => -$diff];
            }
        }

        return $entries;
    }
}
