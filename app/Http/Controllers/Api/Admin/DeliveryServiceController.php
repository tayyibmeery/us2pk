<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryServiceController extends Controller
{
    public function index()
    {
        $services = DeliveryService::orderBy('name')->paginate(20);
        return response()->json($services);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255|unique:delivery_services,name',
            'status' => 'boolean',
        ]);

        $service = DeliveryService::create($validated);
        return response()->json($service, 201);
    }

    public function show(DeliveryService $deliveryService)
    {
        return response()->json($deliveryService);
    }

    public function update(Request $request, DeliveryService $deliveryService)
    {
        $validated = $request->validate([
            'name'   => ['sometimes', 'string', 'max:255', Rule::unique('delivery_services')->ignore($deliveryService->id)],
            'status' => 'boolean',
        ]);

        $deliveryService->update($validated);
        return response()->json($deliveryService);
    }

    public function destroy(DeliveryService $deliveryService)
    {
        if ($deliveryService->shipments()->exists()) {
            return response()->json([
                'message' => 'Cannot delete because it is used in shipments.'
            ], 422);
        }
        $deliveryService->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
