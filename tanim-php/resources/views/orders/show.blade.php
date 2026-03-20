@extends('layouts.app')
@section('title','Order {{ $order->order_number }} — Tanim')
@section('content')
<div class="page-wrap-sm">
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:2rem;flex-wrap:wrap;">
        <a href="{{ route('orders.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:0.875rem;">&#8592; My Orders</a>
        <h1 class="section-title" style="font-size:1.5rem;">Order {{ $order->order_number }}</h1>
        <span style="font-size:0.8rem;font-weight:700;padding:0.3rem 0.85rem;border-radius:9999px;background:{{ $order->statusBg() }};color:{{ $order->statusColor() }};">{{ ucfirst($order->status) }}</span>
    </div>

    @if(session('success'))
    <div class="alert-success" style="margin-bottom:1.5rem;">&#10003; {{ session('success') }}</div>
    @endif

    <div style="display:grid;gap:1.5rem;">
        <div class="page-card" style="padding:1.5rem;">
            <h2 style="font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;">Order Items</h2>
            @foreach($order->items as $item)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                <div>
                    <p style="font-size:0.9rem;font-weight:700;color:var(--text);margin:0;">{{ $item->product_name }}</p>
                    <p style="font-size:0.8rem;color:var(--text-light);margin:0;">{{ $item->quantity }} x &#8369;{{ number_format($item->unit_price, 2) }}</p>
                    @if($item->product)
                    <a href="{{ route('products.show', $item->product) }}#reviews" style="font-size:0.78rem;color:var(--primary);font-weight:700;text-decoration:none;display:inline-block;margin-top:0.25rem;">View product details</a>
                    @endif
                    @if($order->status === 'delivered' && $item->product)
                        @php
                            $canReview = !\App\Models\Review::where('user_id', auth()->id())
                                ->where('product_id', $item->product->id)
                                ->exists();
                        @endphp
                        @if($canReview)
                        <a href="{{ route('products.show', $item->product) }}#reviews" style="font-size:0.78rem;color:var(--wheat-2);font-weight:700;text-decoration:none;display:inline-block;margin:0.25rem 0 0 0.6rem;">Write a review</a>
                        @endif
                    @endif
                </div>
                <span style="font-size:0.9rem;font-weight:700;color:var(--primary);">&#8369;{{ number_format($item->subtotal, 2) }}</span>
            </div>
            @endforeach
            <div style="display:flex;justify-content:space-between;padding-top:1rem;margin-top:0.5rem;">
                <span style="font-size:1rem;font-weight:800;color:var(--text);">Total</span>
                <span style="font-size:1.25rem;font-weight:900;color:var(--primary);">&#8369;{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <div class="page-card" style="padding:1.5rem;">
            <h2 style="font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;">Delivery Details</h2>
            <div style="display:grid;gap:0.75rem;">
                <div><span style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;">Shipping Address</span><p style="font-size:0.9rem;color:var(--text);margin:0.2rem 0 0;">{{ $order->shipping_address }}</p></div>
                <div><span style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;">Contact</span><p style="font-size:0.9rem;color:var(--text);margin:0.2rem 0 0;">{{ $order->contact_number }}</p></div>
                @if($order->notes)<div><span style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;">Notes</span><p style="font-size:0.9rem;color:var(--text);margin:0.2rem 0 0;">{{ $order->notes }}</p></div>@endif
                <div><span style="font-size:0.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;">Placed On</span><p style="font-size:0.9rem;color:var(--text);margin:0.2rem 0 0;">{{ $order->created_at->format('F d, Y h:i A') }}</p></div>
            </div>
        </div>

        <div style="display:flex;gap:1rem;">
            <a href="{{ route('orders.receipt', $order) }}" class="btn-primary" style="flex:1;text-align:center;padding:0.85rem;font-size:0.9rem;border-radius:0.75rem;">
                Download Receipt PDF
            </a>
            <a href="{{ route('marketplace') }}" style="flex:1;text-align:center;padding:0.85rem;background:var(--bg);color:var(--text-muted);font-weight:700;font-size:0.9rem;border-radius:0.75rem;text-decoration:none;border:1px solid var(--border);">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection
