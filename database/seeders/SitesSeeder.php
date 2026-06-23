<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;

class SitesSeeder extends Seeder
{
    public function run()
    {
        $sites = ['Amazon', 'eBay', 'Daraz', 'Walmart', 'AliExpress'];
        foreach ($sites as $name) {
            Site::firstOrCreate(['name' => $name]);
        }
    }
}
