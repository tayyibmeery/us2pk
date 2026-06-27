<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::orderBy('name')->paginate(20);
        return response()->json($sites);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255|unique:sites,name',
            'status' => 'boolean',
        ]);

        $site = Site::create($validated);
        return response()->json($site, 201);
    }

    public function show(Site $site)
    {
        return response()->json($site);
    }

    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'name'   => ['sometimes', 'string', 'max:255', Rule::unique('sites')->ignore($site->id)],
            'status' => 'boolean',
        ]);

        $site->update($validated);
        return response()->json($site);
    }

    public function destroy(Site $site)
    {
        $used = Shipment::where('site_id', $site->id)->exists();

        if ($used) {
            return response()->json([
                'message' => 'Cannot delete because it is used in shipments.'
            ], 422);
        }

        $site->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
