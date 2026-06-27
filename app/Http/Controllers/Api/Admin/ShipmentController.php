<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShipmentController extends Controller
{
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
                $q->where('pcode', 'like', "%{$request->search}%")
                    ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$request->search}%"));
            });
        }

        if ($request->status) {
            $query->where('shipment_status_id', $request->status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    // ✅ Add show() — required by apiResource and called after save
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
            'user_id'                => 'required|exists:users,id',        // ✅ required
            'consolidation_id'       => 'nullable|exists:consolidations,id',
            'description'            => 'nullable|string',
            'weight'                 => 'required|numeric',                 // ✅ required
            'weight_unit'            => 'required|in:ounce,gram,kg,pound', // ✅ required
            'seller_tracker_id'      => 'nullable|string|max:255',
            'site_id'                => 'nullable|exists:sites,id',
            'purchase_date'          => 'nullable|date',
            'comments'               => 'nullable|string',
            'shipment_status_id'     => 'nullable|exists:shipment_statuses,id',
            'arrival_date'           => 'nullable|date',
            'expected_delivery_date' => 'nullable|date',
            'date_delivered'         => 'nullable|date',
            'item_value_pkr'         => 'required|numeric',                // ✅ required
            'company_charges'        => 'required|numeric',                // ✅ required
            'received_amount'        => 'nullable|numeric|min:0',
            'paid_by'                => 'required|in:By Company,By Customer', // ✅ required
            'payment_method_id'      => 'nullable|exists:payment_methods,id',
            'receivable_cod'         => 'nullable|numeric',
            'local_courier_id'       => 'nullable|exists:local_couriers,id',
            'delivery_charges'       => 'nullable|numeric',
            'images'                 => 'nullable|array',
            'images.*'               => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // ✅ Guard in case weight/weight_unit are missing despite validation
        if (!empty($validated['weight']) && !empty($validated['weight_unit'])) {
            $validated['weight_kgs'] = $this->convertToKg($validated['weight'], $validated['weight_unit']);
        }

        $shipment = Shipment::create($validated);

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
        $validated = $request->validate([
            'user_id'                => 'sometimes|exists:users,id',
            'consolidation_id'       => 'nullable|exists:consolidations,id',
            'description'            => 'nullable|string',
            'weight'                 => 'sometimes|numeric',
            'weight_unit'            => 'sometimes|in:ounce,gram,kg,pound',
            'seller_tracker_id'      => 'nullable|string|max:255',
            'site_id'                => 'nullable|exists:sites,id',
            'purchase_date'          => 'nullable|date',
            'comments'               => 'nullable|string',
            'shipment_status_id'     => 'nullable|exists:shipment_statuses,id',
            'arrival_date'           => 'nullable|date',
            'expected_delivery_date' => 'nullable|date',
            'date_delivered'         => 'nullable|date',
            'item_value_pkr'         => 'sometimes|numeric',
            'company_charges'        => 'sometimes|numeric',
            'received_amount'        => 'nullable|numeric|min:0',
            'paid_by'                => 'sometimes|in:By Company,By Customer',
            'payment_method_id'      => 'nullable|exists:payment_methods,id',
            'receivable_cod'         => 'nullable|numeric',
            'local_courier_id'       => 'nullable|exists:local_couriers,id',
            'delivery_charges'       => 'nullable|numeric',
            'images'                 => 'nullable|array',
            'images.*'               => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if (isset($validated['weight']) && isset($validated['weight_unit'])) {
            $validated['weight_kgs'] = $this->convertToKg($validated['weight'], $validated['weight_unit']);
        }

        $shipment->update($validated);

        // ✅ Only once — delete marked images
        if ($request->has('images_to_delete')) {
            $shipment->images()->whereIn('id', $request->images_to_delete)->delete();
        }

        // ✅ Only once — store new images
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
        // Delete associated images from storage
        foreach ($shipment->images as $image) {
            \Storage::disk('public')->delete($image->image_path);
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

    public function generatePcode(Request $request)
    {
        $request->validate(['city_code' => 'required|string']);
        $cityCode = strtoupper($request->city_code);
        $last = Shipment::where('pcode', 'LIKE', $cityCode . '-%')
            ->orderBy('id', 'desc')
            ->first();
        $next = $last ? intval(substr($last->pcode, strpos($last->pcode, '-') + 1)) + 1 : 1;
        return response()->json(['pcode' => $cityCode . '-' . $next]);
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
