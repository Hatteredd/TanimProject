<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    private function denyAdmin(): void
    {
        abort_if(Auth::user()->role === 'admin', 403, 'Admins cannot use the cart.');
    }

    public function index()
    {
        $this->denyAdmin();

        $items = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get();

        $total = $items->sum(fn($i) => $i->quantity * $i->product->price);

        return view('cart', compact('items', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        abort_if(Auth::user()->role === 'admin', 403, 'Admins cannot add items to cart.');
        $qty = max(1, (int) $request->input('quantity', 1));

        $existing = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $qty);
        } else {
            CartItem::create([
                'user_id'    => Auth::id(),
                'product_id' => $product->id,
                'quantity'   => $qty,
            ]);
        }

        // If buy-now flag, redirect straight to checkout
        if ($request->boolean('buy_now')) {
            return redirect()->route('orders.checkout');
        }

        return back()->with('cart_success', "'{$product->name}' added to cart!");
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorize('update', $cartItem);
        $qty = (int) $request->input('quantity', 1);
        if ($qty < 1) {
            $cartItem->delete();
        } else {
            $cartItem->update(['quantity' => $qty]);
        }
        return back();
    }

    public function remove(CartItem $cartItem)
    {
        $this->authorize('update', $cartItem);
        $cartItem->delete();
        return back()->with('cart_success', 'Item removed from cart.');
    }
}
