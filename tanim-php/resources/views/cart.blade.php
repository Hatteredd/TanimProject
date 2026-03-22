@extends('layouts.app')
@section('content')
<div style="min-height:60vh;padding:2.5rem 0;">
<div style="max-width:56rem;margin:0 auto;padding:0 1.5rem;">

    @php
        $backUrl = url()->previous() !== url()->current() ? url()->previous() : route('marketplace');
    @endphp

    <div style="margin-bottom:0.9rem;">
        <a href="{{ $backUrl }}" class="btn-ghost" style="padding:0.45rem 0.95rem;font-size:0.82rem;border-radius:0.65rem;">&larr; Back</a>
    </div>

    <div style="margin-bottom:2rem;">
        <h1 class="section-title">&#128722; Your Cart</h1>
        <p style="color:var(--text-muted);font-size:0.95rem;margin:0.25rem 0 0;">Review your items before checkout</p>
    </div>

    @if(session('cart_success'))
    <div class="alert-success" style="margin-bottom:1.5rem;">&#10003; {{ session('cart_success') }}</div>
    @endif

    @if($items->isEmpty())
    <div class="page-card" style="padding:4rem;text-align:center;">
        <div style="font-size:4rem;margin-bottom:1rem;">&#128722;</div>
        <h3 style="font-size:1.25rem;font-weight:700;color:var(--text);margin-bottom:0.5rem;">Your cart is empty</h3>
        <p style="color:var(--text-muted);margin-bottom:1.5rem;">Browse the marketplace and add some fresh produce!</p>
        <a href="{{ route('marketplace') }}" class="btn-primary" style="padding:0.75rem 2rem;border-radius:0.75rem;">Browse Marketplace</a>
    </div>
    @else
    <div style="display:grid;gap:1rem;margin-bottom:1.5rem;">
        @foreach($items as $item)
        <div class="page-card" style="padding:1.25rem 1.5rem;display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;">
            <div style="background:linear-gradient(135deg,var(--primary-soft),var(--primary-faint));width:4rem;height:4rem;border-radius:0.75rem;display:flex;align-items:center;justify-content:center;font-size:1.75rem;flex-shrink:0;">
                @php $icons = ['Vegetables'=>'🥦','Fruits'=>'🍓','Grains & Rice'=>'🌾','Root Crops'=>'🥔','Herbs & Spices'=>'🌿']; echo $icons[$item->product->category] ?? '🛒'; @endphp
            </div>
            <div style="flex:1;min-width:140px;">
                <p style="font-weight:700;color:var(--text);font-size:0.95rem;margin:0 0 0.2rem;">{{ $item->product->name }}</p>
                <p style="font-size:0.8rem;color:var(--text-muted);margin:0;">&#8369;{{ number_format($item->product->price, 2) }} / {{ $item->product->unit }}</p>
            </div>
            <form method="POST" action="{{ route('cart.update', $item) }}" style="display:flex;align-items:center;gap:0.5rem;">
                @csrf @method('PATCH')
                <button type="button" onclick="const current=parseInt(this.form.quantity.value||'1',10)||1; this.form.quantity.value=Math.max(1,current-1); this.form.submit();"
                    style="width:2rem;height:2rem;border:1.5px solid var(--border);border-radius:0.5rem;background:var(--bg);font-size:1rem;cursor:pointer;color:var(--text);">&#8722;</button>
                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}"
                    onchange="this.value=Math.max(1, parseInt(this.value||'1',10)||1); this.form.submit();"
                    style="width:3.5rem;text-align:center;border:1.5px solid var(--border);border-radius:0.5rem;padding:0.3rem;font-size:0.9rem;font-weight:600;color:var(--text);background:var(--bg);" />
                <button type="button" onclick="const current=parseInt(this.form.quantity.value||'1',10)||1; this.form.quantity.value=Math.min({{ $item->product->stock }},current+1); this.form.submit();"
                    style="width:2rem;height:2rem;border:1.5px solid var(--border);border-radius:0.5rem;background:var(--bg);font-size:1rem;cursor:pointer;color:var(--text);">+</button>
            </form>
            <div style="min-width:80px;text-align:right;">
                <span style="font-size:1rem;font-weight:800;color:var(--primary);">&#8369;{{ number_format($item->quantity * $item->product->price, 2) }}</span>
            </div>
            <form method="POST" action="{{ route('cart.remove', $item) }}">
                @csrf @method('DELETE')
                <button type="submit" title="Remove" style="background:none;border:none;cursor:pointer;color:var(--text-light);padding:0.25rem;"
                    onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-light)'">
                    <svg style="width:1.1rem;height:1.1rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                </button>
            </form>
        </div>
        @endforeach
    </div>

    <div class="page-card" style="padding:1.5rem 2rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <span style="font-size:0.95rem;color:var(--text-muted);font-weight:500;">Subtotal</span>
            <span style="font-size:1.5rem;font-weight:800;color:var(--primary);">&#8369;{{ number_format($total, 2) }}</span>
        </div>
        <p style="font-size:0.8rem;color:var(--text-light);margin:0 0 1.25rem;">Delivery charges calculated at checkout</p>
        <a href="{{ route('orders.checkout') }}" class="btn-primary" style="display:block;text-align:center;padding:0.9rem;font-size:1rem;border-radius:0.75rem;margin-bottom:0.75rem;">
            Proceed to Checkout
        </a>
        <a href="{{ route('marketplace') }}" style="display:block;text-align:center;color:var(--primary);font-size:0.875rem;font-weight:600;text-decoration:none;">
            Continue Shopping
        </a>
    </div>
    @endif

</div>
</div>
@endsection
