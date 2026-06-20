<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::with('user', 'consolidation', 'images');
        if ($request->search) {
            $query->where('pcode', 'like', "%{$request->search}%")
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'               => 'required|exists:users,id',
            'consolidation_id'      => 'nullable|exists:consolidations,id',
            'description'           => 'nullable|string',
            'weight'                => 'required|numeric',
            'weight_unit'           => 'required|in:ounce,gram,kg,pound',
            'seller_tracker_id'     => 'nullable|string',
            'site_name'             => 'nullable|string|max:50',
            'purchase_date'         => 'nullable|date',
            'comments'              => 'nullable|string',
            'status'                => 'required|string',
            'arrival_date'          => 'nullable|date',
            'expected_delivery_date' => 'nullable|date',
            'date_delivered'        => 'nullable|date',
            'item_value_pkr'        => 'required|numeric',
            'company_charges'       => 'required|numeric',
            'received_amount'       => 'nullable|numeric|min:0',
            'paid_by'               => 'required|in:By Company,By Customer',
            'payment_method'        => 'nullable|string|max:50',
            'receivable_cod'        => 'nullable|numeric',
            'local_delivery_by'     => 'nullable|string|max:50',
            'delivery_charges'      => 'nullable|numeric',
            'images'                => 'nullable|array',
            'images.*'              => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Auto-calculate weight_kgs
        $validated['weight_kgs'] = $this->convertToKg($validated['weight'], $validated['weight_unit']);
        // total and receivable_cod will be set in model booted

        $shipment = Shipment::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('shipments', 'public');
                $shipment->images()->create(['image_path' => $path]);
            }
        }

        return response()->json($shipment->load('user', 'images'), 201);
    }

    public function update(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'user_id'               => 'sometimes|exists:users,id',
            'consolidation_id'      => 'nullable|exists:consolidations,id',
            'description'           => 'nullable|string',
            'weight'                => 'sometimes|numeric',
            'weight_unit'           => 'sometimes|in:ounce,gram,kg,pound',
            'seller_tracker_id'     => 'nullable|string',
            'site_name'             => 'nullable|string|max:50',
            'purchase_date'         => 'nullable|date',
            'comments'              => 'nullable|string',
            'status'                => 'sometimes|string',
            'arrival_date'          => 'nullable|date',
            'expected_delivery_date' => 'nullable|date',
            'date_delivered'        => 'nullable|date',
            'item_value_pkr'        => 'sometimes|numeric',
            'company_charges'       => 'sometimes|numeric',
            'received_amount'       => 'nullable|numeric|min:0',
            'paid_by'               => 'sometimes|in:By Company,By Customer',
            'payment_method'        => 'nullable|string|max:50',
            'receivable_cod'        => 'nullable|numeric',
            'local_delivery_by'     => 'nullable|string|max:50',
            'delivery_charges'      => 'nullable|numeric',
            'images'                => 'nullable|array',
            'images.*'              => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if (isset($validated['weight']) && isset($validated['weight_unit'])) {
            $validated['weight_kgs'] = $this->convertToKg($validated['weight'], $validated['weight_unit']);
        }

        $shipment->update($validated);

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('shipments', 'public');
                $shipment->images()->create(['image_path' => $path]);
            }
        }

        return response()->json($shipment->load('user', 'images'));
    }

    public function destroy(Shipment $shipment)
    {
        $shipment->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function fetchCustomer(Request $request)
    {
        $request->validate(['q' => 'required|string']);
        $customer = User::where('pcode', $request->q)
            ->orWhere('name', 'like', "%{$request->q}%")
            ->first();
        return response()->json($customer);
    }

    // Status update (for inline change)
    public function updateStatus(Request $request, Shipment $shipment)
    {
        $request->validate(['status' => 'required|string']);
        $shipment->status = $request->status;
        $shipment->save();
        return response()->json(['message' => 'Status updated']);
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


    // In ShipmentController.php
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
}
