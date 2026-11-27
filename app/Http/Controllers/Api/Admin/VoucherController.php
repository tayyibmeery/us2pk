<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\VoucherService;
use Illuminate\Support\Facades\Log;

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
        // ✅ Trashed view support (SoftDeletes)
        $query = $request->has('trashed') && $request->trashed
            ? Voucher::onlyTrashed()->with('details.account')
            : Voucher::with('details.account');

        $query->orderBy('id', 'desc');

        if ($request->id) {
            $query->where('voucher_no', 'like', "%{$request->id}%");
        }
        if ($request->description) {
            $query->where('description', 'like', "%{$request->description}%");
        }
        if ($request->has('approved')) {
            $query->where('approved', filter_var($request->approved, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->has('source') && $request->source) {
            $query->where('source', $request->source);
        }

        $vouchers = $query->paginate(20);

        $vouchers->getCollection()->transform(function ($voucher) {
            $voucher->total_debit = $voucher->details->sum('debit');
            $voucher->total_credit = $voucher->details->sum('credit');
            $voucher->is_balanced = round($voucher->total_debit, 2) === round($voucher->total_credit, 2);
            return $voucher;
        });

        return response()->json($vouchers);
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
            $validated['description'] ?? 'Manual voucher',
            $validated['entries'],
            false
        );

        return response()->json($voucher->load('details.account'), 201);
    }

    public function show(Voucher $voucher)
    {
        return response()->json($voucher->load('details.account', 'creator', 'approver'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        if ($voucher->approved || $voucher->trashed()) {
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

            $voucher->details()->delete();
            foreach ($entries as $entry) {
                $voucher->details()->create($entry);
            }
        }

        $voucher->update($validated);
        return response()->json($voucher->load('details.account'));
    }

    /**
     * ✅ Password-protected soft delete.
     * - Only MANUAL vouchers can be manually deleted
     * - Approved vouchers CANNOT be deleted
     * - System-generated vouchers are protected
     */
    public function destroy(Request $request, Voucher $voucher)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $adminPassword = env('ADMIN_DELETE_PASSWORD', 'admin123');
        if ($request->password !== $adminPassword) {
            return response()->json(['message' => 'Invalid admin password.'], 403);
        }

        if ($voucher->source !== 'manual') {
            return response()->json([
                'message' => 'System-generated vouchers cannot be deleted manually. They are managed by their source transaction.'
            ], 422);
        }

        if ($voucher->approved) {
            return response()->json([
                'message' => 'Approved vouchers cannot be deleted.'
            ], 422);
        }

        $voucher->is_deleted = true;
        $voucher->deleted_at = now();
        $voucher->save();

        Log::info('Voucher soft-deleted', [
            'voucher_no' => $voucher->voucher_no,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Voucher deleted successfully.']);
    }

    /**
     * ✅ Password-protected restore
     */
    public function restore(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $adminPassword = env('ADMIN_DELETE_PASSWORD', 'admin123');
        if ($request->password !== $adminPassword) {
            return response()->json(['message' => 'Invalid admin password.'], 403);
        }

        $voucher = Voucher::onlyTrashed()->findOrFail($id);
        $voucher->deleted_at = null;
        $voucher->is_deleted = false;
        $voucher->save();

        Log::info('Voucher restored', [
            'voucher_no' => $voucher->voucher_no,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Voucher restored successfully.']);
    }

    public function approve(Voucher $voucher)
    {
        if ($voucher->approved) {
            return response()->json(['message' => 'Already approved'], 422);
        }
        if ($voucher->trashed()) {
            return response()->json(['message' => 'Cannot approve a deleted voucher'], 422);
        }
        $voucher->approved = true;
        $voucher->approved_by = auth()->id();
        $voucher->save();
        return response()->json(['message' => 'Voucher approved']);
    }
}
