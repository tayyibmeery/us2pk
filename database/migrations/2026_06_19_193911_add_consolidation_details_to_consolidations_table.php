<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('consolidations', function (Blueprint $table) {
            $table->string('courier')->nullable();
            $table->date('date_departed')->nullable();
            $table->date('date_reached')->nullable();
            $table->decimal('receiveable_from_bluex', 12, 2)->default(0);
            $table->decimal('pkship_charges', 12, 2)->default(0);
            $table->decimal('bluex_charges', 12, 2)->default(0);
            $table->decimal('ware_house_charges', 12, 2)->default(0);
            $table->decimal('import_taxes', 12, 2)->default(0);
            $table->decimal('net_st_payable', 12, 2)->default(0);
            $table->decimal('direct_cost', 12, 2)->storedAs('ware_house_charges + import_taxes + bluex_charges + net_st_payable');
            $table->decimal('customs_duty', 12, 2)->default(0);
            $table->decimal('sales_tax', 12, 2)->default(0);
            $table->decimal('income_tax', 12, 2)->default(0);
            $table->decimal('caa_charges', 12, 2)->default(0);
            $table->decimal('output_sales_tax', 12, 2)->default(0);
        });
    }

    public function down()
    {
        Schema::table('consolidations', function (Blueprint $table) {
            $table->dropColumn([
                'courier',
                'date_departed',
                'date_reached',
                'receiveable_from_bluex',
                'pkship_charges',
                'bluex_charges',
                'ware_house_charges',
                'import_taxes',
                'net_st_payable',
                'direct_cost',
                'customs_duty',
                'sales_tax',
                'income_tax',
                'caa_charges',
                'output_sales_tax'
            ]);
        });
    }
};
