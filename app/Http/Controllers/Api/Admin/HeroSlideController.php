<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class HeroSlideController extends Controller
{
    /**
     * Display a listing of hero slides.
     */
    public function index(Request $request)
    {
        $query = HeroSlide::query();

        // Search
        if ($request->search) {
            $query->where('title', 'LIKE', "%{$request->search}%")
                ->orWhere('subtitle', 'LIKE', "%{$request->search}%")
                ->orWhere('description', 'LIKE', "%{$request->search}%");
        }

        // Filter by status
        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        // Sorting
        $sortBy = $request->sort_by ?? 'order';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->per_page ?? 20;
        $slides = $query->paginate($perPage);

        return response()->json($slides);
    }

    /**
     * Store a newly created hero slide.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'button1_text' => 'nullable|string|max:100',
            'button1_link' => 'nullable|string|max:255',
            'button2_text' => 'nullable|string|max:100',
            'button2_link' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'sometimes|boolean'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('hero', 'public');
        }

        // Set default order
        if (!isset($validated['order'])) {
            $validated['order'] = HeroSlide::count() + 1;
        }

        // Set default status
        if (!isset($validated['status'])) {
            $validated['status'] = true;
        }

        $slide = HeroSlide::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Hero slide created successfully',
            'data' => $slide
        ], 201);
    }

    /**
     * Display the specified hero slide.
     */
    public function show(HeroSlide $heroSlide)
    {
        return response()->json([
            'success' => true,
            'data' => $heroSlide
        ]);
    }

    /**
     * Update the specified hero slide.
     */
    public function update(Request $request, HeroSlide $heroSlide)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'button1_text' => 'nullable|string|max:100',
            'button1_link' => 'nullable|string|max:255',
            'button2_text' => 'nullable|string|max:100',
            'button2_link' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'sometimes|boolean'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($heroSlide->image) {
                Storage::disk('public')->delete($heroSlide->image);
            }
            $validated['image'] = $request->file('image')->store('hero', 'public');
        }

        $heroSlide->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Hero slide updated successfully',
            'data' => $heroSlide
        ]);
    }

    /**
     * Remove the specified hero slide.
     */
    public function destroy(HeroSlide $heroSlide)
    {
        // Delete image
        if ($heroSlide->image) {
            Storage::disk('public')->delete($heroSlide->image);
        }

        $heroSlide->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hero slide deleted successfully'
        ]);
    }

    /**
     * Reorder hero slides.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:hero_slides,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($request->items as $item) {
            HeroSlide::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hero slides reordered successfully'
        ]);
    }

    /**
     * Update bulk status for hero slides.
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:hero_slides,id',
            'status' => 'required|boolean',
        ]);

        HeroSlide::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Hero slides status updated successfully'
        ]);
    }

    /**
     * Bulk delete hero slides.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:hero_slides,id',
        ]);

        $slides = HeroSlide::whereIn('id', $request->ids)->get();
        foreach ($slides as $slide) {
            if ($slide->image) {
                Storage::disk('public')->delete($slide->image);
            }
        }

        HeroSlide::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hero slides deleted successfully'
        ]);
    }
}
