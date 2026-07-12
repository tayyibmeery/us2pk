<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    public function run()
    {
        $accounts = [
            // ==================== ASSETS ====================
            ['name' => 'Cash Account', 'acc_class' => 'Assets', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Bank Account', 'acc_class' => 'Assets', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Debtors', 'acc_class' => 'Assets', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Inventory', 'acc_class' => 'Assets', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Accounts Receivable', 'acc_class' => 'Assets', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Prepaid Expenses', 'acc_class' => 'Assets', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Fixed Assets', 'acc_class' => 'Assets', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Accumulated Depreciation', 'acc_class' => 'Assets', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],

            // ==================== LIABILITIES ====================
            ['name' => 'Accounts Payable', 'acc_class' => 'Liabilities', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Creditors', 'acc_class' => 'Liabilities', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Cash Payable Expense', 'acc_class' => 'Liabilities', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Sales Tax Payable', 'acc_class' => 'Liabilities', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Income Tax Payable', 'acc_class' => 'Liabilities', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Accrued Expenses', 'acc_class' => 'Liabilities', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Unearned Revenue', 'acc_class' => 'Liabilities', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],

            // ==================== EQUITY ====================
            ['name' => 'Shareholders Capital', 'acc_class' => 'Equity', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Retained Earnings', 'acc_class' => 'Equity', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Owner\'s Equity', 'acc_class' => 'Equity', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Dividends', 'acc_class' => 'Equity', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],

            // ==================== INCOME / REVENUE ====================
            ['name' => 'Revenue - Shipping', 'acc_class' => 'Income', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'Revenue'],
            ['name' => 'Revenue - Item Sales', 'acc_class' => 'Income', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'Revenue'],
            ['name' => 'Revenue - Import', 'acc_class' => 'Income', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'Revenue'],
            ['name' => 'Revenue - Service', 'acc_class' => 'Income', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'Revenue'],
            ['name' => 'Revenue - COD', 'acc_class' => 'Income', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'Revenue'],
            ['name' => 'Revenue - Consolidation', 'acc_class' => 'Income', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'Revenue'],
            ['name' => 'Interest Income', 'acc_class' => 'Income', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'Revenue'],
            ['name' => 'Other Income', 'acc_class' => 'Income', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'Revenue'],

            // ==================== COST OF SALES ====================
            ['name' => 'Cost of Sales - Items', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'Cost of Sales - Shipping', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'Courier Charges Expense', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'Purchases', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'Shipping Charges (USA-Pak)', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'Customs Duty', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'Sales Tax (Import)', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'CAA Charges', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'Warehouse Charges', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'Freight Charges', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'Insurance Expense', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],

            // ==================== OPERATING EXPENSES ====================
            ['name' => 'Salaries & Wages', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Office Rent', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Utilities', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Advertisement', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Repair & Maintenance', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Office Supplies', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Telephone & Internet', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Insurance Premium', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Professional Fees', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Legal Fees', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Accounting Fees', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Depreciation Expense', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Bank Charges', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Travel Expenses', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Entertainment Expenses', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Training & Development', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Marketing Expenses', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Software Expenses', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Vehicle Expenses', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Security Expenses', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Cleaning & Janitorial', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Printing & Stationery', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Postage & Courier', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Membership & Subscription', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],

            // ==================== OTHER PROJECT EXPENSES ====================
            ['name' => 'Project Expenses - Realtor', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Other Project Expenses'],
            ['name' => 'Project Expenses - Web', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Other Project Expenses'],
            ['name' => 'Project Expenses - IT', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Other Project Expenses'],
            ['name' => 'Project Expenses - Marketing', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Other Project Expenses'],
            ['name' => 'Project Expenses - Consulting', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Other Project Expenses'],
        ];

        foreach ($accounts as $accountData) {
            // Check if account already exists
            $existing = Account::where('name', $accountData['name'])->first();
            if ($existing) {
                $this->command->info("Account already exists: {$accountData['name']}");
                continue;
            }

            $account = Account::create($accountData);
            $account->code = 'AC-' . str_pad($account->id, 4, '0', STR_PAD_LEFT);
            $account->save();
            $this->command->info("Created account: {$account->name} with code: {$account->code}");
        }
    }
}
