<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Revenue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $query = Revenue::with('user')->orderBy('date', 'desc')->orderBy('id', 'desc');
        if ($request->type) $query->where('type', $request->type);
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhere('pcode', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }
        $revenues = $query->paginate(20);
        // Add running total
        $running = 0;
        $revenues->getCollection()->transform(function ($rev) use (&$running) {
            $running += $rev->net_revenue;
            $rev->running_total = $running;
            return $rev;
        });
        return response()->json($revenues);
    }

    public function total(Request $request)
    {
        $query = Revenue::query();
        if ($request->type) $query->where('type', $request->type);
        return response()->json([
            'total_revenue'     => $query->sum('revenue'),
            'total_output_tax'  => $query->sum('output_tax'),
            'total_net_revenue' => $query->sum('net_revenue'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_no'     => 'required|string|max:50|unique:revenues',
            'date'           => 'required|date',
            'user_id'        => 'required|exists:users,id',
            'pcode'          => 'required|string|max:50',
            'revenue'        => 'required|numeric|min:0',
            'output_tax'     => 'nullable|numeric|min:0',
            'bought_by'        => 'nullable|string|max:100',
            'vendor_payment' => 'nullable|numeric|min:0',
            'type'           => 'nullable|string|max:50',
        ]);
        $validated['net_revenue'] = $validated['revenue'] - ($validated['output_tax'] ?? 0);
        return response()->json(Revenue::create($validated), 201);
    }

    public function show(Revenue $revenue)
    {
        return response()->json($revenue->load('user'));
    }

    public function update(Request $request, Revenue $revenue)
    {
        $validated = $request->validate([
            'invoice_no'     => ['sometimes', 'string', 'max:50', Rule::unique('revenues')->ignore($revenue->id)],
            'date'           => 'sometimes|date',
            'user_id'        => 'sometimes|exists:users,id',
            'pcode'          => 'sometimes|string|max:50',
            'revenue'        => 'sometimes|numeric|min:0',
            'output_tax'     => 'nullable|numeric|min:0',
            'bought_by'        => 'nullable|string|max:100',
            'vendor_payment' => 'nullable|numeric|min:0',
            'type'           => 'nullable|string|max:50',
        ]);
        if (isset($validated['revenue']) || isset($validated['output_tax'])) {
            $revenueAmount = $validated['revenue'] ?? $revenue->revenue;
            $tax = $validated['output_tax'] ?? $revenue->output_tax;
            $validated['net_revenue'] = $revenueAmount - $tax;
        }
        $revenue->update($validated);
        return response()->json($revenue);
    }

    public function destroy(Revenue $revenue)
    {
        $revenue->delete();
        return response()->json(['message' => 'Revenue record deleted']);
    }
}
