<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CitySeeder::class,
            AdminUserSeeder::class,
            ProhibitedItemSeeder::class,

            SettingSeeder::class,
            WarehouseSeeder::class,
            WeightDiscountSeeder::class,


            WarehouseSeeder::class,

            PaymentMethodsSeeder::class,
            SitesSeeder::class,
            LocalCouriersSeeder::class,
            InternationalCouriersSeeder::class,
            ShipmentStatusesSeeder::class,
            AccountsSeeder::class,
            TransactionTypeAccountSeeder::class,
        ]);
    }
}
