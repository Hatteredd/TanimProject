@extends('layouts.admin')
@section('title','Suppliers')
@section('page-title','🚚 Supplier Management')

@section('content')
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;margin-bottom:2rem;">
    @foreach([
        ['Total Suppliers', $employees->count(), 'var(--primary)'],
        ['Active Suppliers', $activeSuppliers, 'var(--primary-2)'],
        ['Unique Specialties', $uniqueSpecialties, 'var(--warn-text)'],
        ['Unique Locations', $uniqueLocations, 'var(--danger)'],
    ] as [$label, $value, $color])
    <div class="stat-card">
        <p style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin:0 0 .3rem;">{{ $label }}</p>
        <p style="font-size:1.4rem;font-weight:900;color:{{ $color }};font-family:'Outfit';margin:0;">{{ $value }}</p>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1.9fr;gap:1.5rem;">
    <div class="glass" style="border-radius:1.25rem;padding:1.5rem;height:fit-content;">
        <h2 style="font-family:'Outfit';font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;">➕ Add Supplier</h2>
        <form method="POST" action="{{ route('admin.suppliers.store') }}" style="display:flex;flex-direction:column;gap:.85rem;">
            @csrf
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Supplier Name</label>
                <input name="name" type="text" class="input" placeholder="Full supplier name" required />
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Location</label>
                <input name="location" type="text" class="input" placeholder="e.g. Benguet, Cordillera" required />
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Specialty</label>
                <input name="specialty" type="text" class="input" placeholder="e.g. Highland vegetables" />
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Contact Number</label>
                <input name="contact_number" type="text" class="input" placeholder="e.g. 09XXXXXXXXX" />
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Status</label>
                <select name="status" class="input" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Notes</label>
                <textarea name="notes" class="input" rows="3" placeholder="Optional notes"></textarea>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">Add Supplier</button>
        </form>
    </div>

    <div class="glass" style="border-radius:1.25rem;padding:1.5rem;overflow-x:auto;">
        <h2 style="font-family:'Outfit';font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;">Supplier Records</h2>
        <table style="width:100%;border-collapse:collapse;font-size:.82rem;min-width:700px;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);">
                    @foreach(['Supplier','Location','Specialty','Contact','Status',''] as $heading)
                    <th style="padding:.6rem .75rem;text-align:left;font-size:.7rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;white-space:nowrap;">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $supplier)
                <tr style="border-bottom:1px solid var(--border);" onmouseover="this.style.background='var(--primary-faint)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:.7rem .75rem;">
                        <p style="font-weight:700;color:var(--text);margin:0;">{{ $supplier->name }}</p>
                        @if($supplier->notes)
                        <p style="font-size:.72rem;color:var(--text-muted);margin:.15rem 0 0;">{{ $supplier->notes }}</p>
                        @endif
                    </td>
                    <td style="padding:.7rem .75rem;color:var(--text-muted);">{{ $supplier->location }}</td>
                    <td style="padding:.7rem .75rem;color:var(--text-muted);">{{ $supplier->specialty ?: '—' }}</td>
                    <td style="padding:.7rem .75rem;color:var(--text-muted);">{{ $supplier->contact_number ?: '—' }}</td>
                    <td style="padding:.7rem .75rem;">
                        <span style="font-size:.7rem;font-weight:800;padding:.2rem .6rem;border-radius:9999px;background:{{ $supplier->status==='active' ? 'var(--primary-soft)' : 'rgba(220,38,38,.1)' }};color:{{ $supplier->status==='active' ? 'var(--primary-text)' : '#dc2626' }};">
                            {{ ucfirst($supplier->status) }}
                        </span>
                    </td>
                    <td style="padding:.7rem .75rem;">
                        <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--text-light);" onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='var(--text-light)'" onclick="return confirm('Remove {{ $supplier->name }}?')">✕</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="padding:2rem;text-align:center;color:var(--text-muted);">No suppliers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
