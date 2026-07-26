<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index(Request $request)
    {
        $query = BlogPost::query();

        if ($request->search) {
            $query->where('title', 'LIKE', "%{$request->search}%")
                ->orWhere('content', 'LIKE', "%{$request->search}%")
                ->orWhere('excerpt', 'LIKE', "%{$request->search}%")
                ->orWhere('author', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->per_page ?? 20;
        $posts = $query->paginate($perPage);

        return response()->json($posts);
    }

    /**
     * Store a newly created blog post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'section_title' => 'nullable|string|max:255',
            'section_content' => 'nullable|string',
            'published_at' => 'nullable|date',
            'author' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'status' => 'sometimes|boolean'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('blog', 'public');
        }

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (!isset($validated['order'])) {
            $validated['order'] = BlogPost::count() + 1;
        }

        if (!isset($validated['status'])) {
            $validated['status'] = true;
        }

        $post = BlogPost::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Blog post created successfully',
            'data' => $post
        ], 201);
    }

    /**
     * Display the specified blog post.
     */
    public function show(BlogPost $blogPost)
    {
        return response()->json([
            'success' => true,
            'data' => $blogPost
        ]);
    }

    /**
     * Update the specified blog post.
     */
    public function update(Request $request, BlogPost $blogPost)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $blogPost->id,
            'excerpt' => 'nullable|string',
            'content' => 'sometimes|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'section_title' => 'nullable|string|max:255',
            'section_content' => 'nullable|string',
            'published_at' => 'nullable|date',
            'author' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'status' => 'sometimes|boolean'
        ]);

        if ($request->hasFile('image')) {
            if ($blogPost->image) {
                Storage::disk('public')->delete($blogPost->image);
            }
            $validated['image'] = $request->file('image')->store('blog', 'public');
        }

        if (isset($validated['title']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $blogPost->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Blog post updated successfully',
            'data' => $blogPost
        ]);
    }

    /**
     * Remove the specified blog post.
     */
    public function destroy(BlogPost $blogPost)
    {
        if ($blogPost->image) {
            Storage::disk('public')->delete($blogPost->image);
        }

        $blogPost->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog post deleted successfully'
        ]);
    }

    /**
     * Update bulk status for blog posts.
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:blog_posts,id',
            'status' => 'required|boolean',
        ]);

        BlogPost::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Blog posts status updated successfully'
        ]);
    }

    /**
     * Bulk delete blog posts.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:blog_posts,id',
        ]);

        $posts = BlogPost::whereIn('id', $request->ids)->get();
        foreach ($posts as $post) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
        }

        BlogPost::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog posts deleted successfully'
        ]);
    }
}
