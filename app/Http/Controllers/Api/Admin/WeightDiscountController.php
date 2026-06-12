<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\WeightDiscount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class WeightDiscountController extends Controller
{
    public function index()
    {
        return response()->json(WeightDiscount::orderBy('warehouse')->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse'        => 'required|string|max:50|unique:weight_discounts',
            'discount_percent' => 'required|numeric|min:0|max:100',
        ]);
        return response()->json(WeightDiscount::create($validated), 201);
    }

    public function show(WeightDiscount $weightDiscount)
    {
        return response()->json($weightDiscount);
    }

    public function update(Request $request, WeightDiscount $weightDiscount)
    {
        $validated = $request->validate([
            'warehouse'        => ['sometimes', 'string', 'max:50', Rule::unique('weight_discounts')->ignore($weightDiscount->id)],
            'discount_percent' => 'sometimes|numeric|min:0|max:100',
        ]);
        $weightDiscount->update($validated);
        return response()->json($weightDiscount);
    }

    public function destroy(WeightDiscount $weightDiscount)
    {
        $weightDiscount->delete();
        return response()->json(['message' => 'Weight discount deleted']);
    }
}
