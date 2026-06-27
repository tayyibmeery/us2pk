<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consolidation;
use App\Models\InternationalCourier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InternationalCourierController extends Controller
{
    public function index()
    {
        $couriers = InternationalCourier::orderBy('name')->paginate(20);
        return response()->json($couriers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255|unique:international_couriers,name',
            'status' => 'boolean',
        ]);

        $courier = InternationalCourier::create($validated);
        return response()->json($courier, 201);
    }

    public function show(InternationalCourier $internationalCourier)
    {
        return response()->json($internationalCourier);
    }

    public function update(Request $request, InternationalCourier $internationalCourier)
    {
        $validated = $request->validate([
            'name'   => ['sometimes', 'string', 'max:255', Rule::unique('international_couriers')->ignore($internationalCourier->id)],
            'status' => 'boolean',
        ]);

        $internationalCourier->update($validated);
        return response()->json($internationalCourier);
    }

    public function destroy(InternationalCourier $internationalCourier)
    {
        // Check using the correct column name
        $used = Consolidation::where('international_courier_id', $internationalCourier->id)->exists();

        if ($used) {
            return response()->json([
                'message' => 'Cannot delete because it is used in consolidations.'
            ], 422);
        }

        $internationalCourier->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
