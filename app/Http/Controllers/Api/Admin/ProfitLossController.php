<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Account;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ProfitLossController extends Controller
{
    protected function getAccountBalance($account, $start, $end)
    {
        $query = $account->voucherDetails()
            ->whereHas('voucher', function ($q) use ($start, $end) {
                $q->where('approved', true)
                    ->where('is_deleted', false);
                if ($start) $q->whereDate('date', '>=', $start);
                if ($end) $q->whereDate('date', '<=', $end);
            });

        $debit = $query->sum('debit');
        $credit = $query->sum('credit');
        $balance = $debit - $credit;

        if ($account->nature === 'Credit') {
            $balance = $credit - $debit;
        }
        return $balance;
    }

    protected function buildPL($accounts, $start, $end)
    {
        $groups = ['Revenue', 'Cost of Sales', 'Operating Expenses', 'Other Project Expenses'];
        $result = [];

        foreach ($groups as $group) {
            $groupAccounts = $accounts->filter(function ($account) use ($group) {
                return $account->pandlcategory === $group;
            });

            $total = 0;
            $details = [];
            foreach ($groupAccounts as $account) {
                $balance = $this->getAccountBalance($account, $start, $end);
                if ($balance != 0) {
                    $details[$account->name] = $balance;
                    $total += $balance;
                }
            }
            $result[$group] = ['total' => $total, 'details' => $details];
        }

        // Calculate Gross Profit, Operating Profit, Net Profit
        $revenue = $result['Revenue']['total'] ?? 0;
        $costOfSales = $result['Cost of Sales']['total'] ?? 0;
        $operatingExpenses = $result['Operating Expenses']['total'] ?? 0;
        $otherExpenses = $result['Other Project Expenses']['total'] ?? 0;

        $grossProfit = $revenue - $costOfSales;
        $operatingProfit = $grossProfit - $operatingExpenses;
        $netProfit = $operatingProfit - $otherExpenses;

        // Allocations (5%, 15%, 80%) – we can compute based on gross or net
        $allocations = [];
        if ($netProfit != 0) {
            $allocations['5%'] = $netProfit * 0.05;
            $allocations['15%'] = $netProfit * 0.15;
            $allocations['80%'] = $netProfit * 0.80;
        }

        return [
            'revenue' => $revenue,
            'cost_of_sales' => $costOfSales,
            'gross_profit' => $grossProfit,
            'operating_expenses' => $operatingExpenses,
            'operating_profit' => $operatingProfit,
            'other_expenses' => $otherExpenses,
            'net_profit' => $netProfit,
            'allocations' => $allocations,
            'details' => $result,
        ];
    }

    public function sinceInception()
    {
        $accounts = Account::where('is_active', true)->get();
        $data = $this->buildPL($accounts, null, null);
        return response()->json($data);
    }

    public function yearly(Request $request)
    {
        $year = $request->year ?? now()->year;
        $start = Carbon::create($year, 1, 1);
        $end = Carbon::create($year, 12, 31);
        $accounts = Account::where('is_active', true)->get();
        $data = $this->buildPL($accounts, $start, $end);
        return response()->json($data);
    }

    public function quarterly(Request $request)
    {
        $year = $request->year ?? now()->year;
        $quarter = $request->quarter ?? ceil(now()->month / 3);
        $start = Carbon::create($year, ($quarter - 1) * 3 + 1, 1);
        $end = Carbon::create($year, $quarter * 3, 1)->endOfMonth();
        $accounts = Account::where('is_active', true)->get();
        $data = $this->buildPL($accounts, $start, $end);
        return response()->json($data);
    }

    public function monthly(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        $start = Carbon::create($year, $month, 1);
        $end = Carbon::create($year, $month, 1)->endOfMonth();
        $accounts = Account::where('is_active', true)->get();
        $data = $this->buildPL($accounts, $start, $end);
        return response()->json($data);
    }
}
