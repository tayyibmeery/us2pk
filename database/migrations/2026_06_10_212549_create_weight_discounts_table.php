<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('weight_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('warehouse'); // USA, China, Germany, UK
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('weight_discounts');
    }
};
