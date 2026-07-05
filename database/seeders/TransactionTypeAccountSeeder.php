<?php

namespace Database\Seeders;

use App\Models\TransactionTypeAccount;
use App\Models\Account;
use Illuminate\Database\Seeder;

class TransactionTypeAccountSeeder extends Seeder
{
    public function run()
    {
        $mappings = [
            'shipment_revenue' => [
                'debit_account' => 'Debtors',
                'credit_account' => 'Revenue - Shipping',
            ],
            'consolidation_cost' => [
                'debit_account' => 'Customs Duty',
                'credit_account' => 'Creditors',
            ],
            'local_courier_charges' => [
                'debit_account' => 'Shipping Charges (USA-Pak)',
                'credit_account' => 'Creditors',
            ],
        ];

        foreach ($mappings as $type => $accounts) {
            $debit = Account::where('name', $accounts['debit_account'])->first();
            $credit = Account::where('name', $accounts['credit_account'])->first();
            if ($debit && $credit) {
                TransactionTypeAccount::updateOrCreate(
                    ['transaction_type' => $type],
                    ['debit_account_id' => $debit->id, 'credit_account_id' => $credit->id]
                );
            }
        }
    }
}
