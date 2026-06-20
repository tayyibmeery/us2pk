<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SubSubCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Get all subcategories (we need to map each to its category)
        $subCategories = DB::table('sub_categories')->get(['id', 'category_id']);

        // We'll define 2 sub-subcategories per subcategory
        $subSubs = [];

        foreach ($subCategories as $sub) {
            $baseName = $this->getBaseName($sub->id); // helper to generate unique names
            $subSubs[] = [
                'name'              => $baseName . ' - Type A',
                'description'       => 'First sub-subcategory for ' . $baseName,
                'category_id'       => $sub->category_id,
                'sub_category_id'   => $sub->id,
                'status'            => 'Active',
            ];
            $subSubs[] = [
                'name'              => $baseName . ' - Type B',
                'description'       => 'Second sub-subcategory for ' . $baseName,
                'category_id'       => $sub->category_id,
                'sub_category_id'   => $sub->id,
                'status'            => 'Active',
            ];
        }

        foreach ($subSubs as $ss) {
            DB::table('sub_sub_categories')->insert([
                'name'              => $ss['name'],
                'description'       => $ss['description'],
                'category_id'       => $ss['category_id'],
                'sub_category_id'   => $ss['sub_category_id'],
                'status'            => $ss['status'],
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]);
        }
    }

    /**
     * Helper to get a readable name based on subcategory ID (or you can fetch from DB)
     * We'll just use the subcategory name for clarity.
     */
    private function getBaseName($subId)
    {
        $sub = DB::table('sub_categories')->where('id', $subId)->first();
        return $sub ? $sub->name : 'Sub' . $subId;
    }
}
