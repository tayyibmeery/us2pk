<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Invoice;
use App\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Fpdi;

class InvoiceController extends Controller
{
    /* ============================================================
     |  LIST / SHOW
     * ============================================================ */

    public function index(Request $request)
    {
        $query = Invoice::with([
            'user',
            'shipments',
            'shipment.user',
            'shipment.consolidation',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('shipments', function ($sq) use ($search) {
                        $sq->where('shipment_code', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortBy    = $request->sort_by ?? 'date';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Summary BEFORE paginate (use clone to avoid paginator interference)
        $summaryQuery = clone $query;
        $summary = [
            'total_amount'  => (clone $summaryQuery)->sum('amount_due'),
            'total_cod'     => (clone $summaryQuery)->sum('cod'),
            'total_tax'     => (clone $summaryQuery)->sum('output_tax'),
            'total_paid'    => (clone $summaryQuery)->where('status', 'paid')->count(),
            'total_pending' => (clone $summaryQuery)->where('status', 'pending')->count(),
        ];

        $invoices = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'data'       => $invoices->items(),
            'pagination' => [
                'current_page' => $invoices->currentPage(),
                'last_page'    => $invoices->lastPage(),
                'per_page'     => $invoices->perPage(),
                'total'        => $invoices->total(),
                'from'         => $invoices->firstItem(),
                'to'           => $invoices->lastItem(),
            ],
            'summary' => $summary,
        ]);
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load([
            'user.city',
            'shipments.payments',
            'shipment.user.city',
            'shipment.consolidation',
        ]));
    }

    /* ============================================================
     |  CREATE (multi-shipment by user)
     * ============================================================ */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_no'     => 'nullable|string|unique:invoices,invoice_no',
            'user_id'        => 'required|exists:users,id',
            'shipment_ids'   => 'required|array|min:1',
            'shipment_ids.*' => 'integer|exists:shipments,id',
            'date'           => 'required|date',
            'cod_date'       => 'nullable|date|after_or_equal:date',
            'output_tax'     => 'nullable|numeric|min:0',
            'status'         => 'nullable|in:pending,paid,overdue,cancelled',
            'paid_date'      => 'nullable|date',
            'payment_method' => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ]);

        // 1. All shipments must belong to the user
        $shipments = Shipment::whereIn('id', $validated['shipment_ids'])
            ->where('user_id', $validated['user_id'])
            ->get();

        if ($shipments->count() !== count($validated['shipment_ids'])) {
            return response()->json([
                'message' => 'One or more shipments do not belong to the selected user.',
            ], 422);
        }

        // 2. Reject already-invoiced shipments
        $alreadyInvoiced = DB::table('invoice_shipments')
            ->whereIn('shipment_id', $validated['shipment_ids'])
            ->pluck('shipment_id')
            ->toArray();

        if (!empty($alreadyInvoiced)) {
            return response()->json([
                'message'      => 'Some shipments are already invoiced.',
                'shipment_ids' => $alreadyInvoiced,
            ], 422);
        }

