@extends('layouts.admin')
@section('title','Users')
@section('page-title','👤 User Management')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <form method="GET" style="display:flex;gap:.6rem;flex-wrap:wrap;flex:1;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="input" style="flex:1;min-width:180px;" />
        <select name="role" class="input" style="width:auto;min-width:120px;">
            <option value="">All Roles</option>
            @foreach(['admin','buyer'] as $r)
            <option value="{{ $r }}" {{ request('role')===$r?'selected':'' }}>{{ ucfirst($r) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary" style="padding:.6rem 1.1rem;font-size:.85rem;border-radius:.75rem;">Filter</button>
        @if(request()->hasAny(['search','role']))<a href="{{ route('admin.users.index') }}" style="padding:.6rem .9rem;background:var(--bg);color:var(--text-muted);font-size:.85rem;border:1px solid var(--border);border-radius:.75rem;text-decoration:none;">✕</a>@endif
    </form>
    <a href="{{ route('admin.users.create') }}" class="btn-primary" style="padding:.6rem 1.25rem;font-size:.85rem;border-radius:.75rem;white-space:nowrap;">+ Add User</a>
</div>

<div class="glass" style="border-radius:1.25rem;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:var(--bg);border-bottom:1px solid var(--border);">
                <th class="th-cell" style="text-align:left;">User</th>
                <th class="th-cell" style="text-align:left;">Role</th>
                <th class="th-cell" style="text-align:center;">Status</th>
                <th class="th-cell" style="text-align:center;">Verified</th>
                <th class="th-cell" style="text-align:left;">Joined</th>
                <th class="th-cell" style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr style="border-bottom:1px solid var(--border);" class="tr-hover">
                <td class="td-cell">
                    <div style="display:flex;align-items:center;gap:.65rem;">
                        <img src="{{ $user->photoUrl() }}" style="width:2rem;height:2rem;border-radius:9999px;object-fit:cover;border:1px solid var(--border);flex-shrink:0;" />
                        <div>
                            <p style="font-size:.85rem;font-weight:700;color:var(--text);margin:0;">{{ $user->name }}</p>
                            <p style="font-size:.72rem;color:var(--text-muted);margin:0;">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="td-cell">
                    <form method="POST" action="{{ route('admin.users.update-role', $user) }}" style="display:flex;gap:.4rem;align-items:center;">
                        @csrf
                        <select name="role" class="input" style="padding:.28rem .5rem;font-size:.78rem;width:auto;">
                            @foreach(['buyer','admin'] as $r)
                            <option value="{{ $r }}" {{ $user->role===$r?'selected':'' }}>{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" style="padding:.28rem .55rem;background:var(--primary-faint);color:var(--primary);font-size:.7rem;font-weight:700;border:none;border-radius:.4rem;cursor:pointer;">Set</button>
                    </form>
                </td>
                <td class="td-cell" style="text-align:center;">
                    <span style="font-size:.7rem;font-weight:700;padding:.18rem .55rem;border-radius:9999px;background:{{ $user->is_active?'var(--primary-soft)':'var(--danger-soft)' }};color:{{ $user->is_active?'var(--primary-text)':'var(--danger)' }};">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="td-cell" style="text-align:center;">
                    <span style="font-size:.7rem;font-weight:700;padding:.18rem .55rem;border-radius:9999px;background:{{ $user->email_verified_at?'var(--primary-soft)':'var(--warn-soft)' }};color:{{ $user->email_verified_at?'var(--primary-text)':'var(--warn-text)' }};">
                        {{ $user->email_verified_at ? '✓ Verified' : 'Pending' }}
                    </span>
                </td>
                <td class="td-cell" style="color:var(--text-muted);font-size:.78rem;">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="td-cell" style="text-align:center;">
                    <div style="display:flex;gap:.35rem;justify-content:center;flex-wrap:wrap;">
                        <a href="{{ route('admin.users.edit', $user) }}" style="padding:.28rem .65rem;background:var(--primary-faint);color:var(--primary);font-size:.72rem;font-weight:700;border-radius:.45rem;text-decoration:none;">Edit</a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" style="margin:0;">
                            @csrf
                            <button type="submit" style="padding:.28rem .65rem;background:{{ $user->is_active?'var(--danger-soft)':'var(--primary-faint)' }};color:{{ $user->is_active?'var(--danger)':'var(--primary)' }};font-size:.72rem;font-weight:700;border:none;border-radius:.45rem;cursor:pointer;">
                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="margin:0;" onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="padding:.28rem .65rem;background:var(--danger-soft);color:var(--danger);font-size:.72rem;font-weight:700;border:none;border-radius:.45rem;cursor:pointer;">Delete</button>
                        </form>
                        @else
                        <span style="font-size:.72rem;color:var(--text-muted);">You</span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:3rem;text-align:center;color:var(--text-muted);">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1.25rem;">{{ $users->links() }}</div>
@endsection
