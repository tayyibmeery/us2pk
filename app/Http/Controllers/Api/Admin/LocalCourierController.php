<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocalCourier;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocalCourierController extends Controller
{
    public function index()
    {
        $couriers = LocalCourier::orderBy('name')->paginate(20);
        return response()->json($couriers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255|unique:local_couriers,name',
            'status' => 'boolean',
        ]);

        $courier = LocalCourier::create($validated);
        return response()->json($courier, 201);
    }

    public function show(LocalCourier $localCourier)
    {
        return response()->json($localCourier);
    }

    public function update(Request $request, LocalCourier $localCourier)
    {
        $validated = $request->validate([
            'name'   => ['sometimes', 'string', 'max:255', Rule::unique('local_couriers')->ignore($localCourier->id)],
            'status' => 'boolean',
        ]);

        $localCourier->update($validated);
        return response()->json($localCourier);
    }

    public function destroy(LocalCourier $localCourier)
    {
        $used = Shipment::where('local_courier_id', $localCourier->id)->exists();

        if ($used) {
            return response()->json([
                'message' => 'Cannot delete because it is used in shipments.'
            ], 422);
        }

        $localCourier->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
