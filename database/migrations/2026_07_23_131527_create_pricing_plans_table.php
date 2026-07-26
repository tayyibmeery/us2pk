<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('section_title')->nullable();
            $table->text('section_content')->nullable();
            $table->string('price')->nullable();
            $table->string('interval')->nullable(); // year, month, shipment
            $table->text('features')->nullable(); // HTML list
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->boolean('featured')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_plans');
    }
};
