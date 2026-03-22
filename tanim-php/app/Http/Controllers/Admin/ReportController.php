<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $year = now()->year;
        $driver = DB::connection()->getDriverName();
        $orderMonthExpr = $driver === 'sqlite'
            ? "CAST(strftime('%m', orders.created_at) AS INTEGER)"
            : 'MONTH(orders.created_at)';
        $userMonthExpr = $driver === 'sqlite'
            ? "CAST(strftime('%m', users.created_at) AS INTEGER)"
            : 'MONTH(users.created_at)';

        // Monthly sales (all 12 months)
        $monthlySales = Order::leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
            ->whereYear('orders.created_at', $year)
            ->whereNotIn('orders.status', ['cancelled'])
            ->selectRaw("{$orderMonthExpr} as month, COALESCE(SUM(order_items.unit_price * order_items.quantity), 0) as total, COUNT(DISTINCT orders.id) as count")
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
        $topProducts = OrderItem::selectRaw('product_name, SUM(unit_price * quantity) as total, SUM(quantity) as qty')
            ->groupBy('product_name')->orderByDesc('total')->limit(10)->get();

        // Customer registrations by month (buyers only)
        $userRegs = User::where('role', 'buyer')
            ->whereYear('created_at', $year)
            ->selectRaw("{$userMonthExpr} as month, COUNT(*) as count")
            ->groupBy('month')->orderBy('month')->get()->keyBy('month');

        $userRegData = [];
        for ($m = 1; $m <= 12; $m++) {
            $userRegData[] = [
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'count' => (int) ($userRegs[$m]->count ?? 0),
            ];
        }

        // Summary KPIs
        $totalRevenue = (float) (OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled'])
            ->selectRaw('COALESCE(SUM(order_items.unit_price * order_items.quantity), 0) as total')
            ->value('total') ?? 0);
        $totalOrders   = Order::count();
        $totalCustomers = User::where('role', 'buyer')->count();
        $avgOrderValue = (float) (DB::query()
            ->fromSub(
                Order::leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->whereNotIn('orders.status', ['cancelled'])
                    ->groupBy('orders.id')
                    ->selectRaw('COALESCE(SUM(order_items.unit_price * order_items.quantity), 0) as order_total'),
                'order_totals'
            )
            ->avg('order_total') ?? 0);

        // Orders by status
        $ordersByStatus = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')->pluck('count', 'status');

        // Category sales
        $categorySales = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('products.category, SUM(order_items.unit_price * order_items.quantity) as total')
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
