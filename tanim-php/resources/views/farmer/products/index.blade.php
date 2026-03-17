@extends('layouts.app')
@section('title','My Products — Tanim')
@section('content')
<div class="page-wrap">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:2rem;">
        <h1 class="section-title">My Products</h1>
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
            <a href="{{ route('farmer.products.import') }}" class="btn-ghost" style="padding:0.6rem 1.1rem;font-size:0.875rem;border-radius:0.75rem;">📥 Import Excel</a>
            <a href="{{ route('farmer.products.create') }}" class="btn-primary" style="padding:0.6rem 1.1rem;font-size:0.875rem;border-radius:0.75rem;">+ Add Product</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert-success" style="margin-bottom:1.5rem;">✓ {{ session('success') }}</div>
    @endif

    <div class="page-card" style="overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:var(--bg);border-bottom:1px solid var(--border);">
                    <th class="th-cell" style="text-align:left;">Product</th>
                    <th class="th-cell" style="text-align:left;">Category</th>
                    <th class="th-cell" style="text-align:right;">Price</th>
                    <th class="th-cell" style="text-align:right;">Stock</th>
                    <th class="th-cell" style="text-align:center;">Status</th>
                    <th class="th-cell" style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="{{ $product->trashed() ? 'trashed-row' : '' }}" style="border-bottom:1px solid var(--border);{{ $product->trashed() ? 'opacity:0.55;background:var(--danger-soft);' : '' }}">
                    <td class="td-cell">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            @if($product->primaryPhoto())
                            <img src="{{ $product->primaryPhoto() }}" style="width:2.5rem;height:2.5rem;border-radius:0.5rem;object-fit:cover;border:1px solid var(--border);" />
                            @else
                            <div style="width:2.5rem;height:2.5rem;border-radius:0.5rem;background:var(--primary-faint);display:flex;align-items:center;justify-content:center;font-size:1.2rem;">🌿</div>
                            @endif
                            <div>
                                <p style="font-size:0.875rem;font-weight:700;color:var(--text);margin:0;">{{ $product->name }}</p>
                                @if($product->trashed())<span style="font-size:0.7rem;color:var(--danger);font-weight:700;">DELETED</span>@endif
                            </div>
                        </div>
                    </td>
                    <td class="td-cell" style="color:var(--text-muted);">{{ $product->category }}</td>
                    <td class="td-cell" style="text-align:right;font-weight:700;color:var(--primary);">₱{{ number_format($product->price, 2) }}</td>
                    <td class="td-cell" style="text-align:right;font-weight:{{ $product->stock <= 5 ? '700' : '400' }};color:{{ $product->stock <= 5 ? 'var(--danger)' : 'var(--text)' }};">{{ $product->stock }}</td>
                    <td class="td-cell" style="text-align:center;">
                        <span style="font-size:0.72rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:9999px;background:{{ $product->is_active ? 'var(--primary-soft)' : 'var(--bg)' }};color:{{ $product->is_active ? 'var(--primary-text)' : 'var(--text-muted)' }};border:1px solid {{ $product->is_active ? 'rgba(22,163,74,0.2)' : 'var(--border)' }};">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="td-cell" style="text-align:center;">
                        @if($product->trashed())
                        <form method="POST" action="{{ route('farmer.products.restore', $product->id) }}" style="display:inline;">
                            @csrf
                            <button type="submit" style="padding:0.35rem 0.75rem;background:var(--primary-soft);color:var(--primary-text);font-size:0.75rem;font-weight:700;border:none;border-radius:0.5rem;cursor:pointer;">Restore</button>
                        </form>
                        @else
                        <div style="display:flex;gap:0.5rem;justify-content:center;">
                            <a href="{{ route('farmer.products.edit', $product) }}" style="padding:0.35rem 0.75rem;background:rgba(37,99,235,0.1);color:#2563eb;font-size:0.75rem;font-weight:700;border-radius:0.5rem;text-decoration:none;">Edit</a>
                            <form method="POST" action="{{ route('farmer.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="padding:0.35rem 0.75rem;background:var(--danger-soft);color:var(--danger);font-size:0.75rem;font-weight:700;border:none;border-radius:0.5rem;cursor:pointer;">Delete</button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="padding:3rem;text-align:center;color:var(--text-muted);">No products yet. <a href="{{ route('farmer.products.create') }}" style="color:var(--primary);font-weight:700;">Add your first product →</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.5rem;">{{ $products->links() }}</div>
</div>
@endsection
