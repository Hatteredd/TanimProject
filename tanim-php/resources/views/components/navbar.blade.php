<style>
.nav-wrap {
    position: sticky; top: 0; z-index: 100;
    background: var(--bg-glass);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border-bottom: 1px solid var(--border);
    box-shadow: 0 4px 20px rgba(28,18,8,0.06);
    transition: all var(--transition-base);
}
.nav-inner {
    max-width: 80rem; margin: 0 auto;
    padding: 0 1.5rem;
    display: flex; align-items: center; justify-content: space-between;
    height: 4.5rem;
}
.nav-logo {
    display: flex; align-items: center; gap: 0.75rem;
    text-decoration: none; flex-shrink: 0;
}
.nav-logo-icon {
    background: var(--primary-faint);
    border: 1px solid rgba(46,139,46,0.1);
    padding: 0.5rem; border-radius: 0.75rem;
    display: flex; align-items: center; justify-content: center;
    box-shadow: var(--shadow-neu-sm);
}
.nav-logo span {
    font-family: 'Outfit', sans-serif;
    font-weight: 900; font-size: 1.4rem;
    color: var(--text); letter-spacing: -0.02em;
}
.nav-links { display: flex; gap: 1.75rem; align-items: center; }
@media(max-width:768px) { .nav-links { display: none; } }
.nav-link {
    font-size: 0.9rem; font-weight: 700;
    color: var(--text-muted);
    text-decoration: none; transition: all var(--transition-fast);
}
.nav-link:hover { color: var(--primary); transform: translateY(-1px); }
.nav-link.active { color: var(--primary); }
.nav-right { display: flex; align-items: center; gap: 0.75rem; }

/* User Chip */
.user-chip {
    display: flex; align-items: center; gap: 0.6rem;
    padding: 0.35rem 0.75rem 0.35rem 0.35rem;
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: 9999px;
    text-decoration: none;
    transition: all var(--transition-fast);
}
.user-chip:hover {
    background: var(--bg);
    border-color: var(--border-glass);
    box-shadow: var(--shadow-neu-sm);
}
.user-avatar {
    width: 2rem; height: 2rem;
    border-radius: 9999px;
    object-fit: cover;
    border: 2px solid var(--primary-faint);
}
.user-name {
    font-size: 0.85rem; font-weight: 800;
    color: var(--text);
    max-width: 8rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}

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
            <div class="nav-logo-icon">
                <svg style="width:1.5rem;height:1.5rem;color:var(--primary);" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                </svg>
            </div>
            <span>Tanim</span>
        </a>

        {{-- Center links --}}
        <div class="nav-links">
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('marketplace') }}" class="nav-link {{ request()->routeIs('marketplace') ? 'active' : '' }}">Marketplace</a>
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin</a>
                @else
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">Orders</a>
                @endif
            @endauth
        </div>

        {{-- Right actions --}}
        <div class="nav-right">
            {{-- Theme Toggle --}}
            <button onclick="toggleTheme()" class="btn-ghost" style="width:2.25rem;height:2.25rem;padding:0;border-radius:9999px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;" title="Toggle Theme">
                <span id="theme-icon-sun" style="display:none;">☀️</span>
                <span id="theme-icon-moon" style="display:none;">🌙</span>
            </button>

            @auth
                {{-- Cart --}}
                @if(Auth::user()->role !== 'admin')
                <a href="{{ route('cart.index') }}" class="cart-btn" title="View Cart">
                    <svg style="width:1.25rem;height:1.25rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/>
                    </svg>
                    @php $cc = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity'); @endphp
                    @if($cc > 0)<span class="cart-badge">{{ $cc > 9 ? '9+' : $cc }}</span>@endif
                </a>
                @endif

                {{-- User chip --}}
                <a href="{{ route('profile.edit') }}" class="user-chip">
                    <img src="{{ Auth::user()->photoUrl() }}" class="user-avatar" alt="{{ Auth::user()->name }}" />
                    <span class="user-name">{{ Auth::user()->name }}</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-ghost" style="padding:0.5rem 1rem;font-size:0.85rem;">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-link" style="padding:0 0.5rem;">Sign In</a>
                <a href="{{ route('register') }}" class="btn-primary">Join Tanim</a>
            @endauth
        </div>
    </div>
</nav>

<script>
function updateThemeIcons() {
    const isDark = document.documentElement.classList.contains('dark');
    document.getElementById('theme-icon-sun').style.display = isDark ? 'inline-block' : 'none';
    document.getElementById('theme-icon-moon').style.display = isDark ? 'none' : 'inline-block';
}

function toggleTheme() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    updateThemeIcons();
}

// Initialize icons on load
updateThemeIcons();
</script>
