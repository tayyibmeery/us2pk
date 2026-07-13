<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Debtor;
use App\Models\Shipment;

return new class extends Migration
{
    public function up()
    {
        // Fix existing debtors
        $debtors = Debtor::all();
        foreach ($debtors as $debtor) {
            $shipment = $debtor->shipment;
            if (!$shipment) continue;

            $totalPayable = ($shipment->item_value_pkr ?? 0) + ($shipment->company_charges ?? 0);
            $received = $shipment->received_amount ?? 0;
            $courierCharges = $shipment->delivery_charges ?? 0;

            $grossCod = max(0, $totalPayable - $received);
            $netReceivable = max(0, $grossCod - $courierCharges);

            // Update debtor with NET amount
            $debtor->receivable_cod = $netReceivable;
            $debtor->balance = $netReceivable;
            $debtor->amount_due = $netReceivable;
            $debtor->net_receivable = $netReceivable;
            $debtor->cod = $grossCod;
            $debtor->courier_deduction = $courierCharges;
            $debtor->save();
        }
    }

    public function down()
    {
        // No need to rollback
    }
};
