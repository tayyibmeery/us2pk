<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuoteController extends Controller
{
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

            return response()->json([
                'success' => true,
                'message' => 'Quote request received successfully',
                'data'    => $quote
            ], 201);
        } catch (\Exception $e) {
            Log::error('Quote submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit quote request. Please try again.'
            ], 500);
        }
    }
}
