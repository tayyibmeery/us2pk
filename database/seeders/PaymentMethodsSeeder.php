<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\Account;

class PaymentMethodsSeeder extends Seeder
{
    public function run()
    {
        // Get account IDs for mapping
        $cashAccount = Account::where('name', 'Cash Account')->first();
        $bankAccount = Account::where('name', 'Bank Account')->first();

        // If accounts don't exist, create them or use defaults
        if (!$cashAccount) {
            $cashAccount = Account::create([
                'name' => 'Cash Account',
                'code' => 'AC-0001',
                'acc_class' => 'Assets',
                'nature' => 'Debit',
                'ownership' => 'US2PK',
                'pandlcategory' => 'None',
                'is_active' => 1,
            ]);
        }

        if (!$bankAccount) {
            $bankAccount = Account::create([
                'name' => 'Bank Account',
                'code' => 'AC-0002',
                'acc_class' => 'Assets',
                'nature' => 'Debit',
                'ownership' => 'US2PK',
                'pandlcategory' => 'None',
                'is_active' => 1,
            ]);
        }

        // Define payment methods with their account mappings
        $methods = [
            [
                'name' => 'Credit Card',
                'account_id' => $bankAccount->id ?? null,
                'status' => 1,
            ],
            [
                'name' => 'Bank Transfer',
                'account_id' => $bankAccount->id ?? null,
                'status' => 1,
            ],
            [
                'name' => 'Cash on Delivery',
                'account_id' => $cashAccount->id ?? null,
                'status' => 1,
            ],
            [
                'name' => 'JazzCash',
                'account_id' => $cashAccount->id ?? null,
                'status' => 1,
            ],
            [
                'name' => 'EasyPaisa',
                'account_id' => $cashAccount->id ?? null,
                'status' => 1,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['name' => $method['name']],
                [
                    'account_id' => $method['account_id'],
                    'status' => $method['status'],
                ]
            );
        }

        $this->command->info('Payment methods seeded successfully!');
        $this->command->info('Cash Account ID: ' . ($cashAccount->id ?? 'Not found'));
        $this->command->info('Bank Account ID: ' . ($bankAccount->id ?? 'Not found'));
    }
}
