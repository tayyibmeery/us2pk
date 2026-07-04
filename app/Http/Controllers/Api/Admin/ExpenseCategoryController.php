<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        return response()->json(ExpenseCategory::orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories',
            'type' => ['required', Rule::in(['operational', 'salary', 'other'])],
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $category = ExpenseCategory::create($validated);
        return response()->json($category, 201);
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        return response()->json($expenseCategory);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('expense_categories')->ignore($expenseCategory->id)],
            'type' => ['sometimes', Rule::in(['operational', 'salary', 'other'])],
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $expenseCategory->update($validated);
        return response()->json($expenseCategory);
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->expenses()->count() > 0) {
            return response()->json(['message' => 'Cannot delete category with existing expenses'], 422);
        }
        $expenseCategory->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
