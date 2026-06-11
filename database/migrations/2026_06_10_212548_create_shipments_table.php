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
            $table->string('psi')->unique(); // PSI-7879
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('consolidation_id')->nullable()->constrained('consolidations');
            $table->text('description')->nullable();
            $table->decimal('weight', 8, 2);
            $table->enum('weight_unit', ['ounce', 'gram', 'kg', 'pound'])->default('kg');
            $table->decimal('weight_kgs', 8, 2)->nullable();
            $table->string('seller_tracker_id')->nullable();
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
            $table->decimal('total', 12, 2)->storedAs('item_value_pkr + company_charges');
            $table->enum('paid_by', ['By Company', 'By Customer'])->default('By Customer');
            $table->decimal('amount_due', 12, 2)->nullable();
            $table->enum('payment_method', ['Cash', 'ABL', 'Meezan Bank', 'Faisal Bank', 'Jazz Cash', 'Easy Paisa'])->nullable();
            $table->decimal('receivable_cod', 12, 2)->nullable();
            $table->enum('local_delivery_by', ['By BlueEx', 'By Customer'])->nullable();
            $table->decimal('blueex_charges', 12, 2)->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('shipments');
    }
};
