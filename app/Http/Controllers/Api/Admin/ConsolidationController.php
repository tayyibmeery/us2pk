<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Consolidation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConsolidationController extends Controller
{
    public function index(Request $request)
    {
        $query = Consolidation::with('warehouse', 'shipments');
        if ($request->search) {
            $query->where('consol_id', 'like', "%{$request->search}%")
                ->orWhere('awb_number', 'like', "%{$request->search}%");
        }
        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'consol_id'           => 'required|unique:consolidations',
            'awb_number'          => 'nullable|string',
            'warehouse_id'        => 'nullable|exists:warehouses,id',
            'date_dispatched'     => 'nullable|date',
            'total_us2pk_charges' => 'required|numeric',
            'direct_costs'        => 'required|numeric',
            'total_weight_kg'     => 'required|numeric',
        ]);
        $data['gross_income'] = $data['total_us2pk_charges'] - $data['direct_costs'];
        if ($data['total_us2pk_charges'] > 0) {
            $data['roi_percent'] = ($data['gross_income'] / $data['total_us2pk_charges']) * 100;
        }
        $consolidation = Consolidation::create($data);
        return response()->json($consolidation, 201);
    }

    public function show(Consolidation $consolidation)
    {
        return response()->json($consolidation->load('shipments.user'));
    }

    public function update(Request $request, Consolidation $consolidation)
    {
        $consolidation->update($request->all());
        return response()->json($consolidation);
    }

    public function destroy(Consolidation $consolidation)
    {
        $consolidation->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
