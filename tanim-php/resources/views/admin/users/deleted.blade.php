@extends('layouts.admin')
@section('title', 'Deleted Users')
@section('page-title', '🗑️ Deleted Users')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h2 style="margin:0;font-family:'Outfit',sans-serif;font-size:1rem;font-weight:800;color:var(--text);">Deleted Users</h2>
        <p style="margin:.5rem 0 0 0;color:var(--text-muted);font-size:.85rem;">Users that have been soft deleted and can be restored or permanently deleted.</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn-primary" style="padding:.6rem 1.25rem;font-size:.85rem;border-radius:.75rem;white-space:nowrap;">← Back to Users</a>
</div>

<div class="glass" style="border-radius:1.25rem;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:var(--bg);border-bottom:1px solid var(--border);">
                <th class="th-cell" style="text-align:left;">User</th>
                <th class="th-cell" style="text-align:left;">Role</th>
                <th class="th-cell" style="text-align:left;">Deleted At</th>
                <th class="th-cell" style="text-align:left;">Joined</th>
                <th class="th-cell" style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deletedUsers as $user)
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
                    <span class="badge" style="background:{{ $user->role === 'admin' ? 'var(--primary-faint)' : 'var(--earth-faint)' }};color:{{ $user->role === 'admin' ? 'var(--primary)' : 'var(--earth)' }};padding:.25rem .5rem;border-radius:.375rem;font-size:.72rem;font-weight:700;">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="td-cell">
                    <span style="font-size:.78rem;color:var(--text-muted);">
                        {{ $user->deleted_at->format('M d, Y H:i') }}
                    </span>
                </td>
                <td class="td-cell">
                    <span style="font-size:.78rem;color:var(--text-muted);">
                        {{ $user->created_at->format('M d, Y') }}
                    </span>
                </td>
                <td class="td-cell" style="text-align:center;">
                    <div style="display:flex;gap:.4rem;justify-content:center;align-items:center;">
                        <form method="POST" action="{{ route('admin.users.restore', $user) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-primary" style="padding:.35rem .8rem;font-size:.75rem;background:var(--primary);color:white;border:1px solid var(--primary);border-radius:.5rem;cursor:pointer;" onclick="return confirm('Restore this user?')">↺ Restore</button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.force-delete', $user) }}" style="display:inline;" onsubmit="return confirm('Permanently delete this user? This cannot be undone!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-ghost" style="padding:.35rem .8rem;font-size:.75rem;background:#dc2626;color:white;border:1px solid #dc2626;border-radius:.5rem;cursor:pointer;">⚠ Delete Forever</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:3rem;color:var(--text-muted);font-size:.9rem;">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:1rem;">
                        <span style="font-size:2rem;">🗑️</span>
                        <div>
                            <p style="margin:0;font-weight:700;">No deleted users found</p>
                            <p style="margin:.25rem 0 0 0;font-size:.85rem;">Deleted users will appear here for restoration or permanent deletion.</p>
                        </div>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($deletedUsers->hasPages())
    <div style="display:flex;justify-content:center;margin-top:1rem;padding:1rem 0;">
        {{ $deletedUsers->links() }}
    </div>
    @endif
</div>

@endsection
