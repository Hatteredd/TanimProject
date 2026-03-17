@extends('layouts.admin')
@section('title','Data Management')
@section('page-title','🗄️ Database / Data Management')
@section('content')

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.25rem;margin-bottom:2rem;">
    @foreach($tables as $key => $info)
    <div class="page-card" style="padding:1.5rem;">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
            <span style="font-size:2rem;">{{ $info['icon'] }}</span>
            <div>
                <h3 style="font-family:'Outfit',sans-serif;font-size:1rem;font-weight:800;color:var(--text);margin:0;">{{ $info['label'] }}</h3>
                <p style="font-size:.78rem;color:var(--text-muted);margin:0;">{{ number_format($info['count']) }} records</p>
            </div>
        </div>
        <div style="display:flex;gap:.6rem;flex-wrap:wrap;">
            <a href="{{ route('admin.data.table', $key) }}" class="btn-ghost" style="padding:.45rem .9rem;font-size:.78rem;border-radius:.6rem;">View Records</a>
            <a href="{{ route('admin.data.export', $key) }}" style="display:inline-flex;align-items:center;gap:.3rem;padding:.45rem .9rem;background:var(--wheat-soft);color:var(--wheat-2);font-size:.78rem;font-weight:700;border:1px solid rgba(212,168,67,.3);border-radius:.6rem;text-decoration:none;">⬇ Export CSV</a>
        </div>
    </div>
    @endforeach
</div>

<div class="page-card" style="padding:1.5rem;">
    <h2 style="font-family:'Outfit',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);margin:0 0 1rem;">📦 Manage Products</h2>
    <p style="font-size:.875rem;color:var(--text-muted);margin:0 0 1rem;">Add, edit, or remove products from the Tanim catalog via the Products admin panel.</p>
    <a href="{{ route('admin.products.index') }}" class="btn-primary" style="padding:.6rem 1.25rem;font-size:.85rem;border-radius:.75rem;">Go to Products →</a>
</div>
@endsection
