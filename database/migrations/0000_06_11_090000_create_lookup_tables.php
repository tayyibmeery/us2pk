<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Sites
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 2. Payment Methods
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('account_id')
                ->nullable()

                ->constrained('accounts')
                ->nullOnDelete();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 3. Local Couriers (renamed from delivery_services)
        Schema::create('local_couriers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 4. International Couriers (renamed from couriers)
        Schema::create('international_couriers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 5. Shipment Statuses
        Schema::create('shipment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipment_statuses');
        Schema::dropIfExists('international_couriers');
        Schema::dropIfExists('local_couriers');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('sites');
    }
};
