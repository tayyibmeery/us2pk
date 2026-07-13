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

            // Core financial fields - NO "after" clauses
            $table->decimal('total_payable', 12, 2)->default(0);
            $table->decimal('receivable_cod', 12, 2)->default(0);
            $table->decimal('cod', 12, 2)->default(0);
            $table->decimal('amount_due', 12, 2)->default(0);

            // Payment tracking
            $table->decimal('amount_received', 12, 2)->default(0);
            $table->decimal('courier_deduction', 12, 2)->default(0);
            $table->decimal('net_receivable', 12, 2)->nullable();
            $table->decimal('balance', 12, 2)->default(0);

            // Dates
            $table->date('last_payment_date')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index('shipment_id');
            $table->index('user_id');
            $table->index('invoice_no');
            $table->index('balance');
            $table->index('cod');
        });
    }

    public function down()
    {
        Schema::dropIfExists('debtors');
    }
};
