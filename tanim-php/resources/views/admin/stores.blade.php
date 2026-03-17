@extends('layouts.admin')
@section('title','Stores')
@section('page-title','🏪 Store Performance')

@section('content')
<p style="color:var(--text-muted);font-size:.9rem;margin-bottom:1.75rem;">Performance score is computed from: Rating (40 pts) + Sales Volume (30 pts) + Stock Health (20 pts) + Activity (10 pts) = 100 pts</p>

<div style="display:flex;flex-direction:column;gap:1rem;">
@forelse($farmers as $farmer)
@php
    $sc = $storeScores[$farmer->id] ?? ['total'=>0,'ratingScore'=>0,'salesScore'=>0,'stockScore'=>0,'fulfillScore'=>0,'avgRating'=>0,'mySales'=>0,'avgStock'=>0];
    $color = $sc['total'] >= 70 ? 'var(--primary)' : ($sc['total'] >= 40 ? 'var(--warn-text)' : 'var(--danger)');
    $grade = $sc['total'] >= 90 ? 'A+' : ($sc['total'] >= 80 ? 'A' : ($sc['total'] >= 70 ? 'B' : ($sc['total'] >= 50 ? 'C' : 'D')));
@endphp
<div class="glass" style="border-radius:1.25rem;padding:1.5rem;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;transition:transform .2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
    {{-- Avatar & name --}}
    <div style="display:flex;align-items:center;gap:.85rem;flex:0 0 220px;">
        <div style="width:3rem;height:3rem;border-radius:9999px;background:var(--primary-faint);display:flex;align-items:center;justify-content:center;font-family:'Outfit';font-size:1.25rem;font-weight:900;color:var(--primary);">{{ strtoupper(substr($farmer->name,0,1)) }}</div>
        <div>
            <p style="font-weight:800;color:var(--text);font-size:.95rem;margin:0;">{{ $farmer->name }}</p>
            <p style="font-size:.75rem;color:var(--text-muted);margin:0;">{{ $farmer->products->count() }} products</p>
        </div>
    </div>

    {{-- Score bar --}}
    <div style="flex:1;min-width:200px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem;">
            <span style="font-size:.75rem;font-weight:700;color:var(--text-muted);">Overall Score</span>
            <div style="display:flex;align-items:center;gap:.5rem;">
                <span style="font-size:1.2rem;font-weight:900;font-family:'Outfit';color:{{ $color }};">{{ $sc['total'] }}%</span>
                <span style="font-size:.7rem;font-weight:800;background:{{ $color }}22;color:{{ $color }};padding:.15rem .5rem;border-radius:9999px;">{{ $grade }}</span>
            </div>
        </div>
        <div style="height:10px;background:var(--border);border-radius:9999px;overflow:hidden;">
            <div style="height:100%;width:{{ $sc['total'] }}%;background:{{ $color }};border-radius:9999px;"></div>
        </div>
    </div>

    {{-- Sub-scores --}}
    <div style="display:flex;gap:1.5rem;flex-wrap:wrap;">
        @foreach([['⭐ Rating','ratingScore',40],['🛒 Sales','salesScore',30],['📦 Stock','stockScore',20],['✓ Activity','fulfillScore',10]] as [$label,$key,$max])
        <div style="text-align:center;min-width:60px;">
            <p style="font-size:.65rem;font-weight:700;color:var(--text-muted);margin:0 0 .2rem;white-space:nowrap;">{{ $label }}</p>
            <p style="font-size:1rem;font-weight:900;color:var(--primary);font-family:'Outfit';margin:0;">{{ $sc[$key] }}<span style="font-size:.6rem;color:var(--text-muted);">/{{ $max }}</span></p>
        </div>
        @endforeach
    </div>

    {{-- Detailed metrics --}}
    <div style="display:flex;gap:1.5rem;flex-wrap:wrap;">
        <div style="text-align:center;">
            <p style="font-size:.65rem;color:var(--text-muted);margin:0;">Avg Rating</p>
            <p style="font-weight:800;color:var(--text);margin:0;">{{ number_format($sc['avgRating'],1) }}/5</p>
        </div>
        <div style="text-align:center;">
            <p style="font-size:.65rem;color:var(--text-muted);margin:0;">Units Sold</p>
            <p style="font-weight:800;color:var(--text);margin:0;">{{ $sc['mySales'] }}</p>
        </div>
    </div>

    <a href="{{ route('admin.store-detail', $farmer) }}" class="btn-primary" style="font-size:.8rem;padding:.5rem 1rem;border-radius:.65rem;white-space:nowrap;">View Details →</a>
</div>
@empty
<div style="text-align:center;padding:3rem;color:var(--text-muted);">No farmers registered yet.</div>
@endforelse
</div>
@endsection
