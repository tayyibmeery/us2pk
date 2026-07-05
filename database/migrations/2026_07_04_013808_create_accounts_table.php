<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable()->unique();
            $table->enum('acc_class', ['Assets', 'Liabilities', 'Equity', 'Income', 'Expense']);
            $table->enum('nature', ['Debit', 'Credit']);
            $table->enum('ownership', ['US2PK', 'Others'])->default('US2PK');
            $table->enum('pandlcategory', ['Revenue', 'Cost of Sales', 'Operating Expenses', 'Other Project Expenses', 'None'])->default('None');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
