<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalCustomers = User::where('role', 'buyer')->count();
        $totalProducts  = Product::where('is_active', true)->count();
        $totalReviews   = Review::count();
        $avgRating      = round(Review::avg('rating') ?? 0, 1);

        $effectiveTotalSql = "CASE
            WHEN orders.total_amount IS NOT NULL AND orders.total_amount > 0 THEN orders.total_amount
            ELSE ((SELECT COALESCE(SUM(oi.unit_price * oi.quantity), 0) FROM order_items oi WHERE oi.order_id = orders.id) * " . (1 + Order::VAT_RATE) . " + " . Order::SHIPPING_FEE . ")
        END";

        $totalRevenue = (float) (Order::query()
            ->whereNotIn('orders.status', ['cancelled'])
            ->selectRaw("COALESCE(SUM({$effectiveTotalSql}), 0) as total")
            ->value('total') ?? 0);

        $expensesThisMonth = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $expenseByType = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $recentUsers  = User::where('role', 'buyer')->latest()->take(5)->get();
        $recentOrders = Order::with('user')->withComputedTotal()->latest()->take(6)->get();
        $lowStock     = Product::where('stock', '<=', 15)->where('is_active', true)->orderBy('stock')->take(8)->get();

        return view('admin.dashboard', compact(
            'totalCustomers', 'totalProducts', 'totalReviews', 'avgRating',
            'totalRevenue', 'expensesThisMonth', 'expenseByType',
            'recentUsers', 'recentOrders', 'lowStock'
        ));
    }
}
