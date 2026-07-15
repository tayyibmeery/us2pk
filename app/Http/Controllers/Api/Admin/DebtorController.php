<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Debtor;
use App\Models\ShipmentPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DebtorController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Debtor::with(['user', 'shipment.payments', 'shipment.paymentMethod']);

            // Search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_no', 'LIKE', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('pcode', 'LIKE', "%{$search}%");
                        });
                });
            }

            // Status filter
            if ($request->has('status')) {
                if ($request->status === 'unpaid') {
                    $query->where('balance', '>', 0);
                } elseif ($request->status === 'paid') {
                    $query->where('balance', '<=', 0);
                }
            }

            // Date filters
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $debtors = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 20);

            // Transform data for frontend
            $debtors->getCollection()->transform(function ($debtor) {
                $shipment = $debtor->shipment;
                $payments = $shipment?->payments ?? collect();
                $totalPaid = $payments->sum('amount');

                // Group payments by payment method
                $paymentMethods = [
                    'abl' => 0,
                    'scb' => 0,
                    'bafl' => 0,
                    'faisal' => 0,
                    'cash' => 0,
                    'jazzcash' => 0,
                    'easypaisa' => 0,
                ];

                foreach ($payments as $payment) {
                    $method = strtolower($payment->payment_method ?? '');
                    if (isset($paymentMethods[$method])) {
                        $paymentMethods[$method] += $payment->amount;
                    } elseif (strpos($method, 'abl') !== false) {
                        $paymentMethods['abl'] += $payment->amount;
                    } elseif (strpos($method, 'scb') !== false) {
                        $paymentMethods['scb'] += $payment->amount;
                    } elseif (strpos($method, 'bafl') !== false) {
                        $paymentMethods['bafl'] += $payment->amount;
                    } elseif (strpos($method, 'faisal') !== false) {
                        $paymentMethods['faisal'] += $payment->amount;
                    } elseif (strpos($method, 'jazz') !== false) {
                        $paymentMethods['jazzcash'] += $payment->amount;
                    } elseif (strpos($method, 'easy') !== false) {
                        $paymentMethods['easypaisa'] += $payment->amount;
                    } else {
                        $paymentMethods['cash'] += $payment->amount;
                    }
                }

                return [
                    'id' => $debtor->id,
                    'invoice_no' => $debtor->invoice_no,
                    'status' => $shipment?->shipmentStatus?->name ?? 'N/A',
                    'customer_name' => $debtor->user?->name ?? 'N/A',
                    'pcode' => $debtor->user?->pcode ?? 'N/A',
                    'amount_due' => (float) ($debtor->amount_due ?? 0),
                    'abl' => $paymentMethods['abl'] > 0 ? number_format($paymentMethods['abl'], 0) : '-',
                    'scb' => $paymentMethods['scb'] > 0 ? number_format($paymentMethods['scb'], 0) : '-',
                    'bafl' => $paymentMethods['bafl'] > 0 ? number_format($paymentMethods['bafl'], 0) : '-',
                    'faisal' => $paymentMethods['faisal'] > 0 ? number_format($paymentMethods['faisal'], 0) : '-',
                    'cash' => $paymentMethods['cash'] > 0 ? number_format($paymentMethods['cash'], 0) : '-',
                    'jazzcash' => $paymentMethods['jazzcash'] > 0 ? number_format($paymentMethods['jazzcash'], 0) : '-',
                    'easypaisa' => $paymentMethods['easypaisa'] > 0 ? number_format($paymentMethods['easypaisa'], 0) : '-',
                    'cod' => (float) ($debtor->cod ?? 0),
                    'cod_date' => $shipment?->cod_date ?? null,
                    'due_cod' => (float) ($debtor->receivable_cod ?? 0),
                    'balance' => (float) ($debtor->balance ?? 0),
                    'shipment_id' => $shipment?->id,
                    'user_id' => $debtor->user_id,
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
            $debtor->load(['user', 'shipment.payments', 'shipment.paymentMethod']);
            $payments = $debtor->shipment?->payments ?? collect();

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
                'payments' => $payments,
                'payment_status' => $debtor->payment_status,
            ]);
        } catch (\Exception $e) {
            Log::error('DebtorController show error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to load debtor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function recordPayment(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'payment_date' => 'required|date',
                'payment_method' => 'required|string|max:100',
                'reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
            ]);

            $debtor = Debtor::with('shipment')->findOrFail($id);
            $shipment = $debtor->shipment;

            if (!$shipment) {
                return response()->json(['message' => 'Shipment not found for this debtor'], 404);
            }

            DB::beginTransaction();
            try {
                // Create payment record
                $payment = ShipmentPayment::create([
                    'shipment_id' => $shipment->id,
                    'amount' => $validated['amount'],
                    'payment_date' => $validated['payment_date'],
                    'payment_method' => $validated['payment_method'],
                    'reference_number' => $validated['reference_number'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Update debtor balance
                $debtor->applyPayment($validated['amount']);

                // Update shipment received amount
                $shipment->recalcReceivedAmount();

                DB::commit();

                Log::info('Payment recorded for debtor', [
                    'debtor_id' => $debtor->id,
                    'invoice_no' => $debtor->invoice_no,
                    'amount' => $validated['amount'],
                    'method' => $validated['payment_method']
                ]);

                return response()->json([
                    'message' => 'Payment recorded successfully',
                    'payment' => $payment,
                    'debtor' => $debtor,
                    'new_balance' => $debtor->balance,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Payment record error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to record payment: ' . $e->getMessage(),
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
                'total_balance' => (float) $debtors->sum('balance'),
                'total_cod' => (float) $debtors->sum('cod'),
                'total_paid' => (float) $debtors->sum('amount_received'),
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
            $query = Debtor::with(['user', 'shipment']);

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_no', 'LIKE', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('pcode', 'LIKE', "%{$search}%");
                        });
                });
            }

            $debtors = $query->get();

            // Return JSON for now (can be extended for CSV/Excel)
            return response()->json([
                'message' => 'Export data retrieved successfully',
                'count' => $debtors->count(),
                'data' => $debtors->map(function ($debtor) {
                    return [
                        'invoice_no' => $debtor->invoice_no,
                        'customer' => $debtor->user?->name ?? 'N/A',
                        'pcode' => $debtor->user?->pcode ?? 'N/A',
                        'amount_due' => $debtor->amount_due,
                        'balance' => $debtor->balance,
                        'receivable_cod' => $debtor->receivable_cod,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Debtor export error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to export',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
