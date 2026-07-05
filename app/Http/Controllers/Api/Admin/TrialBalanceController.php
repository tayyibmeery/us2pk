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

        $accounts = Account::where('is_active', true)->get();
        $trialBalance = [];

        foreach ($accounts as $account) {
            $balance = $account->getBalanceAttribute();
            $debit = $account->nature === 'Debit' ? max(0, $balance) : 0;
            $credit = $account->nature === 'Credit' ? max(0, -$balance) : 0;
            if ($debit != 0 || $credit != 0) {
                $trialBalance[] = [
                    'account_name' => $account->name,
                    'debit' => $debit,
                    'credit' => $credit,
                ];
            }
        }

        $totalDebit = array_sum(array_column($trialBalance, 'debit'));
        $totalCredit = array_sum(array_column($trialBalance, 'credit'));

        return response()->json([
            'data' => $trialBalance,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ]);
    }
}
