<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DataController extends Controller
{
    public function index()
    {
        $tables = [
            'users'    => ['label' => 'Users',    'count' => User::count(),    'icon' => '👤'],
            'products' => ['label' => 'Products', 'count' => Product::withTrashed()->count(), 'icon' => '📦'],
            'orders'   => ['label' => 'Orders',   'count' => Order::count(),   'icon' => '🛒'],
        ];
        return view('admin.data.index', compact('tables'));
    }

    public function exportCsv(Request $request, string $table)
    {
        $allowed = ['users', 'products', 'orders'];
        abort_if(!in_array($table, $allowed), 404);

        $data = match($table) {
            'users'    => User::all(['id','name','email','role','is_active','created_at']),
            'products' => Product::withTrashed()->get(),
            'orders'   => Order::with('user:id,name,email')->get(),
        };

        $filename = "{$table}-export-" . now()->format('Y-m-d') . ".csv";
        $headers  = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($data, $table) {
            $out = fopen('php://output', 'w');
            if ($data->isEmpty()) { fclose($out); return; }

            // Header row
            fputcsv($out, array_keys($data->first()->toArray()));
            foreach ($data as $row) {
                fputcsv($out, array_values($row->toArray()));
            }
            fclose($out);
        };

        ActivityLog::record('export', "Admin exported {$table} as CSV");
        return Response::stream($callback, 200, $headers);
    }

    public function viewTable(Request $request, string $table)
    {
        $allowed = ['users', 'products', 'orders'];
        abort_if(!in_array($table, $allowed), 404);

        $rows = match($table) {
            'users'    => User::latest()->paginate(25),
            'products' => Product::withTrashed()->latest()->paginate(25),
            'orders'   => Order::with('user')->latest()->paginate(25),
        };

        ActivityLog::record('export', "Admin viewed {$table} table records");
        return view('admin.data.table', compact('rows', 'table'));
    }
}
