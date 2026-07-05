<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryPayment;
use Illuminate\Http\Request;
use App\Services\VoucherService;

class SalaryPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = SalaryPayment::with('employee');
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }
        return response()->json($query->orderBy('paid_date', 'desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'amount' => 'required|numeric|min:0.01',
            'paid_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payment = SalaryPayment::create($validated);
        $voucherService = new VoucherService();
        $voucherService->generateSalaryPaymentVoucher($payment);
        return response()->json($payment->load('employee'), 201);
    }

    public function show(SalaryPayment $salaryPayment)
    {
        return response()->json($salaryPayment->load('employee'));
    }

    public function update(Request $request, SalaryPayment $salaryPayment)
    {
        $validated = $request->validate([
            'employee_id' => 'sometimes|exists:employees,id',
            'month' => 'sometimes|integer|min:1|max:12',
            'year' => 'sometimes|integer|min:2000|max:2100',
            'amount' => 'sometimes|numeric|min:0.01',
            'paid_date' => 'sometimes|date',
            'notes' => 'nullable|string',
        ]);

        $salaryPayment->update($validated);
        $voucherService = new VoucherService();
        $voucherService->generateSalaryPaymentVoucher($salaryPayment);
        return response()->json($salaryPayment->load('employee'));
    }

    public function destroy(SalaryPayment $salaryPayment)
    {
        $salaryPayment->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
