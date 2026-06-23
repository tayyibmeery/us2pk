<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Courier;

class CouriersSeeder extends Seeder
{
    public function run()
    {
        $couriers = ['DHL', 'FedEx', 'Aramex', 'UPS', 'TNT'];
        foreach ($couriers as $name) {
            Courier::firstOrCreate(['name' => $name]);
        }
    }
}
