<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Helpers\ReferenceGenerator;
use App\Models\Employee;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('outlet')->orderBy('first_name')->get();
        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        $outlets = Outlet::whereDoesntHave('employee')->orderBy('name')->get();
        return view('hr.employees.create', compact('outlets'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $validated = $this->normalizePayFields($request->validated());

        if (! empty($validated['outlet_id']) && Employee::where('outlet_id', $validated['outlet_id'])->exists()) {
            return back()->withInput()->with('error', 'That outlet is already staffed by another employee.');
        }

        DB::transaction(function () use ($validated) {
            $employeeNumber = ReferenceGenerator::generateEmployeeNumber();

            Employee::create(array_merge($validated, [
                'employee_number' => $employeeNumber,
            ]));
        });

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Salary employees don't carry commission fields, and commission employees don't
     * carry a basic salary (their pay is computed at payroll-generation time instead).
     */
    protected function normalizePayFields(array $data): array
    {
        if (($data['pay_type'] ?? 'salary') === 'commission') {
            $data['basic_salary'] = 0;
            $data['commission_target'] = $data['commission_target'] ?? 1250;
        } else {
            $data['commission_rate'] = null;
            $data['commission_target'] = null;
        }

        return $data;
    }

    public function show(Employee $employee)
    {
        $employee->load('outlet', 'payrollItems.period', 'assets.category');
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $outlets = Outlet::where(function ($q) use ($employee) {
            $q->whereDoesntHave('employee')->orWhere('id', $employee->outlet_id);
        })->orderBy('name')->get();

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
            'pay_type' => 'required|in:salary,commission',
            'basic_salary' => 'required_if:pay_type,salary|nullable|numeric|min:0',
            'commission_rate' => 'required_if:pay_type,commission|nullable|numeric|min:0',
            'commission_target' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        if (! empty($validated['outlet_id']) && Employee::where('outlet_id', $validated['outlet_id'])->where('id', '!=', $employee->id)->exists()) {
            return back()->withInput()->with('error', 'That outlet is already staffed by another employee.');
        }

        $employee->update($this->normalizePayFields($validated));

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->payrollItems()->exists()) {
            return redirect()->route('employees.index')
                ->with('error', 'Cannot delete an employee with payroll history. Set their status to "terminated" instead.');
        }

        $employee->delete();
        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}