@extends('layouts.admin')
@section('title', $farmer->name . ' Store')
@section('page-title', '🏪 ' . $farmer->name)

@section('content')
@php
$products = $farmer->products;
$totalSold = $products->sum(fn($p) => $p->cartItems->sum('quantity'));
$allReviews = $products->flatMap(fn($p) => $p->reviews);
$avgRating = round($allReviews->avg('rating') ?? 0, 1);
$color = $score['total'] >= 70 ? 'var(--primary)' : ($score['total'] >= 40 ? 'var(--warn-text)' : 'var(--danger)');
$grade = $score['total'] >= 90 ? 'A+' : ($score['total'] >= 80 ? 'A' : ($score['total'] >= 70 ? 'B' : ($score['total'] >= 50 ? 'C' : 'D')));
@endphp

<div style="margin-bottom:1rem;"><a href="{{ route('admin.stores') }}" style="font-size:.875rem;font-weight:600;color:var(--primary);text-decoration:none;">← Back to Stores</a></div>

{{-- Score hero --}}
<div class="glass" style="border-radius:1.5rem;padding:2rem;margin-bottom:2rem;display:flex;align-items:center;gap:2rem;flex-wrap:wrap;">
    <div style="text-align:center;min-width:120px;">
        <div style="font-size:3rem;font-weight:900;font-family:'Outfit';color:{{ $color }};line-height:1;">{{ $score['total'] }}%</div>
        <div style="font-size:.8rem;font-weight:700;background:{{ $color }}22;color:{{ $color }};padding:.25rem .75rem;border-radius:9999px;display:inline-block;margin-top:.4rem;">Grade: {{ $grade }}</div>
        <div style="height:10px;background:var(--border);border-radius:9999px;overflow:hidden;margin-top:1rem;width:120px;">
            <div style="height:100%;width:{{ $score['total'] }}%;background:{{ $color }};border-radius:9999px;"></div>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:1rem;flex:1;">
        @foreach([
            ['⭐ Rating Score','ratingScore','40 pts'],
            ['🛒 Sales Score','salesScore','30 pts'],
            ['📦 Stock Health','stockScore','20 pts'],
            ['✓ Activity','fulfillScore','10 pts'],
        ] as [$l,$k,$max])
        <div class="stat-card" style="text-align:center;">
            <p style="font-size:.72rem;font-weight:700;color:var(--text-muted);margin:0 0 .3rem;">{{ $l }}</p>
            <p style="font-size:1.4rem;font-weight:900;color:var(--primary);font-family:'Outfit';margin:0;">{{ $score[$k] }}<span style="font-size:.7rem;color:var(--text-muted);">/{{ explode(' ',$max)[0] }}</span></p>
        </div>
        @endforeach
    </div>
    <div style="display:grid;grid-template-columns:repeat(2,auto);gap:1.25rem 2rem;">
        @foreach([['📦 Products',$products->count()],['🛒 Total Sold',$totalSold],['⭐ Avg Rating',$avgRating.'/5'],['💬 Reviews',$allReviews->count()]] as [$l,$v])
        <div>
            <p style="font-size:.72rem;color:var(--text-muted);margin:0;">{{ $l }}</p>
            <p style="font-size:1rem;font-weight:800;color:var(--text);margin:0;">{{ $v }}</p>
        </div>
        @endforeach
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
    {{-- Product list --}}
    <div class="glass" style="border-radius:1.25rem;padding:1.5rem;">
        <h2 style="font-family:'Outfit';font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;">📦 Products ({{ $products->count() }})</h2>
        @forelse($products as $product)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.65rem .75rem;background:var(--bg);border-radius:.7rem;margin-bottom:.5rem;">
            <div>
                <p style="font-size:.875rem;font-weight:700;color:var(--text);margin:0;">{{ $product->name }}</p>
                <p style="font-size:.72rem;color:var(--text-muted);margin:0;">{{ $product->category }} · ₱{{ number_format($product->price,2) }}/{{ $product->unit }}</p>
            </div>
            <div style="text-align:right;">
                <p style="font-size:.8rem;font-weight:700;color:{{ $product->stock <= 10 ? 'var(--danger)' : 'var(--primary)' }};margin:0;">{{ $product->stock }} in stock</p>
                <p style="font-size:.72rem;color:var(--text-muted);margin:0;">{{ $product->cartItems->sum('quantity') }} sold</p>
            </div>
        </div>
        @empty
        <p style="color:var(--text-muted);font-size:.875rem;">No products listed.</p>
        @endforelse
    </div>

    {{-- Reviews --}}
    <div class="glass" style="border-radius:1.25rem;padding:1.5rem;overflow-y:auto;max-height:500px;">
        <h2 style="font-family:'Outfit';font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;">💬 Recent Reviews ({{ $allReviews->count() }})</h2>
        @forelse($allReviews->sortByDesc('created_at')->take(10) as $review)
        <div style="padding:.75rem;background:var(--bg);border-radius:.75rem;margin-bottom:.6rem;">
            <div style="display:flex;justify-content:space-between;margin-bottom:.3rem;">
                <span style="font-size:.82rem;font-weight:700;color:var(--text);">{{ $review->user->name }}</span>
                <div style="display:flex;gap:1px;">
                    @for($i=1;$i<=5;$i++)<svg style="width:.75rem;height:.75rem;color:{{ $i<=$review->rating ? '#f59e0b' : 'var(--border)' }}" fill="{{ $i<=$review->rating ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.563.563 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>@endfor
                </div>
            </div>
            <p style="font-size:.8rem;color:var(--text-muted);margin:0;line-height:1.5;">{{ $review->comment ?? '(No comment)' }}</p>
            <p style="font-size:.7rem;color:var(--text-light);margin:.3rem 0 0;">on: <em>{{ $review->product->name ?? '?' }}</em></p>
        </div>
        @empty
        <p style="color:var(--text-muted);font-size:.875rem;">No reviews yet.</p>
        @endforelse
    </div>
</div>
@endsection
