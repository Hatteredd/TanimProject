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
            <div>
                <label class="label">Add Gallery Photos <span style="font-weight:400;color:var(--text-light);">(optional, up to 3 total)</span></label>
                <div id="drop-zone-edit" style="border:2px dashed var(--border);border-radius:.75rem;padding:1.5rem;text-align:center;cursor:pointer;transition:all .2s;background:var(--bg-soft);">
                    <div style="font-size:2rem;margin-bottom:.5rem;">📁</div>
                    <p style="margin:0;font-weight:600;color:var(--text);">Drag photos here or click to select</p>
                    <p style="margin:.25rem 0 0;font-size:.75rem;color:var(--text-light);">You can select multiple photos at once (up to 3)</p>
                </div>
                <input id="photos-input-edit" type="file" class="input" accept="image/*" multiple style="display:none;" />
                <div id="preview-edit" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:.75rem;margin-top:1rem;"></div>
                <p style="margin:.5rem 0 0;font-size:.72rem;color:var(--text-light);">You can keep up to 3 gallery photos per product.</p>
            </div>

            @if($product->photos->isNotEmpty())
            <div>
                <label class="label">Current Gallery</label>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:.75rem;">
                    @foreach($product->photos as $photo)
                    <div style="border:1px solid var(--border);background:var(--bg);border-radius:.75rem;padding:.45rem;position:relative;">
                        <img src="{{ asset('storage/'.$photo->path) }}" alt="Photo" style="width:100%;height:90px;border-radius:.45rem;object-fit:cover;display:block;" />
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
const form = document.getElementById('product-form');
const supplierSelect = document.getElementById('supplier_id');
const supplierLocationPreview = document.getElementById('supplier_location_preview');
const maxFiles = 3;
let selectedFiles = [];

function syncSupplierPreview() {
    const selected = supplierSelect.options[supplierSelect.selectedIndex];
    supplierLocationPreview.value = selected?.dataset?.location || '';
}

supplierSelect.addEventListener('change', syncSupplierPreview);
syncSupplierPreview();

function countCurrentPhotos() {
    // Count existing gallery photos from the DOM
    const galleryLabel = Array.from(document.querySelectorAll('label')).find(l => l.textContent.includes('Current Gallery'));
    if (galleryLabel) {
        const section = galleryLabel.parentElement;
        const photos = section.querySelectorAll('img[alt*="Photo"]');
        return photos.length;
    }
    return 0;
}

function updatePreview(files) {
    selectedFiles = Array.from(files).slice(0, maxFiles);
    preview.innerHTML = '';
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.style.cssText = 'position:relative;border-radius:.5rem;overflow:hidden;border:1px solid var(--border);';
            div.innerHTML = `
                <img src="${e.target.result}" style="width:100%;height:100px;object-fit:cover;display:block;" />
                <div style="position:absolute;top:0;right:0;background:var(--primary);color:white;font-size:.65rem;padding:.25rem .5rem;border-radius:0 0 0 .4rem;font-weight:700;">+${index + 1}</div>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

dropZone.addEventListener('click', () => fileInput.click());

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = 'var(--primary)';
    dropZone.style.backgroundColor = 'var(--primary)';
    dropZone.style.opacity = '0.1';
});

dropZone.addEventListener('dragleave', () => {
    dropZone.style.borderColor = 'var(--border)';
    dropZone.style.backgroundColor = 'var(--bg-soft)';
    dropZone.style.opacity = '1';
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = 'var(--border)';
    dropZone.style.backgroundColor = 'var(--bg-soft)';
    dropZone.style.opacity = '1';
    
    const currentCount = countCurrentPhotos();
    const totalWould = currentCount + e.dataTransfer.files.length;
    
    if (totalWould > maxFiles) {
        const remaining = maxFiles - currentCount;
        alert(`You already have ${currentCount} photo(s). You can add ${remaining} more.\n\nYou tried to add ${e.dataTransfer.files.length} files, which would exceed the limit of ${maxFiles} total photos.\n\nPlease select up to ${remaining} photo(s).`);
        return;
    }
    
    if (e.dataTransfer.files.length > maxFiles) {
        alert(`You can only add up to ${maxFiles} gallery photos. You dropped ${e.dataTransfer.files.length} files.\n\nPlease try with up to ${maxFiles} photos.`);
        return;
    }
    updatePreview(e.dataTransfer.files);
});

fileInput.addEventListener('change', (e) => {
    const currentCount = countCurrentPhotos();
    const totalWould = currentCount + e.target.files.length;
    
    if (totalWould > maxFiles) {
        const remaining = maxFiles - currentCount;
        alert(`You already have ${currentCount} photo(s). You can add ${remaining} more.\n\nYou tried to add ${e.target.files.length} files, which would exceed the limit of ${maxFiles} total photos.\n\nPlease select up to ${remaining} photo(s).`);
        e.target.value = '';
        selectedFiles = [];
        preview.innerHTML = '';
        return;
    }
    
    if (e.target.files.length > maxFiles) {
        alert(`You can only add up to ${maxFiles} gallery photos. You selected ${e.target.files.length} files.\n\nPlease select up to ${maxFiles} photos and try again.`);
        e.target.value = '';
        selectedFiles = [];
        preview.innerHTML = '';
        return;
    }
    updatePreview(e.target.files);
});

function deletePhoto(url) {
    if (!confirm('Remove this photo?')) return;
    
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

form.addEventListener('submit', (e) => {
    if (selectedFiles.length > 0) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        // Remove old photos[] fields if any
        formData.delete('photos[]');
        
        // Add selected files
        selectedFiles.forEach(file => {
            formData.append('photos[]', file);
        });
        
        // Submit via fetch
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.href = '{{ route("admin.products.index") }}';
            } else {
                return response.text().then(html => {
                    alert('Error saving product. Check console for details.');
                    console.log(html);
                });
            }
        })
        .catch(err => {
            console.error('Submit error:', err);
            alert('Error saving product');
        });
    }
});
</script>
@endsection
