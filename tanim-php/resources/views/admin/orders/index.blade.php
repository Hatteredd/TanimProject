@extends('layouts.admin')
@section('title','Orders')
@section('page-title','📦 Order Management')

@section('content')
<form method="GET" style="display:flex;gap:0.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order # or customer..."
        class="input" style="flex:1;min-width:200px;" />
    <select name="status" class="input" style="width:auto;min-width:140px;">
        <option value="">All Statuses</option>
        @foreach($statuses as $s)
        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
        @endforeach
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
                <th style="padding:0.85rem 1rem;text-align:left;font-size:0.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Order #</th>
                <th style="padding:0.85rem 1rem;text-align:left;font-size:0.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Customer</th>
                <th style="padding:0.85rem 1rem;text-align:right;font-size:0.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Total</th>
                <th style="padding:0.85rem 1rem;text-align:center;font-size:0.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Status</th>
                <th style="padding:0.85rem 1rem;text-align:left;font-size:0.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Date</th>
                <th style="padding:0.85rem 1rem;text-align:center;font-size:0.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr style="border-bottom:1px solid var(--border);">
                <td style="padding:0.85rem 1rem;font-size:0.875rem;font-weight:700;color:var(--text);">{{ $order->order_number }}</td>
                <td style="padding:0.85rem 1rem;">
                    <p style="font-size:0.875rem;font-weight:600;color:var(--text);margin:0;">{{ $order->user->name }}</p>
                    <p style="font-size:0.75rem;color:var(--text-muted);margin:0;">{{ $order->user->email }}</p>
                </td>
                <td style="padding:0.85rem 1rem;text-align:right;font-size:0.875rem;font-weight:700;color:var(--primary);">&#8369;{{ number_format($order->total_amount, 2) }}</td>
                <td style="padding:0.85rem 1rem;text-align:center;">
                    <span style="font-size:0.72rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:9999px;background:{{ $order->statusBg() }};color:{{ $order->statusColor() }};">{{ ucfirst($order->status) }}</span>
                </td>
                <td style="padding:0.85rem 1rem;font-size:0.8rem;color:var(--text-muted);">{{ $order->created_at->format('M d, Y') }}</td>
                <td style="padding:0.85rem 1rem;text-align:center;">
                    <a href="{{ route('admin.orders.show', $order) }}" style="padding:0.35rem 0.85rem;background:var(--primary-faint);color:var(--primary);font-size:0.75rem;font-weight:700;border-radius:0.5rem;text-decoration:none;">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:3rem;text-align:center;color:var(--text-muted);">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1.5rem;">{{ $orders->links() }}</div>
@endsection
