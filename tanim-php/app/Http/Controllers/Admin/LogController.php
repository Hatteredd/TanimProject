<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('action'))  $query->where('action', $request->action);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs    = $query->paginate(30)->withQueryString();
        $actions = ActivityLog::distinct()->pluck('action');
        $users   = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('admin.logs.index', compact('logs', 'actions', 'users'));
    }

    public function clear()
    {
        ActivityLog::where('created_at', '<', now()->subDays(30))->delete();
        ActivityLog::record('delete', 'Admin cleared activity logs older than 30 days');
        return back()->with('success', 'Logs older than 30 days cleared.');
    }
}
