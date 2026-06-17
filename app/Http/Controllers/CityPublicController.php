<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\JsonResponse;

class CityPublicController extends Controller
{
    /**
     * Get list of active cities (public, no auth required)
     */
    public function index(): JsonResponse
    {
        $cities = City::where('status', true)
            ->orderBy('city_name')
            ->get(['id', 'city_name', 'city_code']);

        return response()->json($cities);
    }
}
