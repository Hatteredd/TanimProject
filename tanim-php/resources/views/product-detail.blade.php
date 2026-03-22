@extends('layouts.app')
@section('title', $product->name . ' — Tanim')

@section('content')
<style>
:root { --product-price: {{ $product->price }}; }

.pd-wrap {
    min-height: 100vh;
    background: var(--bg);
    padding: 2.5rem 0 4rem;
}
.pd-container {
    max-width: 72rem; margin: 0 auto;
    padding: 0 1.5rem;
}

/* Breadcrumb */
.breadcrumb {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.82rem; color: var(--text-muted);
    margin-bottom: 2rem;
}
.breadcrumb a { color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
.breadcrumb a:hover { color: var(--primary); }
.breadcrumb-sep { opacity: 0.4; }

/* Main grid */
.pd-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2.5rem; margin-bottom: 3.5rem;
}
@media(max-width:768px) { .pd-grid { grid-template-columns: 1fr; } }

/* Image/Icon panel */
.pd-image {
    border-radius: 1.5rem;
    background: linear-gradient(135deg, var(--primary-soft) 0%, var(--primary-faint) 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 8rem; min-height: 340px;
    box-shadow: var(--shadow-neu);
    position: relative; overflow: hidden;
}
.pd-image::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.3), transparent 60%);
}
.pd-category-badge {
    margin-bottom: 1rem;
    display: inline-block;
}

.pd-thumb-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(68px, 1fr));
    gap: 0.55rem;
    margin-top: 0.85rem;
}

.pd-thumb {
    width: 100%;
    height: 68px;
    border-radius: 0.65rem;
    object-fit: cover;
    border: 2px solid var(--border);
    cursor: pointer;
    transition: border-color .15s, transform .15s;
    background: var(--bg);
}

.pd-thumb.active {
    border-color: var(--primary);
    transform: translateY(-1px);
}

/* Info panel */
.pd-info-card {
    background: var(--bg-glass);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border: 1px solid var(--border);
    border-radius: 1.5rem;
    padding: 2rem;
    box-shadow: var(--shadow-neu);
}

.pd-title {
    font-family: 'Outfit', sans-serif;
    font-size: 2rem; font-weight: 900;
    color: var(--text);
    margin: 0 0 0.75rem;
    line-height: 1.2;
}

/* Rating row */
.rating-row {
    display: flex; align-items: center; gap: 0.6rem;
    margin-bottom: 1.5rem;
}
.rating-stars { display: flex; gap: 2px; }
.rating-score { font-weight: 800; color: var(--text); font-size: 0.9rem; }
.rating-count { color: var(--text-muted); font-size: 0.8rem; }

/* Farm meta */
.pd-meta {
    display: flex; flex-direction: column; gap: 0.6rem;
    padding: 1rem 1.25rem;
    background: var(--primary-faint);
    border: 1px solid var(--border);
    border-radius: 1rem;
    margin-bottom: 1.5rem;
}
.pd-meta-row {
    display: flex; align-items: center; gap: 0.6rem;
    font-size: 0.875rem; color: var(--text-muted);
}
.pd-meta-row strong { color: var(--text); }

.pd-desc {
    font-size: 0.95rem; color: var(--text-muted);
    line-height: 1.8; margin-bottom: 1.75rem;
}

/* Price display */
.pd-price-row {
    display: flex; align-items: baseline; gap: 0.4rem;
    margin-bottom: 1.5rem;
}
.pd-price-base { font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 900; color: var(--primary); }
.pd-price-unit { font-size: 0.9rem; color: var(--text-muted); }
.pd-price-total {
    font-size: 0.85rem; font-weight: 700;
    color: var(--text-muted);
    margin-left: 0.5rem;
}

/* Quantity selector */
.qty-row {
    display: flex; align-items: center; gap: 1rem;
    margin-bottom: 1.5rem; flex-wrap: wrap;
}
.qty-label { font-size: 0.875rem; font-weight: 700; color: var(--text); }
.qty-control {
    display: flex; align-items: center;
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: 0.85rem;
    box-shadow: var(--shadow-neu-inset);
    overflow: hidden;
}
.qty-btn {
    width: 2.5rem; height: 2.5rem;
    border: none; background: transparent; cursor: pointer;
    font-size: 1.1rem; color: var(--text-muted);
    transition: background 0.15s, color 0.15s;
    display: flex; align-items: center; justify-content: center;
}
.qty-btn:hover { background: var(--primary-faint); color: var(--primary); }
.qty-input {
    width: 3.5rem; text-align: center;
    border: none; background: transparent;
    font-size: 1rem; font-weight: 800; color: var(--text);
    outline: none;
}
.qty-stock { font-size: 0.78rem; color: var(--text-light); }

