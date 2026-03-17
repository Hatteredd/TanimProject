@extends('layouts.admin')
@section('title','Expenses')
@section('page-title','💰 Company Expense Tracker')

@section('content')
{{-- Summary cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;margin-bottom:2rem;">
    <div class="stat-card">
        <p style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin:0 0 .3rem;">This Month Total</p>
        <p style="font-size:1.5rem;font-weight:900;color:var(--danger);font-family:'Outfit';margin:0;">₱{{ number_format($totalMonth,2) }}</p>
    </div>
    <div class="stat-card">
        <p style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin:0 0 .3rem;">All-Time Total</p>
        <p style="font-size:1.5rem;font-weight:900;color:var(--text);font-family:'Outfit';margin:0;">₱{{ number_format($totalAll,2) }}</p>
    </div>
    @foreach($byType->take(4) as $type => $amt)
    <div class="stat-card">
        <p style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin:0 0 .3rem;">{{ $types[$type] ?? $type }}</p>
        <p style="font-size:1.25rem;font-weight:900;color:var(--primary);font-family:'Outfit';margin:0;">₱{{ number_format($amt,2) }}</p>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1.6fr;gap:1.5rem;">
    {{-- Add Expense Form --}}
    <div class="glass" style="border-radius:1.25rem;padding:1.5rem;height:fit-content;">
        <h2 style="font-family:'Outfit';font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;">➕ Record Expense</h2>
        <form method="POST" action="{{ route('admin.expenses.store') }}" style="display:flex;flex-direction:column;gap:.85rem;">
            @csrf
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Type</label>
                <select name="type" class="input" required>
                    @foreach($types as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Label / Description</label>
                <input name="label" type="text" class="input" required placeholder="e.g. March Meralco Bill" />
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Amount (₱)</label>
                <input name="amount" type="number" step="0.01" class="input" required placeholder="0.00" />
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Date</label>
                <input name="expense_date" type="date" class="input" value="{{ date('Y-m-d') }}" required />
            </div>
            <div style="display:flex;align-items:center;gap:.6rem;">
                <input type="checkbox" name="recurring" id="recurring" value="1" style="width:1rem;height:1rem;accent-color:var(--primary);" />
                <label for="recurring" style="font-size:.85rem;font-weight:600;color:var(--text-muted);cursor:pointer;">Recurring expense</label>
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Notes (optional)</label>
                <textarea name="notes" class="input" rows="2" placeholder="Additional details..."></textarea>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">Record Expense</button>
        </form>
    </div>

    {{-- Expense List --}}
    <div class="glass" style="border-radius:1.25rem;padding:1.5rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
            <h2 style="font-family:'Outfit';font-size:1rem;font-weight:800;color:var(--text);margin:0;">Expense History</h2>
            <form method="GET" action="{{ route('admin.expenses') }}" style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <select name="type" class="input" style="width:auto;padding:.4rem .75rem;">
                    <option value="">All Types</option>
                    @foreach($types as $val => $label)<option value="{{ $val }}" {{ request('type')==$val?'selected':'' }}>{{ $label }}</option>@endforeach
                </select>
                <button type="submit" class="btn-primary" style="padding:.4rem .9rem;font-size:.8rem;">Filter</button>
            </form>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:.85rem;">
                <thead>
                    <tr style="border-bottom:2px solid var(--border);">
                        @foreach(['Date','Type','Label','Amount','Recurring',''] as $h)
                        <th style="padding:.6rem .75rem;text-align:left;font-size:.72rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;white-space:nowrap;">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                    <tr style="border-bottom:1px solid var(--border);transition:background .15s;" onmouseover="this.style.background='var(--primary-faint)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:.65rem .75rem;color:var(--text-muted);white-space:nowrap;">{{ $exp->expense_date->format('M d, Y') }}</td>
                        <td style="padding:.65rem .75rem;"><span class="badge">{{ \App\Models\Expense::types()[$exp->type] ?? $exp->type }}</span></td>
                        <td style="padding:.65rem .75rem;color:var(--text);font-weight:600;">{{ $exp->label }}</td>
                        <td style="padding:.65rem .75rem;font-weight:800;color:var(--danger);white-space:nowrap;">₱{{ number_format($exp->amount,2) }}</td>
                        <td style="padding:.65rem .75rem;color:var(--text-muted);">{{ $exp->recurring ? '🔄 '.$exp->recurring_period : '—' }}</td>
                        <td style="padding:.65rem .75rem;">
                            <form method="POST" action="{{ route('admin.expenses.destroy', $exp) }}">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--text-light);font-size:.8rem;" onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='var(--text-light)'" onclick="return confirm('Delete this expense?')">✕</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="padding:2rem;text-align:center;color:var(--text-muted);">No expenses found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $expenses->links() }}</div>
    </div>
</div>
@endsection
