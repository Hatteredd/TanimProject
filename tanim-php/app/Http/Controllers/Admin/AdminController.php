<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
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

        $totalRevenue = (float) (OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled'])
            ->selectRaw('COALESCE(SUM(order_items.unit_price * order_items.quantity), 0) as total')
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
