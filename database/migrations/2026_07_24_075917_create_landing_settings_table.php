<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('landing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique();
            $table->string('section_name');
            $table->string('component_name');
            $table->string('icon')->nullable();
            $table->boolean('enabled')->default(true);
            $table->integer('order')->default(0);
            $table->string('section_title')->nullable();
            $table->text('section_subtitle')->nullable();
            $table->string('route_path')->nullable();
            $table->string('nav_label')->nullable();
            $table->boolean('show_in_navbar')->default(true);
            $table->boolean('show_in_footer')->default(false);
            $table->json('display_options')->nullable();
            $table->timestamps();

            $table->index(['enabled', 'order']);
            $table->index('show_in_navbar');
            $table->index('show_in_footer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_settings');
    }
};
