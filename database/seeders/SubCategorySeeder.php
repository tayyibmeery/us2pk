<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SubCategorySeeder extends Seeder
{
    public function run(): void
    {
        $subCategories = [
            // From the screenshot, first 30 IDs (748 down to 719)
            ['id' => 748, 'name' => 'bottoms', 'description' => 'bottoms', 'category_id' => 109, 'status' => 'Active', 'created_at' => '2017-01-28 00:00:00'],
            ['id' => 747, 'name' => 'tops', 'description' => 'tops', 'category_id' => 109, 'status' => 'Active', 'created_at' => '2017-01-28 00:00:00'],
            ['id' => 746, 'name' => 'Styles', 'description' => 'Styles', 'category_id' => 108, 'status' => 'Active', 'created_at' => '2017-01-27 00:00:00'],
            ['id' => 745, 'name' => 'Styles', 'description' => 'Styles', 'category_id' => 107, 'status' => 'Active', 'created_at' => '2017-01-27 00:00:00'],
            ['id' => 744, 'name' => 'Styles', 'description' => 'Styles', 'category_id' => 106, 'status' => 'Active', 'created_at' => '2017-01-27 00:00:00'],
            ['id' => 743, 'name' => 'Styles', 'description' => 'Styles', 'category_id' => 105, 'status' => 'Active', 'created_at' => '2017-01-22 00:00:00'],
            ['id' => 742, 'name' => 'Styles', 'description' => 'Styles', 'category_id' => 104, 'status' => 'Active', 'created_at' => '2017-01-22 00:00:00'],
            ['id' => 741, 'name' => 'Styles', 'description' => 'Styles', 'category_id' => 103, 'status' => 'Active', 'created_at' => '2017-01-21 00:00:00'],
            ['id' => 740, 'name' => 'Categories', 'description' => 'Categories', 'category_id' => 97,  'status' => 'Active', 'created_at' => '2017-01-21 00:00:00'], // Flower Cosmetics? (assuming category_id 97)
            ['id' => 739, 'name' => 'Styles', 'description' => 'Styles', 'category_id' => 102, 'status' => 'Active', 'created_at' => '2017-01-21 00:00:00'],
            ['id' => 738, 'name' => 'Styles', 'description' => 'Styles', 'category_id' => 101, 'status' => 'Active', 'created_at' => '2017-01-21 00:00:00'],
            ['id' => 737, 'name' => 'Styles', 'description' => 'Styles', 'category_id' => 100, 'status' => 'Active', 'created_at' => '2017-01-11 00:00:00'],
            ['id' => 736, 'name' => 'Hair Loss Products', 'description' => 'Hair Loss Products', 'category_id' => 85,  'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 735, 'name' => 'Skin Care', 'description' => 'Skin Care', 'category_id' => 85,  'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 734, 'name' => 'Extensions, Wigs & Accessories', 'description' => 'Extensions, Wigs & Accessories', 'category_id' => 85, 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 733, 'name' => 'Eyes', 'description' => 'Eyes', 'category_id' => 85,  'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 732, 'name' => 'Piercing & Tattoo Supplies', 'description' => 'Piercing & Tattoo Supplies', 'category_id' => 98, 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 731, 'name' => 'Perms & Straighteners', 'description' => 'Perms & Straighteners', 'category_id' => 85, 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 730, 'name' => 'Hair & Scalp Care', 'description' => 'Hair & Scalp Care', 'category_id' => 85, 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 729, 'name' => 'Skin Care', 'description' => 'Skin Care', 'category_id' => 87,  'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'], // Moisturizers? Actually might be a sub of Skin Care category, but using as given
            ['id' => 728, 'name' => 'Lip Care', 'description' => 'Lip Care', 'category_id' => 92,  'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 727, 'name' => 'Bath & Bathing Accessories', 'description' => 'Bath & Bathing Accessories', 'category_id' => 98, 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 726, 'name' => 'Skin Care Tools', 'description' => 'Skin Care Tools', 'category_id' => 98, 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 725, 'name' => 'Mirror & Magnifier', 'description' => 'Mirror & Magnifier', 'category_id' => 98, 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 724, 'name' => 'Makeup Brushes & Tools', 'description' => 'Makeup Brushes & Tools', 'category_id' => 98, 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 723, 'name' => 'Feet, Hands & Nails Tools', 'description' => 'Feet, Hands & Nails Tools', 'category_id' => 98, 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 722, 'name' => 'Bathing Accessories', 'description' => 'Bathing Accessories', 'category_id' => 98, 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 721, 'name' => 'Bags & Cases', 'description' => 'Bags & Cases', 'category_id' => 98, 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 720, 'name' => 'Hair Cutting Tools', 'description' => 'Hair Cutting Tools', 'category_id' => 85, 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            ['id' => 719, 'name' => 'Styling Tools & Appliances', 'description' => 'Styling Tools & Appliances', 'category_id' => 85, 'status' => 'Active', 'created_at' => '2016-12-11 00:00:00'],
            // Add more as needed – but we'll keep these 30.
        ];

        foreach ($subCategories as $sub) {
            DB::table('sub_categories')->updateOrInsert(
                ['id' => $sub['id']],
                [
                    'name'          => $sub['name'],
                    'description'   => $sub['description'],
                    'category_id'   => $sub['category_id'],
                    'status'        => $sub['status'],
                    'created_at'    => $sub['created_at'],
                    'updated_at'    => Carbon::now(),
                ]
            );
        }
    }
}
