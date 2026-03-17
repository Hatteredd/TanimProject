@extends('layouts.app')
@section('title','Add Product — Tanim')
@section('content')
<div class="page-wrap-sm">
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:2rem;">
        <a href="{{ route('farmer.products.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:0.875rem;">← Back</a>
        <h1 class="section-title">Add New Product</h1>
    </div>

    @if($errors->any())
    <div class="alert-error" style="margin-bottom:1.5rem;">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('farmer.products.store') }}" enctype="multipart/form-data" class="page-card" style="padding:2rem;">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
            <div style="grid-column:1/-1;">
                <label class="label">Product Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="input" />
            </div>

            <div>
                <label class="label">Category *</label>
                <select name="category" required class="input">
                    <option value="">Select category</option>
                    @foreach(['Vegetables','Fruits','Grains & Rice','Root Crops','Herbs & Spices','Livestock','Seafood','Dairy','Other'] as $cat)
                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label">Unit *</label>
                <select name="unit" required class="input">
                    @foreach(['kg','g','piece','bundle','liter','dozen','sack','box'] as $u)
                    <option value="{{ $u }}" {{ old('unit','kg') === $u ? 'selected' : '' }}>{{ $u }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label">Price (₱) *</label>
                <input type="number" name="price" value="{{ old('price') }}" required min="0.01" step="0.01" class="input" />
            </div>

            <div>
                <label class="label">Stock Quantity *</label>
                <input type="number" name="stock" value="{{ old('stock', 0) }}" required min="0" class="input" />
            </div>

            <div style="grid-column:1/-1;">
                <label class="label">Description * <span style="font-weight:400;color:var(--text-light);">(min. 10 characters)</span></label>
                <textarea name="description" required rows="4" class="input" style="resize:vertical;">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="label">Farm Location</label>
                <input type="text" name="farm_location" value="{{ old('farm_location') }}" class="input" placeholder="e.g. Benguet, Cordillera" />
            </div>

            <div>
                <label class="label">Harvest Date</label>
                <input type="date" name="harvest_date" value="{{ old('harvest_date') }}" class="input" />
            </div>

            <div>
                <label class="label">Primary Photo</label>
                <input type="file" name="image" accept="image/*" class="input" style="cursor:pointer;" />
                <p style="font-size:0.75rem;color:var(--text-light);margin-top:0.25rem;">Max 3MB. JPG, PNG, WebP.</p>
            </div>

            <div>
                <label class="label">Additional Photos</label>
                <input type="file" name="photos[]" accept="image/*" multiple class="input" style="cursor:pointer;" />
                <p style="font-size:0.75rem;color:var(--text-light);margin-top:0.25rem;">Select multiple files. Max 3MB each.</p>
            </div>
        </div>

        <div style="display:flex;gap:1rem;justify-content:flex-end;padding-top:1.25rem;border-top:1px solid var(--border);">
            <a href="{{ route('farmer.products.index') }}" style="padding:0.75rem 1.5rem;background:var(--bg);color:var(--text-muted);font-weight:600;font-size:0.875rem;border-radius:0.75rem;text-decoration:none;border:1px solid var(--border);">Cancel</a>
            <button type="submit" class="btn-primary" style="padding:0.75rem 1.5rem;font-size:0.875rem;border-radius:0.75rem;">Create Product</button>
        </div>
    </form>
</div>
@endsection
