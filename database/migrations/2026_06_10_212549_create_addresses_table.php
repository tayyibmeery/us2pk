<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('warehouse'); // USA, China, UK, Germany
            $table->text('address');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};
