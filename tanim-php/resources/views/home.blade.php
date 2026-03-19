@extends('layouts.app')
@section('content')

{{-- ── HERO ─────────────────────────────────────────── --}}
<section style="position:relative;min-height:100vh;display:flex;align-items:center;overflow:hidden;">
    {{-- Background: deep forest/farm gradient --}}
    <div style="position:absolute;inset:0;background-image:url('{{ asset('images/hero-bg.png') }}');background-size:cover;background-position:center;background-attachment:fixed;z-index:0;"></div>
    <div style="position:absolute;inset:0;background:linear-gradient(160deg,rgba(10,30,10,0.92) 0%,rgba(15,40,10,0.75) 55%,rgba(60,35,10,0.60) 100%);z-index:0;"></div>

    {{-- Organic glow blobs --}}
    <div style="position:absolute;top:6rem;right:8rem;width:20rem;height:20rem;background:rgba(46,139,46,0.14);border-radius:60% 40% 70% 30% / 50% 60% 40% 50%;filter:blur(55px);animation:float 6s ease-in-out infinite;z-index:0;"></div>
    <div style="position:absolute;bottom:8rem;right:20%;width:14rem;height:14rem;background:rgba(212,168,67,0.10);border-radius:40% 60% 30% 70% / 60% 40% 70% 30%;filter:blur(45px);animation:float 8s ease-in-out infinite reverse;z-index:0;"></div>

    {{-- Decorative wheat stalks (right side) --}}
    <div style="position:absolute;right:3rem;bottom:0;z-index:1;opacity:0.18;font-size:8rem;line-height:1;pointer-events:none;animation:sway 5s ease-in-out infinite;">🌾</div>
    <div style="position:absolute;right:8rem;bottom:0;z-index:1;opacity:0.12;font-size:6rem;line-height:1;pointer-events:none;animation:sway 7s ease-in-out infinite reverse;">🌿</div>

    <div style="position:relative;z-index:10;max-width:80rem;margin:0 auto;padding:8rem 1.5rem;width:100%;">
        <div style="max-width:44rem;">
            {{-- Live badge --}}
            <div style="display:inline-flex;align-items:center;gap:0.5rem;background:rgba(212,168,67,0.15);backdrop-filter:blur(12px);border:1px solid rgba(212,168,67,0.30);border-radius:9999px;padding:0.4rem 1rem;margin-bottom:2rem;">
                <span style="width:0.5rem;height:0.5rem;background:#d4a843;border-radius:9999px;display:inline-block;animation:ping 1.5s infinite;"></span>
                <span style="font-size:0.8rem;font-weight:700;color:#e8c060;letter-spacing:0.06em;text-transform:uppercase;">🌾 Philippine Farm Marketplace</span>
            </div>

            <h1 style="font-family:Outfit,sans-serif;font-size:clamp(2.5rem,6vw,4.5rem);font-weight:900;color:#f5f0e8;line-height:1.1;margin:0 0 1.5rem;text-shadow:0 2px 20px rgba(0,0,0,0.4);">
                Tanim —<br />
                <span style="background:linear-gradient(90deg,#7ec47e,#d4a843,#7ec47e);background-size:200%;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;animation:shimmer 4s linear infinite;">Rooted in the Soil,<br />Grown for You.</span>
            </h1>

            <p style="font-size:1.05rem;color:#d4c8a8;margin:0 0 2.5rem;line-height:1.8;max-width:38rem;">
                Tanim (Filipino for <em>"to plant"</em>) connects Filipino farmers directly to buyers — cutting out middlemen, ensuring fair harvest prices, and bringing the freshest produce straight from the farm gate to your table.
            </p>

            <div style="display:flex;flex-wrap:wrap;gap:1rem;margin-bottom:2.5rem;">
                <a href="{{ route('marketplace') }}" class="btn-primary" style="padding:1rem 2rem;font-size:1rem;border-radius:1.25rem;background:linear-gradient(135deg,#2e8b2e,#1f6b1f);box-shadow:0 8px 30px rgba(46,139,46,0.45);">🛒 Browse the Harvest</a>
                <a href="{{ route('register') }}" style="display:inline-flex;align-items:center;gap:0.5rem;padding:1rem 2rem;background:rgba(212,168,67,0.15);backdrop-filter:blur(8px);border:1.5px solid rgba(212,168,67,0.35);color:#e8c060;font-weight:700;font-size:1rem;border-radius:1.25rem;text-decoration:none;transition:all .2s;"
                   onmouseover="this.style.background='rgba(212,168,67,0.25)'" onmouseout="this.style.background='rgba(212,168,67,0.15)'">🌱 Join as a Farmer</a>
            </div>

            {{-- Home Search (Laravel Scout) --}}
            <form method="GET" action="{{ route('home') }}" style="display:grid;gap:.65rem;background:rgba(15,30,15,.45);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.16);border-radius:1rem;padding:.9rem;margin-bottom:1.25rem;">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:.55rem;">
                    <input name="q" type="text" value="{{ request('q') }}" placeholder="Search products (Scout)..." class="input" style="background:rgba(255,255,255,.95);" />
                    <select name="category" class="input" style="background:rgba(255,255,255,.95);">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    <select name="brand" class="input" style="background:rgba(255,255,255,.95);">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand }}" {{ request('brand') === $brand ? 'selected' : '' }}>{{ $brand }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:.55rem;">
                    <select name="type" class="input" style="background:rgba(255,255,255,.95);">
                        <option value="">All Types</option>
                        @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    <input name="min_price" type="number" min="0" step="0.01" value="{{ request('min_price') }}" placeholder="Min Price" class="input" style="background:rgba(255,255,255,.95);" />
                    <input name="max_price" type="number" min="0" step="0.01" value="{{ request('max_price') }}" placeholder="Max Price" class="input" style="background:rgba(255,255,255,.95);" />
                    <button type="submit" class="btn-primary" style="padding:.75rem 1.25rem;border-radius:.7rem;">Search</button>
                    @if(request()->hasAny(['q','category','brand','type','min_price','max_price']))
                    <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;justify-content:center;padding:.75rem 1rem;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.25);color:#f5f0e8;font-size:.85rem;font-weight:700;border-radius:.7rem;text-decoration:none;">Clear</a>
                    @endif
                </div>
            </form>

            {{-- Feature pills --}}
            <div style="display:flex;flex-wrap:wrap;gap:0.65rem;">
                @foreach(['🌿 No Middlemen','🌾 Fair Harvest Prices','📦 Order Tracking','🇵🇭 Proudly Filipino','🌱 Farm Fresh'] as $pill)
                <div style="background:rgba(255,255,255,0.07);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.12);border-radius:9999px;padding:0.4rem 0.9rem;">
                    <span style="font-size:0.78rem;font-weight:700;color:#d4c8a8;">{{ $pill }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div style="position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);z-index:10;animation:bounce 2s infinite;">
        <svg style="width:1.5rem;height:1.5rem;color:rgba(212,168,67,0.6);" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
    </div>
</section>

@if($searchResults)
<section style="padding:4rem 0;background:var(--bg);border-top:1px solid var(--border);">
<div style="max-width:80rem;margin:0 auto;padding:0 1.5rem;">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem;gap:1rem;flex-wrap:wrap;">
        <div>
            <span class="badge-wheat" style="margin-bottom:.55rem;display:inline-block;">Scout Search</span>
            <h2 style="font-family:Outfit,sans-serif;font-size:1.9rem;font-weight:800;color:var(--text);margin:0;">Search Results</h2>
            <p style="font-size:.9rem;color:var(--text-muted);margin:.3rem 0 0;">Found {{ $searchResults->total() }} product(s) for "{{ request('q') }}"</p>
        </div>
    </div>

    @if($searchResults->isEmpty())
    <div style="text-align:center;padding:2.5rem 0;color:var(--text-muted);">No products matched your Scout search and filters.</div>
    @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:1.25rem;">
        @foreach($searchResults as $product)
        <div class="product-card">
            <a href="{{ route('products.show', $product) }}" style="display:block;text-decoration:none;">
                @php $icons = ['Vegetables'=>'🥦','Fruits'=>'🍓','Grains & Rice'=>'🌾','Root Crops'=>'🥔','Herbs & Spices'=>'🌿']; @endphp
                <div class="product-card-img" style="height:140px;">
                    @if($product->primaryPhoto())
                        <img src="{{ $product->primaryPhoto() }}" alt="{{ $product->name }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center;display:block;z-index:0;" />
                    @else
                        <span style="font-size:3rem;position:relative;z-index:1;">{{ $icons[$product->category] ?? '🛒' }}</span>
                    @endif
                </div>
            </a>
            <div style="padding:.9rem;">
                <span class="badge" style="font-size:.65rem;">{{ strtoupper($product->category) }}</span>
                <h3 style="font-size:.95rem;font-weight:700;color:var(--text);margin:.5rem 0 .2rem;">{{ $product->name }}</h3>
                <p style="font-size:.74rem;color:var(--text-muted);margin:0 0 .45rem;">
                    {{ $product->brand ?: 'No Brand' }} · {{ $product->type ?: 'No Type' }}
                </p>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:.55rem;">
                    <div>
                        <span style="font-size:1.1rem;font-weight:800;color:var(--primary);">&#8369;{{ number_format($product->price, 2) }}</span>
                        <span style="font-size:.7rem;color:var(--text-light);">/{{ $product->unit }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:1.25rem;display:flex;justify-content:center;">{{ $searchResults->links() }}</div>
    @endif
</div>
</section>
@endif

{{-- ── FEATURED PRODUCE ──────────────────────────────── --}}
<section style="padding:5rem 0;background:var(--bg);position:relative;overflow:hidden;">
{{-- Decorative leaf --}}
<div style="position:absolute;top:-2rem;left:-2rem;font-size:10rem;opacity:0.04;pointer-events:none;transform:rotate(-20deg);">🍃</div>
<div style="max-width:80rem;margin:0 auto;padding:0 1.5rem;">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <span class="badge-wheat" style="margin-bottom:0.75rem;display:inline-block;">🌾 Fresh Today</span>
            <h2 style="font-family:Outfit,sans-serif;font-size:2.2rem;font-weight:800;color:var(--text);margin:0;">Featured Harvest</h2>
            <p style="font-size:0.95rem;color:var(--text-muted);margin:0.3rem 0 0;">Straight from Filipino farms to your cart</p>
        </div>
        <a href="{{ route('marketplace') }}" class="btn-ghost" style="padding:0.5rem 1.25rem;font-size:0.875rem;border-radius:0.75rem;">View All →</a>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:1.25rem;">
        @foreach($featured as $product)
        <div class="product-card">
            <a href="{{ route('products.show', $product) }}" style="display:block;text-decoration:none;">
                @php $icons = ['Vegetables'=>'🥦','Fruits'=>'🍓','Grains & Rice'=>'🌾','Root Crops'=>'🥔','Herbs & Spices'=>'🌿']; @endphp
                <div class="product-card-img" style="height:140px;">
                    @if($product->primaryPhoto())
                        <img src="{{ $product->primaryPhoto() }}" alt="{{ $product->name }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center;display:block;z-index:0;" />
                    @else
                        <span style="font-size:3rem;position:relative;z-index:1;">{{ $icons[$product->category] ?? '🛒' }}</span>
                    @endif
                </div>
            </a>
            <div style="padding:0.9rem;">
                <span class="badge" style="font-size:0.65rem;">{{ strtoupper($product->category) }}</span>
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--text);margin:0.5rem 0 0.2rem;">{{ $product->name }}</h3>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:0.6rem;">
                    <div>
                        <span style="font-size:1.1rem;font-weight:800;color:var(--primary);">&#8369;{{ number_format($product->price, 2) }}</span>
                        <span style="font-size:0.7rem;color:var(--text-light);">/{{ $product->unit }}</span>
                    </div>
                </div>
                @auth
                @if(Auth::user()->role !== 'admin')
                <form method="POST" action="{{ route('cart.add', $product) }}" style="margin-top:0.75rem;">
                    @csrf<input type="hidden" name="quantity" value="1"/>
                    <button type="submit" class="btn-primary" style="width:100%;padding:0.55rem;font-size:0.8rem;border-radius:0.55rem;">&#128722; Add to Cart</button>
                </form>
                @endif
                @else
                <a href="{{ route('login') }}" class="btn-ghost" style="display:block;margin-top:0.75rem;text-align:center;padding:0.55rem;font-size:0.8rem;border-radius:0.55rem;">Login to Buy</a>
                @endauth
            </div>
        </div>
        @endforeach
    </div>
</div>
</section>

{{-- ── ABOUT ─────────────────────────────────────────── --}}
<section style="padding:5rem 0;background:var(--bg-2);">
<div style="max-width:80rem;margin:0 auto;padding:0 1.5rem;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;">
        <div>
            <span class="badge-earth" style="margin-bottom:1.25rem;display:inline-block;">🌱 About Tanim</span>
            <h2 style="font-family:Outfit,sans-serif;font-size:2.5rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;line-height:1.2;">What is <span style="color:var(--primary);">Tanim</span>?</h2>
            <p style="font-size:1rem;color:var(--text-muted);line-height:1.8;margin:0 0 1rem;">
                <strong style="color:var(--text);">Tanim</strong> (Filipino for <em>"to plant"</em>) is a web-based agricultural marketplace system designed to empower Filipino farmers by providing them a direct digital channel to sell produce — removing intermediaries that reduce farmer income and inflate consumer prices.
            </p>
            <p style="font-size:1rem;color:var(--text-muted);line-height:1.8;margin:0 0 2rem;">
                The platform enables transparent transactions, order management, and a streamlined supply chain from farm gate to consumer.
            </p>
            <a href="{{ route('register') }}" class="btn-primary" style="padding:0.85rem 1.75rem;font-size:0.9rem;border-radius:0.75rem;">Get Started →</a>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            @foreach([['🌾','Farmer Portal','List products, manage inventory and orders.'],['🛒','Buyer Marketplace','Browse and purchase fresh produce.'],['📦','Order Tracking','Real-time status for every order.'],['📊','Dashboard','Analytics for buyers and farmers.']] as [$ic,$t,$d])
            <div class="page-card" style="padding:1.25rem;transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='var(--shadow-hover)'" onmouseout="this.style.transform='none';this.style.boxShadow='var(--shadow-card)'">
                <span style="font-size:1.75rem;display:block;margin-bottom:0.6rem;">{{ $ic }}</span>
                <h3 style="font-size:0.9rem;font-weight:700;color:var(--text);margin:0 0 0.3rem;">{{ $t }}</h3>
                <p style="font-size:0.78rem;color:var(--text-muted);margin:0;line-height:1.5;">{{ $d }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
</section>

{{-- ── FEATURES ──────────────────────────────────────── --}}
<section style="padding:5rem 0;background:var(--bg);">
<div style="max-width:80rem;margin:0 auto;padding:0 1.5rem;">
    <div style="text-align:center;margin-bottom:3.5rem;">
        <span class="badge-wheat" style="margin-bottom:0.75rem;display:inline-block;">🌿 Capabilities</span>
        <h2 style="font-family:Outfit,sans-serif;font-size:2.2rem;font-weight:800;color:var(--text);margin:0 0 0.75rem;">What Can Tanim Do?</h2>
        <p style="font-size:0.95rem;color:var(--text-muted);max-width:36rem;margin:0 auto;line-height:1.7;">A complete agricultural supply chain platform built for the modern Filipino farmer and consumer.</p>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem;">
        @foreach([
            ['🏪','Direct Sourcing','Buyers connect directly with local farmers, maximising freshness and traceability.'],
            ['💰','Fair & Transparent Pricing','Farmers set their own prices. No hidden fees. See exactly where every peso goes.'],
            ['🚚','Streamlined Logistics','Track orders from farm gate to delivery in real time.'],
            ['🌾','Farmer Empowerment','List products, manage inventory, and see income analytics without tech expertise.'],
            ['🔍','Smart Marketplace Search','Filter produce by category, price range, and availability.'],
            ['📋','Admin Control Panel','Admins monitor users, products, and platform-wide performance data.'],
        ] as [$ic,$t,$d])
        <div class="stat-card">
            <div style="background:var(--primary-faint);width:3.2rem;height:3.2rem;border-radius:0.75rem;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:1rem;box-shadow:var(--shadow-neu-sm);">{{ $ic }}</div>
            <h3 style="font-size:1rem;font-weight:700;color:var(--text);margin:0 0 0.5rem;">{{ $t }}</h3>
            <p style="font-size:0.85rem;color:var(--text-muted);margin:0;line-height:1.6;">{{ $d }}</p>
        </div>
        @endforeach
    </div>
</div>
</section>

{{-- ── WHO IS IT FOR ─────────────────────────────────── --}}
<section style="padding:5rem 0;background:var(--bg-2);">
<div style="max-width:80rem;margin:0 auto;padding:0 1.5rem;">
    <div style="text-align:center;margin-bottom:3rem;">
        <span class="badge" style="margin-bottom:0.75rem;display:inline-block;">Audience</span>
        <h2 style="font-family:Outfit,sans-serif;font-size:2.2rem;font-weight:800;color:var(--text);margin:0;">Built for Every Link in the Chain</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.5rem;">
        @foreach([
            ['🌱','Farmers','Sell Direct','Filipino farmers who want to sell harvest directly — without traders taking unfair margins.',['List & manage products','Set your own prices','Receive and fulfil orders','Track income'],'Join as Farmer','register'],
            ['🛍️','Buyers / Consumers','Buy Fresh','Households and businesses wanting authentic, fresh, affordable produce with full transparency.',['Browse fresh farm produce','Secure purchases','Order status tracking','Support local farmers'],'Start Shopping','marketplace'],
            ['⚙️','Administrators','Manage All','Platform administrators who maintain quality listings and a safe marketplace environment.',['Monitor users & activity','Manage product categories','Review transactions','Platform reports'],'Admin Login','login'],
        ] as [$em,$role,$badge,$desc,$perks,$cta,$route])
        <div class="page-card" style="padding:2rem;display:flex;flex-direction:column;">
            <div style="font-size:2.8rem;margin-bottom:1rem;">{{ $em }}</div>
            <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.75rem;">
                <h3 style="font-size:1.1rem;font-weight:800;color:var(--text);margin:0;">{{ $role }}</h3>
                <span class="badge">{{ $badge }}</span>
            </div>
            <p style="font-size:0.875rem;color:var(--text-muted);line-height:1.7;margin:0 0 1.25rem;flex:1;">{{ $desc }}</p>
            <ul style="list-style:none;padding:0;margin:0 0 1.5rem;display:flex;flex-direction:column;gap:0.5rem;">
                @foreach($perks as $p)
                <li style="display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;color:var(--text-muted);">
                    <span style="color:var(--primary);font-weight:900;">&#10003;</span> {{ $p }}
                </li>
                @endforeach
            </ul>
            <a href="{{ route($route) }}" class="btn-primary" style="display:block;text-align:center;padding:0.75rem;font-size:0.875rem;border-radius:0.75rem;">{{ $cta }} →</a>
        </div>
        @endforeach
    </div>
</div>
</section>

{{-- ── CTA BANNER ────────────────────────────────────── --}}
<section style="padding:5rem 0;background:linear-gradient(160deg,#175217 0%,#2e8b2e 40%,#4a3728 100%);position:relative;overflow:hidden;">
    {{-- Organic decorative elements --}}
    <div style="position:absolute;top:0;right:0;width:24rem;height:24rem;background:rgba(212,168,67,0.06);border-radius:9999px;pointer-events:none;"></div>
    <div style="position:absolute;bottom:-2rem;left:2rem;font-size:12rem;opacity:0.06;pointer-events:none;transform:rotate(15deg);">🌾</div>
    <div style="position:absolute;top:1rem;right:4rem;font-size:8rem;opacity:0.05;pointer-events:none;transform:rotate(-10deg);">🌿</div>
    <div style="max-width:56rem;margin:0 auto;padding:0 1.5rem;position:relative;z-index:10;text-align:center;">
        <div style="display:inline-flex;align-items:center;gap:0.5rem;background:rgba(212,168,67,0.15);border:1px solid rgba(212,168,67,0.30);border-radius:9999px;padding:0.4rem 1rem;margin-bottom:1.5rem;">
            <span style="font-size:0.8rem;font-weight:700;color:#e8c060;letter-spacing:0.06em;text-transform:uppercase;">🌱 Grow Together</span>
        </div>
        <h2 style="font-family:Outfit,sans-serif;font-size:2.5rem;font-weight:900;color:#f5f0e8;margin:0 0 1rem;line-height:1.2;">Support Philippine Agriculture Today</h2>
        <p style="font-size:1rem;color:#c4b898;max-width:36rem;margin:0 auto 2.5rem;line-height:1.7;">Join farmers and buyers on Tanim to build a fairer, fresher food supply chain across the Philippines — rooted in community, grown with purpose.</p>
        <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:center;">
            <a href="{{ route('register') }}" style="display:inline-block;padding:1rem 2.5rem;background:#f5f0e8;color:#175217;font-weight:800;font-size:1rem;border-radius:1.25rem;text-decoration:none;box-shadow:0 8px 25px rgba(0,0,0,0.25);"
               onmouseover="this.style.background='#fffbf0'" onmouseout="this.style.background='#f5f0e8'">🌱 Create Free Account</a>
            <a href="{{ route('login') }}" style="display:inline-block;padding:1rem 2.5rem;background:rgba(212,168,67,0.15);backdrop-filter:blur(6px);border:1.5px solid rgba(212,168,67,0.35);color:#e8c060;font-weight:700;font-size:1rem;border-radius:1.25rem;text-decoration:none;"
               onmouseover="this.style.background='rgba(212,168,67,0.25)'" onmouseout="this.style.background='rgba(212,168,67,0.15)'">Sign In</a>
        </div>
    </div>
</section>

{{-- ── DEVELOPERS ────────────────────────────────────── --}}
<section style="padding:5rem 0;background:var(--bg);">
<div style="max-width:80rem;margin:0 auto;padding:0 1.5rem;">
    <div style="text-align:center;margin-bottom:3rem;">
        <span class="badge" style="margin-bottom:0.75rem;display:inline-block;">The Team</span>
        <h2 style="font-family:Outfit,sans-serif;font-size:2.2rem;font-weight:800;color:var(--text);margin:0 0 0.5rem;">Built with Purpose</h2>
        <p style="font-size:0.95rem;color:var(--text-muted);max-width:30rem;margin:0 auto;">A systems development project by IT students tackling real-world agricultural challenges.</p>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:1.5rem;justify-content:center;max-width:42rem;margin:0 auto 2.5rem;">
        @foreach([['P','Pabalan','System Developer'],['L','Llegado','System Developer']] as [$i,$n,$r])
        <div class="page-card" style="flex:1;min-width:16rem;padding:2rem 2.5rem;text-align:center;transition:transform .2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='none'">
            <div style="width:5rem;height:5rem;border-radius:9999px;background:linear-gradient(135deg,#16a34a,#0d9488);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;box-shadow:0 4px 20px rgba(22,163,74,0.3);">
                <span style="font-family:Outfit,sans-serif;font-size:1.75rem;font-weight:900;color:#fff;">{{ $i }}</span>
            </div>
            <h3 style="font-size:1.15rem;font-weight:800;color:var(--text);margin:0 0 0.25rem;">{{ $n }}</h3>
            <p style="font-size:0.82rem;color:var(--text-muted);margin:0 0 0.75rem;">{{ $r }}</p>
            <span class="badge">Tanim Dev Team</span>
        </div>
        @endforeach
    </div>
    <div class="page-card" style="padding:2rem 2.5rem;max-width:42rem;margin:0 auto;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
        <span style="font-size:3rem;flex-shrink:0;">🎓</span>
        <div>
            <p style="font-size:0.7rem;font-weight:700;color:var(--text-light);letter-spacing:0.08em;text-transform:uppercase;margin:0 0 0.3rem;">Affiliated Institution</p>
            <h3 style="font-size:1.1rem;font-weight:800;color:var(--text);margin:0 0 0.15rem;">Technological University of the Philippines</h3>
            <p style="font-size:0.95rem;font-weight:700;color:var(--primary);margin:0 0 0.5rem;">Taguig Campus</p>
            <p style="font-size:0.82rem;color:var(--text-muted);margin:0;line-height:1.6;">Tanim is a systems development project from TUP-Taguig's Information Technology program, applying technology for national agricultural development.</p>
        </div>
    </div>
</div>
</section>

{{-- ── FOOTER ────────────────────────────────────────── --}}
<footer style="background:var(--bg-2);border-top:1px solid var(--border);padding:3rem 0;">
<div style="max-width:80rem;margin:0 auto;padding:0 1.5rem;">
    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1.5rem;">
        <div style="display:flex;align-items:center;gap:0.6rem;">
            <div style="background:var(--primary-faint);padding:0.5rem;border-radius:0.6rem;">
                <svg style="width:1.25rem;height:1.25rem;color:var(--primary)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
            </div>
            <span style="font-family:Outfit,sans-serif;font-size:1.25rem;font-weight:800;color:var(--primary);">Tanim</span>
        </div>
        <p style="font-size:0.8rem;color:var(--text-muted);text-align:center;">
            &copy; {{ date('Y') }} Tanim &mdash; Agricultural Marketplace. Developed by <strong style="color:var(--text);">Pabalan &amp; Llegado</strong>.
            <span style="color:var(--primary);"> Technological University of the Philippines &ndash; Taguig Campus.</span>
        </p>
        <div style="display:flex;gap:1.25rem;">
            @foreach([['Login',route('login')],['Register',route('register')],['Marketplace',route('marketplace')]] as [$l,$h])
            <a href="{{ $h }}" style="font-size:0.82rem;font-weight:600;color:var(--text-muted);text-decoration:none;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">{{ $l }}</a>
            @endforeach
        </div>
    </div>
</div>
</footer>
@endsection
