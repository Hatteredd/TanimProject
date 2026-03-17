<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('month')) $query->whereMonth('expense_date', $request->month)->whereYear('expense_date', $request->year ?? now()->year);

        $expenses    = $query->orderByDesc('expense_date')->paginate(20)->withQueryString();
        $totalAll    = Expense::sum('amount');
        $totalMonth  = Expense::whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount');
        $byType      = Expense::selectRaw('type, SUM(amount) as total')->groupBy('type')->pluck('total', 'type');
        $types       = Expense::types();

        return view('admin.expenses', compact('expenses', 'totalAll', 'totalMonth', 'byType', 'types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'             => 'required|string',
            'label'            => 'required|string|max:200',
            'amount'           => 'required|numeric|min:0',
            'expense_date'     => 'required|date',
            'recurring'        => 'boolean',
            'recurring_period' => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);
        $data['recurring'] = $request->boolean('recurring');
        Expense::create($data);

        return back()->with('success', 'Expense recorded successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Expense removed.');
    }
}
