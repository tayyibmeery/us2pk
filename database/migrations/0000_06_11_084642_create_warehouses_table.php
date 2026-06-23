<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // USA, China, Germany, UK
            $table->string('code')->unique(); // e.g., US, CN, DE
            $table->text('address')->nullable(); // full address
            $table->boolean('status')->default(true); // active/inactive
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouses');
    }
};
