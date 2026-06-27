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
            'name'   => 'required|string|max:255|unique:payment_methods,name',
            'status' => 'boolean',
        ]);

        $method = PaymentMethod::create($validated);
        return response()->json($method, 201);
    }

    public function show(PaymentMethod $paymentMethod)
    {
        return response()->json($paymentMethod);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name'   => ['sometimes', 'string', 'max:255', Rule::unique('payment_methods')->ignore($paymentMethod->id)],
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
