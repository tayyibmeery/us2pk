<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('consolidations', function (Blueprint $table) {
            $table->id();
            $table->string('consol_id')->unique();                 // Consol-439
            $table->string('awb_number')->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses');
            $table->date('date_dispatched')->nullable();

            // ---- existing columns ----
            $table->decimal('total_us2pk_charges', 12, 2)->default(0);
            $table->decimal('direct_costs', 12, 2)->default(0);
            $table->decimal('gross_income', 12, 2)->storedAs('total_us2pk_charges - direct_costs');
            $table->decimal('roi_percent', 8, 2)->nullable();
            $table->decimal('total_weight_kg', 8, 2);

            // ---- new columns from the update ----
            $table->string('courier')->nullable();
            $table->date('date_departed')->nullable();
            $table->date('date_reached')->nullable();
            $table->decimal('receiveable_from_bluex', 12, 2)->default(0);
            $table->decimal('pkship_charges', 12, 2)->default(0);
            $table->decimal('bluex_charges', 12, 2)->default(0);
            $table->decimal('ware_house_charges', 12, 2)->default(0);
            $table->decimal('import_taxes', 12, 2)->default(0);
            $table->decimal('net_st_payable', 12, 2)->default(0);
            // stored column (depends on other columns)
            $table->decimal('direct_cost', 12, 2)->storedAs('ware_house_charges + import_taxes + bluex_charges + net_st_payable');
            $table->decimal('customs_duty', 12, 2)->default(0);
            $table->decimal('sales_tax', 12, 2)->default(0);
            $table->decimal('income_tax', 12, 2)->default(0);
            $table->decimal('caa_charges', 12, 2)->default(0);
            $table->decimal('output_sales_tax', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('consolidations');
    }
};
