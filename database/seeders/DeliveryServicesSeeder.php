<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryService;

class DeliveryServicesSeeder extends Seeder
{
    public function run()
    {
        $services = ['BlueEx', 'TCS', 'Leopard', 'M&P', 'Call Courier'];
        foreach ($services as $name) {
            DeliveryService::firstOrCreate(['name' => $name]);
        }
    }
}
