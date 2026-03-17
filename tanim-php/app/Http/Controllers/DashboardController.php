<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $cartTotal = $cartItems->sum(fn($i) => $i->quantity * $i->product->price);
        $cartCount = $cartItems->sum('quantity');

        return view('dashboard', compact('user', 'cartItems', 'cartTotal', 'cartCount'));
    }
}
