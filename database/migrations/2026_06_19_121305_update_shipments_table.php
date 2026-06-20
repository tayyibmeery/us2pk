<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Rename psi to pcode
            $table->renameColumn('psi', 'pcode');

            // Add site_name (string)
            $table->string('site_name', 50)->nullable()->after('seller_tracker_id');

            // Change payment_method to string (or add more options if keeping enum)
            $table->string('payment_method', 50)->nullable()->change();

            // Add received_amount
            $table->decimal('received_amount', 12, 2)->default(0)->after('company_charges');

            // Rename blueex_charges to delivery_charges
            $table->renameColumn('blueex_charges', 'delivery_charges');

            // If you want to keep local_delivery_by as string, leave as is; or we can change to enum with more options
            // We'll use string for flexibility.
        });

        // Create shipment_images table
        Schema::create('shipment_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->renameColumn('pcode', 'psi');
            $table->dropColumn('site_name');
            // Revert payment_method to enum if needed (but careful)
            $table->renameColumn('delivery_charges', 'blueex_charges');
            $table->dropColumn('received_amount');
        });
        Schema::dropIfExists('shipment_images');
    }
};
