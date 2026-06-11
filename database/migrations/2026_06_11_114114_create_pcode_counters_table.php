// database/migrations/xxxx_xx_xx_xxxxxx_create_pcode_counters_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pcode_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->integer('last_number')->default(0);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('pcode_counters');
    }
};
