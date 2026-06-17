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
            ['id' => 109, 'name' => 'spots apparel', 'description' => 'spots apparel', 'status' => 'Active', 'created_at' => '2017-01-28 00:00:00'],
            ['id' => 108, 'name' => 'Accessories', 'description' => 'Accessories', 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 107, 'name' => 'Swim', 'description' => 'Swim', 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 106, 'name' => 'Apparel', 'description' => 'Apparel', 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 105, 'name' => 'Sleep', 'description' => 'Sleep', 'status' => 'Active', 'created_at' => '2017-01-20 00:00:00'],
            ['id' => 104, 'name' => 'Sport Bras', 'description' => 'Sport Bras', 'status' => 'Active', 'created_at' => '2017-01-20 00:00:00'],
            ['id' => 103, 'name' => 'Lingerie', 'description' => 'Lingerie', 'status' => 'Active', 'created_at' => '2017-01-20 00:00:00'],
            ['id' => 102, 'name' => 'Panties', 'description' => 'Panties', 'status' => 'Active', 'created_at' => '2017-01-20 00:00:00'],
            ['id' => 101, 'name' => 'Bralettes', 'description' => 'Bralettes', 'status' => 'Active', 'created_at' => '2017-01-20 00:00:00'],
            ['id' => 100, 'name' => 'Bras', 'description' => 'Bras', 'status' => 'Active', 'created_at' => '2017-01-11 00:00:00'],
            ['id' => 99,  'name' => 'Shave & Hair Removal', 'description' => 'Shave & Hair Removal', 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 98,  'name' => 'Tools & Accessories', 'description' => 'Tools & Accessories', 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 97,  'name' => 'Flower Cosmetics', 'description' => 'Flower Cosmetics', 'status' => 'Active', 'created_at' => '2016-12-06 00:00:00'],
            ['id' => 96,  'name' => 'Beauty Gift Sets', 'description' => 'Beauty Gift Sets', 'status' => 'Active', 'created_at' => '2016-12-06 00:00:00'],
            ['id' => 95,  'name' => 'Face Masks', 'description' => 'Face Masks', 'status' => 'Active', 'created_at' => '2016-12-04 00:00:00'],
            ['id' => 94,  'name' => 'Eye Treatments', 'description' => 'Eye Treatments', 'status' => 'Active', 'created_at' => '2016-12-04 00:00:00'],
            ['id' => 93,  'name' => 'Exfoliators', 'description' => 'Exfoliators', 'status' => 'Active', 'created_at' => '2016-12-04 00:00:00'],
            ['id' => 92,  'name' => 'Lip Care', 'description' => 'Lip Care', 'status' => 'Active', 'created_at' => '2016-12-04 00:00:00'],
            ['id' => 91,  'name' => 'Body Lotions', 'description' => 'Body Lotions', 'status' => 'Active', 'created_at' => '2016-12-04 00:00:00'],
            ['id' => 90,  'name' => 'Oil & Blemish Control', 'description' => 'Oil & Blemish Control', 'status' => 'Active', 'created_at' => '2016-12-04 00:00:00'],
            ['id' => 89,  'name' => 'Facial Treatments', 'description' => 'Facial Treatments', 'status' => 'Active', 'created_at' => '2016-12-04 00:00:00'],
            ['id' => 88,  'name' => 'Sun Care', 'description' => 'Sun Care', 'status' => 'Active', 'created_at' => '2016-12-04 00:00:00'],
            ['id' => 87,  'name' => 'Moisturizers', 'description' => 'Moisturizers', 'status' => 'Active', 'created_at' => '2016-12-04 00:00:00'],
            ['id' => 86,  'name' => 'facial Cleansers', 'description' => 'facial Cleansers', 'status' => 'Active', 'created_at' => '2016-12-04 00:00:00'],
            ['id' => 85,  'name' => 'Hair Care', 'description' => 'Hair Care', 'status' => 'Active', 'created_at' => '2016-11-17 00:00:00'],
            ['id' => 84,  'name' => 'Body Makeup', 'description' => 'Body Makeup', 'status' => 'Active', 'created_at' => '2016-11-17 00:00:00'],
            ['id' => 83,  'name' => 'Brows', 'description' => 'Brows', 'status' => 'Active', 'created_at' => '2016-11-14 00:00:00'],
            ['id' => 82,  'name' => 'Beauty Tools & Accessories', 'description' => 'Beauty Tools & Accessories', 'status' => 'Active', 'created_at' => '2016-11-14 00:00:00'],
            ['id' => 81,  'name' => 'Baby(Childrenplace)', 'description' => 'Baby(Childrenplace)', 'status' => 'Active', 'created_at' => '2016-11-09 00:00:00'],
            ['id' => 80,  'name' => 'Toddler Boy(childrenplace)', 'description' => 'Toddler Boy(childrenplace)', 'status' => 'Active', 'created_at' => '2016-11-09 00:00:00'],
        ];

        // Insert with automatic timestamps (update if duplicate)
        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['id' => $category['id']],
                [
                    'name'        => $category['name'],
                    'description' => $category['description'],
                    'status'      => $category['status'],
                    'created_at'  => $category['created_at'],
                    'updated_at'  => Carbon::now(),
                ]
            );
        }
    }
}
