<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Debtor;
use App\Models\Invoice;
use App\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;





class DebtorController extends Controller
{

    public function index(Request $request)
    {
        try {
            $query = Debtor::with(['user', 'shipment']);

            // Apply filters
            if ($request->status === 'unpaid') {
                $query->where('balance', '>', 0);
            } elseif ($request->status === 'paid') {
                $query->where('balance', '<=', 0);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            $debtors = $query->latest()->paginate(20);

            // Map and calculate totals safely
            $debtors->getCollection()->transform(function ($debtor) {
                $payments = $debtor->shipment?->payments()?->sum('amount') ?? 0;

                return [
                    'id' => $debtor->id,
                    'invoice_no' => $debtor->invoice_no,
                    'user' => $debtor->user,
                    'shipment' => $debtor->shipment,
                    'total_payable' => $debtor->total_payable ?? 0,
                    'receivable_cod' => $debtor->receivable_cod ?? 0,
                    'balance' => $debtor->balance ?? 0,
                    'amount_received' => $debtor->amount_received ?? 0,
                    'courier_deduction' => $debtor->courier_deduction ?? 0,
                    'net_receivable' => $debtor->net_receivable ?? 0,
                    'cod' => $debtor->cod ?? 0,
                    'last_payment_date' => $debtor->last_payment_date,
                    'created_at' => $debtor->created_at,
                    'payments_total' => $payments,
                ];
            });

            return response()->json($debtors);
        } catch (\Exception $e) {
            Log::error('DebtorController error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to load debtors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Debtor $debtor)
    {
        try {
            $payments = $debtor->shipment?->payments()?->sum('amount') ?? 0;

            return response()->json([
                'id' => $debtor->id,
                'invoice_no' => $debtor->invoice_no,
                'user' => $debtor->user,
                'shipment' => $debtor->shipment,
                'total_payable' => $debtor->total_payable ?? 0,
                'receivable_cod' => $debtor->receivable_cod ?? 0,
                'balance' => $debtor->balance ?? 0,
                'amount_received' => $debtor->amount_received ?? 0,
                'courier_deduction' => $debtor->courier_deduction ?? 0,
                'net_receivable' => $debtor->net_receivable ?? 0,
                'cod' => $debtor->cod ?? 0,
                'last_payment_date' => $debtor->last_payment_date,
                'created_at' => $debtor->created_at,
                'updated_at' => $debtor->updated_at,
                'payments_total' => $payments,
            ]);
        } catch (\Exception $e) {
            Log::error('DebtorController show error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to load debtor',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'invoice_no' => 'required|unique:debtors',
                'shipment_id' => 'required|exists:shipments,id',
                'user_id' => 'required|exists:users,id',
                'amount_due' => 'required|numeric|min:0',
                'receivable_cod' => 'required|numeric|min:0',
                'balance' => 'required|numeric'
            ]);

            $debtor = Debtor::create($validated);

            return response()->json([
                'message' => 'Debtor created successfully',
                'debtor' => $debtor
            ], 201);
        } catch (\Exception $e) {
            Log::error('Debtor store error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create debtor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $debtor = Debtor::findOrFail($id);

            $validated = $request->validate([
                'amount_due' => 'sometimes|numeric|min:0',
                'receivable_cod' => 'sometimes|numeric|min:0',
                'balance' => 'sometimes|numeric'
            ]);

            $debtor->update($validated);

            return response()->json([
                'message' => 'Debtor updated successfully',
                'debtor' => $debtor
            ]);
        } catch (\Exception $e) {
            Log::error('Debtor update error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update debtor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $debtor = Debtor::findOrFail($id);
            $debtor->delete();

            return response()->json([
                'message' => 'Debtor deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Debtor delete error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete debtor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function recordPayment(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'date' => 'required|date',
                'method' => 'required|string',
                'reference' => 'nullable|string',
                'notes' => 'nullable|string'
            ]);

            $debtor = Debtor::with('shipment')->findOrFail($id);

            // Create payment record if you have payments table
            // If not, you can just update the debtor balance

            // Update debtor balance
            $newBalance = $debtor->balance - $validated['amount'];
            $debtor->update(['balance' => max(0, $newBalance)]);

            // If you have a payments table, create the record
            // Payment::create([...]);

            return response()->json([
                'message' => 'Payment recorded successfully',
                'debtor' => $debtor,
                'payment' => [
                    'amount' => $validated['amount'],
                    'date' => $validated['date'],
                    'method' => $validated['method'],
                    'reference' => $validated['reference'] ?? null,
                    'notes' => $validated['notes'] ?? null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Payment record error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to record payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function stats(Request $request)
    {
        try {
            $query = Debtor::query();

            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $debtors = $query->get();

            return response()->json([
                'total_debtors' => $debtors->count(),
                'total_amount_due' => (float) $debtors->sum('amount_due'),
                'total_receivable_cod' => (float) $debtors->sum('receivable_cod'),
                'total_balance' => (float) $debtors->sum('balance')
            ]);
        } catch (\Exception $e) {
            Log::error('Debtor stats error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to fetch stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $query = Debtor::with(['shipment.user', 'shipment']);

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_no', 'LIKE', "%{$search}%")
                        ->orWhereHas('shipment.user', function ($uq) use ($search) {
                            $uq->where('name', 'LIKE', "%{$search}%");
                        });
                });
            }

            $debtors = $query->get();

            // Return as JSON for now
            return response()->json([
                'message' => 'Export functionality coming soon',
                'count' => $debtors->count(),
                'data' => $debtors
            ]);
        } catch (\Exception $e) {
            Log::error('Debtor export error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to export',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync debtors from invoices
     * This can be run as a scheduled job or manually
     */
    public function syncFromInvoices()
    {
        try {
            $invoices = Invoice::with('shipment.user')->get();
            $created = 0;
            $updated = 0;

            foreach ($invoices as $invoice) {
                $totalPaid = $invoice->shipment->payments->sum('amount') ?? 0;
                $balance = ($invoice->amount_due + $invoice->cod) - $totalPaid;

                $debtor = Debtor::updateOrCreate(
                    ['invoice_no' => $invoice->invoice_no],
                    [
                        'shipment_id' => $invoice->shipment_id,
                        'user_id' => $invoice->shipment->user_id,
                        'amount_due' => $invoice->amount_due,
                        'receivable_cod' => $invoice->cod,
                        'balance' => max(0, $balance)
                    ]
                );

                if ($debtor->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }

            return response()->json([
                'message' => 'Debtors synced successfully',
                'created' => $created,
                'updated' => $updated
            ]);
        } catch (\Exception $e) {
            Log::error('Debtor sync error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to sync debtors',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
