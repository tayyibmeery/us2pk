<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentPayment;
use Illuminate\Http\Request;
use App\ShipmentPaymentHelper; // ✅ import the trait

class ShipmentPaymentController extends Controller
{
    use ShipmentPaymentHelper; // ✅ use the trait

    public function index(Shipment $shipment)
    {
        return response()->json($shipment->payments()->latest()->get());
    }

    public function store(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $payment = $shipment->payments()->create($validated);

        // Recalc received_amount using the trait method
        $this->updateShipmentReceivedAmount($shipment);

        return response()->json($payment, 201);
    }

    public function show(ShipmentPayment $payment)
    {
        return response()->json($payment);
    }

    public function update(Request $request, ShipmentPayment $payment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $payment->update($validated);

        $this->updateShipmentReceivedAmount($payment->shipment);

        return response()->json($payment);
    }

    public function destroy(ShipmentPayment $payment)
    {
        $shipment = $payment->shipment;
        $payment->delete();

        $this->updateShipmentReceivedAmount($shipment);

        return response()->json(['message' => 'Deleted']);
    }
}
