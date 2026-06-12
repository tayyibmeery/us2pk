<?php

namespace Database\Seeders;

use App\Models\WeightDiscount;
use Illuminate\Database\Seeder;

class WeightDiscountSeeder extends Seeder
{
    public function run(): void
    {
        $discounts = [
            ['warehouse' => 'USA',     'discount_percent' => 50],
            ['warehouse' => 'China',   'discount_percent' => 0],
            ['warehouse' => 'Germany', 'discount_percent' => 0],
            ['warehouse' => 'UK',      'discount_percent' => 0],
        ];

        foreach ($discounts as $discount) {
            WeightDiscount::updateOrCreate(
                ['warehouse' => $discount['warehouse']],
                $discount
            );
        }
    }
}
