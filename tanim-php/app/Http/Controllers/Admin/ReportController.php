<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

class ReportController extends Controller
{
    public function index()
    {
        $year = now()->year;

        // Monthly sales (all 12 months)
        $monthlySales = Order::whereYear('created_at', $year)
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('month')->orderBy('month')->get()->keyBy('month');

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = [
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'total' => (float) ($monthlySales[$m]->total ?? 0),
                'count' => (int)   ($monthlySales[$m]->count ?? 0),
            ];
        }

        // Top products by revenue
        $topProducts = OrderItem::selectRaw('product_name, SUM(subtotal) as total, SUM(quantity) as qty')
            ->groupBy('product_name')->orderByDesc('total')->limit(10)->get();

        // Customer registrations by month (buyers only)
        $userRegs = User::where('role', 'buyer')
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')->orderBy('month')->get()->keyBy('month');

        $userRegData = [];
        for ($m = 1; $m <= 12; $m++) {
            $userRegData[] = [
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'count' => (int) ($userRegs[$m]->count ?? 0),
            ];
        }

        // Summary KPIs
        $totalRevenue  = Order::whereNotIn('status', ['cancelled'])->sum('total_amount');
        $totalOrders   = Order::count();
        $totalCustomers = User::where('role', 'buyer')->count();
        $avgOrderValue = Order::whereNotIn('status', ['cancelled'])->avg('total_amount') ?? 0;

        // Orders by status
        $ordersByStatus = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')->pluck('count', 'status');

        // Category sales
        $categorySales = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('products.category, SUM(order_items.subtotal) as total')
            ->groupBy('products.category')->orderByDesc('total')->get();

        // Monthly order count for sparkline
        $monthlyOrderCounts = array_column($monthlyData, 'count');

        ActivityLog::record('export', 'Admin viewed reports & analytics');

        return view('admin.reports.index', compact(
            'monthlyData', 'topProducts', 'userRegData',
            'totalRevenue', 'totalOrders', 'totalCustomers', 'avgOrderValue',
            'ordersByStatus', 'categorySales', 'year', 'monthlyOrderCounts'
        ));
    }
}
