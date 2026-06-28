<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\User;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\ShipmentPaymentHelper; // ✅ import trait

class ShipmentController extends Controller
{
    use ShipmentPaymentHelper; // ✅ use the trait

    public function index(Request $request)
    {
        $query = Shipment::with([
            'user',
            'consolidation',
            'images',
            'shipmentStatus',
            'paymentMethod',
            'localCourier',
            'site',
        ]);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('shipment_code', 'like', "%{$request->search}%")
                    ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$request->search}%"));
            });
        }

        if ($request->status) {
            $query->where('shipment_status_id', $request->status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function show(Shipment $shipment)
    {
        return response()->json($shipment->load([
            'user',
            'images',
            'shipmentStatus',
            'paymentMethod',
            'localCourier',
            'site',
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'                => 'required|exists:users,id',
            'consolidation_id'       => 'nullable|exists:consolidations,id',
            'description'            => 'nullable|string',
            'weight'                 => 'required|numeric',
            'weight_unit'            => 'required|in:ounce,gram,kg,pound',
            'seller_tracker_id'      => 'nullable|string|max:255',
            'site_id'                => 'nullable|exists:sites,id',
            'purchase_date'          => 'nullable|date',
            'comments'               => 'nullable|string',
            'shipment_status_id'     => 'nullable|exists:shipment_statuses,id',
            'arrival_date'           => 'nullable|date',
            'expected_delivery_date' => 'nullable|date',
            'date_delivered'         => 'nullable|date',
            'item_value_pkr'         => 'required|numeric',
            'company_charges'        => 'required|numeric',
            'received_amount'        => 'nullable|numeric|min:0',
            'paid_by'                => 'required|in:By Company,By Customer',
            'payment_method_id'      => 'nullable|exists:payment_methods,id',
            'receivable_cod'         => 'nullable|numeric',
            'local_courier_id'       => 'nullable|exists:local_couriers,id',
            'delivery_charges'       => 'nullable|numeric',
            'images'                 => 'nullable|array',
            'images.*'               => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if (!empty($validated['weight']) && !empty($validated['weight_unit'])) {
            $validated['weight_kgs'] = $this->convertToKg($validated['weight'], $validated['weight_unit']);
        }

        $shipment = Shipment::create($validated);

        // ✅ Auto‑create a payment record for the initial received amount
        if ($shipment->received_amount > 0) {
            $paymentDate = $validated['purchase_date'] ?? now()->toDateString();
            $paymentMethod = null;
            if (!empty($validated['payment_method_id'])) {
                $method = PaymentMethod::find($validated['payment_method_id']);
                $paymentMethod = $method ? $method->name : null;
            }

            $shipment->payments()->create([
                'amount'          => $shipment->received_amount,
                'payment_date'    => $paymentDate,
                'payment_method'  => $paymentMethod,
                'reference_number' => null,
                'notes'           => 'Initial payment',
            ]);

            // Recalc received_amount from payments (ensures consistency)
            $this->updateShipmentReceivedAmount($shipment);
        }

        // Upload images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('shipments', 'public');
                $shipment->images()->create(['image_path' => $path]);
            }
        }

        return response()->json($shipment->load([
            'user',
            'images',
            'shipmentStatus',
            'paymentMethod',
            'localCourier',
            'site',
        ]), 201);
    }

    public function update(Request $request, Shipment $shipment)
    {
        // ... unchanged ...
        $validated = $request->validate([
            // same validation as before
        ]);

        if (isset($validated['weight']) && isset($validated['weight_unit'])) {
            $validated['weight_kgs'] = $this->convertToKg($validated['weight'], $validated['weight_unit']);
        }

        $shipment->update($validated);

        if ($request->has('images_to_delete')) {
            $shipment->images()->whereIn('id', $request->images_to_delete)->delete();
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('shipments', 'public');
                $shipment->images()->create(['image_path' => $path]);
            }
        }

        return response()->json($shipment->load([
            'user',
            'images',
            'shipmentStatus',
            'paymentMethod',
            'localCourier',
            'site',
        ]));
    }

    public function destroy(Shipment $shipment)
    {
        foreach ($shipment->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $shipment->images()->delete();
        $shipment->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $request->validate([
            'shipment_status_id' => 'required|exists:shipment_statuses,id'
        ]);

        $shipment->shipment_status_id = $request->shipment_status_id;
        $shipment->save();

        return response()->json([
            'message'  => 'Status updated',
            'shipment' => $shipment->load('shipmentStatus'),
        ]);
    }

    public function fetchCustomer(Request $request)
    {
        $request->validate(['q' => 'required|string']);
        $customer = User::where('pcode', $request->q)
            ->orWhere('name', 'like', "%{$request->q}%")
            ->first();
        return response()->json($customer);
    }

    /**
     * Generate a preview shipment code in the format: SH-{user_pcode}-{next_id}
     */
    public function generateShipmentCode(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $user = User::find($request->user_id);
        $userPcode = $user->pcode ?? 'USR';

        // Get the next available ID
        $last = Shipment::orderBy('id', 'desc')->first();
        $nextId = $last ? $last->id + 1 : 1;

        // Format: SH-{user_pcode}-{next_id}
        $shipmentCode = 'SH-' . $userPcode . '-' . $nextId;

        return response()->json(['shipment_code' => $shipmentCode]);
    }

    private function convertToKg($weight, $unit)
    {
        return match ($unit) {
            'ounce' => $weight * 0.0283495,
            'gram'  => $weight / 1000,
            'pound' => $weight * 0.453592,
            default => $weight,
        };
    }
}
