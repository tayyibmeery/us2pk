<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    public function index()
    {
        return response()->json(Address::orderBy('warehouse')->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse' => 'required|string|max:50|unique:addresses',
            'address'   => 'required|string',
        ]);
        return response()->json(Address::create($validated), 201);
    }

    public function show(Address $address)
    {
        return response()->json($address);
    }

    public function update(Request $request, Address $address)
    {
        $validated = $request->validate([
            'warehouse' => ['sometimes', 'string', 'max:50', Rule::unique('addresses')->ignore($address->id)],
            'address'   => 'sometimes|string',
        ]);
        $address->update($validated);
        return response()->json($address);
    }

    public function destroy(Address $address)
    {
        $address->delete();
        return response()->json(['message' => 'Address deleted']);
    }
}
