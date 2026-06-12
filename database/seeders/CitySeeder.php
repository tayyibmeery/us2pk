<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['city_name' => 'Lahore',       'city_code' => 'LHE', 'status' => true],
            ['city_name' => 'Karachi',      'city_code' => 'KHI', 'status' => true],
            ['city_name' => 'Islamabad',    'city_code' => 'ISB', 'status' => true],
            ['city_name' => 'Rawalpindi',   'city_code' => 'RWP', 'status' => true],
            ['city_name' => 'Faisalabad',   'city_code' => 'LYP', 'status' => true],
            ['city_name' => 'Multan',       'city_code' => 'MUX', 'status' => true],
            ['city_name' => 'Peshawar',     'city_code' => 'PEW', 'status' => true],
            ['city_name' => 'Quetta',       'city_code' => 'QTA', 'status' => true],
            ['city_name' => 'Gujranwala',   'city_code' => 'GRW', 'status' => true],
            ['city_name' => 'Sialkot',      'city_code' => 'SKT', 'status' => true],
            ['city_name' => 'Bahawalpur',   'city_code' => 'BHV', 'status' => true],
            ['city_name' => 'Sargodha',     'city_code' => 'SGD', 'status' => true],
            ['city_name' => 'Sheikhupura',  'city_code' => 'SPA', 'status' => true],
            ['city_name' => 'Wah',          'city_code' => 'WAH', 'status' => true],
            ['city_name' => 'Hyderabad',    'city_code' => 'HDD', 'status' => true],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['city_code' => $city['city_code']],
                $city
            );
        }
    }
}
