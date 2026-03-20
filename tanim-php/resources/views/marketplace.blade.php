@extends('layouts.app')
@section('content')

@if(session('cart_success'))
<div style="position:fixed;top:1rem;right:1rem;z-index:200;background:var(--primary);color:var(--primary-fg);padding:0.75rem 1.25rem;border-radius:0.75rem;font-size:0.875rem;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,0.2);">
    &#10003; {{ session('cart_success') }}
</div>
<script>setTimeout(() => document.querySelector('[style*="position:fixed"]')?.remove(), 3000);</script>
@endif

{{-- Admin Toolbar --}}
@auth
@if(Auth::user()->role === 'admin')
<div style="background:linear-gradient(90deg,rgba(22,163,74,0.12),rgba(212,168,67,0.08));border-bottom:2px solid rgba(22,163,74,0.25);padding:.6rem 0;">
    <div style="max-width:80rem;margin:0 auto;padding:0 1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
        <div style="display:flex;align-items:center;gap:.5rem;">
            <span style="font-size:.7rem;font-weight:800;background:var(--primary);color:#fff;padding:.2rem .6rem;border-radius:9999px;letter-spacing:.05em;">ADMIN VIEW</span>
            <span style="font-size:.78rem;color:var(--text-muted);">You can browse and manage products. Buying is disabled.</span>
        </div>
        <div style="display:flex;gap:.5rem;">
            <a href="{{ route('admin.products.create') }}" style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .9rem;background:var(--primary);color:#fff;font-size:.78rem;font-weight:700;border-radius:.6rem;text-decoration:none;">
                ＋ Add Product
            </a>
            <a href="{{ route('admin.products.index') }}" style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .9rem;background:var(--bg-glass);border:1px solid var(--border);color:var(--text-muted);font-size:.78rem;font-weight:700;border-radius:.6rem;text-decoration:none;">
                🗂 Manage All
            </a>
            <a href="{{ route('admin.dashboard') }}" style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .9rem;background:var(--bg-glass);border:1px solid var(--border);color:var(--text-muted);font-size:.78rem;font-weight:700;border-radius:.6rem;text-decoration:none;">
                ← Dashboard
            </a>
        </div>
    </div>
</div>
@endif
@endauth

{{-- Header --}}
<div style="background:var(--bg-2);border-bottom:1px solid var(--border);padding:2rem 0;">
    <div style="max-width:80rem;margin:0 auto;padding:0 1.5rem;">
        <h1 style="font-family:Outfit,sans-serif;font-size:2rem;font-weight:800;color:var(--text);margin:0 0 0.25rem;">&#127807; Marketplace</h1>
        <p style="color:var(--text-muted);font-size:0.95rem;margin:0;">Browse fresh produce directly from Filipino farmers</p>
    </div>
</div>

