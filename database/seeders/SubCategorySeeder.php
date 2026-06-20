<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SubCategorySeeder extends Seeder
{
    public function run(): void
    {
        // First, get all category IDs (assuming they are inserted in order)
        $categories = DB::table('categories')->pluck('id')->toArray();

        // Define subcategories per category (3 each)
        $subCategories = [
            ['name' => 'Smartphones', 'description' => 'Mobile phones', 'category_id' => $categories[0]],
            ['name' => 'Laptops', 'description' => 'Portable computers', 'category_id' => $categories[0]],
            ['name' => 'Accessories', 'description' => 'Chargers, cases, etc.', 'category_id' => $categories[0]],

            ['name' => 'Men', 'description' => 'Men\'s clothing', 'category_id' => $categories[1]],
            ['name' => 'Women', 'description' => 'Women\'s clothing', 'category_id' => $categories[1]],
            ['name' => 'Kids', 'description' => 'Children\'s clothing', 'category_id' => $categories[1]],

            ['name' => 'Cookware', 'description' => 'Pots, pans, etc.', 'category_id' => $categories[2]],
            ['name' => 'Furniture', 'description' => 'Tables, chairs, etc.', 'category_id' => $categories[2]],
            ['name' => 'Decor', 'description' => 'Home decoration items', 'category_id' => $categories[2]],
        ];

        foreach ($subCategories as $sub) {
            DB::table('sub_categories')->insert([
                'name'          => $sub['name'],
                'description'   => $sub['description'],
                'category_id'   => $sub['category_id'],
                'status'        => 'Active',
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ]);
        }
    }
}
