@extends('layouts.app')
@section('title','My Orders — Tanim')
@section('content')
<div class="page-wrap">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:2rem;">
        <div>
            <h1 class="section-title">My Orders</h1>
            <p style="color:var(--text-muted);font-size:0.9rem;margin:0.25rem 0 0;">Track and manage your purchases</p>
        </div>
        <a href="{{ route('marketplace') }}" class="btn-ghost" style="padding:0.6rem 1.25rem;font-size:0.875rem;border-radius:0.75rem;">Browse More</a>
    </div>

    @if(session('success'))
    <div class="alert-success" style="margin-bottom:1.5rem;">&#10003; {{ session('success') }}</div>
    @endif

    @if($orders->isEmpty())
    <div class="page-card" style="padding:4rem;text-align:center;">
        <div style="font-size:4rem;margin-bottom:1rem;">&#128230;</div>
        <h3 style="font-size:1.25rem;font-weight:700;color:var(--text);margin-bottom:0.5rem;">No orders yet</h3>
        <p style="color:var(--text-muted);margin-bottom:1.5rem;">Browse the marketplace and place your first order!</p>
        <a href="{{ route('marketplace') }}" class="btn-primary" style="padding:0.75rem 2rem;border-radius:0.75rem;">Browse Marketplace</a>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:1rem;">
        @foreach($orders as $order)
        <div class="page-card" style="padding:1.5rem;transition:transform .2s,box-shadow .2s;"
             onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='var(--shadow-hover)'"
             onmouseout="this.style.transform='none';this.style.boxShadow=''">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                    <div style="background:var(--primary-faint);width:3rem;height:3rem;border-radius:0.75rem;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">&#128230;</div>
                    <div>
                        <p style="font-size:0.95rem;font-weight:800;color:var(--text);margin:0 0 0.2rem;">{{ $order->order_number }}</p>
                        <p style="font-size:0.8rem;color:var(--text-muted);margin:0;">{{ $order->created_at->format('F d, Y') }} &middot; {{ $order->items->count() }} item(s)</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                    <span style="font-size:0.78rem;font-weight:700;padding:0.3rem 0.85rem;border-radius:9999px;background:{{ $order->statusBg() }};color:{{ $order->statusColor() }};">
                        {{ ucfirst($order->status) }}
                    </span>
                    <span style="font-size:1.1rem;font-weight:800;color:var(--primary);">&#8369;{{ number_format($order->total_amount, 2) }}</span>
                    <a href="{{ route('orders.show', $order) }}" class="btn-ghost" style="padding:0.45rem 1rem;font-size:0.8rem;border-radius:0.6rem;">View &#8594;</a>
                </div>
            </div>
            @if($order->items->count())
            <div class="divider"></div>
            <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                @foreach($order->items->take(4) as $item)
                <span style="font-size:0.78rem;font-weight:600;color:var(--text-muted);background:var(--bg);border:1px solid var(--border);padding:0.25rem 0.65rem;border-radius:9999px;">
                    {{ $item->product_name }} &times;{{ $item->quantity }}
                </span>
                @endforeach
                @if($order->items->count() > 4)
                <span style="font-size:0.78rem;color:var(--text-light);padding:0.25rem 0.5rem;">+{{ $order->items->count() - 4 }} more</span>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>
    <div style="margin-top:1.5rem;">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
