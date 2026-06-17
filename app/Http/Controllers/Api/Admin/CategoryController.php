<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'status'      => 'required|in:Active,Inactive',
        ]);
        return response()->json(Category::create($validated), 201);
    }

    public function show(Category $category)
    {
        return response()->json($category->load('subCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => ['sometimes', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => 'nullable|string',
            'status'      => 'sometimes|in:Active,Inactive',
        ]);
        $category->update($validated);
        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
