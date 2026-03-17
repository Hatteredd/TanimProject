@extends('layouts.admin')
@section('title','Order {{ $order->order_number }}')
@section('page-title','&#128230; Order Details')

@section('content')
@if(session('success'))
<div class="alert-success" style="margin-bottom:1.5rem;">&#10003; {{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start;">
    <div style="display:flex;flex-direction:column;gap:1.5rem;">
        <div class="glass" style="border-radius:1.25rem;padding:1.5rem;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.25rem;">
                <div>
                    <h2 style="font-size:1.1rem;font-weight:800;color:var(--text);margin:0 0 0.25rem;">{{ $order->order_number }}</h2>
                    <p style="font-size:0.8rem;color:var(--text-muted);margin:0;">{{ $order->created_at->format('F d, Y h:i A') }}</p>
                </div>
                <span style="font-size:0.8rem;font-weight:700;padding:0.3rem 0.85rem;border-radius:9999px;background:{{ $order->statusBg() }};color:{{ $order->statusColor() }};">{{ ucfirst($order->status) }}</span>
            </div>

            <table style="width:100%;border-collapse:collapse;">
                <thead><tr style="border-bottom:1px solid var(--border);">
                    <th class="th-cell" style="text-align:left;padding-left:0;">Product</th>
                    <th class="th-cell" style="text-align:center;">Qty</th>
                    <th class="th-cell" style="text-align:right;">Price</th>
                    <th class="th-cell" style="text-align:right;">Subtotal</th>
                </tr></thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.75rem 0;font-size:0.875rem;color:var(--text);font-weight:600;">{{ $item->product_name }}</td>
                        <td style="padding:0.75rem 0;text-align:center;font-size:0.875rem;color:var(--text-muted);">{{ $item->quantity }}</td>
                        <td style="padding:0.75rem 0;text-align:right;font-size:0.875rem;color:var(--text-muted);">&#8369;{{ number_format($item->unit_price, 2) }}</td>
                        <td style="padding:0.75rem 0;text-align:right;font-size:0.875rem;font-weight:700;color:var(--primary);">&#8369;{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="3" style="padding:0.75rem 0;text-align:right;font-size:1rem;font-weight:800;color:var(--text);">Total</td>
                        <td style="padding:0.75rem 0;text-align:right;font-size:1.1rem;font-weight:900;color:var(--primary);">&#8369;{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="glass" style="border-radius:1.25rem;padding:1.5rem;">
            <h3 style="font-size:0.95rem;font-weight:800;color:var(--text);margin:0 0 1rem;">Delivery Details</h3>
            <div style="display:grid;gap:0.75rem;">
                <div><p style="font-size:0.72rem;font-weight:700;color:var(--text-light);text-transform:uppercase;margin:0 0 0.2rem;">Customer</p><p style="font-size:0.875rem;color:var(--text);margin:0;">{{ $order->user->name }} &middot; {{ $order->user->email }}</p></div>
                <div><p style="font-size:0.72rem;font-weight:700;color:var(--text-light);text-transform:uppercase;margin:0 0 0.2rem;">Address</p><p style="font-size:0.875rem;color:var(--text);margin:0;">{{ $order->shipping_address }}</p></div>
                <div><p style="font-size:0.72rem;font-weight:700;color:var(--text-light);text-transform:uppercase;margin:0 0 0.2rem;">Contact</p><p style="font-size:0.875rem;color:var(--text);margin:0;">{{ $order->contact_number }}</p></div>
                @if($order->notes)<div><p style="font-size:0.72rem;font-weight:700;color:var(--text-light);text-transform:uppercase;margin:0 0 0.2rem;">Notes</p><p style="font-size:0.875rem;color:var(--text);margin:0;">{{ $order->notes }}</p></div>@endif
            </div>
        </div>
    </div>

    <div class="glass" style="border-radius:1.25rem;padding:1.5rem;">
        <h3 style="font-size:0.95rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;">Update Status</h3>
        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
            @csrf
            <div style="display:flex;flex-direction:column;gap:0.75rem;">
                @foreach(\App\Models\Order::statuses() as $status)
                @php $sc = \App\Models\Order::statusColors()[$status]; $sb = \App\Models\Order::statusBgColors()[$status]; @endphp
                <label style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;border:1.5px solid {{ $order->status === $status ? $sc : 'var(--border)' }};border-radius:0.75rem;cursor:pointer;background:{{ $order->status === $status ? $sb : 'transparent' }};">
                    <input type="radio" name="status" value="{{ $status }}" {{ $order->status === $status ? 'checked' : '' }} style="accent-color:{{ $sc }};" />
                    <span style="font-size:0.875rem;font-weight:600;color:{{ $sc }};">{{ ucfirst($status) }}</span>
                </label>
                @endforeach
                <button type="submit" class="btn-primary" style="margin-top:0.5rem;padding:0.75rem;font-size:0.875rem;border-radius:0.75rem;">
                    Update Status
                </button>
            </div>
        </form>

        <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border);">
            <a href="{{ route('orders.receipt', $order) }}" class="btn-ghost" style="display:block;text-align:center;padding:0.65rem;font-size:0.875rem;border-radius:0.75rem;">
                Download Receipt
            </a>
        </div>
    </div>
</div>
@endsection
