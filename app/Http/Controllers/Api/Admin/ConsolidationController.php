<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consolidation;
use App\Models\Shipment;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Services\VoucherService;
use Illuminate\Support\Facades\Log;

class ConsolidationController extends Controller
{
    public function index(Request $request)
    {
        $query = Consolidation::with([
            'warehouse',
            'shipments.user',        // ✅ Correct: user (singular)
            'shipments.user.city',   // ✅ Correct: user (singular)
            'internationalCourier'
        ]);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('consol_id', 'like', "%{$request->search}%")
                    ->orWhere('awb_number', 'like', "%{$request->search}%");
            });
        }
        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'awb_number'               => 'nullable|string|max:100',
            'warehouse_id'             => 'nullable|exists:warehouses,id',
            'international_courier_id' => 'nullable|exists:international_couriers,id',
            'date_departed'            => 'nullable|date',
            'date_reached'             => 'nullable|date',
            'ware_house_charges'       => 'nullable|numeric|min:0',
            'customs_duty'             => 'nullable|numeric|min:0',
            'sales_tax'                => 'nullable|numeric|min:0',
            'income_tax'               => 'nullable|numeric|min:0',
            'caa_charges'              => 'nullable|numeric|min:0',
            'shipment_ids'             => 'nullable|array',
            'shipment_ids.*'           => 'exists:shipments,id',
        ]);

        // Auto-generate consol_id
        $last = Consolidation::orderBy('id', 'desc')->first();
        $next = $last ? intval(str_replace('Consol-', '', $last->consol_id)) + 1 : 1;
        $data['consol_id'] = 'Consol-' . $next;

        // Ensure default values for numeric fields
        foreach (['ware_house_charges', 'customs_duty', 'sales_tax', 'income_tax', 'caa_charges'] as $field) {
            $data[$field] = $data[$field] ?? 0;
        }

        $data['total_weight_kg'] = 0;

        $consolidation = Consolidation::create($data);

        // Attach shipments
        if ($request->has('shipment_ids')) {
            Shipment::whereIn('id', $request->shipment_ids)
                ->update(['consolidation_id' => $consolidation->id]);
        }

        // Recalculate totals
        $consolidation->recalculateTotals();

        // Refresh to get stored columns
        $consolidation->refresh();

        // Generate consolidation cost voucher
        $voucherService = new VoucherService();
        $voucherService->generateConsolidationCostVoucher($consolidation);

        // ✅ Load relationships including user (singular)
        $consolidation->load([
            'warehouse',
            'shipments.user',        // ✅ Correct: user (singular)
            'shipments.user.city',   // ✅ Correct: user (singular)
            'internationalCourier'
        ]);

        return response()->json($consolidation, 201);
    }

    public function show(Consolidation $consolidation)
    {
        // ✅ Load ALL relationships with user data
        $consolidation->load([
            'warehouse',
            'internationalCourier',
            'shipments' => function ($query) {
                $query->with([
                    'user',           // ✅ First load the user
                    'user.city',      // ✅ Then load the user's city
                    'localCourier',
                    'site',
                    'shipmentStatus',
                    'paymentMethod'
                ]);
            }
        ]);

        // Recalculate totals
        $consolidation->recalculateTotals();

        // Refresh to get stored columns
        $consolidation->refresh();

        // ✅ Log for debugging
        Log::info('Consolidation show', [
            'consolidation_id' => $consolidation->id,
            'shipments_count' => $consolidation->shipments->count(),
            'first_shipment_has_user' => $consolidation->shipments->first()?->user ? 'Yes' : 'No',
            'first_shipment_user_name' => $consolidation->shipments->first()?->user?->name ?? 'No user',
            'first_shipment_user_city' => $consolidation->shipments->first()?->user?->city?->city_name ?? 'No city'
        ]);

        return response()->json($consolidation);
    }

    public function update(Request $request, Consolidation $consolidation)
    {
        $data = $request->validate([
            'awb_number'               => 'nullable|string|max:100',
            'warehouse_id'             => 'nullable|exists:warehouses,id',
            'international_courier_id' => 'nullable|exists:international_couriers,id',
            'date_departed'            => 'nullable|date',
            'date_reached'             => 'nullable|date',
            'ware_house_charges'       => 'nullable|numeric|min:0',
            'customs_duty'             => 'nullable|numeric|min:0',
            'sales_tax'                => 'nullable|numeric|min:0',
            'income_tax'               => 'nullable|numeric|min:0',
            'caa_charges'              => 'nullable|numeric|min:0',
            'shipment_ids'             => 'nullable|array',
            'shipment_ids.*'           => 'exists:shipments,id',
        ]);

        // Ensure default values for numeric fields
        foreach (['ware_house_charges', 'customs_duty', 'sales_tax', 'income_tax', 'caa_charges'] as $field) {
            $data[$field] = $data[$field] ?? 0;
        }

        $consolidation->update($data);

        // Detach all shipments, then attach new ones
        Shipment::where('consolidation_id', $consolidation->id)
            ->update(['consolidation_id' => null]);

        if ($request->has('shipment_ids')) {
            Shipment::whereIn('id', $request->shipment_ids)
                ->update(['consolidation_id' => $consolidation->id]);
        }

        // Recalculate totals
        $consolidation->recalculateTotals();

        // Refresh to get stored columns
        $consolidation->refresh();

        // Update consolidation cost voucher (delete old, create new)
        $voucherService = new VoucherService();

        // Delete existing voucher
        $existingVoucher = $consolidation->voucher;
        if ($existingVoucher) {
            $existingVoucher->details()->delete();
            $existingVoucher->delete();
            Log::info('Deleted old consolidation voucher', [
                'consolidation' => $consolidation->consol_id,
                'voucher_no' => $existingVoucher->voucher_no
            ]);
        }

        // Create new voucher
        $voucherService->generateConsolidationCostVoucher($consolidation);

        // ✅ Load relationships including user (singular)
        $consolidation->load([
            'warehouse',
            'shipments.user',        // ✅ Correct: user (singular)
            'shipments.user.city',   // ✅ Correct: user (singular)
            'internationalCourier'
        ]);

        return response()->json($consolidation);
    }

    public function destroy(Consolidation $consolidation)
    {
        try {
            // Delete the consolidation voucher first
            $voucher = $consolidation->voucher;
            if ($voucher) {
                $voucher->details()->delete();
                $voucher->delete();
                Log::info('Deleted consolidation voucher', [
                    'consolidation' => $consolidation->consol_id,
                    'voucher_no' => $voucher->voucher_no
                ]);
            }

            // Detach shipments
            Shipment::where('consolidation_id', $consolidation->id)
                ->update(['consolidation_id' => null]);

            // Delete consolidation
            $consolidation->delete();

            Log::info('Consolidation deleted successfully', [
                'consolidation' => $consolidation->consol_id
            ]);

            return response()->json(['message' => 'Consolidation and associated voucher deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting consolidation: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete consolidation: ' . $e->getMessage()], 500);
        }
    }

    public function shipmentsJson(Request $request)
    {
        $q = $request->input('q');
        $shipments = Shipment::where('shipment_code', 'like', "%{$q}%")
            ->whereNull('consolidation_id')
            ->limit(10)
            ->get(['id', 'shipment_code']);
        return response()->json($shipments);
    }

    public function shipmentDetails(Request $request)
    {
        $shipmentCode = $request->input('shipment_tracker_id');
        $shipment = Shipment::with('user.city')
            ->where('shipment_code', $shipmentCode)
            ->first();

        if (!$shipment) {
            return response()->json(['message' => 'Shipment not found'], 404);
        }

        return response()->json($shipment);
    }
}
