<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Account;

return new class extends Migration
{
    public function up()
    {
        $accounts = [
            ['name' => 'Cash Account',             'acc_class' => 'Assets',      'nature' => 'Debit',  'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Customer Advances',        'acc_class' => 'Liabilities', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'COD Receivable - BlueEx',  'acc_class' => 'Assets',      'nature' => 'Debit',  'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Courier Payable - BlueEx', 'acc_class' => 'Liabilities', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
            ['name' => 'Item Sales',               'acc_class' => 'Income',      'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'Revenue'],
            ['name' => 'Local Delivery Expense',   'acc_class' => 'Expense',     'nature' => 'Debit',  'ownership' => 'US2PK', 'pandlcategory' => 'Operating Expenses'],
            ['name' => 'Income Tax (Import)',      'acc_class' => 'Expense',     'nature' => 'Debit',  'ownership' => 'US2PK', 'pandlcategory' => 'Cost of Sales'],
            ['name' => 'Consolidation Creditors',  'acc_class' => 'Liabilities', 'nature' => 'Credit', 'ownership' => 'US2PK', 'pandlcategory' => 'None'],
        ];

        // ⬇️ KEY CHANGE ⬇️
        // Reserve AC-9001..AC-90xx for system/seeded accounts created by this
        // migration. The PaymentMethodsSeeder (and any future seeder that uses
        // id-based codes) generates AC-0001, AC-0002, ... — completely separate
        // range, so no collision.
        $codeCounter = 9001;

        foreach ($accounts as $data) {
            if (Account::where('name', $data['name'])->exists()) {
                continue;
            }
            $account = Account::create($data);
            $account->code = 'AC-' . $codeCounter;
            $account->save();
            $codeCounter++;
        }

        $this->remapPaymentMethods();
    }

    private function remapPaymentMethods(): void
    {
        if (!Schema::hasTable('payment_methods')) {
            return;
        }

        $codId  = Account::where('name', 'COD Receivable - BlueEx')->value('id');
        $cashId = Account::where('name', 'Cash Account')->value('id');
        $bankId = Account::where('name', 'Bank Account')->value('id');

        if ($codId) {
            DB::table('payment_methods')
                ->where('name', 'Cash on Delivery')
                ->update(['account_id' => $codId]);
        }
        if ($cashId) {
            DB::table('payment_methods')
                ->whereIn('name', ['Cash', 'JazzCash', 'EasyPaisa', 'Credit Card'])
                ->update(['account_id' => $cashId]);
        }
        if ($bankId) {
            DB::table('payment_methods')
                ->whereIn('name', ['Bank Transfer', 'Bank Transfer (IBFT)', 'Cheque'])
                ->update(['account_id' => $bankId]);
        }
    }

    public function down()
    {
        // Never delete Cash Account — it may have existed before this migration.
        Account::whereIn('name', [
            'Customer Advances',
            'COD Receivable - BlueEx',
            'Courier Payable - BlueEx',
            'Item Sales',
            'Local Delivery Expense',
            'Income Tax (Import)',
            'Consolidation Creditors',
        ])->delete();
    }
};
