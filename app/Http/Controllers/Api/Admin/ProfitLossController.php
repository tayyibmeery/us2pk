<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Account;
use App\Models\VoucherDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProfitLossController extends Controller
{
    protected function getAccountBalance($account, $start, $end)
    {
        $query = VoucherDetail::where('account_id', $account->id)
            ->whereHas('voucher', function ($q) use ($start, $end) {
                $q->where('approved', true)
                    ->where('is_deleted', false);
                if ($start) $q->whereDate('date', '>=', $start);
                if ($end) $q->whereDate('date', '<=', $end);
            });

        $debit = $query->sum('debit') ?? 0;
        $credit = $query->sum('credit') ?? 0;

        if ($account->nature === 'Debit') {
            return $debit - $credit;
        } else {
            return $credit - $debit;
        }
    }

    protected function buildPL($accounts, $start, $end)
    {
        $groups = [
            'Revenue' => ['icon' => '📈', 'color' => 'text-emerald-600'],
            'Cost of Sales' => ['icon' => '📦', 'color' => 'text-orange-600'],
            'Operating Expenses' => ['icon' => '💼', 'color' => 'text-blue-600'],
            'Other Project Expenses' => ['icon' => '📋', 'color' => 'text-purple-600'],
        ];

        $result = [];
        $grandTotal = 0;

        foreach ($groups as $group => $info) {
            $groupAccounts = $accounts->filter(function ($account) use ($group) {
                return $account->pandlcategory === $group;
            });

            $total = 0;
            $details = [];
            foreach ($groupAccounts as $account) {
                $balance = $this->getAccountBalance($account, $start, $end);
                if (abs($balance) > 0.01) {
                    $details[$account->name] = [
                        'amount' => $balance,
                        'code' => $account->code,
                    ];
                    $total += $balance;
                }
            }

            $result[$group] = [
                'total' => $total,
                'details' => $details,
                'icon' => $info['icon'],
                'color' => $info['color'],
            ];
            $grandTotal += $total;
        }

        // Calculate Profit Metrics
        $revenue = $result['Revenue']['total'] ?? 0;
        $costOfSales = $result['Cost of Sales']['total'] ?? 0;
        $operatingExpenses = $result['Operating Expenses']['total'] ?? 0;
        $otherExpenses = $result['Other Project Expenses']['total'] ?? 0;

        $grossProfit = $revenue - $costOfSales;
        $operatingProfit = $grossProfit - $operatingExpenses;
        $netProfit = $operatingProfit - $otherExpenses;

        // Profitability Ratios
        $grossProfitMargin = $revenue != 0 ? ($grossProfit / $revenue) * 100 : 0;
        $netProfitMargin = $revenue != 0 ? ($netProfit / $revenue) * 100 : 0;

        // Allocations
        $allocations = [];
        if ($netProfit != 0) {
            $allocations[] = ['name' => '5% Allocation', 'amount' => $netProfit * 0.05, 'percentage' => '5%'];
            $allocations[] = ['name' => '15% Allocation', 'amount' => $netProfit * 0.15, 'percentage' => '15%'];
            $allocations[] = ['name' => '80% Allocation', 'amount' => $netProfit * 0.80, 'percentage' => '80%'];
        }

        return [
            'revenue' => $revenue,
            'cost_of_sales' => $costOfSales,
            'gross_profit' => $grossProfit,
            'gross_profit_margin' => $grossProfitMargin,
            'operating_expenses' => $operatingExpenses,
            'operating_profit' => $operatingProfit,
            'other_expenses' => $otherExpenses,
            'net_profit' => $netProfit,
            'net_profit_margin' => $netProfitMargin,
            'allocations' => $allocations,
            'details' => $result,
            'period' => [
                'start' => $start ? $start->format('Y-m-d') : 'Inception',
                'end' => $end ? $end->format('Y-m-d') : 'Present',
            ],
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
        $start = Carbon::create($year, 1, 1)->startOfDay();
        $end = Carbon::create($year, 12, 31)->endOfDay();
        $accounts = Account::where('is_active', true)->get();
        $data = $this->buildPL($accounts, $start, $end);
        $data['period']['year'] = $year;
        return response()->json($data);
    }

    public function quarterly(Request $request)
    {
        $year = $request->year ?? now()->year;
        $quarter = $request->quarter ?? ceil(now()->month / 3);
        $start = Carbon::create($year, ($quarter - 1) * 3 + 1, 1)->startOfDay();
        $end = Carbon::create($year, $quarter * 3, 1)->endOfMonth()->endOfDay();
        $accounts = Account::where('is_active', true)->get();
        $data = $this->buildPL($accounts, $start, $end);
        $data['period']['year'] = $year;
        $data['period']['quarter'] = $quarter;
        return response()->json($data);
    }

    public function monthly(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        $accounts = Account::where('is_active', true)->get();
        $data = $this->buildPL($accounts, $start, $end);
        $data['period']['year'] = $year;
        $data['period']['month'] = $month;
        $data['period']['month_name'] = Carbon::create($year, $month, 1)->format('F');
        return response()->json($data);
    }

    public function balanceSheet(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        $day = $request->day ?? now()->day;

        $endDate = Carbon::create($year, $month, $day)->endOfDay();
        $startDate = Carbon::create(2000, 1, 1)->startOfDay();

        $accounts = Account::where('is_active', true)->get();

        // Classify accounts by ACC class
        $assetAccounts = $accounts->filter(function ($account) {
            return strtolower($account->acc_class) === 'assets';
        });

        $liabilityAccounts = $accounts->filter(function ($account) {
            return strtolower($account->acc_class) === 'liabilities';
        });

        $equityAccounts = $accounts->filter(function ($account) {
            return strtolower($account->acc_class) === 'equity';
        });

        $assets = $this->calculateBalances($assetAccounts, $startDate, $endDate);
        $liabilities = $this->calculateBalances($liabilityAccounts, $startDate, $endDate);
        $equity = $this->calculateBalances($equityAccounts, $startDate, $endDate);

        $totalAssets = array_sum(array_column($assets, 'balance'));
        $totalLiabilities = array_sum(array_column($liabilities, 'balance'));
        $totalEquity = array_sum(array_column($equity, 'balance'));

        // Add net profit/loss to equity
        $netProfit = $this->getNetProfit($startDate, $endDate);
        $equity[] = [
            'id' => null,
            'name' => 'Net Profit/Loss',
            'balance' => $netProfit,
            'nature' => 'Credit',
            'category' => 'Equity'
        ];
        $totalEquity += $netProfit;

        $liabilitiesAndEquity = $totalLiabilities + $totalEquity;
        $difference = $totalAssets - $liabilitiesAndEquity;

        return response()->json([
            'date' => $endDate->format('Y-m-d'),
            'period' => [
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'formatted' => $endDate->format('F d, Y'),
            ],
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
            'difference' => $difference,
            'equation_balanced' => abs($difference) < 0.01,
            'summary' => [
                'assets' => $totalAssets,
                'liabilities' => $totalLiabilities,
                'equity' => $totalEquity,
                'total' => $liabilitiesAndEquity,
            ]
        ]);
    }

    protected function calculateBalances($accounts, $start, $end)
    {
        $result = [];

        foreach ($accounts as $account) {
            $balance = $this->getAccountBalance($account, $start, $end);

            if (abs($balance) > 0.01) {
                $result[] = [
                    'id' => $account->id,
                    'name' => $account->name,
                    'code' => $account->code,
                    'balance' => $balance,
                    'nature' => $account->nature,
                    'category' => $account->pandlcategory ?? 'General'
                ];
            }
        }

        usort($result, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $result;
    }

    protected function getNetProfit($start, $end)
    {
        $accounts = Account::where('is_active', true)->get();
        $plData = $this->buildPL($accounts, $start, $end);
        return $plData['net_profit'] ?? 0;
    }

    public function balanceSheetToday()
    {
        return $this->balanceSheet(new Request([
            'year' => now()->year,
            'month' => now()->month,
            'day' => now()->day
        ]));
    }

    public function balanceSheetYearly(Request $request)
    {
        $year = $request->year ?? now()->year;
        return $this->balanceSheet(new Request([
            'year' => $year,
            'month' => 12,
            'day' => 31
        ]));
    }
}
