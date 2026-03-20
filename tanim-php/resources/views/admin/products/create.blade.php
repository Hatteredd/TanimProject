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
                    <label class="label">Brand</label>
                    <input name="brand" type="text" class="input" value="{{ old('brand') }}" placeholder="e.g. Tanim Fresh" />
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
            <div>
                <label class="label">Gallery Photos <span style="font-weight:400;color:var(--text-light);">(optional, up to 3)</span></label>
                <div id="drop-zone-create" style="border:2px dashed var(--border);border-radius:.75rem;padding:1.5rem;text-align:center;cursor:pointer;transition:all .2s;background:var(--bg-soft);">
                    <div style="font-size:2rem;margin-bottom:.5rem;">📁</div>
                    <p style="margin:0;font-weight:600;color:var(--text);">Drag photos here or click to select</p>
                    <p style="margin:.25rem 0 0;font-size:.75rem;color:var(--text-light);">You can select multiple photos at once (up to 3)</p>
                </div>
                <input id="photos-input-create" name="photos[]" type="file" class="input" accept="image/*" multiple style="display:none;" />
                <div id="preview-create" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:.75rem;margin-top:1rem;"></div>
                <p style="margin:.5rem 0 0;font-size:.72rem;color:var(--text-light);">Select up to 3 photos. The first photo will be used as primary when no product image is set.</p>
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
<script>
const dropZone = document.getElementById('drop-zone-create');
const fileInput = document.getElementById('photos-input-create');
const preview = document.getElementById('preview-create');
const maxFiles = 3;

function handleFiles(files) {
    if (files.length > maxFiles) {
        alert(`Please select up to ${maxFiles} photos only.`);
        return;
    }
    fileInput.files = files;
    updatePreview(files);
}

function updatePreview(files) {
    preview.innerHTML = '';
    Array.from(files).forEach((file, index) => {
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
    handleFiles(e.dataTransfer.files);
});

fileInput.addEventListener('change', (e) => updatePreview(e.target.files));
</script>
@endsection
