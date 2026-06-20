<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ---- shipments table ----
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();

            // renamed from 'psi' to 'pcode'
            $table->string('pcode')->unique(); // PSI-7879

            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('consolidation_id')->nullable()->constrained('consolidations');
            $table->text('description')->nullable();

            $table->decimal('weight', 8, 2);
            $table->enum('weight_unit', ['ounce', 'gram', 'kg', 'pound'])->default('kg');
            $table->decimal('weight_kgs', 8, 2)->nullable();

            $table->string('seller_tracker_id')->nullable();
            $table->string('site_name', 50)->nullable(); // new column

            $table->date('purchase_date')->nullable();
            $table->text('comments')->nullable();

            $table->enum('status', [
                'Bought by Company',
                'Bought by Customer',
                'Reached Shipment in USA facility',
                'Departed Operations Facility - In Transit',
                'Custom Office at Lahore Airport',
                'Reached Lahore Company Office',
                'Out for Delivery',
                'Delivered'
            ])->default('Bought by Company');

            $table->date('arrival_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->date('date_delivered')->nullable();

            $table->decimal('item_value_pkr', 12, 2);
            $table->decimal('company_charges', 12, 2);
            $table->decimal('received_amount', 12, 2)->default(0); // new column

            // stored column
            $table->decimal('total', 12, 2)->storedAs('item_value_pkr + company_charges');

            $table->enum('paid_by', ['By Company', 'By Customer'])->default('By Customer');
            $table->decimal('amount_due', 12, 2)->nullable();

            // changed from enum to string for flexibility
            $table->string('payment_method', 50)->nullable();

            $table->decimal('receivable_cod', 12, 2)->nullable();
            $table->enum('local_delivery_by', ['By BlueEx', 'By Customer'])->nullable();

            // renamed from blueex_charges to delivery_charges
            $table->decimal('delivery_charges', 12, 2)->nullable();

            $table->timestamps();
        });

        // ---- shipment_images table ----
        Schema::create('shipment_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipment_images');
        Schema::dropIfExists('shipments');
    }
};
