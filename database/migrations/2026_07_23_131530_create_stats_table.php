<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->integer('happy_clients')->default(0);
            $table->integer('complete_shipments')->default(0);
            $table->integer('customer_reviews')->default(0);
            $table->integer('active_services')->default(0);
            $table->string('section_title')->nullable();
            $table->text('section_content')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
