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
        $city = City::firstOrCreate(
            ['city_code' => 'LHE'],
            [
                'city_name' => 'Lahore',
                'status' => true,
            ]
        );

        // Admin User
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
                'source'            => 'System',
                'email_verified_at' => now(),
            ]
        );

        // Demo User
        User::updateOrCreate(
            ['email' => 'user@us2pk.com'],
            [
                'name'              => 'Demo User',
                'password'          => Hash::make('password'),
                'phone'             => '+92-300-9876543',
                'address'           => 'Johar Town, Lahore',
                'city_id'           => $city->id,
                'pcode'             => 'LHE001',
                'role'              => 'user',
                'status'            => 'approved',
                'source'            => 'System',
                'email_verified_at' => now(),
            ]
        );
    }
}
