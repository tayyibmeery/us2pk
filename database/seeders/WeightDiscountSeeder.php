<?php

namespace Database\Seeders;

use App\Models\WeightDiscount;
use App\Models\Warehouse;
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
            // Find warehouse by name (case‑sensitive, adjust if needed)
            $warehouse = Warehouse::where('name', $discount['warehouse'])->first();
            if ($warehouse) {
                WeightDiscount::updateOrCreate(
                    ['warehouse_id' => $warehouse->id],
                    ['discount_percent' => $discount['discount_percent']]
                );
            }
        }
    }
}
