<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add user_id (the billed customer)
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
            $table->index('user_id');
        });

        // 2. Make shipment_id nullable (multi-shipment invoices)
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('shipment_id')->nullable()->change();
        });

        // 3. Pivot: invoices <-> shipments
        Schema::create('invoice_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('cod', 12, 2)->default(0);
            $table->timestamps();

            // One shipment can only be invoiced once
            $table->unique('shipment_id');
            $table->index('invoice_id');
        });

        // 4. Backfill existing invoices
        DB::table('invoices')
            ->whereNotNull('shipment_id')
            ->orderBy('id')
            ->each(function ($invoice) {
                $userId = DB::table('shipments')
                    ->where('id', $invoice->shipment_id)
                    ->value('user_id');

                DB::table('invoices')->where('id', $invoice->id)
                    ->update(['user_id' => $userId]);

                DB::table('invoice_shipments')->insert([
                    'invoice_id'  => $invoice->id,
                    'shipment_id' => $invoice->shipment_id,
                    'amount'      => $invoice->amount_due ?? 0,
                    'cod'         => $invoice->cod ?? 0,
                    'created_at'  => $invoice->created_at ?? now(),
                    'updated_at'  => $invoice->updated_at ?? now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_shipments');
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
