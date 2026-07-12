<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('debtors', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount_due', 12, 2)->default(0); // Amount paid so far
            $table->decimal('receivable_cod', 12, 2)->default(0); // Total amount owed
            $table->decimal('balance', 12, 2)->default(0); // Remaining balance

            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('debtors');
    }
};
