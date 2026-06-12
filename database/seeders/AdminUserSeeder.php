<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $city = City::where('city_code', 'LHE')->first();
        if (!$city) {
            $city = City::create(['city_name' => 'Lahore', 'city_code' => 'LHE', 'status' => true]);
        }

        User::updateOrCreate(
            ['email' => 'admin@us2pk.com'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('password'),
                'phone'             => '+92-300-1234567',
                'address'           => 'Office # 308, 3rd Floor, Al-Qadir Heights, Garden Town, Lahore',
                'city_id'           => $city->id,
                'pcode'             => 'ADMIN01',
                'role'              => 'admin',
                'status'            => 'approved',
                'email_verified_at' => now(),
                'source'            => 'System',
            ]
        );
    }
}
