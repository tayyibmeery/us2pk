<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run()
    {
        $warehouses = [
            [
                'name'    => 'USA',
                'code'    => 'US',
                'address' => "1912 E Osier Ct,\nOntario, CA 91761,\nUnited States",
            ],
            [
                'name'    => 'China',
                'code'    => 'CN',
                'address' => "Room 101, Building A,\nNo. 888 Huaihai Road,\nShanghai, China",
            ],
            [
                'name'    => 'UK',
                'code'    => 'UK',
                'address' => "Unit 12, Heathrow Logistics Centre,\nSipson Road,\nWest Drayton, UB7 0HP,\nUnited Kingdom",
            ],
            [
                'name'    => 'Germany',
                'code'    => 'DE',
                'address' => "Frankfurter Str. 123,\n65428 Rüsselsheim,\nGermany",
            ],
        ];

        foreach ($warehouses as $data) {
            Warehouse::firstOrCreate(
                ['code' => $data['code']], // unique identifier
                $data                      // all other fields
            );
        }
    }
}