<div style="max-width:80rem;margin:0 auto;padding:2rem 1.5rem;">
    {{-- Filters --}}
    <form method="GET" action="{{ route('marketplace') }}" class="page-card" style="padding:1.25rem 1.5rem;margin-bottom:2rem;display:flex;flex-wrap:wrap;gap:0.75rem;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label class="label">Search</label>
            <input name="search" type="text" value="{{ request('search') }}" placeholder="Search produce..." class="input" />
        </div>
        <div style="min-width:160px;">
            <label class="label">Category</label>
            <select name="category" class="input">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:160px;">
            <label class="label">Brand</label>
            <select name="brand" class="input">
                <option value="">All Brands</option>
                @foreach($brands as $brand)
                <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:160px;">
            <label class="label">Type</label>
            <select name="type" class="input">
                <option value="">All Types</option>
                @foreach($types as $type)
                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:130px;">
            <label class="label">Min Price</label>
            <input name="min_price" type="number" min="0" step="0.01" value="{{ request('min_price') }}" class="input" placeholder="0" />
        </div>
        <div style="min-width:130px;">
            <label class="label">Max Price</label>
            <input name="max_price" type="number" min="0" step="0.01" value="{{ request('max_price') }}" class="input" placeholder="9999" />
        </div>
        <div style="min-width:160px;">
            <label class="label">Sort By</label>
            <select name="sort" class="input">
                <option value="newest" {{ request('sort','newest')=='newest'?'selected':'' }}>Newest</option>
                <option value="price_asc" {{ request('sort')=='price_asc'?'selected':'' }}>Price: Low to High</option>
                <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>Price: High to Low</option>
            </select>
        </div>
        <button type="submit" class="btn-primary" style="padding:0.65rem 1.5rem;font-size:0.875rem;border-radius:0.6rem;">Filter</button>
        @if(request()->hasAny(['search','category','brand','type','min_price','max_price','sort']))
        <a href="{{ route('marketplace') }}" style="padding:0.65rem 1rem;color:var(--text-muted);font-size:0.875rem;text-decoration:none;font-weight:500;">&#10005; Clear</a>
        @endif
    </form>

    <p style="font-size:0.875rem;color:var(--text-muted);margin-bottom:1.25rem;">
        Showing <strong style="color:var(--text);">{{ $products->total() }}</strong> products
        @if(request('category')) in <strong style="color:var(--primary);">{{ request('category') }}</strong> @endif
        @if(request('brand')) · brand: <strong style="color:var(--primary);">{{ request('brand') }}</strong> @endif
        @if(request('type')) · type: <strong style="color:var(--primary);">{{ request('type') }}</strong> @endif
    </p>

    @if($products->isEmpty())
    <div style="text-align:center;padding:4rem 0;">
        <div style="font-size:3rem;margin-bottom:1rem;">&#128269;</div>
        <h3 style="font-size:1.25rem;font-weight:700;color:var(--text);margin-bottom:0.5rem;">No products found</h3>
        <p style="color:var(--text-muted);">Try adjusting your search or filters.</p>
    </div>
    @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:1.5rem;margin-bottom:2rem;">
        @foreach($products as $product)
        <div class="product-card">
            <a href="{{ route('products.show', $product) }}" style="display:block;text-decoration:none;">
                @php $icons = ['Vegetables'=>'🥦','Fruits'=>'🍓','Grains & Rice'=>'🌾','Root Crops'=>'🥔','Herbs & Spices'=>'🌿']; @endphp
                <div class="product-card-img" style="height:170px;">
                    @if($product->primaryPhoto())
                        <img src="{{ $product->primaryPhoto() }}" alt="{{ $product->name }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center;display:block;z-index:0;" />
                    @else
                        <span style="font-size:3.5rem;position:relative;z-index:1;">{{ $icons[$product->category] ?? '🛒' }}</span>
                    @endif
                </div>
            </a>
            <div style="padding:1rem;">
                <span class="badge">{{ $product->category }}</span>
                <a href="{{ route('products.show', $product) }}" style="text-decoration:none;">
                    <h3 style="font-size:1rem;font-weight:700;color:var(--text);margin:0.5rem 0 0.25rem;line-height:1.3;">{{ $product->name }}</h3>
                </a>
                <p style="font-size:0.8rem;color:var(--text-muted);margin:0 0 0.75rem;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $product->description }}</p>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
                    <div>
                        <span style="font-size:1.2rem;font-weight:800;color:var(--primary);">&#8369;{{ number_format($product->price, 2) }}</span>
                        <span style="font-size:0.75rem;color:var(--text-light);">/ {{ $product->unit }}</span>
                    </div>
                    <span style="font-size:0.75rem;color:{{ $product->stock <= 10 ? 'var(--danger)' : 'var(--text-muted)' }};">
                        {{ $product->stock <= 10 ? '&#9888; Low stock' : $product->stock . ' in stock' }}
                    </span>
                </div>
                <div style="height:1px;background:linear-gradient(90deg,transparent,var(--border-glass),transparent);margin:0.75rem 0;"></div>
                <div style="padding:0.65rem;border:1px solid var(--border);border-radius:0.8rem;background:var(--bg-glass);">
                @auth
                @if(Auth::user()->role === 'admin')
                {{-- Admin product actions --}}
                <div style="display:flex;gap:.4rem;margin-top:.25rem;">
                    <a href="{{ route('admin.products.edit', $product) }}" style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:.3rem;padding:.5rem;background:var(--wheat-soft);color:var(--wheat-2);font-size:.75rem;font-weight:700;border:1px solid rgba(212,168,67,.3);border-radius:.6rem;text-decoration:none;">
                        ✏️ Edit
                    </a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="padding:.5rem .75rem;background:var(--danger-soft);color:var(--danger);font-size:.75rem;font-weight:700;border:1px solid var(--danger-border);border-radius:.6rem;cursor:pointer;">
                            🗑
                        </button>
                    </form>
                </div>
                @elseif(Auth::user()->role !== 'admin')
                <form method="POST" action="{{ route('cart.add', $product) }}">
                    @csrf<input type="hidden" name="quantity" value="1" />
                    <button type="submit" class="btn-primary" style="width:100%;padding:0.6rem;font-size:0.875rem;border-radius:0.6rem;">&#128722; Add to Cart</button>
                </form>
                @endif
                @else
                <a href="{{ route('login') }}" class="btn-ghost" style="display:block;width:100%;padding:0.6rem;font-size:0.875rem;border-radius:0.6rem;text-align:center;box-sizing:border-box;">Login to Buy</a>
                @endauth
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div style="display:flex;justify-content:center;">{{ $products->links() }}</div>
    @endif
</div>
@endsection
