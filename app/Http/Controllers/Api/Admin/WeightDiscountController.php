<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\WeightDiscount;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class WeightDiscountController extends Controller
{
    public function index()
    {
        $discounts = WeightDiscount::with('warehouse')
            ->orderBy('warehouse_id')
            ->paginate(20);

        return response()->json($discounts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id'     => 'required|exists:warehouses,id',
            'discount_percent' => 'required|numeric|min:0|max:100',
        ]);

        // Ensure unique warehouse discount
        $existing = WeightDiscount::where('warehouse_id', $validated['warehouse_id'])->first();
        if ($existing) {
            return response()->json([
                'message' => 'A discount for this warehouse already exists.'
            ], 422);
        }

        $discount = WeightDiscount::create($validated);
        return response()->json($discount->load('warehouse'), 201);
    }

    public function show(WeightDiscount $weightDiscount)
    {
        return response()->json($weightDiscount->load('warehouse'));
    }

    public function update(Request $request, WeightDiscount $weightDiscount)
    {
        $validated = $request->validate([
            'warehouse_id'     => ['sometimes', 'exists:warehouses,id', Rule::unique('weight_discounts')->ignore($weightDiscount->id)],
            'discount_percent' => 'sometimes|numeric|min:0|max:100',
        ]);

        $weightDiscount->update($validated);
        return response()->json($weightDiscount->load('warehouse'));
    }

    public function destroy(WeightDiscount $weightDiscount)
    {
        $weightDiscount->delete();
        return response()->json(['message' => 'Weight discount deleted']);
    }
}
