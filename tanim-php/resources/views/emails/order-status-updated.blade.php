<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Order Status Update</title></head>
<body style="font-family:Arial,sans-serif;background:#f9fafb;margin:0;padding:20px;">
<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
    <div style="background:linear-gradient(135deg,#16a34a,#15803d);padding:32px;text-align:center;">
        <h1 style="color:#fff;font-size:24px;margin:0;font-weight:900;">🌿 Tanim</h1>
        <p style="color:#bbf7d0;margin:8px 0 0;font-size:14px;">Order Status Update</p>
    </div>

    <div style="padding:32px;">
        <h2 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 8px;">Your Order Status Has Changed</h2>
        <p style="color:#6b7280;font-size:14px;margin:0 0 24px;">Hi {{ $order->user->name }}, here's an update on your order.</p>

        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:20px;margin-bottom:24px;text-align:center;">
            <p style="font-size:12px;color:#6b7280;font-weight:700;text-transform:uppercase;margin:0 0 8px;">Order {{ $order->order_number }}</p>
            <p style="font-size:11px;color:#9ca3af;margin:0 0 12px;">New Status</p>
            <span style="display:inline-block;padding:8px 24px;border-radius:20px;font-size:16px;font-weight:800;background:{{ \App\Models\Order::statusBgColors()[$order->status] ?? 'rgba(107,114,128,0.12)' }};color:{{ $order->statusColor() }};">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        @php
        $messages = [
            'confirmed'  => 'Great news! Your order has been confirmed and is being prepared.',
            'processing' => 'Your order is currently being processed and packed.',
            'shipped'    => 'Your order is on its way! Expect delivery soon.',
            'delivered'  => 'Your order has been delivered. Enjoy your fresh produce!',
            'cancelled'  => 'Your order has been cancelled. Please contact us if you have questions.',
        ];
        @endphp
        @if(isset($messages[$order->status]))
        <p style="font-size:14px;color:#374151;line-height:1.6;margin:0 0 20px;">{{ $messages[$order->status] }}</p>
        @endif

        <div style="padding:16px;background:#f9fafb;border-radius:8px;">
            <p style="font-size:12px;font-weight:700;color:#9ca3af;text-transform:uppercase;margin:0 0 6px;">Order Total</p>
            <p style="font-size:18px;font-weight:900;color:#15803d;margin:0;">₱{{ number_format($order->total_amount, 2) }}</p>
        </div>
    </div>

    <div style="background:#f9fafb;padding:20px;text-align:center;border-top:1px solid #e5e7eb;">
        <p style="font-size:12px;color:#9ca3af;margin:0;">© {{ date('Y') }} Tanim Agricultural Marketplace · TUP-Taguig</p>
    </div>
</div>
</body>
</html>
