<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\VoucherService;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('category', 'creator');
        if ($request->search) {
            $query->where('description', 'like', "%{$request->search}%")
                ->orWhere('reference', 'like', "%{$request->search}%");
        }
        if ($request->category) {
            $query->where('category_id', $request->category);
        }
        if ($request->start_date) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('date', '<=', $request->end_date);
        }
        return response()->json($query->orderBy('date', 'desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
        ]);

        $validated['created_by'] = Auth::id();
        $expense = Expense::create($validated);
        $voucherService = new VoucherService();
        $voucherService->generateExpenseVoucher($expense);
        return response()->json($expense->load('category', 'creator'), 201);
    }

    public function show(Expense $expense)
    {
        return response()->json($expense->load('category', 'creator'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:expense_categories,id',
            'amount' => 'sometimes|numeric|min:0.01',
            'date' => 'sometimes|date',
            'description' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
        ]);

        $expense->update($validated);
        $voucherService = new VoucherService();
        $voucherService->generateExpenseVoucher($expense);
        return response()->json($expense->load('category', 'creator'));
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
