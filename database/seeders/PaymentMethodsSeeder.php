<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodsSeeder extends Seeder
{
    public function run()
    {
        $methods = ['Credit Card', 'Bank Transfer', 'Cash on Delivery', 'JazzCash', 'EasyPaisa'];
        foreach ($methods as $name) {
            PaymentMethod::firstOrCreate(['name' => $name]);
        }
    }
}
