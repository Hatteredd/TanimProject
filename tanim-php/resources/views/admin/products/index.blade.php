@extends('layouts.admin')
@section('title','Products')
@section('page-title','🌾 Content Management — Products')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <form method="GET" style="display:flex;gap:.6rem;flex-wrap:wrap;flex:1;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="input" style="flex:1;min-width:160px;" />
        <select name="category" class="input" style="width:auto;min-width:140px;">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category')===$cat?'selected':'' }}>{{ $cat }}</option>
            @endforeach
        </select>
        <select name="status" class="input" style="width:auto;min-width:120px;">
            <option value="">All Status</option>
            <option value="active" {{ request('status')==='active'?'selected':'' }}>Active</option>
            <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Inactive</option>
            <option value="trashed" {{ request('status')==='trashed'?'selected':'' }}>Deleted</option>
        </select>
        <button type="submit" class="btn-primary" style="padding:.6rem 1.1rem;font-size:.85rem;border-radius:.75rem;">Filter</button>
        @if(request()->hasAny(['search','category','status']))<a href="{{ route('admin.products.index') }}" style="padding:.6rem .9rem;background:var(--bg);color:var(--text-muted);font-size:.85rem;border:1px solid var(--border);border-radius:.75rem;text-decoration:none;">✕</a>@endif
    </form>
    <a href="{{ route('admin.products.create') }}" class="btn-primary" style="padding:.6rem 1.25rem;font-size:.85rem;border-radius:.75rem;white-space:nowrap;">+ Add Product</a>
</div>

<div class="glass" style="border-radius:1.25rem;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:var(--bg);border-bottom:1px solid var(--border);">
                <th class="th-cell" style="text-align:left;">Product</th>
                <th class="th-cell" style="text-align:right;">Price</th>
                <th class="th-cell" style="text-align:center;">Stock</th>
                <th class="th-cell" style="text-align:center;">Status</th>
                <th class="th-cell" style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr style="border-bottom:1px solid var(--border);{{ $product->trashed() ? 'opacity:.55;' : '' }}" class="tr-hover">
                <td class="td-cell">
                    <div>
                        <p style="font-size:.85rem;font-weight:700;color:var(--text);margin:0;">{{ $product->name }}</p>
                        <p style="font-size:.72rem;color:var(--text-muted);margin:0;">{{ $product->category }}</p>
                    </div>
                </td>
                <td class="td-cell" style="text-align:right;font-size:.85rem;font-weight:700;color:var(--primary);">₱{{ number_format($product->price,2) }}</td>
                <td class="td-cell" style="text-align:center;">
                    <span style="font-size:.78rem;font-weight:700;color:{{ $product->stock<=5?'var(--danger)':($product->stock<=15?'var(--wheat-2)':'var(--primary)') }};">{{ $product->stock }}</span>
                </td>
                <td class="td-cell" style="text-align:center;">
                    @if($product->trashed())
                    <span style="font-size:.7rem;font-weight:700;padding:.18rem .55rem;border-radius:9999px;background:var(--danger-soft);color:var(--danger);">Deleted</span>
                    @elseif($product->is_active)
                    <span style="font-size:.7rem;font-weight:700;padding:.18rem .55rem;border-radius:9999px;background:var(--primary-soft);color:var(--primary-text);">Active</span>
                    @else
                    <span style="font-size:.7rem;font-weight:700;padding:.18rem .55rem;border-radius:9999px;background:var(--warn-soft);color:var(--warn-text);">Inactive</span>
                    @endif
                </td>
                <td class="td-cell" style="text-align:center;">
                    <div style="display:flex;gap:.35rem;justify-content:center;flex-wrap:wrap;">
                        @if($product->trashed())
                        <form method="POST" action="{{ route('admin.products.restore', $product->id) }}" style="margin:0;">
                            @csrf
                            <button type="submit" style="padding:.28rem .65rem;background:var(--primary-faint);color:var(--primary);font-size:.72rem;font-weight:700;border:none;border-radius:.45rem;cursor:pointer;">Restore</button>
                        </form>
                        <form method="POST" action="{{ route('admin.products.force-delete', $product->id) }}" style="margin:0;" onsubmit="return confirm('Permanently delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="padding:.28rem .65rem;background:var(--danger-soft);color:var(--danger);font-size:.72rem;font-weight:700;border:none;border-radius:.45rem;cursor:pointer;">Purge</button>
                        </form>
                        @else
                        <a href="{{ route('admin.products.edit', $product) }}" style="padding:.28rem .65rem;background:var(--primary-faint);color:var(--primary);font-size:.72rem;font-weight:700;border-radius:.45rem;text-decoration:none;">Edit</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="margin:0;" onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="padding:.28rem .65rem;background:var(--danger-soft);color:var(--danger);font-size:.72rem;font-weight:700;border:none;border-radius:.45rem;cursor:pointer;">Delete</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:3rem;text-align:center;color:var(--text-muted);">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1.25rem;">{{ $products->links() }}</div>
@endsection
