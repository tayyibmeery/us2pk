<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            ['name' => 'USA',     'code' => 'USA'],
            ['name' => 'China',   'code' => 'CN'],
            ['name' => 'Germany', 'code' => 'DE'],
            ['name' => 'UK',      'code' => 'UK'],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(
                ['code' => $warehouse['code']],
                $warehouse
            );
        }
    }
}
