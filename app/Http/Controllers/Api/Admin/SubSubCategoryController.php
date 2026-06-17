<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\SubSubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class SubSubCategoryController extends Controller
{
    public function index()
    {
        return response()->json(SubSubCategory::with(['category', 'subCategory'])->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'category_id'     => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'status'          => 'required|in:Active,Inactive',
        ]);
        // Ensure unique name within sub_category
        $exists = SubSubCategory::where('name', $validated['name'])
            ->where('sub_category_id', $validated['sub_category_id'])
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'Sub sub category already exists in this sub category'], 422);
        }
        return response()->json(SubSubCategory::create($validated), 201);
    }

    public function show(SubSubCategory $subSubCategory)
    {
        return response()->json($subSubCategory->load(['category', 'subCategory']));
    }

    public function update(Request $request, SubSubCategory $subSubCategory)
    {
        $validated = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'description'     => 'nullable|string',
            'category_id'     => 'sometimes|exists:categories,id',
            'sub_category_id' => 'sometimes|exists:sub_categories,id',
            'status'          => 'sometimes|in:Active,Inactive',
        ]);
        $subSubCategory->update($validated);
        return response()->json($subSubCategory);
    }

    public function destroy(SubSubCategory $subSubCategory)
    {
        $subSubCategory->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
