<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stat;
use Illuminate\Http\Request;

class StatController extends Controller
{
    /**
     * Display a listing of stats.
     */
    public function index(Request $request)
    {
        $query = Stat::query();

        if ($request->search) {
            $query->where('section_title', 'LIKE', "%{$request->search}%")
                ->orWhere('section_content', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = $request->per_page ?? 20;
        $stats = $query->paginate($perPage);

        return response()->json($stats);
    }

    /**
     * Store a newly created stat.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'happy_clients' => 'nullable|integer|min:0',
            'complete_shipments' => 'nullable|integer|min:0',
            'customer_reviews' => 'nullable|integer|min:0',
            'active_services' => 'nullable|integer|min:0',
            'section_title' => 'nullable|string|max:255',
            'section_content' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'status' => 'sometimes|boolean'
        ]);

        if (!isset($validated['status'])) {
            $validated['status'] = true;
        }

        $stat = Stat::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stat created successfully',
            'data' => $stat
        ], 201);
    }

    /**
     * Display the specified stat.
     */
    public function show(Stat $stat)
    {
        return response()->json([
            'success' => true,
            'data' => $stat
        ]);
    }

    /**
     * Update the specified stat.
     */
    public function update(Request $request, Stat $stat)
    {
        $validated = $request->validate([
            'happy_clients' => 'nullable|integer|min:0',
            'complete_shipments' => 'nullable|integer|min:0',
            'customer_reviews' => 'nullable|integer|min:0',
            'active_services' => 'nullable|integer|min:0',
            'section_title' => 'nullable|string|max:255',
            'section_content' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'status' => 'sometimes|boolean'
        ]);

        $stat->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stat updated successfully',
            'data' => $stat
        ]);
    }

    /**
     * Remove the specified stat.
     */
    public function destroy(Stat $stat)
    {
        $stat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stat deleted successfully'
        ]);
    }
}
