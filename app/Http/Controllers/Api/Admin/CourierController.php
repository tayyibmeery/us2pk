<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourierController extends Controller
{
    public function index()
    {
        $couriers = Courier::orderBy('name')->paginate(20);
        return response()->json($couriers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255|unique:couriers,name',
            'status' => 'boolean',
        ]);

        $courier = Courier::create($validated);
        return response()->json($courier, 201);
    }

    public function show(Courier $courier)
    {
        return response()->json($courier);
    }

    public function update(Request $request, Courier $courier)
    {
        $validated = $request->validate([
            'name'   => ['sometimes', 'string', 'max:255', Rule::unique('couriers')->ignore($courier->id)],
            'status' => 'boolean',
        ]);

        $courier->update($validated);
        return response()->json($courier);
    }

    public function destroy(Courier $courier)
    {
        if ($courier->consolidations()->exists()) {
            return response()->json([
                'message' => 'Cannot delete because it is used in consolidations.'
            ], 422);
        }
        $courier->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
