<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingSetting;

class LandingSettingSeeder extends Seeder
{
    public function run(): void
    {
        LandingSetting::initializeDefaults();
    }
}
