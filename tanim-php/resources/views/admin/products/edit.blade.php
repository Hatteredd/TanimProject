@extends('layouts.admin')
@section('title','Edit Product')
@section('page-title','🌾 Edit Product')
@section('content')
<div style="max-width:42rem;">
<div class="page-card" style="padding:1.75rem;">
    <form id="product-form" method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div style="display:grid;gap:1rem;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div style="grid-column:1/-1;">
                    <label class="label">Product Name</label>
                    <input name="name" type="text" class="input" value="{{ old('name',$product->name) }}" required />
                </div>
                <div>
                    <label class="label">Category</label>
                    <select name="category" class="input" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category',$product->category)===$cat?'selected':'' }}>{{ $cat }}</option>
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
                            {{ (string) old('supplier_id', $product->supplier_id) === (string) $supplier->id ? 'selected' : '' }}
                        >
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Type</label>
                    <input name="type" type="text" class="input" value="{{ old('type',$product->type) }}" placeholder="e.g. Organic" />
                </div>
                <div>
                    <label class="label">Unit</label>
                    <select name="unit" class="input" required>
                        @foreach(['kg','g','piece','bundle','liter','dozen','sack','box'] as $u)
                        <option value="{{ $u }}" {{ old('unit',$product->unit)===$u?'selected':'' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Price (₱)</label>
                    <input name="price" type="number" step="0.01" min="0" class="input" value="{{ old('price',$product->price) }}" required />
                </div>
                <div>
                    <label class="label">Stock</label>
                    <input name="stock" type="number" min="0" class="input" value="{{ old('stock',$product->stock) }}" required />
                </div>
                <div>
                    <label class="label">Farm Location (from supplier)</label>
                    <input id="supplier_location_preview" type="text" class="input" value="" readonly />
                </div>
                <div>
                    <label class="label">Harvest Date</label>
                    <input name="harvest_date" type="date" class="input" value="{{ old('harvest_date',$product->harvest_date?->format('Y-m-d')) }}" />
                </div>
                <div style="grid-column:1/-1;">
                    <label class="label">Description</label>
                    <textarea name="description" class="input" rows="3">{{ old('description',$product->description) }}</textarea>
                </div>
            </div>
            <div style="grid-column:1/-1;">
                <label class="label">Add New Photos <span style="font-weight:400;color:var(--text-light);">(Select up to {{ 6 - $product->photos->count() }} more photos)</span></label>
                <div id="drop-zone-edit" style="border:2px dashed var(--border);border-radius:1rem;padding:2rem;text-align:center;cursor:pointer;transition:all .2s;background:var(--bg-2);position:relative;overflow:hidden;">
                    <div id="drop-zone-content">
                        <div style="font-size:3rem;margin-bottom:1rem;">📸</div>
                        <p style="margin:0;font-size:1.1rem;font-weight:800;color:var(--text);">Drop photos here or click to browse</p>
                        <p style="margin:0.5rem 0 0;font-size:0.85rem;color:var(--text-muted);">You can add multiple photos at once.</p>
                    </div>
                    <input id="photos-input-edit" name="photos[]" type="file" class="input" accept="image/*" multiple style="position:absolute;inset:0;opacity:0;cursor:pointer;" />
                </div>
                
                <div id="preview-edit" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:1rem;margin-top:1.5rem;"></div>
                
                @error('photos')
                    <p style="color:var(--danger);font-size:0.85rem;margin-top:0.5rem;font-weight:700;">{{ $message }}</p>
                @enderror
            </div>

            @if($product->photos->isNotEmpty())
            <div>
                <label class="label">Current Gallery</label>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:.75rem;">
                    @foreach($product->photos as $photo)
                    <div style="border:1px solid var(--border);background:var(--bg);border-radius:.75rem;padding:.45rem;position:relative;">
                        <img src="{{ $photo->url() }}" alt="Photo" style="width:100%;height:90px;border-radius:.45rem;object-fit:cover;display:block;" />
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.45rem;gap:.4rem;">
                            @if($photo->is_primary)
                            <span style="font-size:.65rem;font-weight:700;color:var(--primary);">Primary</span>
                            @else
                            <span style="font-size:.65rem;color:var(--text-light);">Gallery</span>
                            @endif
                            <button type="button" onclick="deletePhoto('{{ route('admin.products.photos.destroy', [$product, $photo]) }}')" style="padding:.2rem .45rem;background:var(--danger-soft);color:var(--danger);font-size:.65rem;font-weight:700;border:none;border-radius:.4rem;cursor:pointer;">Delete</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            <div style="display:flex;align-items:center;gap:.6rem;">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active',$product->is_active)?'checked':'' }} style="width:1rem;height:1rem;accent-color:var(--primary);" />
                <label for="is_active" style="font-size:.875rem;font-weight:600;color:var(--text);cursor:pointer;">Active (visible on marketplace)</label>
            </div>
        </div>
        <div style="display:flex;gap:.75rem;margin-top:1.5rem;">
            <button type="submit" class="btn-primary" style="padding:.7rem 1.5rem;font-size:.875rem;border-radius:.75rem;">Save Changes</button>
            <a href="{{ route('admin.products.index') }}" class="btn-ghost" style="padding:.7rem 1.25rem;font-size:.875rem;border-radius:.75rem;">Cancel</a>
        </div>
    </form>
</div>
</div>
<script>
const dropZone = document.getElementById('drop-zone-edit');
const fileInput = document.getElementById('photos-input-edit');
const preview = document.getElementById('preview-edit');
const supplierSelect = document.getElementById('supplier_id');
const supplierLocationPreview = document.getElementById('supplier_location_preview');
const maxTotal = 6;

function syncSupplierPreview() {
    const selected = supplierSelect.options[supplierSelect.selectedIndex];
    supplierLocationPreview.value = selected?.dataset?.location || '';
}

supplierSelect.addEventListener('change', syncSupplierPreview);
syncSupplierPreview();

function countCurrentPhotos() {
    const galleryLabel = Array.from(document.querySelectorAll('label')).find(l => l.textContent.includes('Current Gallery'));
    if (galleryLabel) {
        const section = galleryLabel.parentElement;
        const photos = section.querySelectorAll('img[alt="Photo"]');
        return photos.length;
    }
    return 0;
}

function updatePreview(files) {
    preview.innerHTML = '';
    const currentCount = countCurrentPhotos();
    const remaining = maxTotal - currentCount;
    
    Array.from(files).slice(0, remaining).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = 'animate-fade-in';
            div.style.cssText = 'position:relative;border-radius:1rem;overflow:hidden;border:1px solid var(--border);box-shadow:var(--shadow-neu-sm);aspect-ratio:1;';
            div.innerHTML = `
                <img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;display:block;" />
                <div style="position:absolute;top:0.5rem;right:0.5rem;background:var(--primary);color:white;font-size:0.65rem;padding:0.2rem 0.6rem;border-radius:9999px;font-weight:900;box-shadow:0 2px 8px rgba(0,0,0,0.2);">
                    New
                </div>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

fileInput.addEventListener('change', (e) => {
    const currentCount = countCurrentPhotos();
    if ((currentCount + e.target.files.length) > maxTotal) {
        alert(`You already have ${currentCount} photos. You can only add ${maxTotal - currentCount} more to reach the limit of ${maxTotal}.`);
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

function deletePhoto(url) {
    if (!confirm('Remove this photo permanently?')) return;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            location.reload();
            return;
        }
        throw new Error(`HTTP error! status: ${response.status}`);
    })
    .catch(err => {
        console.error('Delete error:', err);
        alert('Failed to delete photo. Please try again.');
    });
}
</script>
@endsection
