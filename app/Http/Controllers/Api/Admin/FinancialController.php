<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Shipment;
use App\Models\Revenue;
use App\Models\Debtor;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    public function profitAndLoss(Request $request)
    {
        $totalRevenue = Revenue::sum('revenue');
        $totalShippingCost = Shipment::sum('company_charges') + Shipment::sum(\DB::raw('COALESCE(delivery_charges, 0)'));
        $grossProfit = $totalRevenue - $totalShippingCost;
        $totalDue = Debtor::sum('amount_due');
        $totalCollected = Debtor::sum('cod');
        $netProfit = $grossProfit - $totalDue; // simplified

        return response()->json([
            'total_revenue'    => $totalRevenue,
            'total_costs'      => $totalShippingCost,
            'gross_profit'     => $grossProfit,
            'total_outstanding' => $totalDue,
            'total_collected'  => $totalCollected,
            'net_profit'       => $netProfit,
        ]);
    }

    public function trialBalance()
    {
        // This is a simplified version – you would join accounts, etc.
        $debits = [
            'cash' => 100000,
            'inventory' => 50000,
        ];
        $credits = [
            'revenue' => 150000,
        ];
        return response()->json(['debits' => $debits, 'credits' => $credits]);
    }
}
