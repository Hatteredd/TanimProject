@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','📊 Dashboard')
@section('content')

{{-- KPI Grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:1rem;margin-bottom:2rem;">
@foreach([
    ['Total Revenue','₱'.number_format($totalRevenue,2),'var(--primary)','💰',route('admin.reports')],
    ['Total Orders',\App\Models\Order::count(),'var(--sky)','📦',route('admin.orders.index')],
    ['Customers',$totalCustomers,'var(--earth)','👤',route('admin.users.index')],
    ['Active Products',$totalProducts,'var(--primary-2)','🌾',route('admin.products.index')],
    ['Reviews',$totalReviews,'var(--accent)','⭐',route('admin.reviews.index')],
    ['Avg Rating',$avgRating.'/5','var(--wheat)','🏆',route('admin.reports')],
] as [$label,$val,$color,$icon,$link])
<a href="{{ $link }}" style="text-decoration:none;">
<div class="stat-card" style="cursor:pointer;">
    <span style="font-size:1.5rem;display:block;margin-bottom:.4rem;">{{ $icon }}</span>
    <p style="font-size:.68rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin:0 0 .2rem;">{{ $label }}</p>
    <p style="font-size:1.2rem;font-weight:900;color:{{ $color }};font-family:'Outfit',sans-serif;margin:0;">{{ $val }}</p>
</div>
</a>
@endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
    {{-- Recent Orders --}}
    <div class="glass" style="border-radius:1.25rem;padding:1.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h2 style="font-family:'Outfit',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);margin:0;">📦 Recent Orders</h2>
            <a href="{{ route('admin.orders.index') }}" style="font-size:.75rem;color:var(--primary);font-weight:700;text-decoration:none;">View all →</a>
        </div>
        @forelse($recentOrders as $order)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.55rem 0;border-bottom:1px solid var(--border);">
            <div>
                <p style="font-size:.82rem;font-weight:700;color:var(--text);margin:0;">{{ $order->order_number }}</p>
                <p style="font-size:.72rem;color:var(--text-muted);margin:0;">{{ $order->user->name }}</p>
            </div>
            <div style="text-align:right;">
                <p style="font-size:.82rem;font-weight:800;color:var(--primary);margin:0;">₱{{ number_format($order->total_amount,2) }}</p>
                <span style="font-size:.65rem;font-weight:700;padding:.15rem .5rem;border-radius:9999px;background:{{ $order->statusBg() }};color:{{ $order->statusColor() }};">{{ ucfirst($order->status) }}</span>
            </div>
        </div>
        @empty
        <p style="color:var(--text-muted);font-size:.875rem;">No orders yet.</p>
        @endforelse
    </div>

    {{-- Recent Customers --}}
    <div class="glass" style="border-radius:1.25rem;padding:1.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h2 style="font-family:'Outfit',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);margin:0;">👤 Recent Customers</h2>
            <a href="{{ route('admin.users.index') }}" style="font-size:.75rem;color:var(--primary);font-weight:700;text-decoration:none;">View all →</a>
        </div>
        @foreach($recentUsers as $u)
        <div style="display:flex;align-items:center;gap:.65rem;padding:.55rem 0;border-bottom:1px solid var(--border);">
            <div style="width:1.9rem;height:1.9rem;border-radius:9999px;background:var(--primary-faint);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:800;color:var(--primary);flex-shrink:0;">{{ strtoupper(substr($u->name,0,1)) }}</div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:.82rem;font-weight:700;color:var(--text);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $u->name }}</p>
                <p style="font-size:.7rem;color:var(--text-muted);margin:0;">{{ $u->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
    {{-- Low Stock --}}
    <div class="glass" style="border-radius:1.25rem;padding:1.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h2 style="font-family:'Outfit',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);margin:0;">⚠️ Low Stock Alert</h2>
            <a href="{{ route('admin.products.index') }}" style="font-size:.75rem;color:var(--primary);font-weight:700;text-decoration:none;">Manage →</a>
        </div>
        @forelse($lowStock as $product)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.5rem .65rem;background:var(--bg);border-radius:.65rem;margin-bottom:.4rem;">
            <div>
                <p style="font-size:.82rem;font-weight:700;color:var(--text);margin:0;">{{ $product->name }}</p>
                <p style="font-size:.7rem;color:var(--text-muted);margin:0;">{{ $product->category }}</p>
            </div>
            <span style="font-size:.82rem;font-weight:800;color:{{ $product->stock <= 5 ? 'var(--danger)' : 'var(--wheat-2)' }};">{{ $product->stock }} left</span>
        </div>
        @empty
        <p style="color:var(--text-muted);font-size:.875rem;">🎉 All stocks healthy!</p>
        @endforelse
    </div>

    {{-- Expenses --}}
    <div class="glass" style="border-radius:1.25rem;padding:1.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h2 style="font-family:'Outfit',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);margin:0;">💰 Expenses This Month</h2>
            <a href="{{ route('admin.expenses') }}" style="font-size:.75rem;color:var(--primary);font-weight:700;text-decoration:none;">View all →</a>
        </div>
        @php $types = \App\Models\Expense::types(); $total = array_sum($expenseByType->toArray()) ?: 1; @endphp
        @forelse($expenseByType as $type => $amount)
        @php $pct = round(($amount/$total)*100); @endphp
        <div style="margin-bottom:.75rem;">
            <div style="display:flex;justify-content:space-between;font-size:.78rem;font-weight:600;color:var(--text-muted);margin-bottom:.25rem;">
                <span>{{ $types[$type] ?? $type }}</span>
                <span style="color:var(--text);">₱{{ number_format($amount,2) }}</span>
            </div>
            <div style="height:6px;background:var(--border);border-radius:9999px;overflow:hidden;">
                <div style="height:100%;width:{{ $pct }}%;background:var(--primary);border-radius:9999px;"></div>
            </div>
        </div>
        @empty
        <p style="color:var(--text-muted);font-size:.875rem;">No expenses this month.</p>
        @endforelse
        <p style="font-size:.78rem;font-weight:800;color:var(--text);margin:.75rem 0 0;text-align:right;">Total: ₱{{ number_format($expensesThisMonth,2) }}</p>
    </div>
</div>
@endsection
