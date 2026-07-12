<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Account;
use App\Models\VoucherDetail;
use App\Models\Shipment;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $accountId = $request->account_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $source = $request->source;
        $perPage = $request->per_page ?? 20;

        // Get account details
        $account = null;
        if ($accountId) {
            $account = Account::find($accountId);
        }

        // Build query for voucher details
        $query = VoucherDetail::with(['voucher', 'account'])
            ->whereHas('voucher', function ($q) use ($fromDate, $toDate, $source) {
                $q->where('is_deleted', false)
                    ->where('approved', true);

                if ($fromDate) {
                    $q->whereDate('date', '>=', $fromDate);
                }
                if ($toDate) {
                    $q->whereDate('date', '<=', $toDate);
                }
                if ($source) {
                    $q->where('source', $source);
                }
            });

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        // ✅ Order by date ASC (oldest first) for correct running balance
        $details = $query->orderBy('voucher_id', 'asc')
            ->orderBy('id', 'asc')
            ->paginate($perPage);

        // Calculate running balance for the selected account only
        $runningBalance = 0;
        $results = [];

        foreach ($details as $detail) {
            $voucher = $detail->voucher;
            if (!$voucher) continue;

            $debit = (float) $detail->debit;
            $credit = (float) $detail->credit;

            // Get shipment code if reference is shipment
            $shipmentCode = null;
            if ($voucher->reference_type === 'shipment' && $voucher->reference_id) {
                $shipment = Shipment::find($voucher->reference_id);
                if ($shipment) {
                    $shipmentCode = $shipment->shipment_code;
                }
            }

            // ✅ Calculate balance based on account nature
            if ($account && $account->nature === 'Debit') {
                // For Debit nature accounts: Balance increases on Debit, decreases on Credit
                $runningBalance += ($debit - $credit);
            } else if ($account && $account->nature === 'Credit') {
                // For Credit nature accounts: Balance increases on Credit, decreases on Debit
                $runningBalance += ($credit - $debit);
            } else {
                // If no account selected, show individual entry balance
                $runningBalance = ($debit - $credit);
            }

            $results[] = [
                'date' => $voucher->date,
                'voucher_no' => $voucher->voucher_no,
                'source' => $voucher->source,
                'approved' => $voucher->approved,
                'description' => $voucher->description,
                'account_name' => $detail->account->name ?? 'N/A',
                'account_id' => $detail->account_id,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => round($runningBalance, 2),
                'shipment_code' => $shipmentCode,
                'reference_type' => $voucher->reference_type,
                'reference_id' => $voucher->reference_id,
            ];
        }

        // ✅ If no account selected, reset balance to show individual entry balance
        if (!$account) {
            foreach ($results as &$result) {
                $result['balance'] = $result['debit'] - $result['credit'];
            }
        }

        return response()->json([
            'data' => $results,
            'pagination' => [
                'current_page' => $details->currentPage(),
                'last_page' => $details->lastPage(),
                'per_page' => $details->perPage(),
                'total' => $details->total(),
                'from' => $details->firstItem(),
                'to' => $details->lastItem(),
                'prev_page_url' => $details->previousPageUrl(),
                'next_page_url' => $details->nextPageUrl(),
            ],
            'account' => $account ? [
                'id' => $account->id,
                'name' => $account->name,
                'nature' => $account->nature,
            ] : null,
        ]);
    }

    /**
     * Get all accounts with their balances (for the sidebar or summary)
     */
    public function getAccountBalances(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $source = $request->source;

        $accounts = Account::where('is_active', true)->get();
        $result = [];

        foreach ($accounts as $account) {
            $query = VoucherDetail::where('account_id', $account->id)
                ->whereHas('voucher', function ($q) use ($fromDate, $toDate, $source) {
                    $q->where('is_deleted', false)
                        ->where('approved', true);

                    if ($fromDate) {
                        $q->whereDate('date', '>=', $fromDate);
                    }
                    if ($toDate) {
                        $q->whereDate('date', '<=', $toDate);
                    }
                    if ($source) {
                        $q->where('source', $source);
                    }
                });

            $totalDebit = (float) $query->sum('debit');
            $totalCredit = (float) $query->sum('credit');

            if ($totalDebit == 0 && $totalCredit == 0) {
                continue;
            }

            $balance = 0;
            if ($account->nature === 'Debit') {
                $balance = $totalDebit - $totalCredit;
            } else {
                $balance = $totalCredit - $totalDebit;
            }

            $result[] = [
                'id' => $account->id,
                'name' => $account->name,
                'code' => $account->code,
                'class' => $account->acc_class,
                'nature' => $account->nature,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'balance' => round($balance, 2),
            ];
        }

        return response()->json($result);
    }
}
