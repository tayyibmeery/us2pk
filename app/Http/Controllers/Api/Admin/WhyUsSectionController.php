<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhyUsSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WhyUsSectionController extends Controller
{
    /**
     * Display a listing of why us sections.
     */
    public function index(Request $request)
    {
        $query = WhyUsSection::query();

        if ($request->search) {
            $query->where('title', 'LIKE', "%{$request->search}%")
                ->orWhere('content', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = $request->per_page ?? 20;
        $sections = $query->paginate($perPage);

        return response()->json($sections);
    }

    /**
     * Store a newly created why us section.
     */
    public function store(Request $request)
    {
        // Get all data from request
        $data = $request->all();

        // Handle features - decode from JSON string if needed
        if (isset($data['features']) && is_string($data['features'])) {
            $decoded = json_decode($data['features'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data['features'] = $decoded;
            } else {
                $data['features'] = [];
            }
        }

        // If features is not set, set as empty array
        if (!isset($data['features'])) {
            $data['features'] = [];
        }

        // Now validate
        $validated = validator($data, [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string',
            'status' => 'sometimes|boolean'
        ])->validate();

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('whyus', 'public');
        }

        // Set default status
        if (!isset($validated['status'])) {
            $validated['status'] = true;
        }

        $section = WhyUsSection::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Why Us section created successfully',
            'data' => $section
        ], 201);
    }

    /**
     * Display the specified why us section.
     */
    public function show(WhyUsSection $whyUsSection)
    {
        return response()->json([
            'success' => true,
            'data' => $whyUsSection
        ]);
    }

    /**
     * Update the specified why us section.
     */
    public function update(Request $request, WhyUsSection $whyUsSection)
    {
        // Get all data from request
        $data = $request->all();

        // Handle features - decode from JSON string if needed
        if (isset($data['features']) && is_string($data['features'])) {
            $decoded = json_decode($data['features'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data['features'] = $decoded;
            } else {
                $data['features'] = [];
            }
        }

        // If features is not set, keep existing or set as empty array
        if (!isset($data['features'])) {
            $data['features'] = $whyUsSection->features ?? [];
        }

        // Now validate
        $validated = validator($data, [
            'title' => 'sometimes|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string',
            'status' => 'sometimes|boolean'
        ])->validate();

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($whyUsSection->image) {
                Storage::disk('public')->delete($whyUsSection->image);
            }
            $validated['image'] = $request->file('image')->store('whyus', 'public');
        }

        $whyUsSection->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Why Us section updated successfully',
            'data' => $whyUsSection
        ]);
    }

    /**
     * Remove the specified why us section.
     */
    public function destroy(WhyUsSection $whyUsSection)
    {
        if ($whyUsSection->image) {
            Storage::disk('public')->delete($whyUsSection->image);
        }

        $whyUsSection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Why Us section deleted successfully'
        ]);
    }
}
