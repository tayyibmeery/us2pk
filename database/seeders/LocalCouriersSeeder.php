<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LocalCourier;

class LocalCouriersSeeder extends Seeder
{
    public function run()
    {
        $couriers = ['BlueEx', 'TCS', 'Leopard', 'M&P', 'Call Courier'];
        foreach ($couriers as $name) {
            LocalCourier::firstOrCreate(['name' => $name]);
        }
    }
}
