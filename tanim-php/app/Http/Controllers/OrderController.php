<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

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

        try {
            $order = DB::transaction(function () use ($validated) {
                $cartItems = CartItem::with('product')
                    ->where('user_id', Auth::id())
                    ->get();

                if ($cartItems->isEmpty()) {
                    throw new RuntimeException('Your cart is empty.');
                }

                $total = 0;

                $order = Order::create([
                    'user_id'          => Auth::id(),
                    'order_number'     => Order::generateOrderNumber(),
                    'status'           => 'pending',
                    'total_amount'     => 0,
                    'shipping_address' => $validated['shipping_address'],
                    'contact_number'   => $validated['contact_number'],
                    'notes'            => $validated['notes'] ?? null,
                ]);

                foreach ($cartItems as $item) {
                    $product = $item->product()->lockForUpdate()->first();
                    $productName = $item->product?->name ?? 'a selected item';

                    if (!$product || !$product->is_active || $product->stock < $item->quantity) {
                        throw new RuntimeException("Insufficient stock for {$productName}.");
                    }

                    $subtotal = $item->quantity * $product->price;
                    $total += $subtotal;

                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'unit_price'   => $product->price,
                        'quantity'     => $item->quantity,
                        'subtotal'     => $subtotal,
                    ]);

                    $product->decrement('stock', $item->quantity);
                }

                $order->update(['total_amount' => $total]);

                CartItem::where('user_id', Auth::id())->delete();

                return $order;
            });
        } catch (Throwable $e) {
            logger()->error('Order transaction failed: ' . $e->getMessage());
            $message = $e instanceof RuntimeException
                ? $e->getMessage()
                : 'Unable to place order right now. Please try again.';

            return redirect()->route('cart.index')->with('error', $message);
        }

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

        $pdf = Pdf::loadView('pdf.receipt', compact('order'))
            ->setPaper($this->receiptPaperSize($order), 'portrait');
        return $pdf->download('receipt-' . $order->order_number . '.pdf');
    }

    private function generateReceiptPdf(Order $order): ?string
    {
        try {
            $order->load('items.product', 'user');
            $pdf = Pdf::loadView('pdf.receipt', compact('order'))
                ->setPaper($this->receiptPaperSize($order), 'portrait');
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

    private function receiptPaperSize(Order $order): array
    {
        // Slightly wider receipt paper to avoid clipping in PDF renderers.
        $widthMm = 105;
        $baseHeightMm = 130;
        $perItemMm = 8;
        $notesMm = filled($order->notes) ? 12 : 0;
        $heightMm = max(140, $baseHeightMm + ($order->items->count() * $perItemMm) + $notesMm);

        $mmToPt = 2.83464567;

        return [0, 0, $widthMm * $mmToPt, $heightMm * $mmToPt];
    }
}