        $totalAmount = round(
            $shipments->sum(fn($s) => (float) ($s->receivable_cod ?? 0)),
            2
        );

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'invoice_no'     => $validated['invoice_no'] ?? $this->generateInvoiceNumber(),
                'user_id'        => $validated['user_id'],
                'shipment_id'    => $shipments->first()->id, // primary (backward compat)
                'date'           => $validated['date'],
                'amount_due'     => $totalAmount,
                'cod'            => $totalAmount,
                'cod_date'       => $validated['cod_date'] ?? null,
                'output_tax'     => $validated['output_tax'] ?? 0,
                'status'         => $validated['status'] ?? 'pending',
                'paid_date'      => $validated['paid_date'] ?? null,
                'payment_method' => $validated['payment_method'] ?? null,
                'notes'          => $validated['notes'] ?? null,
            ]);

            foreach ($shipments as $shipment) {
                $invoice->shipments()->attach($shipment->id, [
                    'amount' => (float) ($shipment->receivable_cod ?? 0),
                    'cod'    => (float) ($shipment->receivable_cod ?? 0),
                ]);
            }

            DB::commit();

            Log::info('Invoice created', [
                'invoice_no' => $invoice->invoice_no,
                'user_id'    => $validated['user_id'],
                'auth_id'    => auth()->id(),
                'shipments'  => $shipments->pluck('id')->all(),
            ]);

            return response()->json(
                $invoice->load(['user', 'shipments']),
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice creation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    /* ============================================================
     |  CREATE from single shipment (1-click)
     * ============================================================ */

    public function storeFromShipment(Request $request, Shipment $shipment)
    {
        if ($shipment->invoices()->exists()) {
            return response()->json([
                'message' => 'This shipment is already invoiced.',
                'invoice' => $shipment->invoices()->first(),
            ], 422);
        }

        if (!$shipment->receivable_cod || $shipment->receivable_cod <= 0) {
            return response()->json([
                'message' => 'Shipment has no COD amount to invoice.',
            ], 422);
        }

        $validated = $request->validate([
            'date'       => 'nullable|date',
            'output_tax' => 'nullable|numeric|min:0',
            'notes'      => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'invoice_no'  => $this->generateInvoiceNumber(),
                'user_id'     => $shipment->user_id,
                'shipment_id' => $shipment->id,
                'date'        => $validated['date'] ?? now()->toDateString(),
                'amount_due'  => $shipment->receivable_cod,
                'cod'         => $shipment->receivable_cod,
                'output_tax'  => $validated['output_tax'] ?? 0,
                'status'      => 'pending',
                'notes'       => $validated['notes'] ?? null,
            ]);

            $invoice->shipments()->attach($shipment->id, [
                'amount' => (float) $shipment->receivable_cod,
                'cod'    => (float) $shipment->receivable_cod,
            ]);

            DB::commit();
            return response()->json($invoice->load(['user', 'shipments']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    /* ============================================================
     |  HELPERS — for the create-invoice modal
     * ============================================================ */

    /**
     * List users that have at least one COD shipment, annotated with
     * how many of their COD shipments are still unbilled.
     */
    public function users(Request $request)
    {
        $query = \App\Models\User::query()
            ->whereHas('shipments', fn($q) => $q->where('receivable_cod', '>', 0))
            ->withCount([
                'shipments as cod_shipments_count' => fn($q) => $q->where('receivable_cod', '>', 0),
            ]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'LIKE', "%{$s}%")
                    ->orWhere('email', 'LIKE', "%{$s}%");
            });
        }

        $users = $query->limit(100)->get();

        $invoicedShipmentIds = DB::table('invoice_shipments')
            ->pluck('shipment_id')
            ->all();

        $users->each(function ($user) use ($invoicedShipmentIds) {
            $unbilled = $user->shipments()
                ->where('receivable_cod', '>', 0)
                ->whereNotIn('id', $invoicedShipmentIds);

            $user->unbilled_cod_count = (clone $unbilled)->count();
            $user->total_pending_cod  = (float) (clone $unbilled)->sum('receivable_cod');
        });

        return response()->json(['data' => $users]);
    }

    /**
     * All COD shipments for a user, annotated with invoiced status.
     */
    public function userCodShipments(\App\Models\User $user)
    {
        $invoiced = DB::table('invoice_shipments')
            ->join('invoices', 'invoices.id', '=', 'invoice_shipments.invoice_id')
            ->whereIn('invoice_shipments.shipment_id', function ($q) use ($user) {
                $q->select('id')->from('shipments')->where('user_id', $user->id);
            })
            ->pluck('invoices.invoice_no', 'invoice_shipments.shipment_id')
            ->all();

        $shipments = $user->shipments()
            ->where('receivable_cod', '>', 0)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($s) => [
                'id'               => $s->id,
                'shipment_code'    => $s->shipment_code,
                'description'      => $s->description,
                'weight'           => $s->weight,
                'receivable_cod'   => (float) $s->receivable_cod,
                'created_at'       => $s->created_at,
                'already_invoiced' => isset($invoiced[$s->id]),
                'invoice_no'       => $invoiced[$s->id] ?? null,
            ]);

        return response()->json(['data' => $shipments]);
    }

    /* ============================================================
     |  UPDATE / DELETE / CANCEL
     * ============================================================ */

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_no'     => ['sometimes', 'string', Rule::unique('invoices')->ignore($invoice->id)],
            'date'           => 'sometimes|date',
            'amount_due'     => 'sometimes|numeric|min:0',
            'cod'            => 'nullable|numeric|min:0',
            'cod_date'       => 'nullable|date|after_or_equal:date',
            'output_tax'     => 'nullable|numeric|min:0',
            'status'         => 'nullable|in:pending,paid,overdue,cancelled',
            'paid_date'      => 'nullable|date',
            'payment_method' => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $invoice->update($validated);

            if (($validated['status'] ?? null) === 'paid' && !$invoice->paid_date) {
                $invoice->paid_date = now();
                $invoice->save();
            }

            DB::commit();
            Log::info('Invoice updated', [
                'invoice_no' => $invoice->invoice_no,
                'user_id'    => auth()->id(),
            ]);

            return response()->json($invoice->load([
                'user',
                'shipments',
                'shipment.user',
                'shipment.consolidation',
            ]));
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
            $invoice->shipments()->detach();
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
     * ✅ Cancel an invoice: frees its shipments so they can be re-invoiced.
     */
    public function cancel(Request $request, Invoice $invoice)
    {
        // Only pending / overdue invoices can be cancelled
        if (!in_array($invoice->status, ['pending', 'overdue'], true)) {
            return response()->json([
                'message' => $invoice->status === 'paid'
                    ? 'Paid invoices cannot be cancelled. Issue a credit note instead.'
                    : 'This invoice is already cancelled.',
            ], 422);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $invoice->shipments()->detach(); // free the shipments

            $invoice->update([
                'status' => 'cancelled',
                'notes'  => trim(
                    ($invoice->notes ?? '') .
                        "\n[CANCELLED] " . ($validated['reason'] ?? '') .
                        ' — ' . now()->format('d M Y H:i') .
                        ' by ' . (auth()->user()->name ?? 'admin')
                ),
            ]);

            DB::commit();

            Log::info('Invoice cancelled', [
                'invoice_no' => $invoice->invoice_no,
                'reason'     => $validated['reason'] ?? null,
                'auth_id'    => auth()->id(),
            ]);

            return response()->json(
                $invoice->fresh()->load(['user', 'shipments'])
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice cancel failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    /* ============================================================
     |  MISC
     * ============================================================ */

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year   = date('Y');
        $month  = date('m');

        $lastInvoice = Invoice::where('invoice_no', 'LIKE', "{$prefix}-{$year}{$month}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_no, -4);
            $newNumber  = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }

    public function stats(Request $request)
    {
        $query = Invoice::query();

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $totalInvoices = (clone $query)->count();
        $totalAmount   = (clone $query)->sum('amount_due');
        $totalCOD      = (clone $query)->sum('cod');
        $totalTax      = (clone $query)->sum('output_tax');
        $totalPaid     = (clone $query)->where('status', 'paid')->count();
        $totalPending  = (clone $query)->where('status', 'pending')->count();

        return response()->json([
            'total_invoices' => $totalInvoices,
            'total_amount'   => $totalAmount,
            'total_cod'      => $totalCOD,
            'total_tax'      => $totalTax,
            'total_paid'     => $totalPaid,
            'total_pending'  => $totalPending,
            'average_amount' => $totalInvoices > 0 ? $totalAmount / $totalInvoices : 0,
        ]);
    }

    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_method' => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ]);

        $invoice->status         = 'paid';
        $invoice->paid_date      = now();
        $invoice->payment_method = $validated['payment_method'] ?? $invoice->payment_method;
        $invoice->notes          = $validated['notes'] ?? $invoice->notes;
        $invoice->save();

        Log::info('Invoice marked as paid', [
            'invoice_no' => $invoice->invoice_no,
            'user_id'    => auth()->id(),
        ]);

        return response()->json($invoice->load([
            'user',
            'shipments',
            'shipment.user',
        ]));
    }

    /* ============================================================
     |  PDF + PRINT
     * ============================================================ */

    public function download(Invoice $invoice)
    {
        $tmpFile = null;

        try {
            $invoice->load([
                'user.city',
                'shipments.payments',
                'shipment.user.city',
                'shipment.consolidation',
                'shipment.payments',
            ]);

            $letterheadPath = public_path('assets/letterhead.pdf');
            if (!is_file($letterheadPath)) {
                throw new \RuntimeException('Letterhead file not found: public/assets/letterhead.pdf');
            }

            $content = Pdf::loadView('pdf.invoice_content', ['invoice' => $invoice])
                ->setPaper('a4')
                ->output();

            $tmpFile = tempnam(sys_get_temp_dir(), 'inv');
            file_put_contents($tmpFile, $content);

            $fpdi = new Fpdi();
            $fpdi->setSourceFile($letterheadPath);
            $letterheadTpl = $fpdi->importPage(1);

            $pageCount = $fpdi->setSourceFile($tmpFile);
            for ($i = 1; $i <= $pageCount; $i++) {
                $fpdi->AddPage('P', 'A4');
                $fpdi->useTemplate($letterheadTpl, 0, 0, 210, 297);
                $fpdi->useTemplate($fpdi->importPage($i), 0, 0, 210, 297);
            }

            $output = $fpdi->Output('S');

            return response($output, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="invoice-' . $invoice->invoice_no . '.pdf"',
            ]);
        } catch (\Exception $e) {
            Log::error('Invoice PDF download failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to download invoice: ' . $e->getMessage()], 500);
        } finally {
            if ($tmpFile && is_file($tmpFile)) {
                @unlink($tmpFile);
            }
        }
    }

    public function print(Invoice $invoice)
    {
        try {
            $invoice->load([
                'user.city',
                'shipments',
                'shipment.user',
                'shipment.consolidation',
            ]);

            return response()->json([
                'invoice' => $invoice,
                'company' => [
                    'name'    => 'US2PK',
                    'address' => 'Office #407, Garden Tower, Babar Block, Garden Town, Lahore, Pakistan',
                    'phone'   => '+92 301 5233018',
                    'email'   => 'admin@us2pk.com',
                ],
                'currency' => 'PKR',
            ]);
        } catch (\Exception $e) {
            Log::error('Invoice print data failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to get invoice data: ' . $e->getMessage()], 500);
        }
    }
}
