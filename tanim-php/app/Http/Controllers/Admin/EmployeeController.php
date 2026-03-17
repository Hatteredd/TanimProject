<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('name')->get();
        $totalSalary = $employees->where('status','active')->sum('base_salary');
        $totalBonus  = $employees->where('status','active')->sum('bonus');
        $total13th   = $employees->where('status','active')->sum(fn($e) => $e->thirteenthMonth());

        return view('admin.employees', compact('employees','totalSalary','totalBonus','total13th'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:120',
            'position'    => 'required|string|max:120',
            'department'  => 'nullable|string|max:80',
            'base_salary' => 'required|numeric|min:0',
            'bonus'       => 'nullable|numeric|min:0',
            'hire_date'   => 'required|date',
            'status'      => 'in:active,inactive',
            'notes'       => 'nullable|string',
        ]);
        Employee::create($data);
        return back()->with('success', 'Employee added.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return back()->with('success', 'Employee removed.');
    }
}
