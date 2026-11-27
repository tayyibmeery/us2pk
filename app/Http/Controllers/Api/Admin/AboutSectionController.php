<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AboutSectionController extends Controller
{
    /**
     * Display a listing of about sections.
     */
    public function index(Request $request)
    {
        $query = AboutSection::query();

        if ($request->search) {
            $query->where('title', 'LIKE', "%{$request->search}%")
                ->orWhere('subtitle', 'LIKE', "%{$request->search}%")
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
     * Store a newly created about section.
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

        // Now validate - features is already an array
        $validated = validator($data, [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'features' => 'nullable|array',
            'features.*.icon' => 'nullable|string',
            'features.*.title' => 'nullable|string',
            'features.*.description' => 'nullable|string',
            'status' => 'sometimes|boolean'
        ])->validate();

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('about', 'public');
        }

        // Set default status
        if (!isset($validated['status'])) {
            $validated['status'] = true;
        }

        $section = AboutSection::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'About section created successfully',
            'data' => $section
        ], 201);
    }

    /**
     * Display the specified about section.
     */
    public function show(AboutSection $aboutSection)
    {
        return response()->json([
            'success' => true,
            'data' => $aboutSection
        ]);
    }

    /**
     * Update the specified about section.
     */
    public function update(Request $request, AboutSection $aboutSection)
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
            $data['features'] = $aboutSection->features ?? [];
        }

        // Now validate
        $validated = validator($data, [
            'title' => 'sometimes|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'features' => 'nullable|array',
            'features.*.icon' => 'nullable|string',
            'features.*.title' => 'nullable|string',
            'features.*.description' => 'nullable|string',
            'status' => 'sometimes|boolean'
        ])->validate();

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($aboutSection->image) {
                Storage::disk('public')->delete($aboutSection->image);
            }
            $validated['image'] = $request->file('image')->store('about', 'public');
        }

        $aboutSection->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'About section updated successfully',
            'data' => $aboutSection
        ]);
    }

    /**
     * Remove the specified about section.
     */
    public function destroy(AboutSection $aboutSection)
    {
        if ($aboutSection->image) {
            Storage::disk('public')->delete($aboutSection->image);
        }

        $aboutSection->delete();

        return response()->json([
            'success' => true,
            'message' => 'About section deleted successfully'
        ]);
    }

    /**
     * Bulk delete about sections.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:about_sections,id',
        ]);

        $sections = AboutSection::whereIn('id', $request->ids)->get();
        foreach ($sections as $section) {
            if ($section->image) {
                Storage::disk('public')->delete($section->image);
            }
        }

        AboutSection::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'About sections deleted successfully'
        ]);
    }
}
