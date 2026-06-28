<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consolidation;
use App\Models\Shipment;
use Illuminate\Http\Request;

class ConsolidationController extends Controller
{
    public function index(Request $request)
    {
        $query = Consolidation::with('warehouse', 'shipments', 'internationalCourier');
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
            'date_dispatched'          => 'nullable|date',
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

        // ✅ Set a temporary value to satisfy the NOT NULL constraint
        $data['total_weight_kg'] = 0;

        $consolidation = Consolidation::create($data);

        // Attach shipments
        if ($request->has('shipment_ids')) {
            Shipment::whereIn('id', $request->shipment_ids)
                ->update(['consolidation_id' => $consolidation->id]);
        }

        // Recalculate totals (will update total_weight_kg, etc.)
        $consolidation->recalculateTotals();

        return response()->json($consolidation->load('warehouse', 'shipments', 'internationalCourier'), 201);
    }

    public function show(Consolidation $consolidation)
    {
        $consolidation->load('shipments.user.city', 'internationalCourier', 'warehouse');
        $consolidation->recalculateTotals(); // ensure fresh totals
        return response()->json($consolidation);
    }

    public function update(Request $request, Consolidation $consolidation)
    {
        $data = $request->validate([
            'awb_number'               => 'nullable|string|max:100',
            'warehouse_id'             => 'nullable|exists:warehouses,id',
            'date_dispatched'          => 'nullable|date',
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

        $consolidation->update($data);

        // Detach all shipments, then attach new ones
        Shipment::where('consolidation_id', $consolidation->id)
            ->update(['consolidation_id' => null]);

        if ($request->has('shipment_ids')) {
            Shipment::whereIn('id', $request->shipment_ids)
                ->update(['consolidation_id' => $consolidation->id]);
        }

        $consolidation->recalculateTotals();

        return response()->json($consolidation->load('warehouse', 'shipments', 'internationalCourier'));
    }

    public function destroy(Consolidation $consolidation)
    {
        Shipment::where('consolidation_id', $consolidation->id)
            ->update(['consolidation_id' => null]);
        $consolidation->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // ✅ Autocomplete – search by shipment_code (was psi)
    public function shipmentsJson(Request $request)
    {
        $q = $request->input('q');
        $shipments = Shipment::where('shipment_code', 'like', "%{$q}%")
            ->whereNull('consolidation_id') // only unassigned
            ->limit(10)
            ->get(['id', 'shipment_code']);
        return response()->json($shipments);
    }

    // ✅ Fetch full shipment details by shipment_code
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
