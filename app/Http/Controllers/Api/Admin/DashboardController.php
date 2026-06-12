<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Models\Shipment;
use App\Models\Consolidation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'total_users'        => User::where('role', 'user')->count(),
            'total_shipments'    => Shipment::count(),
            'total_consolidations' => Consolidation::count(),
            'pending_approvals'  => User::where('status', 'verified')->count(),
        ]);
    }
}
