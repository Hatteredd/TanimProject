<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Order Confirmation</title></head>
<body style="font-family:Arial,sans-serif;background:#f9fafb;margin:0;padding:20px;">
<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
    <div style="background:linear-gradient(135deg,#16a34a,#15803d);padding:32px;text-align:center;">
        <h1 style="color:#fff;font-size:24px;margin:0;font-weight:900;">🌿 Tanim</h1>
        <p style="color:#bbf7d0;margin:8px 0 0;font-size:14px;">Philippine Agricultural Marketplace</p>
    </div>

    <div style="padding:32px;">
        <h2 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 8px;">Order Confirmed! 🎉</h2>
        <p style="color:#6b7280;font-size:14px;margin:0 0 24px;">Hi {{ $order->user->name }}, your order has been placed successfully.</p>

        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:16px;margin-bottom:24px;">
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                <span style="font-size:12px;color:#6b7280;font-weight:700;text-transform:uppercase;">Order Number</span>
                <span style="font-size:14px;font-weight:800;color:#15803d;">{{ $order->order_number }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="font-size:12px;color:#6b7280;font-weight:700;text-transform:uppercase;">Total Amount</span>
                <span style="font-size:16px;font-weight:900;color:#15803d;">₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <h3 style="font-size:14px;font-weight:700;color:#374151;margin:0 0 12px;text-transform:uppercase;letter-spacing:0.05em;">Items Ordered</h3>
        @foreach($order->items as $item)
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6;">
            <div>
                <p style="font-size:14px;font-weight:600;color:#111827;margin:0;">{{ $item->product_name }}</p>
                <p style="font-size:12px;color:#9ca3af;margin:2px 0 0;">{{ $item->quantity }} × ₱{{ number_format($item->unit_price, 2) }}</p>
            </div>
            <span style="font-size:14px;font-weight:700;color:#15803d;">₱{{ number_format($item->subtotal, 2) }}</span>
        </div>
        @endforeach

        <div style="margin-top:20px;padding:16px;background:#f9fafb;border-radius:8px;">
            <p style="font-size:12px;font-weight:700;color:#9ca3af;text-transform:uppercase;margin:0 0 6px;">Delivery Address</p>
            <p style="font-size:14px;color:#374151;margin:0;">{{ $order->shipping_address }}</p>
            <p style="font-size:13px;color:#6b7280;margin:4px 0 0;">📞 {{ $order->contact_number }}</p>
        </div>

        <div style="text-align:center;margin-top:18px;">
            <a href="{{ route('orders.receipt', $order) }}" style="display:inline-block;background:#111827;color:#ffffff;text-decoration:none;font-size:13px;font-weight:700;padding:10px 18px;border-radius:8px;">
                Download / Print Receipt
            </a>
        </div>

        <p style="font-size:13px;color:#6b7280;margin:20px 0 0;line-height:1.6;">
            A PDF receipt is attached to this email. You can also view your order status anytime by logging into your Tanim account.
        </p>
    </div>

    <div style="background:#f9fafb;padding:20px;text-align:center;border-top:1px solid #e5e7eb;">
        <p style="font-size:12px;color:#9ca3af;margin:0;">© {{ date('Y') }} Tanim Agricultural Marketplace · TUP-Taguig</p>
        <p style="font-size:11px;color:#d1d5db;margin:4px 0 0;">Developed by Pabalan &amp; Llegado</p>
    </div>
</div>
</body>
</html>
