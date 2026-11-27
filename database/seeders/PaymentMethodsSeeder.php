<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\Account;

class PaymentMethodsSeeder extends Seeder
{
    public function run()
    {
        $cashAccount = Account::where('name', 'Cash Account')->first();
        $bankAccount = Account::where('name', 'Bank Account')->first();
        $easyAccount = Account::where('name', 'EasyPaisa Account')->first();
        $jazzAccount = Account::where('name', 'JazzCash Account')->first();
        $codAccount = Account::where('name', 'COD Receivable - BlueEx')->first();

        $methods = [
            ['name' => 'Credit Card', 'account_id' => $bankAccount->id ?? null, 'status' => 1],
            ['name' => 'Bank Transfer', 'account_id' => $bankAccount->id ?? null, 'status' => 1],
            ['name' => 'Cash on Delivery', 'account_id' => $codAccount->id ?? null, 'status' => 1],
            ['name' => 'JazzCash', 'account_id' => $jazzAccount->id ?? null, 'status' => 1],
            ['name' => 'EasyPaisa', 'account_id' => $easyAccount->id ?? null, 'status' => 1],
            ['name' => 'Cash', 'account_id' => $cashAccount->id ?? null, 'status' => 1],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['name' => $method['name']],
                ['account_id' => $method['account_id'], 'status' => $method['status']]
            );
        }

        $this->command->info('Payment methods seeded successfully!');
    }
}
