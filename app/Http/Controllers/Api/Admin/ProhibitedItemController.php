<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProhibitedItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProhibitedItemController extends Controller
{
    /**
     * Display a listing of prohibited items.
     */
    public function index(Request $request)
    {
        $query = ProhibitedItem::query();

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('item_name', 'LIKE', "%{$request->search}%")
                    ->orWhere('category', 'LIKE', "%{$request->search}%")
                    ->orWhere('description', 'LIKE', "%{$request->search}%")
                    ->orWhere('reason', 'LIKE', "%{$request->search}%");
            });
        }

        // Filter by category
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Filter by severity
        if ($request->severity) {
            $query->where('severity', $request->severity);
        }

        // Filter by active status
        if ($request->has('is_active') && $request->is_active !== null && $request->is_active !== '') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        // Sorting
        $sortBy = $request->sort_by ?? 'order';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->per_page ?? 20;
        $items = $query->paginate($perPage);

        // Get categories for filter dropdown
        $categories = ProhibitedItem::getCategories();

        return response()->json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'categories' => $categories,
                'severity_options' => ProhibitedItem::getSeverityOptions(),
            ]
        ]);
    }

    /**
     * Store a newly created prohibited item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'reason' => 'nullable|string',
            'severity' => ['nullable', 'string', Rule::in(['high', 'medium', 'low'])],
            'icon' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
            'order' => 'nullable|integer'
        ]);

        // Set defaults
        if (!isset($validated['severity'])) {
            $validated['severity'] = 'high';
        }

        if (!isset($validated['order'])) {
            $validated['order'] = ProhibitedItem::count() + 1;
        }

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        $item = ProhibitedItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Prohibited item created successfully',
            'data' => $item
        ], 201);
    }

    /**
     * Display the specified prohibited item.
     */
    public function show(ProhibitedItem $prohibitedItem)
    {
        return response()->json([
            'success' => true,
            'data' => $prohibitedItem
        ]);
    }

    /**
     * Update the specified prohibited item.
     */
    public function update(Request $request, ProhibitedItem $prohibitedItem)
    {
        $validated = $request->validate([
            'item_name' => 'sometimes|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'reason' => 'nullable|string',
            'severity' => ['nullable', 'string', Rule::in(['high', 'medium', 'low'])],
            'icon' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
            'order' => 'nullable|integer'
        ]);

        $prohibitedItem->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Prohibited item updated successfully',
            'data' => $prohibitedItem
        ]);
    }

    /**
     * Remove the specified prohibited item.
     */
    public function destroy(ProhibitedItem $prohibitedItem)
    {
        $prohibitedItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Prohibited item deleted successfully'
        ]);
    }

    /**
     * Reorder prohibited items.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:prohibited_items,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($request->items as $item) {
            ProhibitedItem::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Prohibited items reordered successfully'
        ]);
    }

    /**
     * Update bulk status for prohibited items.
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:prohibited_items,id',
            'is_active' => 'required|boolean',
        ]);

        ProhibitedItem::whereIn('id', $request->ids)->update(['is_active' => $request->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Prohibited items status updated successfully'
        ]);
    }

    /**
     * Bulk delete prohibited items.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:prohibited_items,id',
        ]);

        ProhibitedItem::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Prohibited items deleted successfully'
        ]);
    }

    /**
     * Get all categories.
     */
    public function getCategories()
    {
        $categories = ProhibitedItem::getCategories();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get severity options.
     */
    public function getSeverityOptions()
    {
        return response()->json([
            'success' => true,
            'data' => ProhibitedItem::getSeverityOptions()
        ]);
    }

    /**
     * Export prohibited items to CSV.
     */
    public function export(Request $request)
    {
        $query = ProhibitedItem::query();

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->severity) {
            $query->where('severity', $request->severity);
        }

        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $items = $query->orderBy('order')->get();

        $csv = "ID,Item Name,Category,Description,Reason,Severity,Status,Order,Created At\n";
        foreach ($items as $item) {
            $status = $item->is_active ? 'Active' : 'Inactive';
            $csv .= "{$item->id},{$item->item_name},{$item->category},{$item->description},{$item->reason},{$item->severity},{$status},{$item->order},{$item->created_at}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="prohibited-items.csv"');
    }
}
