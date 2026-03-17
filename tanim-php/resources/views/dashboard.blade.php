@extends('layouts.app')
@section('content')
<div style="min-height:80vh;padding:2.5rem 0;">
<div style="max-width:72rem;margin:0 auto;padding:0 1.5rem;">

    {{-- Welcome header --}}
    <div style="background:linear-gradient(135deg,var(--primary),var(--primary-2));border-radius:1.25rem;padding:2rem 2.5rem;margin-bottom:2rem;color:var(--primary-fg);position:relative;overflow:hidden;">
        <div style="position:absolute;top:-40px;right:-40px;width:160px;height:160px;background:rgba(255,255,255,0.06);border-radius:9999px;pointer-events:none;"></div>
        <p style="font-size:0.85rem;font-weight:600;opacity:0.8;margin:0 0 0.4rem;letter-spacing:0.05em;text-transform:uppercase;">Welcome back</p>
        <h1 style="font-family:Outfit,sans-serif;font-size:1.75rem;font-weight:800;margin:0 0 0.5rem;">{{ $user->name }} &#128075;</h1>
        <p style="font-size:0.9rem;opacity:0.85;margin:0;">Role: <strong>{{ ucfirst($user->role) }}</strong> &nbsp;&middot;&nbsp; {{ $user->email }}</p>
    </div>

    {{-- Quick stats --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:2rem;">
        @foreach([
            ['🛒', 'Cart Items', $cartCount . ' items'],
            ['💰', 'Cart Total', '₱' . number_format($cartTotal, 2)],
            ['📦', 'My Orders', 'View all'],
            ['📅', 'Member Since', $user->created_at->format('M Y')],
        ] as [$icon, $label, $value])
        <div class="stat-card">
            <span style="font-size:1.6rem;display:block;margin-bottom:0.5rem;">{{ $icon }}</span>
            <p style="font-size:0.72rem;font-weight:700;color:var(--text-muted);margin:0 0 0.2rem;text-transform:uppercase;letter-spacing:0.05em;">{{ $label }}</p>
            <p style="font-size:1.1rem;font-weight:800;color:var(--text);margin:0;">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
        {{-- Cart preview --}}
        <div class="page-card" style="padding:1.5rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
                <h2 style="font-family:Outfit,sans-serif;font-size:1.1rem;font-weight:800;color:var(--text);margin:0;">Your Cart</h2>
                <a href="{{ route('cart.index') }}" style="font-size:0.8rem;font-weight:600;color:var(--primary);text-decoration:none;">View All →</a>
            </div>
            @if($cartItems->isEmpty())
            <div style="text-align:center;padding:2rem 0;color:var(--text-muted);">
                <div style="font-size:2.5rem;margin-bottom:0.75rem;">&#128722;</div>
                <p style="font-size:0.875rem;margin:0;">Your cart is empty</p>
                <a href="{{ route('marketplace') }}" style="display:inline-block;margin-top:1rem;font-size:0.875rem;font-weight:600;color:var(--primary);text-decoration:none;">Browse Marketplace →</a>
            </div>
            @else
            <div style="display:flex;flex-direction:column;gap:0.75rem;">
                @foreach($cartItems->take(4) as $item)
                <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;background:var(--bg);border-radius:0.75rem;border:1px solid var(--border);">
                    <div style="font-size:1.5rem;width:2.5rem;text-align:center;">
                        @php $icons = ['Vegetables'=>'🥦','Fruits'=>'🍓','Grains & Rice'=>'🌾','Root Crops'=>'🥔','Herbs & Spices'=>'🌿']; echo $icons[$item->product->category] ?? '🛒'; @endphp
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:0.85rem;font-weight:700;color:var(--text);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item->product->name }}</p>
                        <p style="font-size:0.75rem;color:var(--text-muted);margin:0;">{{ $item->quantity }} &times; &#8369;{{ number_format($item->product->price, 2) }}</p>
                    </div>
                    <span style="font-size:0.9rem;font-weight:800;color:var(--primary);">&#8369;{{ number_format($item->quantity * $item->product->price, 2) }}</span>
                </div>
                @endforeach
                @if($cartItems->count() > 4)
                <p style="font-size:0.8rem;color:var(--text-light);text-align:center;margin:0;">+{{ $cartItems->count() - 4 }} more items</p>
                @endif
                <div style="border-top:1px solid var(--border);padding-top:0.75rem;margin-top:0.25rem;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-weight:700;color:var(--text-muted);font-size:0.9rem;">Total</span>
                    <span style="font-size:1.1rem;font-weight:800;color:var(--primary);">&#8369;{{ number_format($cartTotal, 2) }}</span>
                </div>
                <a href="{{ route('cart.index') }}" class="btn-primary" style="display:block;text-align:center;padding:0.7rem;font-size:0.875rem;border-radius:0.65rem;">View Cart &amp; Checkout →</a>
            </div>
            @endif
        </div>

        {{-- Quick links --}}
        <div style="display:flex;flex-direction:column;gap:1rem;">
            <div class="page-card" style="padding:1.5rem;">
                <h2 style="font-family:Outfit,sans-serif;font-size:1.1rem;font-weight:800;color:var(--text);margin:0 0 1rem;">Quick Actions</h2>
                <div style="display:flex;flex-direction:column;gap:0.6rem;">
                    <a href="{{ route('marketplace') }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1rem;background:var(--primary-faint);border:1px solid var(--border-glass);border-radius:0.85rem;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--primary-soft)'" onmouseout="this.style.background='var(--primary-faint)'">
                        <span style="font-size:1.3rem;">&#127807;</span>
                        <span style="font-size:0.9rem;font-weight:600;color:var(--primary);">Browse Marketplace</span>
                        <span style="margin-left:auto;color:var(--primary);opacity:0.6;">→</span>
                    </a>
                    <a href="{{ route('cart.index') }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1rem;background:var(--bg-glass);border:1px solid var(--border-glass);border-radius:0.85rem;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--primary-faint)'" onmouseout="this.style.background='var(--bg-glass)'">
                        <span style="font-size:1.3rem;">&#128722;</span>
                        <span style="font-size:0.9rem;font-weight:600;color:var(--text);">View My Cart</span>
                        <span style="margin-left:auto;color:var(--text-muted);opacity:0.6;">→</span>
                    </a>
                    <a href="{{ route('orders.index') }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1rem;background:var(--bg-glass);border:1px solid var(--border-glass);border-radius:0.85rem;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--primary-faint)'" onmouseout="this.style.background='var(--bg-glass)'">
                        <span style="font-size:1.3rem;">&#128230;</span>
                        <span style="font-size:0.9rem;font-weight:600;color:var(--text);">My Orders</span>
                        <span style="margin-left:auto;color:var(--text-muted);opacity:0.6;">→</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1rem;background:var(--bg-glass);border:1px solid var(--border-glass);border-radius:0.85rem;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--primary-faint)'" onmouseout="this.style.background='var(--bg-glass)'">
                        <span style="font-size:1.3rem;">&#128100;</span>
                        <span style="font-size:0.9rem;font-weight:600;color:var(--text);">Account Settings</span>
                        <span style="margin-left:auto;color:var(--text-muted);opacity:0.6;">→</span>
                    </a>
                </div>
            </div>

            <div style="background:var(--primary-faint);border:1px solid rgba(22,163,74,0.2);border-radius:1.25rem;padding:1.5rem;">
                <h3 style="font-size:0.9rem;font-weight:800;color:var(--primary-text);margin:0 0 0.5rem;">
                    @if($user->role === 'farmer') &#127806; Farmer Account @else &#128717; Buyer Account @endif
                </h3>
                <p style="font-size:0.8rem;color:var(--text-muted);margin:0;">
                    @if($user->role === 'farmer')
                        You can list your harvest on the marketplace and manage your orders from here.
                    @else
                        Browse fresh produce from local farmers and manage your cart here.
                    @endif
                </p>
            </div>
        </div>
    </div>

</div>
</div>
@endsection
