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

<div class="page-wrap">
    <div style="display:flex;gap:2rem;align-items:flex-start;">
        {{-- Sidebar Filters --}}
        <aside style="width:280px;flex-shrink:0;position:sticky;top:2rem;">
            <div class="page-card" style="padding:1.5rem;">
                <h2 style="font-size:1.1rem;font-weight:800;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.5rem;">
                    <span>&#128269;</span> Filters
                </h2>

                <form method="GET" action="{{ route('marketplace') }}" style="display:flex;flex-direction:column;gap:1.25rem;">
                    <div>
                        <label class="label">Search</label>
                        <input name="search" type="text" value="{{ request('search') }}" placeholder="What are you looking for?" class="input" />
                    </div>

                    <div>
                        <label class="label">Category</label>
                        <select name="category" class="input">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label">Brand</label>
                        <select name="brand" class="input">
                            <option value="">All Brands</option>
                            @foreach($brands as $brandId => $brandName)
                            <option value="{{ $brandId }}" {{ (string) request('brand') === (string) $brandId ? 'selected' : '' }}>{{ $brandName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label">Type</label>
                        <select name="type" class="input">
                            <option value="">All Types</option>
                            @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                        <div>
                            <label class="label">Min Price</label>
                            <input name="min_price" type="number" min="0" step="0.01" value="{{ request('min_price') }}" class="input" placeholder="0" />
                        </div>
                        <div>
                            <label class="label">Max Price</label>
                            <input name="max_price" type="number" min="0" step="0.01" value="{{ request('max_price') }}" class="input" placeholder="9k+" />
                        </div>
                    </div>

                    <div>
                        <label class="label">Sort By</label>
                        <select name="sort" class="input">
                            <option value="newest" {{ request('sort','newest')=='newest'?'selected':'' }}>Newest Arrival</option>
                            <option value="price_asc" {{ request('sort')=='price_asc'?'selected':'' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>Price: High to Low</option>
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:0.75rem;margin-top:0.5rem;">
                        <button type="submit" class="btn-primary" style="width:100%;">Apply Filters</button>
                        @if(request()->hasAny(['search','category','brand','type','min_price','max_price','sort']))
                        <a href="{{ route('marketplace') }}" class="btn-ghost" style="width:100%;text-align:center;">Clear All</a>
                        @endif
                    </div>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <div style="flex:1;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                <p style="font-size:0.9rem;color:var(--text-muted);">
                    Showing <strong style="color:var(--text);">{{ $products->total() }}</strong> fresh products
                    @if(request('category')) in <span class="badge" style="margin-left:0.5rem;">{{ request('category') }}</span> @endif
                </p>
            </div>

            @if($products->isEmpty())
            <div class="page-card" style="text-align:center;padding:5rem 2rem;">
                <div style="font-size:4rem;margin-bottom:1.5rem;filter:grayscale(1);">🌱</div>
                <h3 style="font-size:1.5rem;font-weight:800;color:var(--text);margin-bottom:0.75rem;">No products found</h3>
                <p style="color:var(--text-muted);max-width:300px;margin:0 auto 2rem;">We couldn't find any produce matching your current filters. Try broadening your search.</p>
                <a href="{{ route('marketplace') }}" class="btn-primary">View All Products</a>
            </div>
            @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1.5rem;margin-bottom:3rem;">
                @foreach($products as $product)
                <div class="product-card animate-fade-in" style="animation-delay: {{ $loop->index * 50 }}ms">
                    <a href="{{ route('products.show', $product) }}" class="product-card-img">
                        @if($product->primaryPhoto())
                            <img src="{{ $product->primaryPhoto() }}" alt="{{ $product->name }}" loading="lazy" />
                        @else
                            <div style="font-size:3.5rem;">🌿</div>
                        @endif
                        <div style="position:absolute;top:0.75rem;left:0.75rem;z-index:10;">
                            <span class="badge">{{ $product->category }}</span>
                        </div>
                    </a>
                    
                    <div style="padding:1.25rem;">
                        <a href="{{ route('products.show', $product) }}" style="text-decoration:none;">
                            <h3 style="font-size:1.05rem;font-weight:800;color:var(--text);margin:0 0 0.5rem;line-height:1.3;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;">{{ $product->name }}</h3>
                        </a>
                        
                        <div style="display:flex;align-items:center;gap:0.4rem;margin-bottom:0.75rem;">
                            <span style="font-size:0.75rem;color:var(--text-light);">By</span>
                            <span style="font-size:0.75rem;font-weight:700;color:var(--primary);">{{ $product->brand }}</span>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.25rem;">
                            <div>
                                <span style="font-size:1.25rem;font-weight:900;color:var(--text);">&#8369;{{ number_format($product->price, 2) }}</span>
                                <span style="font-size:0.8rem;color:var(--text-muted);">/{{ $product->unit }}</span>
                            </div>
                            <div style="text-align:right;">
                                @if($product->stock <= 5)
                                    <span style="font-size:0.7rem;font-weight:800;color:var(--danger);text-transform:uppercase;">Only {{ $product->stock }} left</span>
                                @else
                                    <span style="font-size:0.7rem;font-weight:600;color:var(--text-light);">{{ $product->stock }} in stock</span>
                                @endif
                            </div>
                        </div>

                        <div style="display:flex;gap:0.5rem;">
                            @auth
                                @if(Auth::user()->role === 'admin')
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn-ghost" style="flex:1;padding:0.5rem;font-size:0.8rem;">✏️ Edit</a>
                                @else
                                    <form method="POST" action="{{ route('cart.add', $product) }}" style="flex:1;">
                                        @csrf<input type="hidden" name="quantity" value="1" />
                                        <button type="submit" class="btn-primary" style="width:100%;padding:0.6rem;font-size:0.85rem;">Add to Cart</button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn-ghost" style="flex:1;padding:0.6rem;font-size:0.85rem;">Login to Buy</a>
                            @endauth
                            <a href="{{ route('products.show', $product) }}" class="btn-ghost" style="padding:0.6rem;aspect-ratio:1;display:flex;align-items:center;justify-content:center;">👁</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div style="display:flex;justify-content:center;">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
