<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        abort_if(Auth::user()->role === 'admin', 403, 'Admins cannot access customer orders this way.');

        $orders = Order::with('items')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== Auth::id() && Auth::user()->role !== 'admin', 403);
        $order->load('items.product', 'user');
        return view('orders.show', compact('order'));
    }

    public function checkout()
    {
        abort_if(Auth::user()->role === 'admin', 403, 'Admins cannot place orders.');

        $cartItems = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = $cartItems->sum(fn($i) => $i->quantity * $i->product->price);

        return view('orders.checkout', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()->role === 'admin', 403, 'Admins cannot place orders.');

        $validated = $request->validate([
            'shipping_address' => ['required', 'string', 'max:500'],
            'contact_number'   => ['required', 'string', 'max:20'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $cartItems = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = $cartItems->sum(fn($i) => $i->quantity * $i->product->price);

        $order = Order::create([
            'user_id'          => Auth::id(),
            'order_number'     => Order::generateOrderNumber(),
            'status'           => 'pending',
            'total_amount'     => $total,
            'shipping_address' => $validated['shipping_address'],
            'contact_number'   => $validated['contact_number'],
            'notes'            => $validated['notes'] ?? null,
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item->product_id,
                'product_name' => $item->product->name,
                'unit_price'   => $item->product->price,
                'quantity'     => $item->quantity,
                'subtotal'     => $item->quantity * $item->product->price,
            ]);

            // Reduce stock
            $item->product->decrement('stock', $item->quantity);
        }

        // Clear cart
        CartItem::where('user_id', Auth::id())->delete();

        // Generate PDF receipt
        $pdfPath = $this->generateReceiptPdf($order);

        // Send confirmation email
        try {
            Mail::to($order->user->email)->send(new OrderConfirmationMail($order->load('items'), $pdfPath));
        } catch (\Exception $e) {
            // Log but don't fail the order
            logger()->error('Order email failed: ' . $e->getMessage());
        }

        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully! A confirmation email has been sent.');
    }

    public function downloadReceipt(Order $order)
    {
        abort_if($order->user_id !== Auth::id() && Auth::user()->role !== 'admin', 403);
        $order->load('items.product', 'user');

        $pdf = Pdf::loadView('pdf.receipt', compact('order'));
        return $pdf->download('receipt-' . $order->order_number . '.pdf');
    }

    private function generateReceiptPdf(Order $order): ?string
    {
        try {
            $order->load('items', 'user');
            $pdf = Pdf::loadView('pdf.receipt', compact('order'));
            $path = storage_path('app/receipts/receipt-' . $order->order_number . '.pdf');

            if (!is_dir(storage_path('app/receipts'))) {
                mkdir(storage_path('app/receipts'), 0755, true);
            }

            $pdf->save($path);
            return $path;
        } catch (\Exception $e) {
            logger()->error('PDF generation failed: ' . $e->getMessage());
            return null;
        }
    }
}
