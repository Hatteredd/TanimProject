<style>
.nav-wrap {
    position: sticky; top: 0; z-index: 100;
    background: linear-gradient(180deg, rgba(250,247,240,0.95), rgba(250,247,240,0.88));
    backdrop-filter: blur(14px) saturate(130%);
    -webkit-backdrop-filter: blur(14px) saturate(130%);
    border-bottom: 1px solid var(--border);
    box-shadow: 0 6px 24px rgba(28,18,8,0.10);
    transition: background 0.35s ease, border-color 0.35s ease;
}
.nav-inner {
    max-width: 80rem; margin: 0 auto;
    padding: 0 1.5rem;
    display: flex; align-items: center; justify-content: space-between;
    height: 4rem;
}
.nav-logo {
    display: flex; align-items: center; gap: 0.5rem;
    text-decoration: none; flex-shrink: 0;
}
.nav-logo-icon {
    background: linear-gradient(135deg, rgba(46,139,46,0.12), rgba(212,168,67,0.08));
    border: 1px solid rgba(212,168,67,0.18);
    padding: 0.45rem; border-radius: 0.55rem;
    display: flex; align-items: center; justify-content: center;
}
.nav-logo span {
    font-family: 'Outfit', sans-serif;
    font-weight: 800; font-size: 1.3rem;
    color: var(--primary); letter-spacing: -0.02em;
}
.nav-links { display: flex; gap: 1.5rem; align-items: center; }
@media(max-width:680px) { .nav-links { display: none; } }
.nav-link {
    font-size: 0.875rem; font-weight: 600;
    color: var(--text-muted);
    text-decoration: none; transition: color 0.2s;
}
.nav-link:hover { color: var(--primary); }
.nav-right { display: flex; align-items: center; gap: 0.5rem; }

/* Cart bubble */
.cart-btn {
    position: relative;
    width: 2.25rem; height: 2.25rem; border-radius: 9999px;
    display: flex; align-items: center; justify-content: center;
    text-decoration: none; color: var(--text-muted);
    transition: background 0.2s, color 0.2s;
}
.cart-btn:hover { background: var(--primary-faint); color: var(--primary); }
.cart-badge {
    position: absolute; top: -2px; right: -2px;
    background: var(--primary); color: var(--primary-fg);
    font-size: 0.6rem; font-weight: 800;
    width: 1rem; height: 1rem; border-radius: 9999px;
    display: flex; align-items: center; justify-content: center;
}
</style>

<nav class="nav-wrap">
    <div class="nav-inner">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="nav-logo">
            <div class="nav-logo-icon" style="background:linear-gradient(135deg,rgba(46,139,46,0.15),rgba(212,168,67,0.10));border:1px solid rgba(212,168,67,0.20);">
                <svg style="width:1.3rem;height:1.3rem;color:var(--primary);" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                </svg>
            </div>
            <span style="font-family:'Outfit',sans-serif;font-weight:800;font-size:1.3rem;color:var(--primary);letter-spacing:-0.02em;">Tanim</span>
            <span style="font-size:0.65rem;font-weight:700;color:var(--text-light);letter-spacing:0.04em;text-transform:uppercase;margin-left:-0.2rem;align-self:flex-end;padding-bottom:0.1rem;">🌾</span>
        </a>

        {{-- Center links --}}
        <div class="nav-links">
            @auth
            @if(Auth::user()->role !== 'admin')
            <a href="{{ route('marketplace') }}" class="nav-link">Marketplace</a>
            @endif
            @if(Auth::user()->role !== 'admin')
            <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
            @endif
            @if(Auth::user()->role !== 'admin')
            <a href="{{ route('orders.index') }}" class="nav-link">My Orders</a>
            @endif
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="nav-link" style="color:var(--primary);font-weight:700;">Admin Panel</a>
            <a href="{{ route('marketplace') }}" class="nav-link">Marketplace</a>
            @endif
            @else
            <a href="{{ route('marketplace') }}" class="nav-link">Marketplace</a>
            @endauth
        </div>

        {{-- Right actions --}}
        <div class="nav-right">
            @auth
                {{-- Cart (buyers/farmers only) --}}
                @if(Auth::user()->role !== 'admin')
                <a href="{{ route('cart.index') }}" class="cart-btn" title="Cart">
                    <svg style="width:1.15rem;height:1.15rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/>
                    </svg>
                    @php $cc = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity'); @endphp
                    @if($cc > 0)<span class="cart-badge">{{ $cc > 9 ? '9+' : $cc }}</span>@endif
                </a>
                @endif

                {{-- User chip --}}
                <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:0.4rem;text-decoration:none;">
                    <img src="{{ Auth::user()->photoUrl() }}" style="width:1.75rem;height:1.75rem;border-radius:9999px;object-fit:cover;border:2px solid var(--primary-faint);" />
                    <span style="font-size:0.8rem;font-weight:700;color:var(--text);max-width:7rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Auth::user()->name }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-ghost" style="padding:0.4rem 0.9rem;font-size:0.8rem;border-radius:0.6rem;">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-link" style="padding:0 0.4rem;">Sign In</a>
                <a href="{{ route('register') }}" class="btn-primary" style="padding:0.45rem 1.1rem;font-size:0.85rem;border-radius:0.65rem;">Get Started</a>
            @endauth
        </div>
    </div>
</nav>
