<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::query();
        if ($request->warehouse) $query->where('warehouse', $request->warehouse);
        if ($request->category) $query->where('category', 'like', "%{$request->category}%");
        if ($request->status !== null) $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        return response()->json($query->orderBy('name')->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:stores',
            'warehouse' => 'required|string|max:50',
            'category'  => 'required|string|max:100',
            'days'      => 'required|integer|min:0',
            'status'    => 'boolean',
        ]);
        return response()->json(Store::create($validated), 201);
    }

    public function show(Store $store)
    {
        return response()->json($store->load('products'));
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name'      => ['sometimes', 'string', 'max:255', Rule::unique('stores')->ignore($store->id)],
            'warehouse' => 'sometimes|string|max:50',
            'category'  => 'sometimes|string|max:100',
            'days'      => 'sometimes|integer|min:0',
            'status'    => 'sometimes|boolean',
        ]);
        $store->update($validated);
        return response()->json($store);
    }

    public function destroy(Store $store)
    {
        $store->products()->delete();
        $store->delete();
        return response()->json(['message' => 'Store deleted']);
    }
}
