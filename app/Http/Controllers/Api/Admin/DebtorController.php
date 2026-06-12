<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Debtor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DebtorController extends Controller
{
    public function index(Request $request)
    {
        $query = Debtor::with(['user.city', 'user.shipments' => fn($q) => $q->latest()->take(1)])
            ->orderBy('created_at', 'desc');
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%")->orWhere('pcode', 'like', "%{$search}%"));
            });
        }
        $debtors = $query->paginate(20);
        $debtors->getCollection()->transform(function ($debtor) {
            $debtor->shipment_status = $debtor->user->shipments->first()?->status;
            return $debtor;
        });
        return response()->json($debtors);
    }

    public function show(Debtor $debtor)
    {
        return response()->json($debtor->load('user.city'));
    }

    public function update(Request $request, Debtor $debtor)
    {
        $validated = $request->validate([
            'amount_due' => 'sometimes|numeric|min:0',
            'cod'        => 'sometimes|numeric|min:0',
            'cod_date'   => 'nullable|date',
            'balance'    => 'sometimes|numeric',
        ]);
        $debtor->update($validated);
        return response()->json($debtor);
    }

    public function destroy(Debtor $debtor)
    {
        $debtor->delete();
        return response()->json(['message' => 'Debtor record deleted']);
    }
}
