<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // ✅ Use DB::table() to bypass SoftDeletes scopes
        // because the deleted_at column doesn't exist yet.
        $debtors = DB::table('debtors')->get();

        foreach ($debtors as $debtorRow) {
            // ✅ Use DB::table for Shipment too
            $shipment = DB::table('shipments')->find($debtorRow->shipment_id);
            if (!$shipment) continue;

            $totalPayable = ($shipment->item_value_pkr ?? 0) + ($shipment->company_charges ?? 0);
            $received = $shipment->received_amount ?? 0;
            $courierCharges = $shipment->delivery_charges ?? 0;

            $grossCod = max(0, $totalPayable - $received);
            $netReceivable = max(0, $grossCod - $courierCharges);

            DB::table('debtors')
                ->where('id', $debtorRow->id)
                ->update([
                    'receivable_cod' => $netReceivable,
                    'balance' => $netReceivable,
                    'amount_due' => $netReceivable,
                    'net_receivable' => $netReceivable,
                    'cod' => $grossCod,
                    'courier_deduction' => $courierCharges,
                ]);
        }
    }

    public function down()
    {
        // No need to rollback
    }
};