/* Action buttons */
.pd-actions {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 0.75rem; margin-bottom: 1rem;
}
@media(max-width:400px) { .pd-actions { grid-template-columns: 1fr; } }

/* ─── REVIEWS SECTION ─── */
.section-heading {
    font-family: 'Outfit', sans-serif;
    font-size: 1.4rem; font-weight: 800;
    color: var(--text); margin: 0 0 1.5rem;
}
.review-summary {
    display: flex; gap: 2.5rem; align-items: center;
    background: var(--bg-glass);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border: 1px solid var(--border);
    border-radius: 1.25rem; padding: 1.75rem 2rem;
    margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1.5rem;
}
.review-big-score {
    font-family: 'Outfit', sans-serif;
    font-size: 4rem; font-weight: 900;
    color: var(--text);
    line-height: 1;
}
.review-breakdown { flex: 1; display: flex; flex-direction: column; gap: 4px; min-width: 160px;}
.review-bar-row { display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: var(--text-muted); }
.review-bar-track { flex: 1; height: 6px; background: var(--border); border-radius: 9999px; overflow: hidden; }
.review-bar-fill { height: 100%; background: var(--primary); border-radius: 9999px; }

.review-card {
    background: var(--bg-glass);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border: 1px solid var(--border);
    border-radius: 1.1rem; padding: 1.25rem 1.5rem;
    margin-bottom: 1rem;
    box-shadow: var(--shadow-neu-sm);
    transition: transform 0.2s;
}
.review-card:hover { transform: translateY(-2px); }
.review-user-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
.review-name { font-weight: 700; font-size: 0.9rem; color: var(--text); }
.review-date { font-size: 0.75rem; color: var(--text-light); }
.review-comment { font-size: 0.875rem; color: var(--text-muted); line-height: 1.7; margin-top: 0.5rem; }

/* ─── MORE FROM FARM ─── */
.more-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.25rem;
}
.more-card {
    background: var(--bg-glass);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border: 1px solid var(--border);
    border-radius: 1.1rem; overflow: hidden;
    box-shadow: var(--shadow-neu-sm);
    transition: transform 0.2s, box-shadow 0.2s;
    text-decoration: none; display: block;
    color: var(--text);
}
.more-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }
.more-card-img {
    height: 110px;
    background: linear-gradient(135deg, var(--primary-soft), var(--primary-faint));
    display: flex; align-items: center; justify-content: center;
    font-size: 2.8rem;
}
.more-card-body { padding: 0.85rem; }
.more-card-name { font-size: 0.875rem; font-weight: 700; color: var(--text); margin: 0 0 0.3rem; }
.more-card-price { font-size: 1rem; font-weight: 800; color: var(--primary); }
</style>

<div class="pd-wrap">

{{-- Admin Toolbar --}}
@auth
@if(Auth::user()->role === 'admin')
<div style="background:linear-gradient(90deg,rgba(22,163,74,0.12),rgba(212,168,67,0.08));border-bottom:2px solid rgba(22,163,74,0.25);padding:.65rem 1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
    <div style="display:flex;align-items:center;gap:.5rem;">
        <span style="font-size:.7rem;font-weight:800;background:var(--primary);color:#fff;padding:.2rem .6rem;border-radius:9999px;letter-spacing:.05em;">ADMIN VIEW</span>
        <span style="font-size:.78rem;color:var(--text-muted);">Managing: <strong style="color:var(--text);">{{ $product->name }}</strong></span>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('admin.products.edit', $product) }}" style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .9rem;background:var(--wheat-soft);color:var(--wheat-2);font-size:.78rem;font-weight:700;border:1px solid rgba(212,168,67,.3);border-radius:.6rem;text-decoration:none;">
            ✏️ Edit Product
        </a>
        <a href="{{ route('admin.products.create') }}" style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .9rem;background:var(--primary);color:#fff;font-size:.78rem;font-weight:700;border-radius:.6rem;text-decoration:none;">
            ＋ Add New
        </a>
        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product permanently?')" style="margin:0;">
            @csrf @method('DELETE')
            <button type="submit" style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .9rem;background:var(--danger-soft);color:var(--danger);font-size:.78rem;font-weight:700;border:1px solid var(--danger-border);border-radius:.6rem;cursor:pointer;">
                🗑 Delete
            </button>
        </form>
        <a href="{{ route('marketplace') }}" style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .9rem;background:var(--bg-glass);border:1px solid var(--border);color:var(--text-muted);font-size:.78rem;font-weight:700;border-radius:.6rem;text-decoration:none;">
            ← Marketplace
        </a>
    </div>
