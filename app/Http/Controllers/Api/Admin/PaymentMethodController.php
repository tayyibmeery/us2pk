<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::orderBy('name')->paginate(20);
        return response()->json($methods);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:payment_methods',
            'account_id' => 'nullable|exists:accounts,id',
            'status' => 'boolean',
        ]);

        $paymentMethod = PaymentMethod::create($validated);
        return response()->json($paymentMethod, 201);
    }



    public function show(PaymentMethod $paymentMethod)
    {
        return response()->json($paymentMethod);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|unique:payment_methods,name,' . $paymentMethod->id,
            'account_id' => 'nullable|exists:accounts,id',
            'status' => 'boolean',
        ]);

        $paymentMethod->update($validated);
        return response()->json($paymentMethod);
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        // Check if any shipment uses this payment method via payment_method_id
        $used = Shipment::where('payment_method_id', $paymentMethod->id)->exists();

        if ($used) {
            return response()->json([
                'message' => 'Cannot delete because it is used in shipments.'
            ], 422);
        }

        $paymentMethod->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
