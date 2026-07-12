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

    /**
     * Generate Balance Sheet
     */
    public function balanceSheet(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        $day = $request->day ?? now()->day;

        // For balance sheet, we want data up to a specific date
        $endDate = Carbon::create($year, $month, $day)->endOfDay();
        $startDate = Carbon::create(2000, 1, 1); // From the beginning or any cutoff

        // Get all active accounts
        $accounts = Account::where('is_active', true)->get();

        // Classify accounts by type
        $assetAccounts = $accounts->filter(function ($account) {
            return $account->type === 'Asset';
        });

        $liabilityAccounts = $accounts->filter(function ($account) {
            return $account->type === 'Liability';
        });

        $equityAccounts = $accounts->filter(function ($account) {
            return $account->type === 'Equity';
        });

        // Calculate balances
        $assets = $this->calculateBalances($assetAccounts, $startDate, $endDate);
        $liabilities = $this->calculateBalances($liabilityAccounts, $startDate, $endDate);
        $equity = $this->calculateBalances($equityAccounts, $startDate, $endDate);

        // Calculate totals
        $totalAssets = array_sum(array_column($assets, 'balance'));
        $totalLiabilities = array_sum(array_column($liabilities, 'balance'));
        $totalEquity = array_sum(array_column($equity, 'balance'));

        // Add net profit/loss to equity if we have P&L for the period
        $netProfit = $this->getNetProfit($startDate, $endDate);

        // If net profit is positive, add to retained earnings
        // If negative, subtract from retained earnings
        $equity['Net Profit/Loss'] = [
            'name' => 'Net Profit/Loss',
            'balance' => $netProfit,
            'type' => 'Equity'
        ];
        $totalEquity += $netProfit;

        // The accounting equation should balance: Assets = Liabilities + Equity
        $liabilitiesAndEquity = $totalLiabilities + $totalEquity;

        return response()->json([
            'date' => $endDate->format('Y-m-d'),
            'assets' => [
                'items' => $assets,
                'total' => $totalAssets
            ],
            'liabilities' => [
                'items' => $liabilities,
                'total' => $totalLiabilities
            ],
            'equity' => [
                'items' => $equity,
                'total' => $totalEquity
            ],
            'total_liabilities_equity' => $liabilitiesAndEquity,
            'difference' => $totalAssets - $liabilitiesAndEquity, // Should be 0
            'equation_balanced' => ($totalAssets - $liabilitiesAndEquity) == 0
        ]);
    }

    /**
     * Calculate balances for a collection of accounts
     */
    protected function calculateBalances($accounts, $start, $end)
    {
        $result = [];

        foreach ($accounts as $account) {
            $balance = $this->getAccountBalance($account, $start, $end);

            // Only include accounts with non-zero balance
            if ($balance != 0) {
                $result[] = [
                    'id' => $account->id,
                    'name' => $account->name,
                    'balance' => $balance,
                    'type' => $account->type,
                    'nature' => $account->nature,
                    'category' => $account->pandlcategory ?? 'General'
                ];
            }
        }

        return $result;
    }

    /**
     * Get net profit/loss for a specific period
     */
    protected function getNetProfit($start, $end)
    {
        // Get all active accounts
        $accounts = Account::where('is_active', true)->get();

        // Build P&L for the period
        $plData = $this->buildPL($accounts, $start, $end);

        // Return the net profit
        return $plData['net_profit'] ?? 0;
    }

    /**
     * Balance Sheet as of today (convenience method)
     */
    public function balanceSheetToday()
    {
        return $this->balanceSheet(new Request([
            'year' => now()->year,
            'month' => now()->month,
            'day' => now()->day
        ]));
    }

    /**
     * Balance Sheet with year/month selection
     */
    public function balanceSheetYearly(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? 12;
        $day = $request->day ?? 31;

        return $this->balanceSheet(new Request([
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]));
    }
}
