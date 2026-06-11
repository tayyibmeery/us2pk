<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique(); // PSI-999
            $table->foreignId('shipment_id')->constrained('shipments');
            $table->date('date');
            $table->decimal('amount_due', 12, 2);
            $table->decimal('cod', 12, 2)->default(0);
            $table->date('cod_date')->nullable();
            $table->decimal('output_tax', 12, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
