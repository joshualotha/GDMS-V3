<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Helpers\ReferenceGenerator;
use App\Models\Employee;
use App\Models\Outlet;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('outlet')->orderBy('first_name')->get();
        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        $outlets = Outlet::orderBy('name')->get();
        return view('hr.employees.create', compact('outlets'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $validated = $request->validated();
        
        $employeeNumber = ReferenceGenerator::generateEmployeeNumber();

        Employee::create(array_merge($validated, [
            'employee_number' => $employeeNumber,
        ]));

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load('outlet', 'payrollItems.period');
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $outlets = Outlet::orderBy('name')->get();
        return view('hr.employees.edit', compact('employee', 'outlets'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'id_number' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'role_title' => 'nullable|string|max:255',
            'outlet_id' => 'nullable|exists:outlets,id',
            'hire_date' => 'nullable|date',
            'basic_salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}