<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherDetail;
use App\Models\TransactionTypeAccount;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class VoucherService
{
    /**
     * Generate a voucher number (USV-00001)
     */
    public function generateVoucherNumber()
    {
        $last = Voucher::orderBy('id', 'desc')->first();
        $next = $last ? intval(substr($last->voucher_no, 4)) + 1 : 1;
        return 'USV-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Create a voucher with details
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
     * Generate voucher for shipment revenue
     */
    public function generateShipmentVoucher($shipment)
    {
        $mapping = TransactionTypeAccount::where('transaction_type', 'shipment_revenue')->first();
        if (!$mapping) {
            throw new \Exception('No mapping for shipment_revenue');
        }

        $total = $shipment->item_value_pkr + $shipment->company_charges;
        $received = $shipment->received_amount ?? 0;
        $receivable = max(0, $total - $received);

        $entries = [];

        $debtorAccountId = Account::where('name', 'Debtors')->value('id');
        $cashAccountId = Account::where('name', 'Cash Account')->value('id');
        $revenueAccountId = $mapping->credit_account_id;
        $taxPayableAccountId = Account::where('name', 'Sales Tax Payable')->value('id');

        if ($receivable > 0) {
            $entries[] = ['account_id' => $debtorAccountId, 'debit' => $receivable];
        }
        if ($received > 0) {
            $entries[] = ['account_id' => $cashAccountId, 'debit' => $received];
        }
        $entries[] = ['account_id' => $revenueAccountId, 'credit' => $shipment->company_charges];
        if ($shipment->output_tax > 0) {
            $entries[] = ['account_id' => $taxPayableAccountId, 'credit' => $shipment->output_tax];
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

    /**
     * Generate voucher for consolidation costs
     */
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
     * Generate voucher for salary payment
     */
    public function generateSalaryPaymentVoucher($salaryPayment)
    {
        $salaryAccountId = Account::where('name', 'Salaries & Wages Expense')->value('id');
        $cashAccountId = Account::where('name', 'Cash Account')->value('id');

        $entries = [
            ['account_id' => $salaryAccountId, 'debit' => $salaryPayment->amount],
            ['account_id' => $cashAccountId, 'credit' => $salaryPayment->amount],
        ];

        return $this->generateVoucher(
            'system',
            'salary_payment',
            $salaryPayment->id,
            $salaryPayment->paid_date,
            "Salary payment to {$salaryPayment->employee->name} for {$salaryPayment->month}/{$salaryPayment->year}",
            $entries
        );
    }
}
