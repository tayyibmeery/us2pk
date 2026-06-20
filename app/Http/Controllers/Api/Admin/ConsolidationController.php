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
        $query = Consolidation::with('warehouse', 'shipments');
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
            'awb_number'          => 'nullable|string|max:100',
            'warehouse_id'        => 'nullable|exists:warehouses,id',
            'date_dispatched'     => 'nullable|date',
            'courier'             => 'nullable|string|max:100',
            'date_departed'       => 'nullable|date',
            'date_reached'        => 'nullable|date',
            'ware_house_charges'  => 'nullable|numeric|min:0',
            'customs_duty'        => 'nullable|numeric|min:0',
            'sales_tax'           => 'nullable|numeric|min:0',
            'income_tax'          => 'nullable|numeric|min:0',
            'caa_charges'         => 'nullable|numeric|min:0',
            'shipment_ids'        => 'nullable|array',
            'shipment_ids.*'      => 'exists:shipments,id',
        ]);

        // Auto-generate consol_id
        $last = Consolidation::orderBy('id', 'desc')->first();
        $next = $last ? intval(str_replace('Consol-', '', $last->consol_id)) + 1 : 1;
        $data['consol_id'] = 'Consol-' . $next;

        // Set default values
        $data['ware_house_charges'] = $data['ware_house_charges'] ?? 0;
        $data['customs_duty']       = $data['customs_duty'] ?? 0;
        $data['sales_tax']          = $data['sales_tax'] ?? 0;
        $data['income_tax']         = $data['income_tax'] ?? 0;
        $data['caa_charges']        = $data['caa_charges'] ?? 0;

        $consolidation = Consolidation::create($data);

        // Attach shipments
        if ($request->has('shipment_ids')) {
            Shipment::whereIn('id', $request->shipment_ids)->update(['consolidation_id' => $consolidation->id]);
        }

        $consolidation->recalculateTotals();

        return response()->json($consolidation->load('warehouse', 'shipments'), 201);
    }

    public function show(Consolidation $consolidation)
    {
        $consolidation->load('shipments.user.city');
        // Recalculate totals before sending (to ensure consistency)
        $consolidation->recalculateTotals();
        return response()->json($consolidation);
    }

    public function update(Request $request, Consolidation $consolidation)
    {
        $data = $request->validate([
            'awb_number'          => 'nullable|string|max:100',
            'warehouse_id'        => 'nullable|exists:warehouses,id',
            'date_dispatched'     => 'nullable|date',
            'courier'             => 'nullable|string|max:100',
            'date_departed'       => 'nullable|date',
            'date_reached'        => 'nullable|date',
            'ware_house_charges'  => 'nullable|numeric|min:0',
            'customs_duty'        => 'nullable|numeric|min:0',
            'sales_tax'           => 'nullable|numeric|min:0',
            'income_tax'          => 'nullable|numeric|min:0',
            'caa_charges'         => 'nullable|numeric|min:0',
            'shipment_ids'        => 'nullable|array',
            'shipment_ids.*'      => 'exists:shipments,id',
        ]);

        $consolidation->update($data);

        // Detach all shipments first, then attach new ones
        if ($request->has('shipment_ids')) {
            Shipment::where('consolidation_id', $consolidation->id)->update(['consolidation_id' => null]);
            Shipment::whereIn('id', $request->shipment_ids)->update(['consolidation_id' => $consolidation->id]);
        }

        $consolidation->recalculateTotals();

        return response()->json($consolidation->load('warehouse', 'shipments'));
    }

    public function destroy(Consolidation $consolidation)
    {
        // Detach shipments before deleting
        Shipment::where('consolidation_id', $consolidation->id)->update(['consolidation_id' => null]);
        $consolidation->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // Autocomplete shipments by PSI
    public function shipmentsJson(Request $request)
    {
        $q = $request->input('q');
        $shipments = Shipment::where('psi', 'like', "%{$q}%")
            ->whereNull('consolidation_id') // only unassigned shipments
            ->limit(10)
            ->get(['id', 'psi']);
        return response()->json($shipments);
    }

    // Fetch full shipment details by PSI (for adding to consolidation)
    public function shipmentDetails(Request $request)
    {
        $psi = $request->input('shipment_tracker_id');
        $shipment = Shipment::with('user.city')->where('psi', $psi)->first();
        if (!$shipment) {
            return response()->json(['message' => 'Shipment not found'], 404);
        }
        // Return the shipment data along with a pre-rendered HTML row (optional)
        // For Vue we will render the row on frontend, so just return the shipment object.
        return response()->json($shipment);
    }
}
