<?php
// database/migrations/2024_01_01_000000_create_pages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type')->default('page');
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->json('meta')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->foreignId('parent_id')->nullable()->constrained('pages')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['type', 'status']);
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
