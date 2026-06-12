<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query();
        if ($request->status !== null) {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }
        return response()->json($query->orderBy('title')->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'slug'    => 'nullable|string|max:255|unique:pages',
            'content' => 'required|string',
            'status'  => 'boolean',
        ]);
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        return response()->json(Page::create($validated), 201);
    }

    public function show(Page $page)
    {
        return response()->json($page);
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title'   => 'sometimes|string|max:255',
            'slug'    => ['sometimes', 'string', 'max:255', Rule::unique('pages')->ignore($page->id)],
            'content' => 'sometimes|string',
            'status'  => 'sometimes|boolean',
        ]);
        if (isset($validated['slug']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title'] ?? $page->title);
        }
        $page->update($validated);
        return response()->json($page);
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return response()->json(['message' => 'Page deleted']);
    }
}
