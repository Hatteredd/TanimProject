<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt — {{ $order->order_number }}</title>
    <style>
        @page { margin: 2.5mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "Courier New", "DejaVu Sans Mono", monospace;
            font-size: 10px;
            line-height: 1.35;
            color: #000;
            background: #fff;
        }
        .receipt {
            width: 100%;
            margin: 0;
            border: 1px solid #000;
            padding: 6px;
            overflow: hidden;
        }
        .center { text-align: center; }
        .shop-name { font-size: 16px; font-weight: 700; letter-spacing: 0.08em; }
        .title { font-size: 12px; font-weight: 700; margin-top: 4px; }
        .muted { font-size: 10px; }
        .rule {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .meta,
        .totals {
            width: 100%;
            border-collapse: collapse;
        }
        .meta td,
        .totals td {
            padding: 2px 0;
            vertical-align: top;
        }
        .label {
            width: 86px;
            font-weight: 700;
        }
        .item-table {
            width: 96%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-left: auto;
            margin-right: auto;
            table-layout: fixed;
        }
        .item-table th,
        .item-table td {
            border-bottom: 1px dashed #000;
            padding: 4px 2px;
            font-size: 10px;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .item-table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            font-size: 10px;
        }
        .t-right { text-align: right; }
        .t-center { text-align: center; }
        .totals {
            margin-top: 8px;
            width: 96%;
            margin-left: auto;
            margin-right: auto;
            table-layout: fixed;
        }
        .item-table th:last-child,
        .item-table td:last-child,
        .totals td:last-child {
            padding-right: 8px;
        }
        .totals .grand td {
            border-top: 1px solid #000;
            font-weight: 700;
            padding-top: 5px;
        }
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="center">
            <div class="shop-name">TANIM</div>
            <div class="muted">Philippine Agricultural Marketplace</div>
            <div class="title">OFFICIAL RECEIPT</div>
        </div>

        <div class="rule"></div>

        <table class="meta">
            <tr>
                <td class="label">Order No.</td>
                <td>: {{ $order->order_number }}</td>
            </tr>
            <tr>
                <td class="label">Date</td>
                <td>: {{ $order->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td>: {{ strtoupper($order->status) }}</td>
            </tr>
            <tr>
                <td class="label">Customer</td>
                <td>: {{ $order->user->name }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td>: {{ $order->user->email }}</td>
            </tr>
            <tr>
                <td class="label">Contact</td>
                <td>: {{ $order->contact_number }}</td>
            </tr>
            <tr>
                <td class="label">Address</td>
                <td>: {{ $order->shipping_address }}</td>
            </tr>
            @if($order->notes)
            <tr>
                <td class="label">Notes</td>
                <td>: {{ $order->notes }}</td>
            </tr>
            @endif
        </table>

        <div class="rule"></div>

        <table class="item-table">
            <thead>
                <tr>
                    <th style="width:48%;">Product</th>
                    <th class="t-center" style="width:10%;">Qty</th>
                    <th class="t-right" style="width:21%;">Unit Price</th>
                    <th class="t-right" style="width:21%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="t-center">{{ $item->quantity }}</td>
                    <td class="t-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="t-right">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="center">No line items found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td class="t-right" style="width:78%;">Subtotal</td>
                <td class="t-right" style="width:22%;">{{ number_format($order->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="t-right">Tax</td>
                <td class="t-right">0.00</td>
            </tr>
            <tr class="grand">
                <td class="t-right">TOTAL AMOUNT</td>
                <td class="t-right">{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </table>

        <div class="rule"></div>

        <div class="footer">
            <p>This receipt is generated electronically and is valid for printing.</p>
            <p>Thank you for your order.</p>
        </div>
    </div>
</body>
</html>
