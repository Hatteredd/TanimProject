<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Tanim Admin</title>
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800,900|dm-sans:400,500,600,700" rel="stylesheet">
    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css','resources/js/app.js'])
    @else
        @php
            $fallbackCss = file_get_contents(resource_path('css/app.css')) ?: '';
            $fallbackCss = preg_replace('/^\s*@import\s+"tailwindcss";\s*$/m', '', $fallbackCss);
        @endphp
        <style>{!! $fallbackCss !!}</style>
    @endif
    <script>(function(){const s=localStorage.getItem('tanim-theme');if(s==='dark'||(!s&&window.matchMedia('(prefers-color-scheme:dark)').matches))document.documentElement.classList.add('dark')})();</script>
    <style>
    /* ── Shell ── */
    .admin-shell { display:flex; min-height:100vh; background:var(--bg); }

    /* ── Sidebar ── */
    .admin-sidebar {
        width:15rem; flex-shrink:0;
        background:var(--bg-glass-2);
        backdrop-filter:var(--glass-blur);
        -webkit-backdrop-filter:var(--glass-blur);
        border-right:1px solid var(--border);
        display:flex; flex-direction:column;
        position:sticky; top:0; height:100vh; overflow-y:auto;
        box-shadow:var(--shadow-card);
        transition:background .35s,border .35s;
    }
    .admin-main { flex:1; min-width:0; display:flex; flex-direction:column; }

    /* ── Sidebar logo ── */
    .sidebar-logo {
        display:flex; align-items:center; gap:.6rem;
        padding:1.25rem 1rem .9rem;
        border-bottom:1px solid var(--border);
        text-decoration:none; flex-shrink:0;
    }
    .sidebar-logo-text { font-family:'Outfit',sans-serif; font-size:1.15rem; font-weight:800; color:var(--primary); }
    .sidebar-badge { font-size:.6rem; font-weight:800; background:var(--wheat-soft); color:var(--wheat-2); padding:.15rem .5rem; border-radius:9999px; border:1px solid rgba(212,168,67,.25); }

    /* ── Nav groups ── */
    .sidebar-nav { padding:.6rem .6rem; flex:1; display:flex; flex-direction:column; gap:.1rem; overflow-y:auto; }
    .nav-group-label {
        font-size:.6rem; font-weight:800; color:var(--text-light);
        text-transform:uppercase; letter-spacing:.08em;
        padding:.75rem .5rem .3rem; margin-top:.25rem;
    }
    .nav-item {
        display:flex; align-items:center; gap:.55rem;
        padding:.55rem .75rem; border-radius:.75rem;
        font-size:.82rem; font-weight:600; color:var(--text-muted);
        text-decoration:none; transition:all .18s; white-space:nowrap;
    }
    .nav-item:hover { background:var(--primary-faint); color:var(--primary); }
    .nav-item.active { background:var(--primary-faint); color:var(--primary); box-shadow:var(--shadow-neu-sm); font-weight:700; }
    .nav-item .nav-icon { font-size:.95rem; flex-shrink:0; width:1.1rem; text-align:center; }

    /* ── Sidebar bottom ── */
    .sidebar-bottom { padding:.6rem; border-top:1px solid var(--border); flex-shrink:0; }

    /* ── Topbar ── */
    .admin-topbar {
        background:var(--bg-glass); backdrop-filter:var(--glass-blur); -webkit-backdrop-filter:var(--glass-blur);
        border-bottom:1px solid var(--border);
        padding:.65rem 1.5rem; display:flex; align-items:center; justify-content:space-between;
        position:sticky; top:0; z-index:20; flex-shrink:0;
    }

    /* ── Page content ── */
    .page-content { padding:1.75rem 1.5rem; flex:1; }

    /* ── Mobile ── */
    @media(max-width:900px){
        .admin-sidebar { display:none; }
        .page-content { padding:1rem; }
    }

    /* ── Breadcrumb ── */
    .admin-breadcrumb { display:flex; align-items:center; gap:.4rem; font-size:.75rem; color:var(--text-muted); }
    .admin-breadcrumb a { color:var(--text-muted); text-decoration:none; }
    .admin-breadcrumb a:hover { color:var(--primary); }
    </style>
