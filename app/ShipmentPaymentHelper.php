<?php

namespace App;

use App\Models\Shipment;

trait ShipmentPaymentHelper
{
    protected function updateShipmentReceivedAmount(Shipment $shipment): void
    {
        $totalReceived = $shipment->payments()->sum('amount') ?? 0;
        $shipment->received_amount = $totalReceived;
        $shipment->saveQuietly();

        // ✅ Recalculate receivable_cod/amount_due from the current payments
        $shipment->recalculateReceivable();

        if ($shipment->consolidation_id) {
            $shipment->consolidation->recalculateTotals();
        }
    }
}



