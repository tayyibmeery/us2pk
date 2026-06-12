<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no');
            $table->date('date');
            $table->string('type')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->string('pcode');
            $table->decimal('revenue', 12, 2);
            $table->decimal('output_tax', 12, 2)->default(0);
            $table->decimal('net_revenue', 12, 2)->storedAs('revenue - output_tax');
            $table->string('paid_by')->nullable(); // PkShip, Customer
            $table->decimal('vendor_payment', 12, 2)->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('revenues');
    }
};
