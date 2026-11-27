<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class QuoteRequestController extends Controller
{
    /**
     * Display a listing of quote requests.
     */
    public function index(Request $request)
    {
        $query = QuoteRequest::query();

        if ($request->search) {
            $query->where('name', 'LIKE', "%{$request->search}%")
                ->orWhere('email', 'LIKE', "%{$request->search}%")
                ->orWhere('mobile', 'LIKE', "%{$request->search}%")
                ->orWhere('service', 'LIKE', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->per_page ?? 20;
        $quotes = $query->paginate($perPage);

        return response()->json($quotes);
    }

    /**
     * Store a newly created quote request (Public endpoint).
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'    => 'required|string|max:255',
                'email'   => 'required|email|max:255',
                'mobile'  => 'nullable|string|max:50',
                'service' => 'nullable|string|max:100',
                'note'    => 'nullable|string|max:2000',
            ]);

            $quote = QuoteRequest::create([
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'mobile'  => $validated['mobile'] ?? null,
                'service' => $validated['service'] ?? null,
                'note'    => $validated['note'] ?? null,
                'status'  => 'pending'
            ]);

            // Optional: Send email notification
            // Mail::to('info@us2pk.com')->send(new NewQuoteRequest($validated));

            return response()->json([
                'success' => true,
                'message' => 'Quote request received successfully',
                'data'    => $quote
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Quote submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit quote request. Please try again.'
            ], 500);
        }
    }

    /**
     * Display the specified quote request.
     */
    public function show(QuoteRequest $quoteRequest)
    {
        return response()->json([
            'success' => true,
            'data' => $quoteRequest
        ]);
    }

    /**
     * Update the status of a quote request.
     */
    public function updateStatus(Request $request, QuoteRequest $quoteRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,contacted,completed'
        ]);

        $quoteRequest->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Quote request status updated successfully',
            'data' => $quoteRequest
        ]);
    }

    /**
     * Remove the specified quote request.
     */
    public function destroy(QuoteRequest $quoteRequest)
    {
        $quoteRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quote request deleted successfully'
        ]);
    }

    /**
     * Bulk delete quote requests.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:quote_requests,id',
        ]);

        QuoteRequest::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quote requests deleted successfully'
        ]);
    }

    /**
     * Get statistics for quote requests.
     */
    public function stats()
    {
        $stats = [
            'total' => QuoteRequest::count(),
            'pending' => QuoteRequest::where('status', 'pending')->count(),
            'contacted' => QuoteRequest::where('status', 'contacted')->count(),
            'completed' => QuoteRequest::where('status', 'completed')->count(),
            'today' => QuoteRequest::whereDate('created_at', today())->count(),
            'this_week' => QuoteRequest::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => QuoteRequest::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Export quote requests to CSV.
     */
    public function export(Request $request)
    {
        $query = QuoteRequest::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $quotes = $query->orderBy('created_at', 'desc')->get();

        $csv = "ID,Name,Email,Mobile,Service,Status,Created At\n";
        foreach ($quotes as $quote) {
            $csv .= "{$quote->id},{$quote->name},{$quote->email},{$quote->mobile},{$quote->service},{$quote->status},{$quote->created_at}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="quote-requests.csv"');
    }
}
