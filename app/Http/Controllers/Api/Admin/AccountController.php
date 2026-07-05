<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = Account::query();
        if ($request->name) $query->where('name', 'like', "%{$request->name}%");
        if ($request->class) $query->where('acc_class', $request->class);
        if ($request->nature) $query->where('nature', $request->nature);
        if ($request->ownership) $query->where('ownership', $request->ownership);
        if ($request->pandlcategory) $query->where('pandlcategory', $request->pandlcategory);
        if ($request->status !== null) $query->where('is_active', $request->status);
        return response()->json($query->orderBy('name')->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:accounts',
            'acc_class' => ['required', Rule::in(['Assets', 'Liabilities', 'Equity', 'Income', 'Expense'])],
            'nature' => ['required', Rule::in(['Debit', 'Credit'])],
            'ownership' => ['required', Rule::in(['US2PK', 'Others'])],
            'pandlcategory' => ['required', Rule::in(['Revenue', 'Cost of Sales', 'Operating Expenses', 'Other Project Expenses', 'None'])],
            'is_active' => 'boolean',
        ]);

        $account = Account::create($validated);
        $account->code = 'AC-' . str_pad($account->id, 4, '0', STR_PAD_LEFT);
        $account->save();

        return response()->json($account, 201);
    }

    public function show(Account $account)
    {
        return response()->json($account->load('voucherDetails'));
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('accounts')->ignore($account->id)],
            'acc_class' => ['sometimes', Rule::in(['Assets', 'Liabilities', 'Equity', 'Income', 'Expense'])],
            'nature' => ['sometimes', Rule::in(['Debit', 'Credit'])],
            'ownership' => ['sometimes', Rule::in(['US2PK', 'Others'])],
            'pandlcategory' => ['sometimes', Rule::in(['Revenue', 'Cost of Sales', 'Operating Expenses', 'Other Project Expenses', 'None'])],
            'is_active' => 'sometimes|boolean',
        ]);

        $account->update($validated);
        return response()->json($account);
    }

    public function destroy(Account $account)
    {
        if ($account->voucherDetails()->exists()) {
            return response()->json(['message' => 'Cannot delete account with voucher details'], 422);
        }
        $account->delete();
        return response()->json(['message' => 'Account deleted']);
    }

    public function toggleStatus(Account $account)
    {
        $account->is_active = !$account->is_active;
        $account->save();
        return response()->json(['message' => 'Status toggled']);
    }
}
