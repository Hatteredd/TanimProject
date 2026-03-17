@extends('layouts.admin')
@section('title','Activity Logs')
@section('page-title','📋 Activity Logs')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <form method="GET" style="display:flex;gap:.6rem;flex-wrap:wrap;flex:1;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description..." class="input" style="flex:1;min-width:160px;" />
        <select name="action" class="input" style="width:auto;min-width:120px;">
            <option value="">All Actions</option>
            @foreach($actions as $a)
            <option value="{{ $a }}" {{ request('action')===$a?'selected':'' }}>{{ ucfirst($a) }}</option>
            @endforeach
        </select>
        <select name="user_id" class="input" style="width:auto;min-width:140px;">
            <option value="">All Users</option>
            @foreach($users as $u)
            <option value="{{ $u->id }}" {{ request('user_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ request('date') }}" class="input" style="width:auto;" />
        <button type="submit" class="btn-primary" style="padding:.6rem 1.1rem;font-size:.85rem;border-radius:.75rem;">Filter</button>
        @if(request()->hasAny(['search','action','user_id','date']))<a href="{{ route('admin.logs') }}" style="padding:.6rem .9rem;background:var(--bg);color:var(--text-muted);font-size:.85rem;border:1px solid var(--border);border-radius:.75rem;text-decoration:none;">✕</a>@endif
    </form>
    <form method="POST" action="{{ route('admin.logs.clear') }}" onsubmit="return confirm('Clear logs older than 30 days?')">
        @csrf
        <button type="submit" class="btn-danger" style="padding:.6rem 1.1rem;font-size:.82rem;border-radius:.75rem;white-space:nowrap;">🗑 Clear Old Logs</button>
    </form>
</div>

<div class="glass" style="border-radius:1.25rem;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:var(--bg);border-bottom:1px solid var(--border);">
                <th class="th-cell" style="text-align:left;">Time</th>
                <th class="th-cell" style="text-align:left;">User</th>
                <th class="th-cell" style="text-align:center;">Action</th>
                <th class="th-cell" style="text-align:left;">Description</th>
                <th class="th-cell" style="text-align:left;">IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr style="border-bottom:1px solid var(--border);" class="tr-hover">
                <td class="td-cell" style="font-size:.75rem;color:var(--text-muted);white-space:nowrap;">{{ $log->created_at->format('M d, Y H:i') }}</td>
                <td class="td-cell">
                    @if($log->user)
                    <p style="font-size:.82rem;font-weight:700;color:var(--text);margin:0;">{{ $log->user->name }}</p>
                    <p style="font-size:.7rem;color:var(--text-muted);margin:0;">{{ ucfirst($log->user->role) }}</p>
                    @else
                    <span style="font-size:.78rem;color:var(--text-light);">System</span>
                    @endif
                </td>
                <td class="td-cell" style="text-align:center;">
                    <span style="font-size:.7rem;font-weight:800;padding:.2rem .6rem;border-radius:9999px;background:{{ $log->actionColor() }}20;color:{{ $log->actionColor() }};">{{ ucfirst($log->action) }}</span>
                </td>
                <td class="td-cell" style="font-size:.82rem;color:var(--text);">{{ $log->description }}</td>
                <td class="td-cell" style="font-size:.75rem;color:var(--text-muted);">{{ $log->ip_address ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:3rem;text-align:center;color:var(--text-muted);">No activity logs found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1.25rem;">{{ $logs->links() }}</div>
@endsection
