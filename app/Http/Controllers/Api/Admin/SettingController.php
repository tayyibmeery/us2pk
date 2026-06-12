<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function index()
    {
        return response()->json(Setting::orderBy('name')->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:settings',
            'value'       => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        return response()->json(Setting::create($validated), 201);
    }

    public function show(Setting $setting)
    {
        return response()->json($setting);
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'name'        => ['sometimes', 'string', 'max:100', Rule::unique('settings')->ignore($setting->id)],
            'value'       => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        $setting->update($validated);
        return response()->json($setting);
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();
        return response()->json(['message' => 'Setting deleted']);
    }
}
