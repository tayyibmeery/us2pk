<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentPayment;
use App\Models\Debtor;
use App\ShipmentPaymentHelper;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentPaymentController extends Controller
{
    use ShipmentPaymentHelper;

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
            'actual_courier_charges' => 'nullable|numeric|min:0',
        ]);

        if (($validated['payment_method'] ?? '') === 'Cash on Delivery' && !$shipment->date_delivered) {
            return response()->json([
                'message' => 'COD payment can only be recorded after the shipment is marked as Delivered.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $voucherService = new VoucherService();

            if ($request->filled('actual_courier_charges') && $validated['payment_method'] === 'Cash on Delivery') {
                $shipment->update([
                    'delivery_charges' => $validated['actual_courier_charges']
                ]);
                $shipment->recalculateReceivable();
                $voucherService->generateDeliveryChargeVoucher($shipment);
            }

            $payment = $shipment->payments()->create([
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->updateShipmentReceivedAmount($shipment);
            $voucherService->generatePaymentVoucher($shipment, $payment);

            DB::commit();
            return response()->json($payment, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment store error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to record payment: ' . $e->getMessage()], 500);
        }
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
            'actual_courier_charges' => 'nullable|numeric|min:0',
        ]);

        $shipment = $payment->shipment;

        if (($validated['payment_method'] ?? '') === 'Cash on Delivery' && !$shipment->date_delivered) {
            return response()->json(['message' => 'COD payment can only be recorded after the shipment is Delivered.'], 422);
        }

        DB::beginTransaction();
        try {
            $voucherService = new VoucherService();

            // Reverse old vouchers
            $voucherService->reversePaymentVoucher($payment);

            if ($request->filled('actual_courier_charges') && $validated['payment_method'] === 'Cash on Delivery') {
                $shipment->update([
                    'delivery_charges' => $validated['actual_courier_charges']
                ]);
                $shipment->recalculateReceivable();
                $voucherService->generateDeliveryChargeVoucher($shipment);
            }

            $payment->update($validated);
            $this->updateShipmentReceivedAmount($shipment);

            // Re-generate new vouchers
            $voucherService->generatePaymentVoucher($shipment, $payment);

            // ✅ Recalculate revenue AFTER payment is updated
            if ($shipment && $shipment->date_delivered) {
                $voucherService->generateDeliveryRevenueVoucher($shipment);
            }

            DB::commit();
            return response()->json($payment);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment update error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update payment: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(ShipmentPayment $payment)
    {
        DB::beginTransaction();
        try {
            $shipment = $payment->shipment;
            $voucherService = new VoucherService();

            // 1. Reverse the vouchers (remittance + courier fee)
            $voucherService->reversePaymentVoucher($payment);

            // 2. Delete the payment
            $payment->delete();
            $this->updateShipmentReceivedAmount($shipment);

            // 3. ✅ Recalculate revenue AFTER deletion so the advance sum is fresh
            if ($shipment && $shipment->date_delivered) {
                $voucherService->generateDeliveryRevenueVoucher($shipment);
            }

            DB::commit();
            return response()->json(['message' => 'Payment reversed successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment delete error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete payment: ' . $e->getMessage()], 500);
        }
    }
}
