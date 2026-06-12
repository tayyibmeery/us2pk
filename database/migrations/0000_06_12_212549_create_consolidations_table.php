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
            $table->string('consol_id')->unique(); // Consol-439
            $table->string('awb_number')->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses');
            $table->date('date_dispatched')->nullable();
            $table->decimal('total_us2pk_charges', 12, 2)->default(0);
            $table->decimal('direct_costs', 12, 2)->default(0);
            $table->decimal('gross_income', 12, 2)->storedAs('total_us2pk_charges - direct_costs');
            $table->decimal('roi_percent', 8, 2)->nullable();
            $table->decimal('total_weight_kg', 8, 2);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('consolidations');
    }
};
