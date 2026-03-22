<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'report_type' => ['nullable', 'in:all,sales,customers,products,orders'],
            'status' => ['nullable', 'in:' . implode(',', Order::statuses())],
            'customer_role' => ['nullable', 'in:all,buyer,farmer,admin'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        $fromInput = $request->input('from_date', now()->startOfYear()->toDateString());
        $toInput = $request->input('to_date', now()->toDateString());

        $fromDate = Carbon::parse($fromInput)->startOfDay();
        $toDate = Carbon::parse($toInput)->endOfDay();

        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate->copy()->startOfDay(), $fromDate->copy()->endOfDay()];
        }

        $reportTypes = ['all', 'sales', 'customers', 'products', 'orders'];
        $reportType = in_array($request->input('report_type'), $reportTypes, true)
            ? $request->input('report_type')
            : 'all';

        $statuses = Order::statuses();
        $statusFilter = $request->filled('status') && in_array($request->input('status'), $statuses, true)
            ? $request->input('status')
            : null;

        $roleOptions = ['all', 'buyer', 'farmer', 'admin'];
        $roleFilter = in_array($request->input('customer_role', 'buyer'), $roleOptions, true)
            ? $request->input('customer_role', 'buyer')
            : 'buyer';

        $availableCategories = Product::query()
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $categoryFilter = $request->filled('category') && $availableCategories->contains($request->input('category'))
            ? $request->input('category')
            : null;

        $driver = DB::connection()->getDriverName();
        $rangeDays = $fromDate->diffInDays($toDate);
        $groupByMonthly = $rangeDays > 62;

        $periodExpr = $driver === 'sqlite'
            ? ($groupByMonthly
                ? "strftime('%Y-%m', orders.created_at)"
                : "strftime('%Y-%m-%d', orders.created_at)")
            : ($groupByMonthly
                ? "DATE_FORMAT(orders.created_at, '%Y-%m')"
                : 'DATE(orders.created_at)');

        $userPeriodExpr = $driver === 'sqlite'
            ? ($groupByMonthly
                ? "strftime('%Y-%m', users.created_at)"
                : "strftime('%Y-%m-%d', users.created_at)")
            : ($groupByMonthly
                ? "DATE_FORMAT(users.created_at, '%Y-%m')"
                : 'DATE(users.created_at)');

        $ordersBase = Order::query()
            ->whereBetween('orders.created_at', [$fromDate, $toDate]);

        if ($statusFilter) {
            $ordersBase->where('orders.status', $statusFilter);
        }

        if ($categoryFilter) {
            $ordersBase->whereExists(function ($q) use ($categoryFilter) {
                $q->selectRaw('1')
                    ->from('order_items as oi')
                    ->join('products as p', 'oi.product_id', '=', 'p.id')
                    ->whereColumn('oi.order_id', 'orders.id')
                    ->where('p.category', $categoryFilter);
            });
        }

        $salesQuery = Order::query()
            ->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$fromDate, $toDate])
            ->selectRaw("{$periodExpr} as period, COALESCE(SUM(order_items.unit_price * order_items.quantity), 0) as total, COUNT(DISTINCT orders.id) as count")
            ->groupBy('period')
            ->orderBy('period');

        if ($statusFilter) {
            $salesQuery->where('orders.status', $statusFilter);
        } else {
            $salesQuery->whereNotIn('orders.status', ['cancelled']);
        }

        if ($categoryFilter) {
            $salesQuery->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.category', $categoryFilter);
        }

        $salesBuckets = $salesQuery->get()->keyBy('period');
        $periodKeys = $this->buildPeriodKeys($fromDate, $toDate, $groupByMonthly);

        $monthlyData = collect($periodKeys)->map(function (string $period) use ($salesBuckets, $groupByMonthly) {
            $row = $salesBuckets->get($period);

            return [
                'month' => $this->formatPeriodLabel($period, $groupByMonthly),
                'total' => (float) ($row->total ?? 0),
                'count' => (int) ($row->count ?? 0),
            ];
        })->values()->all();

        $topProducts = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$fromDate, $toDate])
            ->when($statusFilter, fn ($q) => $q->where('orders.status', $statusFilter), fn ($q) => $q->whereNotIn('orders.status', ['cancelled']))
            ->when($categoryFilter, fn ($q) => $q->join('products', 'order_items.product_id', '=', 'products.id')->where('products.category', $categoryFilter))
            ->selectRaw('order_items.product_name, SUM(order_items.unit_price * order_items.quantity) as total, SUM(order_items.quantity) as qty')
            ->groupBy('order_items.product_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $userRegs = User::query()
            ->whereBetween('users.created_at', [$fromDate, $toDate])
            ->when($roleFilter !== 'all', fn ($q) => $q->where('users.role', $roleFilter))
            ->selectRaw("{$userPeriodExpr} as period, COUNT(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $userRegData = collect($periodKeys)->map(function (string $period) use ($userRegs, $groupByMonthly) {
            $row = $userRegs->get($period);

            return [
                'month' => $this->formatPeriodLabel($period, $groupByMonthly),
                'count' => (int) ($row->count ?? 0),
            ];
        })->values()->all();

        $totalRevenue = (float) (OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$fromDate, $toDate])
            ->when($statusFilter, fn ($q) => $q->where('orders.status', $statusFilter), fn ($q) => $q->whereNotIn('orders.status', ['cancelled']))
            ->when($categoryFilter, fn ($q) => $q->join('products', 'order_items.product_id', '=', 'products.id')->where('products.category', $categoryFilter))
            ->selectRaw('COALESCE(SUM(order_items.unit_price * order_items.quantity), 0) as total')
            ->value('total') ?? 0);

        $totalOrders = (clone $ordersBase)->count();

        $totalCustomers = (int) User::query()
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->when($roleFilter !== 'all', fn ($q) => $q->where('role', $roleFilter))
            ->count();

        $avgOrderValue = (float) (DB::query()
            ->fromSub(
                Order::query()
                    ->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->when($statusFilter, fn ($q) => $q->where('orders.status', $statusFilter), fn ($q) => $q->whereNotIn('orders.status', ['cancelled']))
                    ->when($categoryFilter, function ($q) use ($categoryFilter) {
                        $q->join('products', 'order_items.product_id', '=', 'products.id')
                            ->where('products.category', $categoryFilter);
                    })
                    ->groupBy('orders.id')
                    ->selectRaw('COALESCE(SUM(order_items.unit_price * order_items.quantity), 0) as order_total'),
                'order_totals'
            )
            ->avg('order_total') ?? 0);

        $ordersByStatus = (clone $ordersBase)
            ->selectRaw('orders.status, COUNT(*) as count')
            ->groupBy('orders.status')
            ->pluck('count', 'status');

        $categorySales = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$fromDate, $toDate])
            ->when($statusFilter, fn ($q) => $q->where('orders.status', $statusFilter), fn ($q) => $q->whereNotIn('orders.status', ['cancelled']))
            ->when($categoryFilter, fn ($q) => $q->where('products.category', $categoryFilter))
            ->selectRaw('products.category, SUM(order_items.unit_price * order_items.quantity) as total')
            ->groupBy('products.category')
            ->orderByDesc('total')
            ->get();

        // Monthly order count for sparkline
        $monthlyOrderCounts = array_column($monthlyData, 'count');

        ActivityLog::record('export', 'Admin viewed reports & analytics');

        return view('admin.reports.index', compact(
            'monthlyData', 'topProducts', 'userRegData',
            'totalRevenue', 'totalOrders', 'totalCustomers', 'avgOrderValue',
            'ordersByStatus', 'categorySales', 'monthlyOrderCounts',
            'fromDate', 'toDate', 'reportType', 'statusFilter', 'roleFilter',
            'categoryFilter', 'availableCategories', 'statuses'
        ));
    }

    private function buildPeriodKeys(Carbon $fromDate, Carbon $toDate, bool $groupByMonthly): array
    {
        $keys = [];

        if ($groupByMonthly) {
            $cursor = $fromDate->copy()->startOfMonth();
            $end = $toDate->copy()->startOfMonth();

            while ($cursor->lte($end)) {
                $keys[] = $cursor->format('Y-m');
                $cursor->addMonth();
            }

            return $keys;
        }

        $cursor = $fromDate->copy()->startOfDay();
        $end = $toDate->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $keys[] = $cursor->format('Y-m-d');
            $cursor->addDay();
        }

        return $keys;
    }

    private function formatPeriodLabel(string $period, bool $groupByMonthly): string
    {
        return $groupByMonthly
            ? Carbon::createFromFormat('Y-m', $period)->format('M Y')
            : Carbon::createFromFormat('Y-m-d', $period)->format('M d');
    }
}
