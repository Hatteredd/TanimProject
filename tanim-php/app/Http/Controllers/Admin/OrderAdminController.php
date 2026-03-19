<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $orders   = $query->paginate(15)->withQueryString();
        $statuses = Order::statuses();

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', Order::statuses())],
        ]);

        $order->update($validated);
        $order->load('items.product', 'user');

        $pdfPath = $this->generateReceiptPdf($order);

        // Send status update email
        try {
            Mail::to($order->user->email)->send(new OrderStatusUpdatedMail($order, $pdfPath));
        } catch (\Exception $e) {
            logger()->error('Status email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Order status updated to ' . ucfirst($validated['status']) . '.');
    }

    public function charts()
    {
        // Yearly sales by month
        $yearlySales = Order::whereYear('created_at', now()->year)
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = [
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'total' => (float) ($yearlySales[$m]->total ?? 0),
                'count' => (int) ($yearlySales[$m]->count ?? 0),
            ];
        }

        // Sales per product (top 10)
        $productSales = \App\Models\OrderItem::selectRaw('product_name, SUM(subtotal) as total, SUM(quantity) as qty')
            ->groupBy('product_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('admin.charts', compact('monthlyData', 'productSales'));
    }

    public function salesByDateRange(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $sales = Order::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($sales);
    }

    private function generateReceiptPdf(Order $order): ?string
    {
        try {
            $pdf = Pdf::loadView('pdf.receipt', compact('order'));
            $path = storage_path('app/receipts/receipt-' . $order->order_number . '.pdf');

            if (!is_dir(storage_path('app/receipts'))) {
                mkdir(storage_path('app/receipts'), 0755, true);
            }

            $pdf->save($path);
            return $path;
        } catch (\Exception $e) {
            logger()->error('Status receipt generation failed: ' . $e->getMessage());
            return null;
        }
    }
}
