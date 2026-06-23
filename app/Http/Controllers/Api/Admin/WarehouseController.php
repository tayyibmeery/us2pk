<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    /**
     * Display a listing of warehouses.
     */
    public function index()
    {
        $warehouses = Warehouse::orderBy('name')->paginate(20);
        return response()->json($warehouses);
    }

    /**
     * Store a newly created warehouse.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255|unique:warehouses,name',
            'code'    => 'required|string|max:50|unique:warehouses,code',
            'address' => 'nullable|string',
            'status'  => 'boolean',
        ]);

        $warehouse = Warehouse::create($validated);
        return response()->json($warehouse, 201);
    }

    /**
     * Display the specified warehouse.
     */
    public function show(Warehouse $warehouse)
    {
        return response()->json($warehouse);
    }

    /**
     * Update the specified warehouse.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name'    => ['sometimes', 'string', 'max:255', Rule::unique('warehouses')->ignore($warehouse->id)],
            'code'    => ['sometimes', 'string', 'max:50', Rule::unique('warehouses')->ignore($warehouse->id)],
            'address' => 'nullable|string',
            'status'  => 'boolean',
        ]);

        $warehouse->update($validated);
        return response()->json($warehouse);
    }

    /**
     * Remove the specified warehouse.
     */
    public function destroy(Warehouse $warehouse)
    {
        // Check if warehouse is in use (has related consolidations or weight discounts)
        if ($warehouse->consolidations()->exists() || $warehouse->weightDiscounts()->exists()) {
            return response()->json([
                'message' => 'Cannot delete warehouse because it has related records.'
            ], 422);
        }

        $warehouse->delete();
        return response()->json(['message' => 'Warehouse deleted successfully']);
    }
}
