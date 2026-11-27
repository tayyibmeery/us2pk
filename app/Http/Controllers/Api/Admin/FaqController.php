<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of FAQs.
     */
    public function index(Request $request)
    {
        $query = Faq::query();

        if ($request->search) {
            $query->where('question', 'LIKE', "%{$request->search}%")
                ->orWhere('answer', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        $sortBy = $request->sort_by ?? 'order';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->per_page ?? 20;
        $faqs = $query->paginate($perPage);

        return response()->json($faqs);
    }

    /**
     * Store a newly created FAQ.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'section_title' => 'nullable|string|max:255',
            'section_content' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'sometimes|boolean'
        ]);

        if (!isset($validated['order'])) {
            $validated['order'] = Faq::count() + 1;
        }

        if (!isset($validated['status'])) {
            $validated['status'] = true;
        }

        $faq = Faq::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ created successfully',
            'data' => $faq
        ], 201);
    }

    /**
     * Display the specified FAQ.
     */
    public function show(Faq $faq)
    {
        return response()->json([
            'success' => true,
            'data' => $faq
        ]);
    }

    /**
     * Update the specified FAQ.
     */
    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'sometimes|string|max:255',
            'answer' => 'sometimes|string',
            'section_title' => 'nullable|string|max:255',
            'section_content' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'sometimes|boolean'
        ]);

        $faq->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ updated successfully',
            'data' => $faq
        ]);
    }

    /**
     * Remove the specified FAQ.
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ deleted successfully'
        ]);
    }

    /**
     * Reorder FAQs.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:faqs,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($request->items as $item) {
            Faq::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'FAQs reordered successfully'
        ]);
    }

    /**
     * Update bulk status for FAQs.
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:faqs,id',
            'status' => 'required|boolean',
        ]);

        Faq::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'FAQs status updated successfully'
        ]);
    }

    /**
     * Bulk delete FAQs.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:faqs,id',
        ]);

        Faq::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQs deleted successfully'
        ]);
    }
}
