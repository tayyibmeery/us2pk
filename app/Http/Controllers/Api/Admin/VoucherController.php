<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\VoucherService;

class VoucherController extends Controller
{
    public function showByNumber($voucherNo)
    {
        $voucher = Voucher::with('details.account', 'creator', 'approver')
            ->where('voucher_no', $voucherNo)
            ->first();

        if (!$voucher) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }

        return response()->json($voucher);
    }
    public function index(Request $request)
    {
        $query = Voucher::with('details.account')->orderBy('id', 'desc');

        if ($request->id) {
            $query->where('voucher_no', 'like', "%{$request->id}%");
        }
        if ($request->description) {
            $query->where('description', 'like', "%{$request->description}%");
        }
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        if ($request->has('approved')) {
            $query->where('approved', $request->approved);
        }
        if ($request->has('is_deleted')) {
            $query->where('is_deleted', $request->is_deleted);
        }

        return response()->json($query->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:accounts,id',
            'entries.*.debit' => 'numeric|min:0',
            'entries.*.credit' => 'numeric|min:0',
            'entries.*.description' => 'nullable|string',
        ]);

        $entries = collect($validated['entries'])->map(function ($entry) {
            return [
                'account_id' => $entry['account_id'],
                'debit' => $entry['debit'] ?? 0,
                'credit' => $entry['credit'] ?? 0,
                'description' => $entry['description'] ?? null,
            ];
        })->toArray();

        $totalDebit = array_sum(array_column($entries, 'debit'));
        $totalCredit = array_sum(array_column($entries, 'credit'));
        if ($totalDebit != $totalCredit) {
            return response()->json(['message' => 'Total debits must equal total credits'], 422);
        }

        $voucherService = new VoucherService();
        $voucher = $voucherService->generateVoucher(
            'manual',
            'manual',
            null,
            $validated['date'],
            $validated['description'],
            $entries
        );

        return response()->json($voucher->load('details.account'), 201);
    }

    public function show(Voucher $voucher)
    {
        return response()->json($voucher->load('details.account', 'creator', 'approver'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        if ($voucher->approved || $voucher->is_deleted) {
            return response()->json(['message' => 'Cannot update approved/deleted voucher'], 422);
        }

        $validated = $request->validate([
            'date' => 'sometimes|date',
            'description' => 'nullable|string',
            'entries' => 'sometimes|array|min:2',
            'entries.*.account_id' => 'required|exists:accounts,id',
            'entries.*.debit' => 'numeric|min:0',
            'entries.*.credit' => 'numeric|min:0',
            'entries.*.description' => 'nullable|string',
        ]);

        if (isset($validated['entries'])) {
            $entries = collect($validated['entries'])->map(function ($entry) {
                return [
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                    'description' => $entry['description'] ?? null,
                ];
            })->toArray();

            $totalDebit = array_sum(array_column($entries, 'debit'));
            $totalCredit = array_sum(array_column($entries, 'credit'));
            if ($totalDebit != $totalCredit) {
                return response()->json(['message' => 'Total debits must equal total credits'], 422);
            }

            // Delete old details and add new ones
            $voucher->details()->delete();
            foreach ($entries as $entry) {
                $voucher->details()->create($entry);
            }
        }

        $voucher->update($validated);
        return response()->json($voucher->load('details.account'));
    }

    public function destroy(Voucher $voucher)
    {
        if ($voucher->approved) {
            return response()->json(['message' => 'Cannot delete approved voucher'], 422);
        }
        $voucher->is_deleted = true;
        $voucher->save();
        return response()->json(['message' => 'Voucher marked as deleted']);
    }

    public function approve(Voucher $voucher)
    {
        if ($voucher->approved) {
            return response()->json(['message' => 'Already approved'], 422);
        }
        $voucher->approved = true;
        $voucher->approved_by = auth()->id();
        $voucher->save();
        return response()->json(['message' => 'Voucher approved']);
    }
}
