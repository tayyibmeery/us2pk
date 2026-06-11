<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();

            // Optional: ensure unique sub-category name within a category
            $table->unique(['name', 'category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_categories');
    }
};
