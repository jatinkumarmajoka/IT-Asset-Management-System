<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::with('department')
            ->withCount('assets')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($x) => $x->where('name', 'like', "%{$term}%")
                    ->orWhere('employee_code', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                );
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create', [
            'departments' => Department::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_code' => ['required', 'string', 'max:50', 'unique:employees,employee_code'],
            'name' => ['required', 'string', 'max:150'],
            'designation' => ['nullable', 'string', 'max:150'],
            'department_id' => ['required', 'exists:departments,id'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        Employee::create($data);

        return redirect()->route('employees.index')->with('success', 'Employee created.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', [
            'employee' => $employee,
            'departments' => Department::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'employee_code' => ['required', 'string', 'max:50', Rule::unique('employees', 'employee_code')->ignore($employee->id)],
            'name' => ['required', 'string', 'max:150'],
            'designation' => ['nullable', 'string', 'max:150'],
            'department_id' => ['required', 'exists:departments,id'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->assets()->exists()) {
            return back()->with('error', 'Cannot delete an employee currently linked to assets.');
        }

        $employee->delete();

        return back()->with('success','Employee deleted.');
    }
}
