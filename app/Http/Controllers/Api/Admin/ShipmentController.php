<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\ShipmentStatus;
use App\Models\Debtor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\ShipmentPaymentHelper;
use App\Services\VoucherService;

class ShipmentController extends Controller
{
    use ShipmentPaymentHelper;

    public function index(Request $request)
    {
        $query = $request->has('trashed') && $request->trashed
            ? Shipment::onlyTrashed()->with(['user', 'consolidation', 'images', 'shipmentStatus', 'paymentMethod', 'localCourier', 'site'])
            : Shipment::with(['user', 'consolidation', 'images', 'shipmentStatus', 'paymentMethod', 'localCourier', 'site']);

        if ($request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('shipment_code', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('comments', 'like', "%{$searchTerm}%")
                    ->orWhere('seller_tracker_id', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($q2) use ($searchTerm) {
                        $q2->where('name', 'like', "%{$searchTerm}%")
                            ->orWhere('email', 'like', "%{$searchTerm}%");
                    });
            });
        }

        if ($request->status) {
            $query->where('shipment_status_id', $request->status);
        }

        $sortBy    = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return response()->json($query->paginate(20));
    }

    public function show(Shipment $shipment)
    {
        $shipment->load([
            'user',
            'shipment_status',
            'local_courier',
            'consolidation',
            'payments',
            'images',
            'payment_method',
            'site',
            'invoices',
        ]);

        return response()->json($shipment);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'                => 'required|exists:users,id',
            'consolidation_id'       => 'nullable|exists:consolidations,id',
            'description'            => 'nullable|string',
            'weight'                 => 'required|numeric',
            'weight_unit'            => 'required|in:ounce,gram,kg,pound',
            'seller_tracker_id'      => 'nullable|string|max:255',
            'site_id'                => 'nullable|exists:sites,id',
            'purchase_date'          => 'nullable|date',
            'comments'               => 'nullable|string',
            'shipment_status_id'     => 'nullable|exists:shipment_statuses,id',
            'expected_delivery_date' => 'nullable|date',
            'item_value_pkr'         => 'required|numeric',
            'company_charges'        => 'required|numeric',
            'received_amount'        => 'nullable|numeric|min:0',
            'bought_by'              => 'required|in:By Company,By Customer',
            'payment_method_id'      => 'nullable|exists:payment_methods,id',
            'local_courier_id'       => 'nullable|exists:local_couriers,id',
            'delivery_charges'       => 'nullable|numeric',
            'images'                 => 'nullable|array',
            'images.*'               => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if (!empty($validated['weight']) && !empty($validated['weight_unit'])) {
            $validated['weight_kgs'] = $this->convertToKg($validated['weight'], $validated['weight_unit']);
        }

        $shipment = Shipment::create($validated);

        if ($shipment->received_amount > 0) {
            $paymentDate = $validated['purchase_date'] ?? now()->toDateString();
            $paymentMethod = null;
            if (!empty($validated['payment_method_id'])) {
                $method = PaymentMethod::find($validated['payment_method_id']);
                $paymentMethod = $method ? $method->name : null;
            }
            $payment = $shipment->payments()->create([
                'amount'           => $shipment->received_amount,
                'payment_date'     => $paymentDate,
                'payment_method'   => $paymentMethod,
                'reference_number' => null,
                'notes'            => 'Initial payment',
            ]);
            (new VoucherService())->generatePaymentVoucher($shipment, $payment);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('shipments', 'public');
                $shipment->images()->create(['image_path' => $path]);
            }
        }

        return response()->json($shipment->load([
            'user',
            'images',
            'shipmentStatus',
            'paymentMethod',
            'localCourier',
            'site',
        ]), 201);
    }

    public function update(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'user_id'                => 'sometimes|exists:users,id',
            'description'            => 'sometimes|string',
            'weight'                 => 'sometimes|numeric',
            'weight_unit'            => 'sometimes|in:ounce,gram,kg,pound',
            'seller_tracker_id'      => 'sometimes|string|max:255',
            'site_id'                => 'sometimes|exists:sites,id',
            'purchase_date'          => 'sometimes|date',
            'comments'               => 'sometimes|string',
            'shipment_status_id'     => 'sometimes|exists:shipment_statuses,id',
            'expected_delivery_date' => 'sometimes|date',
            'item_value_pkr'         => 'sometimes|numeric',
            'company_charges'        => 'sometimes|numeric',
            'received_amount'        => 'sometimes|numeric|min:0',
            'bought_by'              => 'sometimes|in:By Company,By Customer',
            'payment_method_id'      => 'sometimes|exists:payment_methods,id',
            'local_courier_id'       => 'sometimes|exists:local_couriers,id',
            'delivery_charges'       => 'sometimes|numeric',
        ]);

        if (isset($validated['weight']) && isset($validated['weight_unit'])) {
            $validated['weight_kgs'] = $this->convertToKg($validated['weight'], $validated['weight_unit']);
        }

        $oldStatusId = $shipment->shipment_status_id;

        $shipment->update($validated);

        if (isset($validated['shipment_status_id']) && $validated['shipment_status_id'] != $oldStatusId) {
            $this->handleStatusTransition($shipment, $oldStatusId);
        }

        if ($request->has('received_amount') && $shipment->wasChanged('received_amount')) {
            $payment = $shipment->payments()->where('notes', 'Initial payment')->first();
            if ($payment) {
                $payment->amount = $shipment->received_amount;
                $payment->save();
            } else {
                $paymentDate = $shipment->purchase_date ?? now()->toDateString();
                $paymentMethod = $shipment->paymentMethod ? $shipment->paymentMethod->name : null;
                $shipment->payments()->create([
                    'amount'           => $shipment->received_amount,
                    'payment_date'     => $paymentDate,
                    'payment_method'   => $paymentMethod,
                    'reference_number' => null,
                    'notes'            => 'Initial payment',
                ]);
            }
            $this->updateShipmentReceivedAmount($shipment);
        }

        if ($request->has('images_to_delete')) {
            $ids = $request->input('images_to_delete');
            if (is_array($ids)) {
                $shipment->images()->whereIn('id', $ids)->delete();
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('shipments', 'public');
                $shipment->images()->create(['image_path' => $path]);
            }
        }

        return response()->json($shipment->load([
            'user',
            'images',
            'shipmentStatus',
            'paymentMethod',
            'localCourier',
            'site',
        ]));
    }

    public function destroy(Request $request, Shipment $shipment)
    {
        $request->validate(['password' => 'required|string']);

        $adminPassword = env('ADMIN_DELETE_PASSWORD', 'admin123');
        if ($request->password !== $adminPassword) {
            return response()->json(['message' => 'Invalid admin password.'], 403);
        }

        DB::beginTransaction();
        try {
            $voucherService = new VoucherService();

            if ($shipment->consolidation_id) {
                $consolidation = $shipment->consolidation;
                $shipment->consolidation_id = null;
                $shipment->saveQuietly();
                if ($consolidation) $consolidation->recalculateTotals();
            }

            $shipment->payments()->delete();
            $voucherService->softDeleteShipmentVouchers($shipment);
            Debtor::where('shipment_id', $shipment->id)->delete();
            $shipment->delete();

            DB::commit();
            return response()->json(['message' => 'Shipment and all related records soft-deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment soft-delete error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete shipment: ' . $e->getMessage()], 500);
        }
    }

    public function restore(Request $request, $id)
    {
        $request->validate(['password' => 'required|string']);

        $adminPassword = env('ADMIN_DELETE_PASSWORD', 'admin123');
        if ($request->password !== $adminPassword) {
            return response()->json(['message' => 'Invalid admin password.'], 403);
        }

        $shipment = Shipment::onlyTrashed()->findOrFail($id);

        DB::beginTransaction();
        try {
            $shipment->restore();
            $shipment->payments()->onlyTrashed()->restore();
            (new VoucherService())->restoreShipmentVouchers($shipment);
            Debtor::onlyTrashed()->where('shipment_id', $shipment->id)->restore();

            DB::commit();
            return response()->json(['message' => 'Shipment and related records restored successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment restore error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to restore shipment: ' . $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $request->validate([
            'shipment_status_id' => 'required|exists:shipment_statuses,id',
        ]);

        $newStatus = ShipmentStatus::findOrFail($request->shipment_status_id);

        if (strcasecmp($newStatus->name, 'Delivered') === 0) {
            $shipment->load('consolidation');
            $reached = $shipment->consolidation?->date_reached;
            if ($reached && now()->toDateString() < $reached->toDateString()) {
                return response()->json([
                    'message' => "Cannot mark as Delivered before the consolidation's date reached ({$reached->toDateString()})",
                ], 422);
            }
        }

        $oldStatusId = $shipment->shipment_status_id;
        $shipment->shipment_status_id = $newStatus->id;
        $shipment->save();

        $this->handleStatusTransition($shipment, $oldStatusId);

        return response()->json([
            'message'  => 'Status updated',
            'shipment' => $shipment->fresh()->load('shipmentStatus'),
        ]);
    }

    private function handleStatusTransition(Shipment $shipment, ?int $oldStatusId): void
    {
        $newStatus = ShipmentStatus::find($shipment->shipment_status_id);
        if (!$newStatus) return;

        $oldStatus = $oldStatusId ? ShipmentStatus::find($oldStatusId) : null;

        $isDamaged   = strcasecmp($newStatus->name, 'Damaged - Returned') === 0;
        $wasDamaged  = $oldStatus && strcasecmp($oldStatus->name, 'Damaged - Returned') === 0;
        $isDelivered = strcasecmp($newStatus->name, 'Delivered') === 0;

        $voucherService = new VoucherService();

        if ($wasDamaged && !$isDamaged) {
            $voucherService->reverseDamagedShipmentVouchers($shipment);
        }

        if ($isDamaged) {
            $wasDelivered = !is_null($shipment->date_delivered);

            $shipment->date_delivered = null;
            $shipment->receivable_cod = 0;
            $shipment->amount_due = 0;
            $shipment->saveQuietly();

            Debtor::where('shipment_id', $shipment->id)->delete();

            $voucherService->generateDamagedShipmentVouchers($shipment, $wasDelivered);
        } elseif ($isDelivered) {
            $shipment->date_delivered = now()->toDateString();
            $shipment->saveQuietly();
            $voucherService->syncShipmentVouchers($shipment);
        } else {
            $shipment->date_delivered = null;
            $shipment->saveQuietly();
        }
    }

    public function fetchCustomer(Request $request)
    {
        $request->validate(['q' => 'required|string']);
        $customer = User::where('pcode', $request->q)
            ->orWhere('name', 'like', "%{$request->q}%")
            ->first();
        return response()->json($customer);
    }

    public function generateShipmentCode(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $last   = Shipment::orderBy('id', 'desc')->first();
        $nextId = $last ? $last->id + 1 : 1;
        return response()->json(['shipment_code' => 'SH-' . $nextId]);
    }

    private function convertToKg($weight, $unit)
    {
        return match ($unit) {
            'ounce' => $weight * 0.0283495,
            'gram'  => $weight / 1000,
            'pound' => $weight * 0.453592,
            default => $weight,
        };
    }

    private function syncShipmentVouchers($shipment)
    {
        return (new VoucherService())->syncShipmentVouchers($shipment);
    }

    public function searchUsers(Request $request)
    {
        $request->validate([
            'search'   => 'nullable|string|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);
        $query = User::query();
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('pcode', 'like', "%{$search}%");
            });
        }
        $perPage = $request->per_page ?? 20;
        return response()->json($query->orderBy('name')->paginate($perPage));
    }

    public function updateBulkStatus(Request $request)
    {
        $request->validate([
            'ids'                => 'required|array',
            'ids.*'              => 'required|exists:shipments,id',
            'shipment_status_id' => 'required|exists:shipment_statuses,id',
        ]);

        $status      = ShipmentStatus::findOrFail($request->shipment_status_id);
        $isDelivered = strcasecmp($status->name, 'Delivered') === 0;
        $isDamaged   = strcasecmp($status->name, 'Damaged - Returned') === 0;
        $today       = now()->toDateString();

        $updated = 0;
        $skipped = [];

        foreach (Shipment::whereIn('id', $request->ids)->get() as $shipment) {
            if ($isDelivered) {
                $shipment->load('consolidation');
                $reached = $shipment->consolidation?->date_reached?->toDateString();
                if ($reached && $today < $reached) {
                    $skipped[] = $shipment->shipment_code;
                    continue;
                }
            }

            $oldStatusId = $shipment->shipment_status_id;
            $shipment->shipment_status_id = $status->id;
            $shipment->save();

            $this->handleStatusTransition($shipment, $oldStatusId);

            $updated++;
        }

        $message = "{$updated} shipment(s) updated successfully";
        if (!empty($skipped)) {
            $message .= '; skipped (consolidation not yet reached): ' . implode(', ', $skipped);
        }

        return response()->json(['message' => $message, 'updated_count' => $updated]);
    }
}
