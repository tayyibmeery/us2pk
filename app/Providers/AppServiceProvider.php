<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Register morph map
        Relation::morphMap([
            'shipment' => \App\Models\Shipment::class,
            'consolidation' => \App\Models\Consolidation::class,
            'manual' => \App\Models\Voucher::class,
        ]);
    }
}