</div>
@endif
@endauth

<div class="pd-container">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb" aria-label="breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span class="breadcrumb-sep">›</span>
        <a href="{{ route('marketplace') }}">Marketplace</a>
        <span class="breadcrumb-sep">›</span>
        <a href="{{ route('marketplace', ['category' => $product->category]) }}">{{ $product->category }}</a>
        <span class="breadcrumb-sep">›</span>
        <span style="color:var(--text);font-weight:600;">{{ $product->name }}</span>
    </nav>

    {{-- Flash message --}}
    @if(session('cart_success'))
    <div style="background:var(--primary-soft);border:1px solid var(--primary);border-radius:0.85rem;padding:0.75rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;font-weight:700;color:var(--primary-text);">
        ✓ {{ session('cart_success') }}
    </div>
    @endif

    {{-- ── MAIN PRODUCT GRID ── --}}
    <div class="pd-grid">

        {{-- Image --}}
        <div style="position:relative;">
            @php
                $galleryPhotos = $product->photos;
                $mainPhoto = $galleryPhotos->firstWhere('is_primary', true) ?? $galleryPhotos->first();
            @endphp
            <div class="pd-image">
                @php $icons = ['Vegetables'=>'🥦','Fruits'=>'🍓','Grains & Rice'=>'🌾','Root Crops'=>'🥔','Herbs & Spices'=>'🌿']; @endphp
                @if($mainPhoto || $product->primaryPhoto())
                    <img id="main-product-image" src="{{ $mainPhoto ? asset('storage/'.$mainPhoto->path) : $product->primaryPhoto() }}" alt="{{ $product->name }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center;z-index:0;" />
                @else
                    {{ $icons[$product->category] ?? '🛒' }}
                @endif
            </div>
            <div class="pd-category-badge">
                <span class="badge">{{ $product->category }}</span>
            </div>

            @if($galleryPhotos->isNotEmpty())
            <div class="pd-thumb-grid">
                @foreach($galleryPhotos as $photo)
                <img
                    src="{{ asset('storage/'.$photo->path) }}"
                    alt="{{ $product->name }} photo {{ $loop->iteration }}"
                    class="pd-thumb {{ ($mainPhoto && $mainPhoto->id === $photo->id) ? 'active' : '' }}"
                    data-photo-src="{{ asset('storage/'.$photo->path) }}"
                />
                @endforeach
            </div>
            @endif

            {{-- Stock status --}}
            <div style="margin-top:1rem;text-align:center;">
                @if($product->stock <= 0)
                    <span style="background:#fee2e2;color:#dc2626;padding:0.4rem 1rem;border-radius:9999px;font-size:0.8rem;font-weight:700;">⚠ Out of Stock</span>
                @elseif($product->stock <= 15)
                    <span style="background:#fef3c7;color:#92400e;padding:0.4rem 1rem;border-radius:9999px;font-size:0.8rem;font-weight:700;">⚠ Only {{ $product->stock }} left</span>
                @else
                    <span style="background:var(--primary-soft);color:var(--primary-text);padding:0.4rem 1rem;border-radius:9999px;font-size:0.8rem;font-weight:700;">✓ In Stock ({{ $product->stock }} available)</span>
                @endif
            </div>
        </div>

        {{-- Product info --}}
        <div class="pd-info-card">
            <h1 class="pd-title">{{ $product->name }}</h1>

            {{-- Stars --}}
            <div class="rating-row">
                <div class="rating-stars">
                    @for($i=1;$i<=5;$i++)
                    <svg style="width:1.1rem;height:1.1rem;color:{{ $i <= round($avgRating) ? '#f59e0b' : 'var(--border)' }}" fill="{{ $i <= round($avgRating) ? 'currentColor' : 'none' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.563.563 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                    @endfor
                </div>
                <span class="rating-score">{{ $avgRating }}</span>
                <span class="rating-count">({{ $reviewCount }} review{{ $reviewCount !== 1 ? 's' : '' }})</span>
            </div>

            {{-- Farm meta --}}
            <div class="pd-meta">
                @if($product->supplier)
                <div class="pd-meta-row">
                    <span>🚚</span>
                    <span>Supplier: <strong>{{ $product->supplier->name }}</strong></span>
                </div>
                @endif
                @if($product->farm_location)
                <div class="pd-meta-row">
                    <span>📍</span>
                    <span>Farm Location: <strong>{{ $product->farm_location }}</strong></span>
                </div>
                @endif
                @if($product->harvest_date)
                <div class="pd-meta-row">
                    <span>🗓</span>
                    <span>Harvested: <strong>{{ $product->harvest_date->format('F d, Y') }}</strong></span>
                </div>
                @endif
            </div>

            {{-- Description --}}
            <p class="pd-desc">{{ $product->description }}</p>

            {{-- Price --}}
            <div class="pd-price-row">
                <span class="pd-price-base">₱<span id="total-price">{{ number_format($product->price, 2) }}</span></span>
                <span class="pd-price-unit">/ {{ $product->unit }}</span>
                <span class="pd-price-total" id="price-note">&nbsp;</span>
            </div>

            {{-- Quantity --}}
            <div class="qty-row">
                <span class="qty-label">Quantity:</span>
                <div class="qty-control">
                    <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
                    <input type="number" class="qty-input" id="qty-input" value="1" min="1" max="{{ $product->stock }}" oninput="updatePrice(this.value)">
                    <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                </div>
                <span class="qty-stock">Max: {{ $product->stock }} {{ $product->unit }}(s)</span>
            </div>

            {{-- Action buttons --}}
            @if($product->stock > 0)
            <div class="pd-actions">
                @auth
                @if(Auth::user()->role !== 'admin')
                {{-- Add to Cart --}}
                <form method="POST" action="{{ route('cart.add', $product) }}" id="cart-form">
                    @csrf
                    <input type="hidden" name="quantity" id="cart-qty" value="1">
                    <button type="submit" class="btn-ghost" style="width:100%;justify-content:center;">
                        🛒 Add to Cart
                    </button>
                </form>
                {{-- Buy Now --}}
                <form method="POST" action="{{ route('cart.add', $product) }}" id="buy-form">
                    @csrf
                    <input type="hidden" name="quantity" id="buy-qty" value="1">
                    <input type="hidden" name="buy_now" value="1">
                    <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">
                        ⚡ Buy Now
                    </button>
                </form>
                @else
                <div style="grid-column:span 2;padding:0.85rem;background:var(--earth-soft);border:1px solid rgba(139,94,60,0.20);border-radius:0.85rem;text-align:center;color:var(--earth);font-size:0.875rem;font-weight:600;">
                    🛡 Admins cannot purchase products.
                </div>
                @endif
                @else
                <a href="{{ route('login') }}" class="btn-ghost" style="justify-content:center;">🔒 Login to Order</a>
                <a href="{{ route('register') }}" class="btn-primary" style="justify-content:center;">Get Started →</a>
                @endauth
            </div>
            @else
            <div style="padding:1rem;background:var(--primary-faint);border-radius:0.85rem;text-align:center;color:var(--text-muted);font-weight:600;">
                Currently out of stock. Check back soon!
            </div>
            @endif

            {{-- Guarantees --}}
            <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);">
                @foreach(['🌿 Farm Fresh','📦 Careful Packing','🔒 Secure Checkout'] as $g)
                <span style="font-size:0.75rem;font-weight:600;color:var(--text-muted);">{{ $g }}</span>
                @endforeach
            </div>
        </div>
    </div>


    {{-- ── REVIEWS SECTION ── --}}
    <section id="reviews" style="margin-bottom:3.5rem;">
        <h2 class="section-heading">⭐ Customer Reviews</h2>

        <div class="review-summary">
            <div style="text-align:center;">
                <div class="review-big-score">{{ $avgRating }}</div>
                <div class="rating-stars" style="justify-content:center;margin:0.4rem 0;">
                    @for($i=1;$i<=5;$i++)
                    <svg style="width:1.1rem;height:1.1rem;color:{{ $i <= round($avgRating) ? '#f59e0b' : 'var(--border)' }}" fill="{{ $i <= round($avgRating) ? 'currentColor' : 'none' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.563.563 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                    @endfor
                </div>
                <div style="font-size:0.8rem;color:var(--text-muted);">{{ $reviewCount }} reviews</div>
            </div>

            {{-- Bar breakdown --}}
            <div class="review-breakdown">
                @for($star=5;$star>=1;$star--)
                @php $cnt = $reviews->where('rating', $star)->count(); $pct = $reviewCount > 0 ? ($cnt/$reviewCount)*100 : 0; @endphp
                <div class="review-bar-row">
                    <span style="width:0.8rem;">{{ $star }}</span>
                    <svg style="width:0.8rem;height:0.8rem;color:#f59e0b;" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.563.563 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                    <div class="review-bar-track"><div class="review-bar-fill" style="width:{{ $pct }}%;"></div></div>
                    <span style="width:1rem;text-align:right;">{{ $cnt }}</span>
                </div>
                @endfor
            </div>
        </div>

        {{-- Write a Review --}}
        @auth
        @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:0.75rem;padding:0.85rem 1rem;margin-bottom:1.25rem;color:#15803d;font-size:0.875rem;font-weight:600;">✓ {{ session('success') }}</div>
        @endif
        @if($errors->has('review'))
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:0.75rem;padding:0.85rem 1rem;margin-bottom:1.25rem;color:#dc2626;font-size:0.875rem;">{{ $errors->first('review') }}</div>
        @endif

        @if($canReview && !$userReview)
        <div class="glass" style="border-radius:1.25rem;padding:1.5rem;margin-bottom:1.5rem;">
            <h3 style="font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1rem;">Write a Review</h3>
            <form method="POST" action="{{ route('reviews.store', $product) }}">
                @csrf
                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:var(--text);margin-bottom:0.5rem;">Rating *</label>
                    <div style="display:flex;gap:0.5rem;">
                        @for($i=1;$i<=5;$i++)
                        <label style="cursor:pointer;font-size:1.5rem;" title="{{ $i }} star">
                            <input type="radio" name="rating" value="{{ $i }}" style="display:none;" required />
                            <span class="star-label" data-val="{{ $i }}" style="color:var(--border);">★</span>
                        </label>
                        @endfor
                    </div>
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:var(--text);margin-bottom:0.5rem;">Comment</label>
                    <textarea name="comment" rows="3" style="width:100%;padding:0.75rem 1rem;border:1.5px solid var(--border);border-radius:0.75rem;font-size:0.875rem;background:var(--bg);color:var(--text);outline:none;resize:vertical;box-sizing:border-box;" placeholder="Share your experience..."></textarea>
                </div>
                <button type="submit" class="btn-primary" style="padding:0.65rem 1.5rem;">Submit Review</button>
            </form>
        </div>
        @elseif($canReview)
        <div class="glass" style="border-radius:1.25rem;padding:1.5rem;margin-bottom:1.5rem;">
            <h3 style="font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1rem;">Update Your Review</h3>
            <form method="POST" action="{{ route('reviews.update', $userReview) }}">
                @csrf @method('PATCH')
                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:var(--text);margin-bottom:0.5rem;">Rating *</label>
                    <div style="display:flex;gap:0.5rem;">
                        @for($i=1;$i<=5;$i++)
                        <label style="cursor:pointer;font-size:1.5rem;">
                            <input type="radio" name="rating" value="{{ $i }}" {{ $userReview->rating == $i ? 'checked' : '' }} style="display:none;" required />
                            <span class="star-label" data-val="{{ $i }}" style="color:{{ $i <= $userReview->rating ? '#f59e0b' : 'var(--border)' }};">★</span>
                        </label>
                        @endfor
                    </div>
                </div>
                <div style="margin-bottom:1rem;">
                    <textarea name="comment" rows="3" style="width:100%;padding:0.75rem 1rem;border:1.5px solid var(--border);border-radius:0.75rem;font-size:0.875rem;background:var(--bg);color:var(--text);outline:none;resize:vertical;box-sizing:border-box;">{{ $userReview->comment }}</textarea>
                </div>
                <button type="submit" class="btn-primary" style="padding:0.65rem 1.5rem;">Update Review</button>
            </form>
            <form method="POST" action="{{ route('reviews.destroy', $userReview) }}" onsubmit="return confirm('Delete your review?')" style="margin-top:0.75rem;display:inline-block;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-ghost" style="padding:0.65rem 1.5rem;color:#dc2626;">Delete</button>
            </form>
        </div>
        @else
        <div class="glass" style="border-radius:1.25rem;padding:1.1rem 1.25rem;margin-bottom:1.5rem;">
            <p style="margin:0;font-size:0.86rem;color:var(--text-muted);">You can submit a review after this product is delivered in one of your orders.</p>
        </div>
        @endif
        @endauth

        {{-- Individual reviews --}}
        @forelse($reviews as $review)
        <div class="review-card">
            <div class="review-user-row">
                <div style="display:flex;align-items:center;gap:0.6rem;">
                    <div style="width:2rem;height:2rem;border-radius:9999px;background:var(--primary-faint);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:800;color:var(--primary);">
                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    </div>
                    <span class="review-name">{{ $review->user->name }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:0.5rem;">
                    <div style="display:flex;gap:1px;">
                        @for($i=1;$i<=5;$i++)<svg style="width:0.8rem;height:0.8rem;color:{{ $i<=$review->rating ? '#f59e0b' : 'var(--border)' }}" fill="{{ $i<=$review->rating ? 'currentColor' : 'none' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.563.563 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>@endfor
                    </div>
                    <span class="review-date">{{ $review->created_at->diffForHumans() }}</span>
                </div>
            </div>
            @if($review->comment)
            <p class="review-comment">"{{ $review->comment }}"</p>
            @endif
        </div>
        @empty
        <div style="text-align:center;padding:2.5rem;color:var(--text-muted);font-size:0.9rem;">
            No reviews yet. Be the first to review this product!
        </div>
        @endforelse
    </section>


    {{-- ── MORE FROM TANIM ── --}}
    @if($fromSameFarm->isNotEmpty())
    <section>
        <h2 class="section-heading">🌾 More from Tanim</h2>
        <div class="more-grid">
            @foreach($fromSameFarm as $other)
            <a href="{{ route('products.show', $other) }}" class="more-card">
                <div class="more-card-img">
                    @if($other->primaryPhoto())
                        <img src="{{ $other->primaryPhoto() }}" alt="{{ $other->name }}" style="width:100%;height:100%;object-fit:cover;object-position:center;" />
                    @else
                        {{ $icons[$other->category] ?? '🛒' }}
                    @endif
                </div>
                <div class="more-card-body">
                    <span class="badge" style="margin-bottom:0.4rem;display:inline-block;">{{ $other->category }}</span>
                    <p class="more-card-name">{{ $other->name }}</p>
                    <span class="more-card-price">₱{{ number_format($other->price, 2) }}</span>
                    <span style="font-size:0.72rem;color:var(--text-light);">/{{ $other->unit }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

</div>
</div>

<script>
const basePrice = {{ $product->price }};
const maxStock  = {{ $product->stock }};

function updatePrice(val) {
    const qty = Math.max(1, Math.min(maxStock, parseInt(val) || 1));
    document.getElementById('qty-input').value = qty;
    const total = (basePrice * qty).toFixed(2);
    const fmt   = new Intl.NumberFormat('en-PH', {minimumFractionDigits:2}).format(total);
    document.getElementById('total-price').textContent = fmt;
    document.getElementById('price-note').textContent = qty > 1 ? `(₱{{ number_format($product->price, 2) }} × ${qty})` : '';
    // sync hidden inputs
    if (document.getElementById('cart-qty')) document.getElementById('cart-qty').value = qty;
    if (document.getElementById('buy-qty'))  document.getElementById('buy-qty').value  = qty;
}

function changeQty(delta) {
    const input = document.getElementById('qty-input');
    const newVal = Math.max(1, Math.min(maxStock, parseInt(input.value || 1) + delta));
    input.value = newVal;
    updatePrice(newVal);
}

// Init
updatePrice(1);
</script>

<script>
// Star rating interaction
document.querySelectorAll('.star-label').forEach(star => {
    star.addEventListener('mouseover', function() {
        const val = parseInt(this.dataset.val);
        this.closest('form').querySelectorAll('.star-label').forEach((s, i) => {
            s.style.color = i < val ? '#f59e0b' : 'var(--border)';
        });
    });
    star.addEventListener('click', function() {
        const val = parseInt(this.dataset.val);
        const radio = this.previousElementSibling;
        radio.checked = true;
        this.closest('form').querySelectorAll('.star-label').forEach((s, i) => {
            s.style.color = i < val ? '#f59e0b' : 'var(--border)';
        });
    });
});

document.querySelectorAll('.pd-thumb').forEach((thumb) => {
    thumb.addEventListener('click', function () {
        const target = document.getElementById('main-product-image');
        if (!target) return;

        target.src = this.dataset.photoSrc;
        document.querySelectorAll('.pd-thumb').forEach((el) => el.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>
@endsection
