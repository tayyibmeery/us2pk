<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('debtors', function (Blueprint $table) {
            $table->decimal('cod', 12, 2)->default(0)->after('receivable_cod');
            $table->decimal('total_payable', 12, 2)->default(0)->after('cod');
        });
    }

    public function down()
    {
        Schema::table('debtors', function (Blueprint $table) {
            $table->dropColumn(['cod', 'total_payable']);
        });
    }
};
