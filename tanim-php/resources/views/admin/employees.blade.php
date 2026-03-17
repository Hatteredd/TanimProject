@extends('layouts.admin')
@section('title','Employees')
@section('page-title','👥 Employee Management')

@section('content')
{{-- Payroll summary --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;margin-bottom:2rem;">
    @foreach([
        ['Monthly Payroll', '₱'.number_format($totalSalary,2), 'var(--danger)'],
        ['Total Bonuses',   '₱'.number_format($totalBonus,2),  'var(--warn-text)'],
        ['13th Month (Total)', '₱'.number_format($total13th,2), 'var(--primary-2)'],
    ] as [$l,$v,$c])
    <div class="stat-card">
        <p style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin:0 0 .3rem;">{{ $l }}</p>
        <p style="font-size:1.4rem;font-weight:900;color:{{ $c }};font-family:'Outfit';margin:0;">{{ $v }}</p>
    </div>
    @endforeach
    <div class="stat-card">
        <p style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin:0 0 .3rem;">Active Employees</p>
        <p style="font-size:1.4rem;font-weight:900;color:var(--primary);font-family:'Outfit';margin:0;">{{ $employees->where('status','active')->count() }}</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1.8fr;gap:1.5rem;">
    {{-- Add Employee Form --}}
    <div class="glass" style="border-radius:1.25rem;padding:1.5rem;height:fit-content;">
        <h2 style="font-family:'Outfit';font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;">➕ Add Employee</h2>
        <form method="POST" action="{{ route('admin.employees.store') }}" style="display:flex;flex-direction:column;gap:.85rem;">
            @csrf
            @foreach([['Name','name','text','Full name'],['Position','position','text','e.g. Delivery Driver'],['Department','department','text','e.g. Logistics']] as [$l,$n,$t,$p])
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">{{ $l }}</label>
                <input name="{{ $n }}" type="{{ $t }}" class="input" placeholder="{{ $p }}" required />
            </div>
            @endforeach
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Base Salary (monthly ₱)</label>
                <input name="base_salary" type="number" step="0.01" class="input" placeholder="0.00" required />
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Bonus (₱)</label>
                <input name="bonus" type="number" step="0.01" class="input" placeholder="0.00" value="0" />
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Hire Date</label>
                <input name="hire_date" type="date" class="input" value="{{ date('Y-m-d') }}" required />
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Status</label>
                <select name="status" class="input">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">Add Employee</button>
        </form>
    </div>

    {{-- Employee Table --}}
    <div class="glass" style="border-radius:1.25rem;padding:1.5rem;overflow-x:auto;">
        <h2 style="font-family:'Outfit';font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;">Employee Records</h2>
        <table style="width:100%;border-collapse:collapse;font-size:.82rem;min-width:680px;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);">
                    @foreach(['Employee','Department','Base Salary','Bonus','13th Month','Tenure','Status',''] as $h)
                    <th style="padding:.6rem .75rem;text-align:left;font-size:.7rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                <tr style="border-bottom:1px solid var(--border);" onmouseover="this.style.background='var(--primary-faint)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:.7rem .75rem;">
                        <p style="font-weight:700;color:var(--text);margin:0;">{{ $emp->name }}</p>
                        <p style="font-size:.72rem;color:var(--text-muted);margin:0;">{{ $emp->position }}</p>
                    </td>
                    <td style="padding:.7rem .75rem;color:var(--text-muted);">{{ $emp->department }}</td>
                    <td style="padding:.7rem .75rem;font-weight:700;color:var(--text);white-space:nowrap;">₱{{ number_format($emp->base_salary,2) }}</td>
                    <td style="padding:.7rem .75rem;font-weight:700;color:var(--warn-text);white-space:nowrap;">₱{{ number_format($emp->bonus,2) }}</td>
                    <td style="padding:.7rem .75rem;font-weight:700;color:var(--primary-2);white-space:nowrap;">₱{{ number_format($emp->thirteenthMonth(),2) }}</td>
                    <td style="padding:.7rem .75rem;color:var(--text-muted);white-space:nowrap;">{{ $emp->yearsOfService() }} yrs</td>
                    <td style="padding:.7rem .75rem;">
                        <span style="font-size:.7rem;font-weight:800;padding:.2rem .6rem;border-radius:9999px;background:{{ $emp->status==='active' ? 'var(--primary-soft)' : 'rgba(220,38,38,.1)' }};color:{{ $emp->status==='active' ? 'var(--primary-text)' : '#dc2626' }};">
                            {{ ucfirst($emp->status) }}
                        </span>
                    </td>
                    <td style="padding:.7rem .75rem;">
                        <form method="POST" action="{{ route('admin.employees.destroy', $emp) }}">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--text-light);" onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='var(--text-light)'" onclick="return confirm('Remove {{ $emp->name }}?')">✕</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="padding:2rem;text-align:center;color:var(--text-muted);">No employees found.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- Payroll legend --}}
        <div style="margin-top:1.5rem;padding:1rem;background:var(--bg);border-radius:.85rem;border:1px solid var(--border);">
            <p style="font-size:.75rem;font-weight:700;color:var(--text-muted);margin:0 0 .4rem;">📌 How 13th Month Pay is Calculated</p>
            <p style="font-size:.8rem;color:var(--text-muted);margin:0;line-height:1.6;">
                <strong style="color:var(--text);">13th Month = (Basic Salary × Months Worked This Year) ÷ 12</strong><br>
                Per Philippine Labor Law (PD 851). Paid on or before December 24.
            </p>
        </div>
    </div>
</div>
@endsection
