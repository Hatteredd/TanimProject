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
        $activeSuppliers = $employees->where('status', 'active')->count();
        $uniqueSpecialties = $employees->pluck('specialty')->filter()->unique()->count();
        $uniqueLocations = $employees->pluck('location')->filter()->unique()->count();

        return view('admin.employees', compact('employees', 'activeSuppliers', 'uniqueSpecialties', 'uniqueLocations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:120',
            'location'       => 'required|string|max:200',
            'specialty'      => 'nullable|string|max:120',
            'contact_number' => 'nullable|string|max:30',
            'status'         => 'required|in:active,inactive',
            'notes'          => 'nullable|string|max:1000',
        ]);
        Employee::create($data);
        return back()->with('success', 'Supplier added.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return back()->with('success', 'Supplier removed.');
    }
}
