<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('shipments', 'deleted_at')) {
            Schema::table('shipments', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('shipment_payments', 'deleted_at')) {
            Schema::table('shipment_payments', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('vouchers', 'deleted_at')) {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('debtors', 'deleted_at')) {
            Schema::table('debtors', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('shipments', 'deleted_at')) {
            Schema::table('shipments', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('shipment_payments', 'deleted_at')) {
            Schema::table('shipment_payments', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('vouchers', 'deleted_at')) {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('debtors', 'deleted_at')) {
            Schema::table('debtors', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
