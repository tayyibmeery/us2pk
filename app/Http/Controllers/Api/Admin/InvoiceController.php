<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Invoice;
use App\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['shipment.user', 'shipment.consolidation']);

        // Optional filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'LIKE', "%{$search}%")
                    ->orWhereHas('shipment', function ($sq) use ($search) {
                        $sq->where('pcode', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $invoices = $query->orderBy('date', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json($invoices);
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load(['shipment.user', 'shipment.consolidation']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_no'   => 'nullable|string|unique:invoices,invoice_no', // Make it nullable
            'shipment_id'  => 'required|exists:shipments,id',
            'date'         => 'required|date',
            'amount_due'   => 'required|numeric|min:0',
            'cod'          => 'nullable|numeric|min:0',
            'cod_date'     => 'nullable|date|after_or_equal:date',
            'output_tax'   => 'nullable|numeric|min:0',
        ]);

        // Auto-generate invoice number if not provided
        if (empty($validated['invoice_no'])) {
            $validated['invoice_no'] = $this->generateInvoiceNumber();
        }

        $invoice = Invoice::create($validated);

        return response()->json($invoice->load('shipment'), 201);
    }

    /**
     * Generate a unique invoice number
     */


    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_no'   => ['sometimes', 'string', Rule::unique('invoices')->ignore($invoice->id)],
            'shipment_id'  => 'sometimes|exists:shipments,id',
            'date'         => 'sometimes|date',
            'amount_due'   => 'sometimes|numeric|min:0',
            'cod'          => 'nullable|numeric|min:0',
            'cod_date'     => 'nullable|date|after_or_equal:date',
            'output_tax'   => 'nullable|numeric|min:0',
        ]);

        $invoice->update($validated);

        return response()->json($invoice->load('shipment'));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted successfully']);
    }

    /**
     * Generate a unique invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');

        // Get the last invoice number for this month
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
        $totalInvoices = Invoice::count();
        $totalAmount = Invoice::sum('amount_due');
        $totalCOD = Invoice::sum('cod');
        $totalTax = Invoice::sum('output_tax');

        return response()->json([
            'total_invoices' => $totalInvoices,
            'total_amount' => $totalAmount,
            'total_cod' => $totalCOD,
            'total_tax' => $totalTax,
            'average_amount' => $totalInvoices > 0 ? $totalAmount / $totalInvoices : 0,
        ]);
    }
}
