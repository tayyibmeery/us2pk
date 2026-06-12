<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::with('user', 'consolidation');
        if ($request->search) {
            $query->where('psi', 'like', "%{$request->search}%")
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'               => 'required|exists:users,id',
            'consolidation_id'      => 'nullable|exists:consolidations,id',
            'description'           => 'nullable|string',
            'weight'                => 'required|numeric',
            'weight_unit'           => 'required|in:ounce,gram,kg,pound',
            'seller_tracker_id'     => 'nullable|string',
            'purchase_date'         => 'nullable|date',
            'comments'              => 'nullable|string',
            'status'                => 'required|string',
            'arrival_date'          => 'nullable|date',
            'expected_delivery_date' => 'nullable|date',
            'date_delivered'        => 'nullable|date',
            'item_value_pkr'        => 'required|numeric',
            'company_charges'       => 'required|numeric',
            'paid_by'               => 'required|in:By Company,By Customer',
            'payment_method'        => 'nullable|string',
            'receivable_cod'        => 'nullable|numeric',
            'local_delivery_by'     => 'nullable|string',
            'blueex_charges'        => 'nullable|numeric',
        ]);

        $data['weight_kgs'] = $this->convertToKg($data['weight'], $data['weight_unit']);
        $data['total'] = $data['item_value_pkr'] + $data['company_charges'];
        $data['amount_due'] = $data['paid_by'] === 'By Customer' ? $data['total'] : 0;

        $shipment = Shipment::create($data);
        return response()->json($shipment, 201);
    }

    public function update(Request $request, Shipment $shipment)
    {
        $data = $request->validate([
            'user_id'               => 'sometimes|exists:users,id',
            'consolidation_id'      => 'nullable|exists:consolidations,id',
            'description'           => 'nullable|string',
            'weight'                => 'sometimes|numeric',
            'weight_unit'           => 'sometimes|in:ounce,gram,kg,pound',
            'weight_kgs'            => 'nullable|numeric',
            'seller_tracker_id'     => 'nullable|string',
            'purchase_date'         => 'nullable|date',
            'comments'              => 'nullable|string',
            'status'                => 'sometimes|string',
            'arrival_date'          => 'nullable|date',
            'expected_delivery_date' => 'nullable|date',
            'date_delivered'        => 'nullable|date',
            'item_value_pkr'        => 'sometimes|numeric',
            'company_charges'       => 'sometimes|numeric',
            'paid_by'               => 'sometimes|in:By Company,By Customer',
            'payment_method'        => 'nullable|string',
            'receivable_cod'        => 'nullable|numeric',
            'local_delivery_by'     => 'nullable|string',
            'blueex_charges'        => 'nullable|numeric',
        ]);

        if (isset($data['weight']) && isset($data['weight_unit'])) {
            $data['weight_kgs'] = $this->convertToKg($data['weight'], $data['weight_unit']);
        }
        if (isset($data['item_value_pkr']) || isset($data['company_charges'])) {
            $item = $data['item_value_pkr'] ?? $shipment->item_value_pkr;
            $charges = $data['company_charges'] ?? $shipment->company_charges;
            $data['total'] = $item + $charges;
            $paidBy = $data['paid_by'] ?? $shipment->paid_by;
            $data['amount_due'] = $paidBy === 'By Customer' ? $data['total'] : 0;
        }

        $shipment->update($data);
        return response()->json($shipment);
    }

    public function destroy(Shipment $shipment)
    {
        $shipment->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function fetchCustomer(Request $request)
    {
        $request->validate(['q' => 'required|string']);
        $customer = User::where('pcode', $request->q)
            ->orWhere('name', 'like', "%{$request->q}%")
            ->first();
        return response()->json($customer);
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
}
