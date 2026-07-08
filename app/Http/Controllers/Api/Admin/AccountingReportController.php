<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShipmentPayment;
use App\Models\Consolidation;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountingReportController extends Controller
{
    public function summary(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $year = $request->year;
        $month = $request->month;

        // Build date filters
        $startDate = Carbon::create($year, $month ?? 1, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month ?? 12, 1)->endOfMonth();

        if ($month) {
            // Single month
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        }

        // Income from shipment payments
        $income = ShipmentPayment::whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        // Expenses from consolidations (based on date_reached or date_departed? Use date_reached)
        $consolidationExpenses = Consolidation::whereBetween('date_reached', [$startDate, $endDate])
            ->selectRaw('SUM(courier_charges + ware_house_charges + import_taxes + net_st_payable) as total')
            ->value('total') ?? 0;





        $totalExpenses = $consolidationExpenses  ;
        $profit = $income - $totalExpenses;

        return response()->json([
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'year' => $year,
                'month' => $month,
            ],
            'income' => round($income, 2),
            'expenses' => [
                'consolidation' => round($consolidationExpenses, 2),

                'total' => round($totalExpenses, 2),
            ],
            'profit' => round($profit, 2),
        ]);
    }

    // Monthly breakdown for a given year
    public function monthlyBreakdown(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $year = $request->year;
        $months = [];

        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create($year, $m, 1)->startOfMonth();
            $end = Carbon::create($year, $m, 1)->endOfMonth();

            $income = ShipmentPayment::whereBetween('payment_date', [$start, $end])->sum('amount');
            $consExp = Consolidation::whereBetween('date_reached', [$start, $end])
                ->selectRaw('SUM(courier_charges + ware_house_charges + import_taxes + net_st_payable) as total')
                ->value('total') ?? 0;



            $months[] = [
                'month' => $m,
                'month_name' => $start->format('F'),
                'income' => round($income, 2),
                'expenses' => [
                    'consolidation' => round($consExp, 2),

                    
                    'total' => round($consExp  2),
                ],
                'profit' => round($income - ($consExp), 2),
            ];
        }

        return response()->json($months);
    }
}
