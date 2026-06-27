<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShipmentStatusController extends Controller
{
    public function index()
    {
        $statuses = ShipmentStatus::orderBy('name')->paginate(20);
        return response()->json($statuses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255|unique:shipment_statuses,name',
            'status' => 'boolean',
        ]);

        $status = ShipmentStatus::create($validated);
        return response()->json($status, 201);
    }

    public function show(ShipmentStatus $shipmentStatus)
    {
        return response()->json($shipmentStatus);
    }

    public function update(Request $request, ShipmentStatus $shipmentStatus)
    {
        $validated = $request->validate([
            'name'   => ['sometimes', 'string', 'max:255', Rule::unique('shipment_statuses')->ignore($shipmentStatus->id)],
            'status' => 'boolean',
        ]);

        $shipmentStatus->update($validated);
        return response()->json($shipmentStatus);
    }

    public function destroy(ShipmentStatus $shipmentStatus)
    {
        // Check if any shipment uses this status ID
        $used = Shipment::where('shipment_status_id', $shipmentStatus->id)->exists();

        if ($used) {
            return response()->json([
                'message' => 'Cannot delete because it is used in shipments.'
            ], 422);
        }

        $shipmentStatus->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
