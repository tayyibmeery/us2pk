<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SubSubCategorySeeder extends Seeder
{
    public function run(): void
    {
        $subSubs = [
            // From screenshot, first 30 (IDs 1014 down to 984)
            ['id' => 1014, 'name' => 'sweat pants and joggers', 'description' => 'sweat pants and joggers', 'category_id' => 109, 'sub_category_id' => 748, 'status' => 'Active', 'created_at' => '2017-01-28 00:00:00'],
            ['id' => 1013, 'name' => 'yoga and leggings', 'description' => 'yoga and leggings', 'category_id' => 109, 'sub_category_id' => 748, 'status' => 'Active', 'created_at' => '2017-01-28 00:00:00'],
            ['id' => 1012, 'name' => 'performance bottom', 'description' => 'performance bottom', 'category_id' => 109, 'sub_category_id' => 748, 'status' => 'Active', 'created_at' => '2017-01-28 00:00:00'],
            ['id' => 1011, 'name' => 'all bottoms', 'description' => 'all bottoms', 'category_id' => 109, 'sub_category_id' => 748, 'status' => 'Active', 'created_at' => '2017-01-28 00:00:00'],
            ['id' => 1010, 'name' => 'tees and tanks', 'description' => 'tees and tanks', 'category_id' => 109, 'sub_category_id' => 747, 'status' => 'Active', 'created_at' => '2017-01-28 00:00:00'],
            ['id' => 1009, 'name' => 'hoodie and jacket', 'description' => 'hoodie and jacket', 'category_id' => 109, 'sub_category_id' => 747, 'status' => 'Active', 'created_at' => '2017-01-28 00:00:00'],
            ['id' => 1008, 'name' => 'all tops', 'description' => 'all tops', 'category_id' => 109, 'sub_category_id' => 747, 'status' => 'Active', 'created_at' => '2017-01-28 00:00:00'],
            ['id' => 1007, 'name' => 'UGG Australia', 'description' => 'UGG Australia', 'category_id' => 108, 'sub_category_id' => 746, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 1006, 'name' => 'Beauty', 'description' => 'Beauty', 'category_id' => 108, 'sub_category_id' => 746, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 1005, 'name' => 'Bag Packs and Bag', 'description' => 'Bag Packs and Bag', 'category_id' => 108, 'sub_category_id' => 746, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 1004, 'name' => 'One Pieces', 'description' => 'One Pieces', 'category_id' => 107, 'sub_category_id' => 745, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 1003, 'name' => 'Bottoms', 'description' => 'Bottoms', 'category_id' => 107, 'sub_category_id' => 745, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 1002, 'name' => 'Tops', 'description' => 'Tops', 'category_id' => 107, 'sub_category_id' => 745, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 1001, 'name' => 'Sleep', 'description' => 'Sleep', 'category_id' => 106, 'sub_category_id' => 744, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 1000, 'name' => 'Youga and Leggings', 'description' => 'Youga and Leggings', 'category_id' => 106, 'sub_category_id' => 744, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 999,  'name' => 'Lounge Bottoms', 'description' => 'Lounge Bottoms', 'category_id' => 106, 'sub_category_id' => 744, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 998,  'name' => 'Tees and Tanks', 'description' => 'Tees and Tanks', 'category_id' => 106, 'sub_category_id' => 744, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 997,  'name' => 'Hoodies and sweat shirts', 'description' => 'Hoodies and sweat shirts', 'category_id' => 106, 'sub_category_id' => 744, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 996,  'name' => 'boyshorts', 'description' => 'boyshorts', 'category_id' => 102, 'sub_category_id' => 739, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 995,  'name' => 'hipsters', 'description' => 'hipsters', 'category_id' => 102, 'sub_category_id' => 739, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 994,  'name' => 'cheeksters', 'description' => 'cheeksters', 'category_id' => 102, 'sub_category_id' => 739, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 993,  'name' => 'cheeksters', 'description' => 'cheeksters', 'category_id' => 101, 'sub_category_id' => 738, 'status' => 'Active', 'created_at' => '2017-01-26 00:00:00'],
            ['id' => 991,  'name' => 'Shorts & Rompers', 'description' => 'Shorts & Rompers', 'category_id' => 105, 'sub_category_id' => 743, 'status' => 'Active', 'created_at' => '2017-01-21 00:00:00'],
            ['id' => 990,  'name' => 'Robes', 'description' => 'Robes', 'category_id' => 105, 'sub_category_id' => 743, 'status' => 'Active', 'created_at' => '2017-01-21 00:00:00'],
            ['id' => 989,  'name' => 'Sleepshirts', 'description' => 'Sleepshirts', 'category_id' => 105, 'sub_category_id' => 743, 'status' => 'Active', 'created_at' => '2017-01-21 00:00:00'],
            ['id' => 988,  'name' => 'Pajamas', 'description' => 'Pajamas', 'category_id' => 105, 'sub_category_id' => 743, 'status' => 'Active', 'created_at' => '2017-01-21 00:00:00'],
            ['id' => 987,  'name' => 'Wireless Styles', 'description' => 'Wireless Styles', 'category_id' => 104, 'sub_category_id' => 742, 'status' => 'Active', 'created_at' => '2017-01-21 00:00:00'],
            ['id' => 986,  'name' => 'Shop By Support', 'description' => 'Shop By Support', 'category_id' => 104, 'sub_category_id' => 742, 'status' => 'Active', 'created_at' => '2017-01-21 00:00:00'],
            ['id' => 985,  'name' => 'Bras By Size XS-XL, A-DDD', 'description' => 'Bras By Size XS-XL, A-DDD', 'category_id' => 104, 'sub_category_id' => 742, 'status' => 'Active', 'created_at' => '2017-01-21 00:00:00'],
            ['id' => 984,  'name' => 'Kimonos', 'description' => 'Kimonos', 'category_id' => 103, 'sub_category_id' => 741, 'status' => 'Active', 'created_at' => '2017-01-21 00:00:00'],
        ];

        foreach ($subSubs as $subSub) {
            DB::table('sub_sub_categories')->updateOrInsert(
                ['id' => $subSub['id']],
                [
                    'name'              => $subSub['name'],
                    'description'       => $subSub['description'],
                    'category_id'       => $subSub['category_id'],
                    'sub_category_id'   => $subSub['sub_category_id'],
                    'status'            => $subSub['status'],
                    'created_at'        => $subSub['created_at'],
                    'updated_at'        => Carbon::now(),
                ]
            );
        }
    }
}
