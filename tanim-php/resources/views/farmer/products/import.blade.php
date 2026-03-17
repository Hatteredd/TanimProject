@extends('layouts.app')
@section('title','Import Products — Tanim')
@section('content')
<div class="page-wrap-xs">
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:2rem;">
        <a href="{{ route('farmer.products.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:0.875rem;">← Back</a>
        <h1 class="section-title">Import Products via Excel</h1>
    </div>

    @if(session('success'))
    <div class="alert-success" style="margin-bottom:1.5rem;">✓ {{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-error" style="margin-bottom:1.5rem;">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <div style="background:var(--warn-soft);border:1px solid var(--warn-border);border-radius:1rem;padding:1.5rem;margin-bottom:1.5rem;">
        <h3 style="font-size:0.95rem;font-weight:700;color:var(--warn-text);margin:0 0 0.75rem;">📋 Required Excel Columns</h3>
        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
            @foreach(['name','category','description','price','unit','stock','farm_location','harvest_date'] as $col)
            <code style="background:var(--warn-border);color:var(--warn-text);padding:0.2rem 0.5rem;border-radius:0.35rem;font-size:0.8rem;font-weight:700;">{{ $col }}</code>
            @endforeach
        </div>
        <p style="font-size:0.8rem;color:var(--warn-text);margin:0.75rem 0 0;opacity:0.85;">First row must be the header row. Only <code>name</code> is required. Date format: YYYY-MM-DD.</p>
    </div>

    <form method="POST" action="{{ route('farmer.products.import.store') }}" enctype="multipart/form-data" class="page-card" style="padding:2rem;">
        @csrf

        <div style="margin-bottom:1.5rem;">
            <label class="label">Select Excel File (.xlsx, .xls, .csv)</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="input" style="cursor:pointer;border-style:dashed;" />
            <p style="font-size:0.75rem;color:var(--text-light);margin-top:0.35rem;">Max file size: 5MB</p>
        </div>

        <button type="submit" class="btn-primary" style="width:100%;padding:0.85rem;font-size:0.95rem;border-radius:0.75rem;">
            📥 Import Products
        </button>
    </form>
</div>
@endsection
