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
    <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
        <form method="POST" action="{{ route('admin.products.import') }}" enctype="multipart/form-data" style="display:flex;gap:.45rem;align-items:center;">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="input" style="width:200px;padding:.45rem;font-size:.75rem;" required />
            <button type="submit" style="padding:.6rem .9rem;background:var(--bg);color:var(--text);font-size:.78rem;font-weight:700;border:1px solid var(--border);border-radius:.75rem;cursor:pointer;">Import Excel</button>
        </form>
        <a href="{{ route('admin.products.create') }}" class="btn-primary" style="padding:.6rem 1.25rem;font-size:.85rem;border-radius:.75rem;white-space:nowrap;">+ Add Product</a>
    </div>
</div>

<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;margin:-.35rem 0 .8rem;">
    <p style="margin:0;font-size:.75rem;color:var(--text-light);">Worksheet headings: <strong>name, category, description, price, unit, stock, farm_location, harvest_date, brand, type</strong></p>
    <p style="margin:0;font-size:.75rem;color:var(--text-light);">Total rows: <span id="datatable-count">{{ $products->count() }}</span></p>
</div>

<div class="glass" style="border-radius:1.25rem;overflow:hidden;">
    <table id="products-table" style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:var(--bg);border-bottom:1px solid var(--border);">
                <th class="th-cell" data-sort="name" style="text-align:left;cursor:pointer;">Product</th>
                <th class="th-cell" data-sort="price" style="text-align:right;cursor:pointer;">Price</th>
                <th class="th-cell" data-sort="stock" style="text-align:center;cursor:pointer;">Stock</th>
                <th class="th-cell" data-sort="status" style="text-align:center;cursor:pointer;">Status</th>
                <th class="th-cell" style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody id="products-table-body">
            @forelse($products as $product)
            <tr
                style="border-bottom:1px solid var(--border);{{ $product->trashed() ? 'opacity:.55;' : '' }}"
                class="tr-hover"
                data-name="{{ strtolower($product->name) }}"
                data-price="{{ (float) $product->price }}"
                data-stock="{{ (int) $product->stock }}"
                data-status="{{ $product->trashed() ? 'deleted' : ($product->is_active ? 'active' : 'inactive') }}"
            >
                <td class="td-cell">
                    <div style="display:flex;align-items:center;gap:.6rem;">
                        @if($product->primaryPhoto())
                        <img src="{{ $product->primaryPhoto() }}" alt="{{ $product->name }}" style="width:2.2rem;height:2.2rem;border-radius:.45rem;object-fit:cover;border:1px solid var(--border);" />
                        @endif
                        <div>
                        <p style="font-size:.85rem;font-weight:700;color:var(--text);margin:0;">{{ $product->name }}</p>
                        <p style="font-size:.72rem;color:var(--text-muted);margin:0;">{{ $product->category }} @if($product->photos->count()) · {{ $product->photos->count() }} photo{{ $product->photos->count() > 1 ? 's' : '' }} @endif</p>
                        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const body = document.getElementById('products-table-body');
    if (!body) return;

    const headers = document.querySelectorAll('#products-table th[data-sort]');
    let currentSort = { key: 'name', direction: 'asc' };

    function sortRows(key, direction) {
        const rows = Array.from(body.querySelectorAll('tr[data-name]'));
        rows.sort((a, b) => {
            const av = a.dataset[key] ?? '';
            const bv = b.dataset[key] ?? '';

            if (key === 'price' || key === 'stock') {
                return direction === 'asc' ? Number(av) - Number(bv) : Number(bv) - Number(av);
            }

            return direction === 'asc'
                ? String(av).localeCompare(String(bv))
                : String(bv).localeCompare(String(av));
        });

        rows.forEach((row) => body.appendChild(row));
        const countEl = document.getElementById('datatable-count');
        if (countEl) countEl.textContent = rows.length;
    }

    headers.forEach((header) => {
        header.addEventListener('click', function () {
            const key = this.dataset.sort;
            const direction = currentSort.key === key && currentSort.direction === 'asc' ? 'desc' : 'asc';
            currentSort = { key, direction };
            sortRows(key, direction);
        });
    });

    sortRows('name', 'asc');
});
</script>
@endsection