</head>
<body>
<div class="admin-shell">

    {{-- ── SIDEBAR ── --}}
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
            <div style="background:linear-gradient(135deg,rgba(46,139,46,.15),rgba(212,168,67,.10));border:1px solid rgba(212,168,67,.20);padding:.4rem;border-radius:.5rem;display:flex;align-items:center;justify-content:center;">
                <svg style="width:1.1rem;height:1.1rem;color:var(--primary);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
            </div>
            <div>
                <div class="sidebar-logo-text">Tanim</div>
                <div class="sidebar-badge">Admin Panel</div>
            </div>
        </a>

        <nav class="sidebar-nav">
            @php $r = request()->route()->getName(); @endphp

            {{-- Overview --}}
            <div class="nav-group-label">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ $r === 'admin.dashboard' ? 'active' : '' }}">
                <span class="nav-icon">📊</span> Dashboard
            </a>
            <a href="{{ route('admin.reports') }}" class="nav-item {{ $r === 'admin.reports' ? 'active' : '' }}">
                <span class="nav-icon">📈</span> Reports & Analytics
            </a>

            {{-- User Management --}}
            <div class="nav-group-label">User Management</div>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ str_starts_with($r,'admin.users') ? 'active' : '' }}">
                <span class="nav-icon">👤</span> Users
            </a>
            <a href="{{ route('admin.admins.index') }}" class="nav-item {{ request('role') === 'admin' ? 'active' : '' }}">
                <span class="nav-icon">🛡️</span> Manage Admins
            </a>

            {{-- Content Management --}}
            <div class="nav-group-label">Content</div>
            <a href="{{ route('marketplace') }}" class="nav-item {{ $r === 'marketplace' ? 'active' : '' }}">
                <span class="nav-icon">🛒</span> View Marketplace
            </a>
            <a href="{{ route('admin.products.index') }}" class="nav-item {{ str_starts_with($r,'admin.products') ? 'active' : '' }}">
                <span class="nav-icon">🌾</span> Products
            </a>
            <a href="{{ route('admin.reviews.index') }}" class="nav-item {{ $r === 'admin.reviews.index' ? 'active' : '' }}">
                <span class="nav-icon">⭐</span> Reviews
            </a>

            {{-- Orders & Sales --}}
            <div class="nav-group-label">Orders & Sales</div>
            <a href="{{ route('admin.orders.index') }}" class="nav-item {{ str_starts_with($r,'admin.orders') ? 'active' : '' }}">
                <span class="nav-icon">📦</span> Orders
            </a>
            <a href="{{ route('admin.expenses') }}" class="nav-item {{ $r === 'admin.expenses' ? 'active' : '' }}">
                <span class="nav-icon">💰</span> Expenses
            </a>
            <a href="{{ route('admin.suppliers') }}" class="nav-item {{ str_starts_with($r,'admin.suppliers') ? 'active' : '' }}">
                <span class="nav-icon">🚚</span> Suppliers
            </a>

            {{-- Data Management --}}
            <div class="nav-group-label">Data</div>
            <a href="{{ route('admin.data.index') }}" class="nav-item {{ str_starts_with($r,'admin.data') ? 'active' : '' }}">
                <span class="nav-icon">🗄️</span> Database Records
            </a>

            {{-- System --}}
            <div class="nav-group-label">System</div>
            <a href="{{ route('admin.logs') }}" class="nav-item {{ $r === 'admin.logs' ? 'active' : '' }}">
                <span class="nav-icon">📋</span> Activity Logs
            </a>

            <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid var(--border);">
                <a href="{{ route('home') }}" class="nav-item">
                    <span class="nav-icon">🌐</span> Back to Site
                </a>
            </div>
        </nav>

        <div class="sidebar-bottom">
            <div style="display:flex;align-items:center;gap:.5rem;padding:.4rem .5rem .6rem;">
                <div style="width:2rem;height:2rem;border-radius:9999px;background:linear-gradient(135deg,var(--primary),var(--primary-2));display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:800;color:#fff;flex-shrink:0;">
                    {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                </div>
                <div style="min-width:0;">
                    <p style="font-size:.78rem;font-weight:700;color:var(--text);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Auth::user()->name }}</p>
                    <p style="font-size:.65rem;color:var(--text-muted);margin:0;">Administrator</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" style="width:100%;padding:.45rem;background:var(--danger-soft);color:var(--danger);font-weight:700;font-size:.78rem;border:1px solid var(--danger-border);border-radius:.6rem;cursor:pointer;">
                    ↩ Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN ── --}}
    <div class="admin-main">
        <div class="admin-topbar">
            <div>
                <h1 style="font-family:'Outfit',sans-serif;font-size:1rem;font-weight:800;color:var(--text);margin:0;">@yield('page-title','Dashboard')</h1>
                <p style="font-size:.7rem;color:var(--text-muted);margin:0;">{{ now()->format('l, F d, Y') }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:.6rem;">
                {{-- Quick stats in topbar --}}
                @php
                    $pendingOrders = \App\Models\Order::where('status','pending')->count();
                @endphp
                @if($pendingOrders > 0)
                <a href="{{ route('admin.orders.index', ['status'=>'pending']) }}" style="display:flex;align-items:center;gap:.35rem;background:var(--warn-soft);border:1px solid var(--warn-border);border-radius:.6rem;padding:.3rem .7rem;text-decoration:none;">
                    <span style="font-size:.7rem;font-weight:800;color:var(--warn-text);">📦 {{ $pendingOrders }} pending</span>
                </a>
                @endif
                <button onclick="toggleTheme()" style="width:2rem;height:2rem;border-radius:9999px;border:none;background:var(--primary-faint);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--primary);" title="Toggle theme">
                    <svg id="icon-sun" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                    <svg id="icon-moon" style="width:1rem;height:1rem;display:none;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                </button>
            </div>
        </div>

        @if(session('success'))
        <div style="margin:.75rem 1.5rem 0;background:var(--primary-soft);border:1px solid var(--primary-mid);border-left:3px solid var(--primary);border-radius:.75rem;padding:.65rem 1rem;font-size:.85rem;font-weight:600;color:var(--primary-text);">
            ✓ {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div style="margin:.75rem 1.5rem 0;background:var(--danger-soft);border:1px solid var(--danger-border);border-left:3px solid var(--danger);border-radius:.75rem;padding:.65rem 1rem;font-size:.85rem;color:var(--danger);">
            @foreach($errors->all() as $e) <div>✗ {{ $e }}</div> @endforeach
        </div>
        @endif

        <div class="page-content">
            @yield('content')
        </div>
    </div>
</div>

<script>
function toggleTheme(){const d=document.documentElement.classList.toggle('dark');localStorage.setItem('tanim-theme',d?'dark':'light');document.getElementById('icon-sun').style.display=d?'none':'block';document.getElementById('icon-moon').style.display=d?'block':'none';}
document.addEventListener('DOMContentLoaded',function(){const d=document.documentElement.classList.contains('dark');document.getElementById('icon-sun').style.display=d?'none':'block';document.getElementById('icon-moon').style.display=d?'block':'none';});
</script>
</body>
</html>
