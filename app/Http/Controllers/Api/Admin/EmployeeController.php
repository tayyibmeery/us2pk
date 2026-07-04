<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return response()->json(Employee::orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'monthly_salary' => 'required|numeric|min:0',
            'joining_date' => 'nullable|date',
            'status' => 'boolean',
        ]);

        $employee = Employee::create($validated);
        return response()->json($employee, 201);
    }

    public function show(Employee $employee)
    {
        return response()->json($employee);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'position' => 'nullable|string|max:255',
            'monthly_salary' => 'sometimes|numeric|min:0',
            'joining_date' => 'nullable|date',
            'status' => 'boolean',
        ]);

        $employee->update($validated);
        return response()->json($employee);
    }

    public function destroy(Employee $employee)
    {
        if ($employee->salaryPayments()->count() > 0) {
            return response()->json(['message' => 'Cannot delete employee with salary records'], 422);
        }
        $employee->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
