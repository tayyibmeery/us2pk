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
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 3. Delivery Services
        Schema::create('delivery_services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 4. Couriers
        Schema::create('couriers', function (Blueprint $table) {
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
        Schema::dropIfExists('couriers');
        Schema::dropIfExists('delivery_services');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('sites');
    }
};
