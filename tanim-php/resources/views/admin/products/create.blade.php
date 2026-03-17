@extends('layouts.admin')
@section('title','Add Product')
@section('page-title','🌾 Add Product')
@section('content')
<div style="max-width:42rem;">
<div class="page-card" style="padding:1.75rem;">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        <div style="display:grid;gap:1rem;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div style="grid-column:1/-1;">
                    <label class="label">Product Name</label>
                    <input name="name" type="text" class="input" value="{{ old('name') }}" required placeholder="e.g. Organic Tomatoes" />
                </div>
                <div>
                    <label class="label">Category</label>
                    <select name="category" class="input" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category')===$cat?'selected':'' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Unit</label>
                    <select name="unit" class="input" required>
                        @foreach(['kg','g','piece','bundle','liter','dozen','sack','box'] as $u)
                        <option value="{{ $u }}" {{ old('unit','kg')===$u?'selected':'' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Price (₱)</label>
                    <input name="price" type="number" step="0.01" min="0" class="input" value="{{ old('price') }}" required />
                </div>
                <div>
                    <label class="label">Stock</label>
                    <input name="stock" type="number" min="0" class="input" value="{{ old('stock',0) }}" required />
                </div>
                <div>
                    <label class="label">Farm Location</label>
                    <input name="farm_location" type="text" class="input" value="{{ old('farm_location') }}" placeholder="e.g. Benguet, Philippines" />
                </div>
                <div>
                    <label class="label">Harvest Date</label>
                    <input name="harvest_date" type="date" class="input" value="{{ old('harvest_date') }}" />
                </div>
                <div style="grid-column:1/-1;">
                    <label class="label">Description</label>
                    <textarea name="description" class="input" rows="3" placeholder="Product description...">{{ old('description') }}</textarea>
                </div>
            </div>
            <div>
                <label class="label">Product Image</label>
                <input name="image" type="file" class="input" accept="image/*" style="padding:.5rem;" />
            </div>
            <div style="display:flex;align-items:center;gap:.6rem;">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active','1')?'checked':'' }} style="width:1rem;height:1rem;accent-color:var(--primary);" />
                <label for="is_active" style="font-size:.875rem;font-weight:600;color:var(--text);cursor:pointer;">Active (visible on marketplace)</label>
            </div>
        </div>
        <div style="display:flex;gap:.75rem;margin-top:1.5rem;">
            <button type="submit" class="btn-primary" style="padding:.7rem 1.5rem;font-size:.875rem;border-radius:.75rem;">Create Product</button>
            <a href="{{ route('admin.products.index') }}" class="btn-ghost" style="padding:.7rem 1.25rem;font-size:.875rem;border-radius:.75rem;">Cancel</a>
        </div>
    </form>
</div>
</div>
@endsection
