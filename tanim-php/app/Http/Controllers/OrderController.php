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
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class OrderController extends Controller
{
    public function index()
    {
        abort_if(Auth::user()->role === 'admin', 403, 'Admins cannot access customer orders this way.');

        $orders = Order::with('items')->withComputedTotal()
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

        $subtotal = $cartItems->sum(fn($i) => $i->quantity * $i->product->price);
        $tax = $subtotal * Order::VAT_RATE;
        $shipping_fee = Order::SHIPPING_FEE;
        $total = $subtotal + $tax + $shipping_fee;
        $paymentMethods = Order::paymentMethods();

        return view('orders.checkout', compact('cartItems', 'subtotal', 'tax', 'shipping_fee', 'total', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()->role === 'admin', 403, 'Admins cannot place orders.');

        $validated = $request->validate([
            'shipping_address' => ['required', 'string', 'max:500'],
            'contact_number'   => ['required', 'string', 'regex:/^(?:\+63|0)9\d{9}$/'],
            'notes'            => ['nullable', 'string', 'max:500'],
            'payment_method'   => ['required', 'in:cod,gcash,bank_transfer'],
        ], [
            'contact_number.regex' => 'Please enter a valid Philippine mobile number.',
        ]);

        $validated['contact_number'] = preg_replace('/\s+|-/', '', $validated['contact_number']);

        try {
            $order = DB::transaction(function () use ($validated) {
                $cartItems = CartItem::with('product')
                    ->where('user_id', Auth::id())
                    ->get();

                if ($cartItems->isEmpty()) {
                    throw new RuntimeException('Your cart is empty.');
                }

                $subtotal = $cartItems->sum(fn($i) => $i->quantity * $i->product->price);
                $tax = $subtotal * Order::VAT_RATE;
                $shipping_fee = Order::SHIPPING_FEE;
                $totalAmount = $subtotal + $tax + $shipping_fee;

                $orderPayload = [
                    'user_id'          => Auth::id(),
                    'order_number'     => Order::generateOrderNumber(),
                    'status'           => 'pending',
                    'shipping_address' => $validated['shipping_address'],
                    'contact_number'   => $validated['contact_number'],
                    'notes'            => $validated['notes'] ?? null,
                    // 'total_amount'     => $totalAmount, // removed, now computed
                ];

                if (\Schema::hasColumn('orders', 'payment_method')) {
                    $orderPayload['payment_method'] = $validated['payment_method'];
                }

                $order = Order::create($orderPayload);

                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id'    => $order->id,
                        'product_id'  => $item->product_id,
                        'product_name'=> $item->product->name,
                        'quantity'    => $item->quantity,
                        'unit_price'  => $item->product->price,
                    ]);
                }

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
