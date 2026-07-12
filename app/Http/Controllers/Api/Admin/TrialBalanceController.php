<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TrialBalanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ? \Carbon\Carbon::parse($request->date) : now();
        $showUnapproved = $request->show_unapproved ?? false;

        $accounts = Account::where('is_active', true)->get();
        $trialBalance = [];

        foreach ($accounts as $account) {
            $balance = $this->getAccountBalance($account, $date, $showUnapproved);

            if (abs($balance) > 0.01) {
                if ($account->nature === 'Debit') {
                    $debit = $balance > 0 ? $balance : 0;
                    $credit = $balance < 0 ? abs($balance) : 0;
                } else {
                    $debit = $balance < 0 ? abs($balance) : 0;
                    $credit = $balance > 0 ? $balance : 0;
                }

                $trialBalance[] = [
                    'account_name' => $account->name,
                    'debit' => round($debit, 2),
                    'credit' => round($credit, 2),
                ];
            }
        }

        $totalDebit = array_sum(array_column($trialBalance, 'debit'));
        $totalCredit = array_sum(array_column($trialBalance, 'credit'));

        return response()->json([
            'data' => $trialBalance,
            'total_debit' => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
        ]);
    }

    private function getAccountBalance($account, $date, $showUnapproved = false)
    {
        $query = $account->voucherDetails()
            ->whereHas('voucher', function ($q) use ($date, $showUnapproved) {
                $q->where('date', '<=', $date)
                    ->where('is_deleted', false);

                // If not showing unapproved, only include approved vouchers
                if (!$showUnapproved) {
                    $q->where('approved', true);
                }
            });

        $details = $query->get();
        $debit = $details->sum('debit');
        $credit = $details->sum('credit');

        if ($account->nature === 'Debit') {
            return $debit - $credit;
        } else {
            return $credit - $debit;
        }
    }
}
