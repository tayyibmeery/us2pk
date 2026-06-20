<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and gadgets', 'status' => 'Active'],
            ['name' => 'Clothing', 'description' => 'Apparel and fashion', 'status' => 'Active'],
            ['name' => 'Home & Kitchen', 'description' => 'Home appliances and kitchenware', 'status' => 'Active'],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->insert([
                'name'        => $cat['name'],
                'description' => $cat['description'],
                'status'      => $cat['status'],
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ]);
        }
    }
}
