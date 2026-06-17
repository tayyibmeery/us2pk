<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class SubCategoryController extends Controller
{
    public function index()
    {
        // return response()->json(SubCategory::with('category')->paginate(20));
        return response()->json(SubCategory::with('category')->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'status'      => 'required|in:Active,Inactive',
        ]);
        // Ensure unique name per category
        $exists = SubCategory::where('name', $validated['name'])
            ->where('category_id', $validated['category_id'])
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'Sub category already exists in this category'], 422);
        }
        return response()->json(SubCategory::create($validated), 201);
    }

    public function show(SubCategory $subCategory)
    {
        return response()->json($subCategory->load('category', 'subSubCategories'));
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
            'status'      => 'sometimes|in:Active,Inactive',
        ]);
        $subCategory->update($validated);
        return response()->json($subCategory);
    }

    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
