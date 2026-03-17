@extends('layouts.app')
@section('title','Checkout — Tanim')
@section('content')
<div class="page-wrap-sm">
    <h1 class="section-title" style="margin-bottom:2rem;">Checkout</h1>

    @if($errors->any())
    <div class="alert-error" style="margin-bottom:1.5rem;">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 360px;gap:2rem;align-items:start;">
        <form method="POST" action="{{ route('orders.store') }}" class="page-card" style="padding:2rem;">
            @csrf
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--text);margin:0 0 1.5rem;">Delivery Details</h2>

            <div style="display:flex;flex-direction:column;gap:1.25rem;">
                <div>
                    <label class="label">Shipping Address *</label>
                    <textarea name="shipping_address" required rows="3" class="input" style="resize:vertical;"
                        placeholder="House/Unit No., Street, Barangay, City, Province">{{ old('shipping_address') }}</textarea>
                </div>

                <div>
                    <label class="label">Contact Number *</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number') }}" required class="input" placeholder="09XX-XXX-XXXX" />
                </div>

                <div>
                    <label class="label">Order Notes <span style="font-weight:400;color:var(--text-light);">(optional)</span></label>
                    <textarea name="notes" rows="2" class="input" style="resize:vertical;" placeholder="Special instructions...">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn-primary" style="padding:0.9rem;font-size:1rem;border-radius:0.75rem;">
                    🛒 Place Order — ₱{{ number_format($total, 2) }}
                </button>
            </div>
        </form>

        <div class="page-card" style="padding:1.5rem;">
            <h2 style="font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;">Order Summary</h2>
            @foreach($cartItems as $item)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.6rem 0;border-bottom:1px solid var(--border);">
                <div>
                    <p style="font-size:0.875rem;font-weight:600;color:var(--text);margin:0;">{{ $item->product->name }}</p>
                    <p style="font-size:0.75rem;color:var(--text-light);margin:0;">{{ $item->quantity }} × ₱{{ number_format($item->product->price, 2) }}</p>
                </div>
                <span style="font-size:0.875rem;font-weight:700;color:var(--primary);">₱{{ number_format($item->quantity * $item->product->price, 2) }}</span>
            </div>
            @endforeach
            <div style="display:flex;justify-content:space-between;align-items:center;padding-top:1rem;margin-top:0.5rem;">
                <span style="font-size:1rem;font-weight:800;color:var(--text);">Total</span>
                <span style="font-size:1.25rem;font-weight:900;color:var(--primary);">₱{{ number_format($total, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
