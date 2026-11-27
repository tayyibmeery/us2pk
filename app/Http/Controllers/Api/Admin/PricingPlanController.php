<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use Illuminate\Http\Request;

class PricingPlanController extends Controller
{
    /**
     * Display a listing of pricing plans.
     */
    public function index(Request $request)
    {
        $query = PricingPlan::query();

        if ($request->search) {
            $query->where('title', 'LIKE', "%{$request->search}%")
                ->orWhere('price', 'LIKE', "%{$request->search}%")
                ->orWhere('features', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->featured) {
            $query->where('featured', filter_var($request->featured, FILTER_VALIDATE_BOOLEAN));
        }

        $sortBy = $request->sort_by ?? 'order';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->per_page ?? 20;
        $plans = $query->paginate($perPage);

        return response()->json($plans);
    }

    /**
     * Store a newly created pricing plan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'section_title' => 'nullable|string|max:255',
            'section_content' => 'nullable|string',
            'price' => 'nullable|string|max:50',
            'interval' => 'nullable|string|max:50',
            'features' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'featured' => 'sometimes|boolean',
            'order' => 'nullable|integer',
            'status' => 'sometimes|boolean'
        ]);

        if (!isset($validated['order'])) {
            $validated['order'] = PricingPlan::count() + 1;
        }

        if (!isset($validated['featured'])) {
            $validated['featured'] = false;
        }

        if (!isset($validated['status'])) {
            $validated['status'] = true;
        }

        $plan = PricingPlan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pricing plan created successfully',
            'data' => $plan
        ], 201);
    }

    /**
     * Display the specified pricing plan.
     */
    public function show(PricingPlan $pricingPlan)
    {
        return response()->json([
            'success' => true,
            'data' => $pricingPlan
        ]);
    }

    /**
     * Update the specified pricing plan.
     */
    public function update(Request $request, PricingPlan $pricingPlan)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'section_title' => 'nullable|string|max:255',
            'section_content' => 'nullable|string',
            'price' => 'nullable|string|max:50',
            'interval' => 'nullable|string|max:50',
            'features' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'featured' => 'sometimes|boolean',
            'order' => 'nullable|integer',
            'status' => 'sometimes|boolean'
        ]);

        $pricingPlan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pricing plan updated successfully',
            'data' => $pricingPlan
        ]);
    }

    /**
     * Remove the specified pricing plan.
     */
    public function destroy(PricingPlan $pricingPlan)
    {
        $pricingPlan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pricing plan deleted successfully'
        ]);
    }

    /**
     * Reorder pricing plans.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:pricing_plans,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($request->items as $item) {
            PricingPlan::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pricing plans reordered successfully'
        ]);
    }

    /**
     * Update bulk status for pricing plans.
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:pricing_plans,id',
            'status' => 'required|boolean',
        ]);

        PricingPlan::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Pricing plans status updated successfully'
        ]);
    }

    /**
     * Bulk delete pricing plans.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:pricing_plans,id',
        ]);

        PricingPlan::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pricing plans deleted successfully'
        ]);
    }
}
