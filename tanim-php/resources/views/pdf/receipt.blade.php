<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt — {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #16a34a; padding-bottom: 20px; }
        .logo { font-size: 28px; font-weight: 900; color: #16a34a; }
        .subtitle { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .receipt-title { font-size: 18px; font-weight: 700; color: #111827; margin-top: 12px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 24px; }
        .meta-block { }
        .meta-label { font-size: 10px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }
        .meta-value { font-size: 13px; font-weight: 600; color: #111827; margin-top: 3px; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #dcfce7; color: #15803d; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f9fafb; padding: 8px 10px; text-align: left; font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; }
        td { padding: 10px; border-bottom: 1px solid #f3f4f6; font-size: 12px; }
        .total-row td { font-weight: 700; font-size: 14px; border-top: 2px solid #e5e7eb; border-bottom: none; }
        .total-amount { color: #15803d; font-size: 16px; font-weight: 900; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 10px; color: #9ca3af; }
        .delivery { background: #f9fafb; border-radius: 8px; padding: 14px; margin-bottom: 20px; }
        .delivery h3 { font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 8px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🌿 Tanim</div>
        <div class="subtitle">Philippine Agricultural Marketplace · tanim.ph</div>
        <div class="receipt-title">Official Receipt</div>
    </div>

    <table style="margin-bottom:20px;border:none;">
        <tr>
            <td style="border:none;padding:4px 0;width:50%;">
                <div class="meta-label">Order Number</div>
                <div class="meta-value">{{ $order->order_number }}</div>
            </td>
            <td style="border:none;padding:4px 0;text-align:right;">
                <div class="meta-label">Date</div>
                <div class="meta-value">{{ $order->created_at->format('F d, Y') }}</div>
            </td>
        </tr>
        <tr>
            <td style="border:none;padding:4px 0;">
                <div class="meta-label">Customer</div>
                <div class="meta-value">{{ $order->user->name }}</div>
                <div style="font-size:11px;color:#6b7280;">{{ $order->user->email }}</div>
            </td>
            <td style="border:none;padding:4px 0;text-align:right;">
                <div class="meta-label">Status</div>
                <div style="margin-top:3px;"><span class="status-badge">{{ ucfirst($order->status) }}</span></div>
            </td>
        </tr>
    </table>

    <div class="delivery">
        <h3>Delivery Information</h3>
        <div style="font-size:12px;color:#374151;">{{ $order->shipping_address }}</div>
        <div style="font-size:11px;color:#6b7280;margin-top:4px;">Contact: {{ $order->contact_number }}</div>
        @if($order->notes)<div style="font-size:11px;color:#6b7280;margin-top:4px;">Notes: {{ $order->notes }}</div>@endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th style="text-align:center;">Qty</th>
                <th style="text-align:right;">Unit Price</th>
                <th style="text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td style="text-align:center;">{{ $item->quantity }}</td>
                <td style="text-align:right;">₱{{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align:right;">₱{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align:right;">Total Amount</td>
                <td style="text-align:right;" class="total-amount">₱{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Thank you for supporting Philippine agriculture through Tanim!</p>
        <p style="margin-top:6px;">This is a computer-generated receipt. No signature required.</p>
        <p style="margin-top:6px;">Tanim Agricultural Marketplace · Developed by Pabalan &amp; Llegado · TUP-Taguig</p>
    </div>
</body>
</html>
