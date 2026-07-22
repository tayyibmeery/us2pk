<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    /**
     * Get all pages with filtering, sorting, and pagination
     */
    public function index(Request $request)
    {
        $query = Page::query();

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        $sortBy = $request->sort_by ?? 'order';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->per_page ?? 20;
        $pages = $query->paginate($perPage);

        return response()->json($pages);
    }

    /**
     * Get all page types for dropdown
     */
    public function getTypes()
    {
        $types = [
            'page' => 'Page',
            'hero' => 'Hero Section',
            'service' => 'Service',
            'testimonial' => 'Testimonial',
            'team' => 'Team Member',
            'pricing' => 'Pricing Plan',
            'faq' => 'FAQ',
            'blog' => 'Blog Post',
            'about' => 'About Section',
            'whyus' => 'Why Us Section',
            'contact' => 'Contact Section',
        ];

        return response()->json($types);
    }

    /**
     * Display the specified page
     */
    public function show(Page $page)
    {
        return response()->json($page);
    }

    /**
     * Store a newly created page
     */
    public function store(Request $request)
    {
        $data = $request->all();

        // Handle meta field - decode JSON string to array
        if (isset($data['meta']) && is_string($data['meta'])) {
            $decoded = json_decode($data['meta'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data['meta'] = $decoded;
            } else {
                $data['meta'] = [];
            }
        }

        // If meta is not present or null, set as empty array
        if (!isset($data['meta'])) {
            $data['meta'] = [];
        }

        // Validate - REMOVE meta validation since we handle it manually
        $validated = validator($data, [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages',
            'type' => 'required|string|in:page,hero,service,testimonial,team,pricing,faq,blog,about,whyus,contact',
            'content' => 'nullable|string',
            'status' => 'sometimes|boolean',
            'order' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'icon' => 'nullable|string|max:255',
            // REMOVED: 'meta' => 'nullable|array',
            'parent_id' => 'nullable|exists:pages,id',
        ])->validate();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('pages', 'public');
            $validated['image'] = $imagePath;
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Set default order
        if (!isset($validated['order'])) {
            $validated['order'] = Page::where('type', $validated['type'])->count();
        }

        // Set default status
        if (!isset($validated['status'])) {
            $validated['status'] = true;
        }

        // Ensure meta is an array
        $validated['meta'] = $data['meta'] ?? [];

        $page = Page::create($validated);

        return response()->json($page, 201);
    }

    /**
     * Update the specified page
     */
    public function update(Request $request, Page $page)
    {
        $data = $request->all();

        // Handle meta field - decode JSON string to array
        if (isset($data['meta']) && is_string($data['meta'])) {
            $decoded = json_decode($data['meta'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data['meta'] = $decoded;
            } else {
                $data['meta'] = [];
            }
        }

        // If meta is not present or null, set as empty array
        if (!isset($data['meta'])) {
            $data['meta'] = [];
        }

        // Validate - REMOVE meta validation since we handle it manually
        $validated = validator($data, [
            'title' => 'sometimes|string|max:255',
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('pages')->ignore($page->id)],
            'type' => 'sometimes|string|in:page,hero,service,testimonial,team,pricing,faq,blog,about,whyus,contact',
            'content' => 'nullable|string',
            'status' => 'sometimes|boolean',
            'order' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'icon' => 'nullable|string|max:255',
            // REMOVED: 'meta' => 'nullable|array',
            'parent_id' => 'nullable|exists:pages,id',
        ])->validate();

        // Handle image upload - only if new file is provided
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($page->image) {
                $oldImagePath = $page->image;
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            $imagePath = $request->file('image')->store('pages', 'public');
            $validated['image'] = $imagePath;
        }

        // Generate slug if not provided
        if (isset($validated['slug']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title'] ?? $page->title);
        }

        // Ensure meta is an array
        $validated['meta'] = $data['meta'] ?? [];

        $page->update($validated);

        return response()->json($page);
    }

    /**
     * Remove the specified page
     */
    public function destroy(Page $page)
    {
        if ($page->image) {
            $imagePath = $page->image;
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $page->delete();
        return response()->json(['message' => 'Page deleted successfully']);
    }

    /**
     * Upload image only
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $path = $request->file('image')->store('pages', 'public');

        return response()->json([
            'success' => true,
            'data' => [
                'path' => $path,
                'url' => asset('storage/' . $path),
            ],
            'message' => 'Image uploaded successfully'
        ]);
    }

    /**
     * Delete image from page
     */
    public function deleteImage(Request $request, Page $page)
    {
        if ($page->image) {
            $imagePath = $page->image;
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $page->update(['image' => null]);

            return response()->json(['message' => 'Image deleted successfully']);
        }

        return response()->json(['message' => 'No image to delete'], 404);
    }

    /**
     * Reorder pages
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'pages' => 'required|array',
            'pages.*.id' => 'required|integer|exists:pages,id',
            'pages.*.order' => 'required|integer',
        ]);

        foreach ($request->pages as $pageData) {
            Page::where('id', $pageData['id'])->update(['order' => $pageData['order']]);
        }

        return response()->json(['message' => 'Pages reordered successfully']);
    }

    /**
     * Bulk delete pages
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:pages,id',
        ]);

        $pages = Page::whereIn('id', $request->ids)->get();
        foreach ($pages as $page) {
            if ($page->image) {
                $imagePath = $page->image;
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
        }

        Page::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Pages deleted successfully']);
    }

    /**
     * Bulk update status
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:pages,id',
            'status' => 'required|boolean',
        ]);

        Page::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return response()->json(['message' => 'Pages status updated successfully']);
    }
}
