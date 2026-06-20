<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of warehouses.
     */
    public function index()
    {
        return response()->json(Warehouse::all());
    }

    // You can add store, show, update, destroy if needed later.
}
