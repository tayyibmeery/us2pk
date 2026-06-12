<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with('shipment.user')
            ->orderBy('date', 'desc')
            ->paginate(20);
        return response()->json($invoices);
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load('shipment.user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_no'   => 'required|unique:invoices',
            'shipment_id'  => 'required|exists:shipments,id',
            'date'         => 'required|date',
            'amount_due'   => 'required|numeric',
            'cod'          => 'nullable|numeric',
            'cod_date'     => 'nullable|date',
            'output_tax'   => 'nullable|numeric',
        ]);
        return response()->json(Invoice::create($validated), 201);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $invoice->update($request->all());
        return response()->json($invoice);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted']);
    }
}
