<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    public function run()
    {
        $accounts = [
            // Assets
            ['name' => 'Cash Account', 'acc_class' => 'Assets', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Debtors', 'acc_class' => 'Assets', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Bank Account', 'acc_class' => 'Assets', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],

            // Liabilities
            ['name' => 'Creditors', 'acc_class' => 'Liabilities', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Sales Tax Payable', 'acc_class' => 'Liabilities', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],

            // Equity
            ['name' => 'Shareholders Capital', 'acc_class' => 'Equity', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Retained Earnings', 'acc_class' => 'Equity', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],

            // Income
            ['name' => 'Revenue - Shipping', 'acc_class' => 'Income', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'Revenue'],
            ['name' => 'Revenue - Import', 'acc_class' => 'Income', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'Revenue'],

            // Cost of Sales
            ['name' => 'Purchases', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'Shipping Charges (USA-Pak)', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'Customs Duty', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'Sales Tax (Import)', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'CAA Charges', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],

            // Operating Expenses
            ['name' => 'Salaries & Wages', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Office Rent', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Utilities', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Advertisement', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Repair & Maintenance', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],

            // Other Project Expenses
            ['name' => 'Project Expenses - Realtor', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Other Project Expenses'],
            ['name' => 'Project Expenses - Web', 'acc_class' => 'Expense', 'nature' => 'Debit', 'ownership' => 'US2PK', 'pandlcategory' => 'Other Project Expenses'],
        ];

        foreach ($accounts as $account) {
            $acc = Account::create($account);
            $acc->code = 'AC-' . str_pad($acc->id, 4, '0', STR_PAD_LEFT);
            $acc->save();
        }
    }
}
