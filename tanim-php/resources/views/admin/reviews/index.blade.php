@extends('layouts.admin')
@section('title','Reviews')
@section('page-title','&#11088; Review Management')

@section('content')
<form method="GET" style="display:flex;gap:0.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by user or product..."
        class="input" style="flex:1;min-width:200px;" />
    <select name="rating" class="input" style="width:auto;min-width:130px;">
        <option value="">All Ratings</option>
        @for($i=5;$i>=1;$i--)<option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} &#9733;</option>@endfor
    </select>
    <button type="submit" class="btn-primary" style="padding:0.65rem 1.25rem;font-size:0.875rem;border-radius:0.75rem;">Filter</button>
</form>

@if(session('success'))
<div class="alert-success" style="margin-bottom:1.5rem;">&#10003; {{ session('success') }}</div>
@endif

<div class="glass" style="border-radius:1.25rem;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:var(--bg);border-bottom:1px solid var(--border);">
                <th class="th-cell" style="text-align:left;">User</th>
                <th class="th-cell" style="text-align:left;">Product</th>
                <th class="th-cell" style="text-align:center;">Rating</th>
                <th class="th-cell" style="text-align:left;">Comment</th>
                <th class="th-cell" style="text-align:left;">Date</th>
                <th class="th-cell" style="text-align:center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
            <tr style="border-bottom:1px solid var(--border);">
                <td class="td-cell">
                    <p style="font-size:0.875rem;font-weight:700;color:var(--text);margin:0;">{{ $review->user->name }}</p>
                    <p style="font-size:0.75rem;color:var(--text-muted);margin:0;">{{ $review->user->email }}</p>
                </td>
                <td class="td-cell">{{ $review->product->name }}</td>
                <td class="td-cell" style="text-align:center;">
                    <span style="font-size:0.875rem;font-weight:800;color:#f59e0b;">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                </td>
                <td class="td-cell" style="color:var(--text-muted);max-width:200px;font-size:0.8rem;">{{ Str::limit($review->comment, 80) }}</td>
                <td class="td-cell" style="color:var(--text-muted);font-size:0.8rem;">{{ $review->created_at->format('M d, Y') }}</td>
                <td class="td-cell" style="text-align:center;">
                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="padding:0.35rem 0.85rem;background:var(--danger-soft);color:var(--danger);font-size:0.75rem;font-weight:700;border:none;border-radius:0.5rem;cursor:pointer;">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:3rem;text-align:center;color:var(--text-muted);">No reviews found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1.5rem;">{{ $reviews->links() }}</div>
@endsection
