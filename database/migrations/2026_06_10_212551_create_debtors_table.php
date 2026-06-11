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
            $table->string('invoice_no');
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('amount_due', 12, 2);
            $table->decimal('cod', 12, 2)->default(0);
            $table->date('cod_date')->nullable();
            $table->decimal('balance', 12, 2);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('debtors');
    }
};
