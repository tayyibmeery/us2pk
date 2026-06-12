<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['name' => 'us_dollar_rate', 'value' => '153', 'description' => 'US Dollar to Pakistani Rupee'],
            ['name' => 'JazzCash Fee for Mobile Account(%)', 'value' => '1', 'description' => 'Fee for JazzCash mobile account payments'],
            ['name' => 'JazzCash Fee for Voucher Payment(%)', 'value' => '2', 'description' => 'Fee for JazzCash voucher payments'],
            ['name' => 'JazzCash Fee for Credit Card(%)', 'value' => '3', 'description' => 'Fee for JazzCash credit card payments'],
            ['name' => 'EasyPaisa Fee for Mobile Account(%)', 'value' => '1', 'description' => 'Fee for EasyPaisa mobile account payments'],
            ['name' => 'EasyPaisa Fee for Voucher Payment(%)', 'value' => '2', 'description' => 'Fee for EasyPaisa voucher payments'],
            ['name' => 'EasyPaisa Fee for Credit Card(%)', 'value' => '3', 'description' => 'Fee for EasyPaisa credit card payments'],
            ['name' => 'blue_ex_base_charge', 'value' => '200', 'description' => 'Base delivery charge for BlueEx (PKR)'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['name' => $setting['name']],
                $setting
            );
        }
    }
}
