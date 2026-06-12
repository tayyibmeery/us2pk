<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [
            [
                'warehouse' => 'USA',
                'address'   => "1912 E Osier Ct,\nOntario, CA 91761,\nUnited States",
            ],
            [
                'warehouse' => 'China',
                'address'   => "Room 101, Building A,\nNo. 888 Huaihai Road,\nShanghai, China",
            ],
            [
                'warehouse' => 'UK',
                'address'   => "Unit 12, Heathrow Logistics Centre,\nSipson Road,\nWest Drayton, UB7 0HP,\nUnited Kingdom",
            ],
            [
                'warehouse' => 'Germany',
                'address'   => "Frankfurter Str. 123,\n65428 Rüsselsheim,\nGermany",
            ],
        ];

        foreach ($addresses as $address) {
            Address::updateOrCreate(
                ['warehouse' => $address['warehouse']],
                $address
            );
        }
    }
}
