@extends('layouts.admin')
@section('title', ucfirst($table).' Records')
@section('page-title', '🗄️ '.ucfirst($table).' Records')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <a href="{{ route('admin.data.index') }}" style="font-size:.85rem;color:var(--text-muted);text-decoration:none;">← Back to Data Management</a>
    <a href="{{ route('admin.data.export', $table) }}" style="display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.1rem;background:var(--wheat-soft);color:var(--wheat-2);font-size:.82rem;font-weight:700;border:1px solid rgba(212,168,67,.3);border-radius:.75rem;text-decoration:none;">⬇ Export CSV</a>
</div>

<div class="glass" style="border-radius:1.25rem;overflow:auto;">
    <table style="width:100%;border-collapse:collapse;min-width:600px;">
        <thead>
            <tr style="background:var(--bg);border-bottom:1px solid var(--border);">
                @if($rows->isNotEmpty())
                @foreach(array_keys($rows->first()->toArray()) as $col)
                <th class="th-cell" style="text-align:left;">{{ $col }}</th>
                @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr style="border-bottom:1px solid var(--border);" class="tr-hover">
                @foreach($row->toArray() as $val)
                <td class="td-cell" style="font-size:.78rem;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    @if(is_array($val)) {{ json_encode($val) }}
                    @elseif(is_null($val)) <span style="color:var(--text-light);">null</span>
                    @else {{ $val }}
                    @endif
                </td>
                @endforeach
            </tr>
            @empty
            <tr><td colspan="10" style="padding:3rem;text-align:center;color:var(--text-muted);">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1.25rem;">{{ $rows->links() }}</div>
@endsection
