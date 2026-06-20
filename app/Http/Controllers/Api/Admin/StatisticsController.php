<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Models\Shipment;
use App\Models\Debtor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function topCities()
    {
        $totalUsers = User::where('role', 'user')->count();
        $cities = User::select('city_id', DB::raw('count(*) as total'))
            ->where('role', 'user')
            ->groupBy('city_id')
            ->with('city')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) use ($totalUsers) {  // <--- add use ($totalUsers)
                return [
                    'city'       => $item->city->city_name ?? 'Unknown',
                    'count'      => $item->total,
                    'percentage' => $totalUsers ? round(($item->total / $totalUsers) * 100, 2) : 0,
                ];
            });
        return response()->json($cities);
    }



    public function activeUsers()
    {
        $active = User::where('status', 'approved')->where('role', 'user')->count();
        $total = User::where('role', 'user')->count();
        return response()->json([
            'active'     => $active,
            'total'      => $total,
            'percentage' => $total ? round(($active / $total) * 100, 2) : 0,
        ]);
    }

    public function cityWiseBusiness()
    {
        $business = Shipment::select('users.city_id', DB::raw('SUM(shipments.company_charges) as total'))
            ->join('users', 'shipments.user_id', '=', 'users.id')
            ->groupBy('users.city_id')
            ->with('user.city')
            ->get()
            ->map(function ($item) {
                return [
                    'city'  => $item->user->city->city_name ?? 'Unknown',
                    'total' => $item->total,
                ];
            });
        return response()->json($business);
    }

    public function shipmentsStats()
    {
        $stats = Shipment::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        return response()->json($stats);
    }

    public function deliveryTime()
    {
        $avgDays = Shipment::whereNotNull('arrival_date')
            ->whereNotNull('date_delivered')
            ->select(DB::raw('AVG(DATEDIFF(date_delivered, arrival_date)) as avg_days'))
            ->first();
        return response()->json(['average_delivery_days' => round($avgDays->avg_days ?? 0, 2)]);
    }

    public function debtorsBalance()
    {
        $totalDue = Debtor::sum('amount_due');
        $totalPaid = Debtor::sum('cod');
        $balance = Debtor::sum('balance');
        return response()->json([
            'total_due'    => $totalDue,
            'total_paid'   => $totalPaid,
            'balance'      => $balance,
        ]);
    }
}
