<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InternationalCourier;

class InternationalCouriersSeeder extends Seeder
{
    public function run()
    {
        $couriers = ['DHL', 'FedEx', 'Aramex', 'UPS', 'TNT'];
        foreach ($couriers as $name) {
            InternationalCourier::firstOrCreate(['name' => $name]);
        }
    }
}

