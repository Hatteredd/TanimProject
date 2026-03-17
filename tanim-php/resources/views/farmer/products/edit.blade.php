@extends('layouts.app')
@section('title','Edit Product — Tanim')
@section('content')
<div class="page-wrap-sm">
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:2rem;">
        <a href="{{ route('farmer.products.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:0.875rem;">← Back</a>
        <h1 class="section-title">Edit Product</h1>
    </div>

    @if($errors->any())
    <div class="alert-error" style="margin-bottom:1.5rem;">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('farmer.products.update', $product) }}" enctype="multipart/form-data" class="page-card" style="padding:2rem;">
        @csrf @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
            <div style="grid-column:1/-1;">
                <label class="label">Product Name *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="input" />
            </div>

            <div>
                <label class="label">Category *</label>
                <select name="category" required class="input">
                    @foreach(['Vegetables','Fruits','Grains & Rice','Root Crops','Herbs & Spices','Livestock','Seafood','Dairy','Other'] as $cat)
                    <option value="{{ $cat }}" {{ old('category', $product->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label">Unit *</label>
                <select name="unit" required class="input">
                    @foreach(['kg','g','piece','bundle','liter','dozen','sack','box'] as $u)
                    <option value="{{ $u }}" {{ old('unit', $product->unit) === $u ? 'selected' : '' }}>{{ $u }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label">Price (₱) *</label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" required min="0.01" step="0.01" class="input" />
            </div>

            <div>
                <label class="label">Stock Quantity *</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required min="0" class="input" />
            </div>

            <div style="grid-column:1/-1;">
                <label class="label">Description *</label>
                <textarea name="description" required rows="4" class="input" style="resize:vertical;">{{ old('description', $product->description) }}</textarea>
            </div>

            <div>
                <label class="label">Farm Location</label>
                <input type="text" name="farm_location" value="{{ old('farm_location', $product->farm_location) }}" class="input" />
            </div>

            <div>
                <label class="label">Harvest Date</label>
                <input type="date" name="harvest_date" value="{{ old('harvest_date', $product->harvest_date?->format('Y-m-d')) }}" class="input" />
            </div>

            <div>
                <label class="label">Status</label>
                <select name="is_active" class="input">
                    <option value="1" {{ old('is_active', $product->is_active) ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !old('is_active', $product->is_active) ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            @if($product->photos->count())
            <div style="grid-column:1/-1;">
                <label class="label" style="margin-bottom:0.75rem;">Current Photos</label>
                <div style="display:flex;flex-wrap:wrap;gap:0.75rem;">
                    @foreach($product->photos as $photo)
                    <div style="position:relative;">
                        <img src="{{ $photo->url() }}" style="width:6rem;height:6rem;object-fit:cover;border-radius:0.75rem;border:2px solid var(--border);" />
                        <label style="position:absolute;top:0.25rem;right:0.25rem;background:rgba(220,38,38,0.85);border-radius:9999px;width:1.25rem;height:1.25rem;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                            <input type="checkbox" name="delete_photos[]" value="{{ $photo->id }}" style="display:none;" onchange="this.parentElement.style.background=this.checked?'rgba(220,38,38,1)':'rgba(220,38,38,0.85)'" />
                            <span style="color:#fff;font-size:0.7rem;font-weight:900;">✕</span>
                        </label>
                        @if($photo->is_primary)<span style="position:absolute;bottom:0.25rem;left:0.25rem;background:var(--primary);color:var(--primary-fg);font-size:0.6rem;font-weight:700;padding:0.1rem 0.35rem;border-radius:9999px;">Primary</span>@endif
                    </div>
                    @endforeach
                </div>
                <p style="font-size:0.75rem;color:var(--text-light);margin-top:0.5rem;">Check the ✕ on photos you want to delete.</p>
            </div>
            @endif

            <div>
                <label class="label">Replace Primary Photo</label>
                <input type="file" name="image" accept="image/*" class="input" style="cursor:pointer;" />
            </div>

            <div>
                <label class="label">Add More Photos</label>
                <input type="file" name="photos[]" accept="image/*" multiple class="input" style="cursor:pointer;" />
            </div>
        </div>

        <div style="display:flex;gap:1rem;justify-content:flex-end;padding-top:1.25rem;border-top:1px solid var(--border);">
            <a href="{{ route('farmer.products.index') }}" style="padding:0.75rem 1.5rem;background:var(--bg);color:var(--text-muted);font-weight:600;font-size:0.875rem;border-radius:0.75rem;text-decoration:none;border:1px solid var(--border);">Cancel</a>
            <button type="submit" class="btn-primary" style="padding:0.75rem 1.5rem;font-size:0.875rem;border-radius:0.75rem;">Save Changes</button>
        </div>
    </form>
</div>
@endsection
