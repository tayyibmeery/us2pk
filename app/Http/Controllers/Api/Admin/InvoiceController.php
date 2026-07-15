<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Invoice;
use App\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['shipment.user', 'shipment.consolidation']);

        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'LIKE', "%{$search}%")
                    ->orWhereHas('shipment', function ($sq) use ($search) {
                        $sq->where('shipment_code', 'LIKE', "%{$search}%")
                            ->orWhere('id', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Date filters
        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->sort_by ?? 'date';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $invoices = $query->paginate($request->per_page ?? 20);

        // Calculate summary
        $summary = [
            'total_amount' => $query->sum('amount_due'),
            'total_cod' => $query->sum('cod'),
            'total_tax' => $query->sum('output_tax'),
            'total_paid' => $query->where('status', 'paid')->count(),
            'total_pending' => $query->where('status', 'pending')->count(),
        ];

        return response()->json([
            'data' => $invoices->items(),
            'pagination' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'per_page' => $invoices->perPage(),
                'total' => $invoices->total(),
                'from' => $invoices->firstItem(),
                'to' => $invoices->lastItem(),
            ],
            'summary' => $summary,
        ]);
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load(['shipment.user', 'shipment.consolidation']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_no' => 'nullable|string|unique:invoices,invoice_no',
            'shipment_id' => 'required|exists:shipments,id',
            'date' => 'required|date',
            'amount_due' => 'required|numeric|min:0',
            'cod' => 'nullable|numeric|min:0',
            'cod_date' => 'nullable|date|after_or_equal:date',
            'output_tax' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,paid,overdue,cancelled',
            'paid_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Auto-generate invoice number if not provided
        if (empty($validated['invoice_no'])) {
            $validated['invoice_no'] = $this->generateInvoiceNumber();
        }

        // Set default status
        if (empty($validated['status'])) {
            $validated['status'] = 'pending';
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::create($validated);

            // Update shipment with invoice reference
            $shipment = Shipment::find($validated['shipment_id']);
            if ($shipment) {
                // You can add invoice_id to shipments table if needed
            }

            DB::commit();
            Log::info('Invoice created', ['invoice_no' => $invoice->invoice_no, 'user_id' => auth()->id()]);

            return response()->json($invoice->load('shipment'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice creation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create invoice: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_no' => ['sometimes', 'string', Rule::unique('invoices')->ignore($invoice->id)],
            'shipment_id' => 'sometimes|exists:shipments,id',
            'date' => 'sometimes|date',
            'amount_due' => 'sometimes|numeric|min:0',
            'cod' => 'nullable|numeric|min:0',
            'cod_date' => 'nullable|date|after_or_equal:date',
            'output_tax' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,paid,overdue,cancelled',
            'paid_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $invoice->update($validated);

            // If status changed to paid, set paid_date
            if ($validated['status'] === 'paid' && !$invoice->paid_date) {
                $invoice->paid_date = now();
                $invoice->save();
            }

            DB::commit();
            Log::info('Invoice updated', ['invoice_no' => $invoice->invoice_no, 'user_id' => auth()->id()]);

            return response()->json($invoice->load('shipment'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update invoice: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Invoice $invoice)
    {
        DB::beginTransaction();
        try {
            $invoiceNo = $invoice->invoice_no;
            $invoice->delete();

            DB::commit();
            Log::info('Invoice deleted', ['invoice_no' => $invoiceNo, 'user_id' => auth()->id()]);

            return response()->json(['message' => 'Invoice deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete invoice: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate a unique invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');

        $lastInvoice = Invoice::where('invoice_no', 'LIKE', "{$prefix}-{$year}{$month}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_no, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }

    /**
     * Get invoice statistics
     */
    public function stats(Request $request)
    {
        $query = Invoice::query();

        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $totalInvoices = $query->count();
        $totalAmount = $query->sum('amount_due');
        $totalCOD = $query->sum('cod');
        $totalTax = $query->sum('output_tax');
        $totalPaid = $query->where('status', 'paid')->count();
        $totalPending = $query->where('status', 'pending')->count();

        return response()->json([
            'total_invoices' => $totalInvoices,
            'total_amount' => $totalAmount,
            'total_cod' => $totalCOD,
            'total_tax' => $totalTax,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
            'average_amount' => $totalInvoices > 0 ? $totalAmount / $totalInvoices : 0,
        ]);
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_method' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $invoice->status = 'paid';
        $invoice->paid_date = now();
        $invoice->payment_method = $validated['payment_method'] ?? $invoice->payment_method;
        $invoice->notes = $validated['notes'] ?? $invoice->notes;
        $invoice->save();

        Log::info('Invoice marked as paid', ['invoice_no' => $invoice->invoice_no, 'user_id' => auth()->id()]);

        return response()->json($invoice->load('shipment'));
    }



    /**
     * Download invoice as PDF
     */
    public function download(Invoice $invoice)
    {
        try {
            $invoice->load(['shipment.user', 'shipment.consolidation']);

            $data = [
                'invoice' => $invoice,
                'company' => [
                    'name' => 'US2PK Logistics',
                    'address' => '123 Business Street, Lahore, Pakistan',
                    'phone' => '+92-300-1234567',
                    'email' => 'info@us2pk.com',
                ],
                'currency' => 'PKR',
            ];

            // Use DomPDF to generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', $data);

            return $pdf->download("invoice-{$invoice->invoice_no}.pdf");
        } catch (\Exception $e) {
            Log::error('Invoice PDF download failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to download invoice: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get invoice data for printing
     */
    public function print(Invoice $invoice)
    {
        try {
            $invoice->load(['shipment.user', 'shipment.consolidation']);

            return response()->json([
                'invoice' => $invoice,
                'company' => [
                    'name' => 'US2PK Logistics',
                    'address' => '123 Business Street, Lahore, Pakistan',
                    'phone' => '+92-300-1234567',
                    'email' => 'info@us2pk.com',
                ],
                'currency' => 'PKR',
            ]);
        } catch (\Exception $e) {
            Log::error('Invoice print data failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to get invoice data: ' . $e->getMessage()], 500);
        }
    }
}
