<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShipmentStatus;

class ShipmentStatusesSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            'Bought by Company',
            'Bought by Customer',
            'Reached Shipment in USA facility',
            'Departed Operations Facility - In Transit',
            'Custom Office at Lahore Airport',
            'Reached Lahore Company Office',
            'Out for Delivery',
            'Delivered'
        ];
        foreach ($statuses as $name) {
            ShipmentStatus::firstOrCreate(['name' => $name]);
        }
    }
}
