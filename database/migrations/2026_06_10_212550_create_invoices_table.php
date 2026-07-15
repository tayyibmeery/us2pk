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
            $table->string('invoice_no')->unique();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->date('date');
            $table->decimal('amount_due', 12, 2);
            $table->decimal('cod', 12, 2)->default(0);
            $table->date('cod_date')->nullable();
            $table->decimal('output_tax', 12, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->date('paid_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('date');
            $table->index('invoice_no');
            $table->index('shipment_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
