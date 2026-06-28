<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_code')->unique();

            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('consolidation_id')->nullable()->constrained('consolidations');

            $table->text('description')->nullable();

            $table->decimal('weight', 8, 2);
            $table->enum('weight_unit', ['ounce', 'gram', 'kg', 'pound'])->default('kg');
            $table->decimal('weight_kgs', 8, 2)->nullable();

            $table->string('seller_tracker_id')->nullable();
            $table->date('purchase_date')->nullable();
            $table->text('comments')->nullable();

            // ✅ Foreign keys to lookup tables (instead of ENUMs)
            $table->foreignId('shipment_status_id')
                ->nullable()
                ->constrained('shipment_statuses')
                ->nullOnDelete();

            $table->foreignId('payment_method_id')
                ->nullable()
                ->constrained('payment_methods')
                ->nullOnDelete();

            $table->foreignId('local_courier_id')
                ->nullable()
                ->constrained('local_couriers')
                ->nullOnDelete();

            $table->foreignId('site_id')
                ->nullable()
                ->constrained('sites')
                ->nullOnDelete();

            // Paid by – can remain ENUM, or also become a foreign key to a lookup table
            $table->enum('paid_by', ['By Company', 'By Customer'])->default('By Customer');

            // Dates
            $table->date('arrival_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->date('date_delivered')->nullable();

            // Financials
            $table->decimal('item_value_pkr', 12, 2);
            $table->decimal('company_charges', 12, 2);
            $table->decimal('received_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->storedAs('item_value_pkr + company_charges');
            $table->decimal('amount_due', 12, 2)->nullable();
            $table->decimal('receivable_cod', 12, 2)->nullable();
            $table->decimal('delivery_charges', 12, 2)->nullable();

            $table->timestamps();
        });

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
