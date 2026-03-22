@extends('layouts.admin')
@section('title','Add Product')
@section('page-title','🌾 Add Product')
@section('content')
<div style="max-width:42rem;">
<div class="page-card" style="padding:1.75rem;">
            <form id="product-form" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
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
                    <label class="label">Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="input" required>
                        <option value="">Select supplier</option>
                        @foreach($suppliers as $supplier)
                        <option
                            value="{{ $supplier->id }}"
                            data-location="{{ $supplier->location }}"
                            {{ (string) old('supplier_id') === (string) $supplier->id ? 'selected' : '' }}
                        >
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Type</label>
                    <input name="type" type="text" class="input" value="{{ old('type') }}" placeholder="e.g. Organic" />
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
                    <label class="label">Farm Location (from supplier)</label>
                    <input id="supplier_location_preview" type="text" class="input" value="" readonly />
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
            <div style="grid-column:1/-1;">
                <label class="label">Product Photos <span style="font-weight:400;color:var(--text-light);">(Select up to 6 photos at once)</span></label>
                <div id="drop-zone-create" style="border:2px dashed var(--border);border-radius:1rem;padding:2rem;text-align:center;cursor:pointer;transition:all .2s;background:var(--bg-2);position:relative;overflow:hidden;">
                    <div id="drop-zone-content">
                        <div style="font-size:3rem;margin-bottom:1rem;">📸</div>
                        <p style="margin:0;font-size:1.1rem;font-weight:800;color:var(--text);">Drop photos here or click to browse</p>
                        <p style="margin:0.5rem 0 0;font-size:0.85rem;color:var(--text-muted);">The first photo will be the main cover image.</p>
                    </div>
                    <input id="photos-input-create" name="photos[]" type="file" class="input" accept="image/*" multiple style="position:absolute;inset:0;opacity:0;cursor:pointer;" />
                </div>
                
                <div id="preview-create" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:1rem;margin-top:1.5rem;"></div>
                
                @error('photos')
                    <p style="color:var(--danger);font-size:0.85rem;margin-top:0.5rem;font-weight:700;">{{ $message }}</p>
                @enderror
            </div>
            <div style="display:flex;align-items:center;gap:.6rem;">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active','1')?'checked':'' }} style="width:1rem;height:1rem;accent-color:var(--primary);" />
                <label for="is_active" style="font-size:.875rem;font-weight:600;color:var(--text);cursor:pointer;">Active (visible on marketplace)</label>
            </div>
        </div>
        <div style="display:flex;gap:.75rem;margin-top:1.5rem;">
            <button type="submit" class="btn-primary" style="padding:.7rem 1.5rem;font-size:.875rem;border-radius:.75rem;">Create Product</button>
            <a href="{{ route('admin.products.index') }}" class="btn-ghost" style="padding:.7rem 1.25rem;font-size:.875rem;border-radius:.75rem;">Cancel</a>
            </form>
</div>
</div>
<script>
const dropZone = document.getElementById('drop-zone-create');
const fileInput = document.getElementById('photos-input-create');
const preview = document.getElementById('preview-create');
const supplierSelect = document.getElementById('supplier_id');
const supplierLocationPreview = document.getElementById('supplier_location_preview');
const maxFiles = 6;

function syncSupplierPreview() {
    const selected = supplierSelect.options[supplierSelect.selectedIndex];
    supplierLocationPreview.value = selected?.dataset?.location || '';
}

supplierSelect.addEventListener('change', syncSupplierPreview);
syncSupplierPreview();

function updatePreview(files) {
    preview.innerHTML = '';
    Array.from(files).slice(0, maxFiles).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = 'animate-fade-in';
            div.style.cssText = 'position:relative;border-radius:1rem;overflow:hidden;border:1px solid var(--border);box-shadow:var(--shadow-neu-sm);aspect-ratio:1;';
            div.innerHTML = `
                <img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;display:block;" />
                <div style="position:absolute;top:0.5rem;right:0.5rem;background:var(--primary);color:white;font-size:0.65rem;padding:0.2rem 0.6rem;border-radius:9999px;font-weight:900;box-shadow:0 2px 8px rgba(0,0,0,0.2);">
                    ${index === 0 ? 'Cover' : 'Gallery'}
                </div>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > maxFiles) {
        alert(`You can only select up to ${maxFiles} photos at once.`);
        e.target.value = '';
        preview.innerHTML = '';
        return;
    }
    updatePreview(e.target.files);
});

// Drag & Drop feedback
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = 'var(--primary)';
    dropZone.style.background = 'var(--primary-faint)';
});

dropZone.addEventListener('dragleave', () => {
    dropZone.style.borderColor = 'var(--border)';
    dropZone.style.background = 'var(--bg-2)';
});

dropZone.addEventListener('drop', () => {
    dropZone.style.borderColor = 'var(--border)';
    dropZone.style.background = 'var(--bg-2)';
});
</script>
@endsection
