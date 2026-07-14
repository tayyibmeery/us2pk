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

        // ✅ Get all details ordered by account, then by date
        $details = $query->orderBy('account_id')
            ->orderBy('voucher_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // ✅ Group by account and calculate running balance per account
        $groupedResults = [];
        $accountBalances = [];
        $accountTotals = [];

        foreach ($details as $detail) {
            $voucher = $detail->voucher;
            if (!$voucher) continue;

            $debit = (float) $detail->debit;
            $credit = (float) $detail->credit;
            $accountIdKey = $detail->account_id;
            $accountName = $detail->account->name ?? 'N/A';
            $accountNature = $detail->account->nature ?? 'Debit';

            // Get shipment code if reference is shipment
            $shipmentCode = null;
            if ($voucher->reference_type === 'shipment' && $voucher->reference_id) {
                $shipment = Shipment::find($voucher->reference_id);
                if ($shipment) {
                    $shipmentCode = $shipment->shipment_code;
                }
            }

            // Initialize account group if not exists
            if (!isset($groupedResults[$accountIdKey])) {
                $groupedResults[$accountIdKey] = [
                    'account_id' => $accountIdKey,
                    'account_name' => $accountName,
                    'account_nature' => $accountNature,
                    'transactions' => [],
                    'total_debit' => 0,
                    'total_credit' => 0,
                    'closing_balance' => 0,
                ];
                $accountBalances[$accountIdKey] = 0;
            }

            // Calculate running balance based on account nature
            if ($accountNature === 'Debit') {
                $accountBalances[$accountIdKey] += $debit;
                $accountBalances[$accountIdKey] -= $credit;
            } else {
                $accountBalances[$accountIdKey] += $credit;
                $accountBalances[$accountIdKey] -= $debit;
            }

            // Add transaction to account group
            $groupedResults[$accountIdKey]['transactions'][] = [
                'date' => $voucher->date,
                'voucher_no' => $voucher->voucher_no,
                'particular' => $voucher->description,
                'shipment_code' => $shipmentCode,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => round($accountBalances[$accountIdKey], 2),
                'balance_type' => $accountBalances[$accountIdKey] >= 0 ? ($accountNature === 'Debit' ? 'Dr' : 'Cr') : ($accountNature === 'Debit' ? 'Cr' : 'Dr'),
            ];

            // Update totals
            $groupedResults[$accountIdKey]['total_debit'] += $debit;
            $groupedResults[$accountIdKey]['total_credit'] += $credit;
            $groupedResults[$accountIdKey]['closing_balance'] = round($accountBalances[$accountIdKey], 2);
        }

        // Convert to array and sort by account name
        $accountsData = array_values($groupedResults);
        usort($accountsData, function ($a, $b) {
            return strcmp($a['account_name'], $b['account_name']);
        });

        // ✅ If a specific account is selected, return only that account's transactions
        if ($accountId) {
            $accountData = $groupedResults[$accountId] ?? null;
            return response()->json([
                'account' => $accountData ? [
                    'id' => $accountData['account_id'],
                    'name' => $accountData['account_name'],
                    'nature' => $accountData['account_nature'],
                    'total_debit' => $accountData['total_debit'],
                    'total_credit' => $accountData['total_credit'],
                    'closing_balance' => $accountData['closing_balance'],
                ] : null,
                'transactions' => $accountData ? $accountData['transactions'] : [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 100,
                    'total' => count($accountData ? $accountData['transactions'] : []),
                ],
            ]);
        }

        // ✅ Return all accounts with their transactions
        return response()->json([
            'accounts' => $accountsData,
            'pagination' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 100,
                'total' => count($accountsData),
            ],
        ]);
    }

    /**
     * Get all accounts with their balances (for dropdown)
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
            $balanceType = '';
            if ($account->nature === 'Debit') {
                $balance = $totalDebit - $totalCredit;
                $balanceType = $balance >= 0 ? 'Dr' : 'Cr';
            } else {
                $balance = $totalCredit - $totalDebit;
                $balanceType = $balance >= 0 ? 'Cr' : 'Dr';
            }

            $result[] = [
                'id' => $account->id,
                'name' => $account->name,
                'code' => $account->code,
                'class' => $account->acc_class,
                'nature' => $account->nature,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'balance' => round(abs($balance), 2),
                'balance_type' => $balanceType,
            ];
        }

        return response()->json($result);
    }
}
