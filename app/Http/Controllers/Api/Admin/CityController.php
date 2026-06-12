<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    public function index()
    {
        return response()->json(City::paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'city_name' => 'required|string|max:100',
            'city_code' => 'required|string|max:10|unique:cities',
            'status'    => 'boolean',
        ]);
        return response()->json(City::create($validated), 201);
    }

    public function show(City $city)
    {
        return response()->json($city);
    }

    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'city_name' => 'sometimes|string|max:100',
            'city_code' => ['sometimes', 'string', 'max:10', Rule::unique('cities')->ignore($city->id)],
            'status'    => 'sometimes|boolean',
        ]);
        $city->update($validated);
        return response()->json($city);
    }

    public function destroy(City $city)
    {
        $city->delete();
        return response()->json(['message' => 'City deleted']);
    }
}
