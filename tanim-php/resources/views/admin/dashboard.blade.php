@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','📊 Dashboard Overview')
@section('content')

<div class="page-wrap" style="padding:0;">
    {{-- KPI Grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.5rem;margin-bottom:2.5rem;">
        @foreach([
            ['Total Revenue','₱'.number_format($totalRevenue,2),'var(--primary)','💰',route('admin.reports')],
            ['Total Orders',\App\Models\Order::count(),'var(--sky)','📦',route('admin.orders.index')],
            ['Customers',$totalCustomers,'var(--earth)','👤',route('admin.users.index')],
            ['Active Products',$totalProducts,'var(--primary-2)','🌾',route('admin.products.index')],
            ['Reviews',$totalReviews,'var(--accent)','⭐',route('admin.reviews.index')],
            ['Avg Rating',$avgRating.'/5','var(--wheat)','🏆',route('admin.reports')],
        ] as [$label,$val,$color,$icon,$link])
        <a href="{{ $link }}" style="text-decoration:none;">
            <div class="page-card animate-fade-in" style="padding:1.5rem;cursor:pointer;transition:all var(--transition-base);position:relative;overflow:hidden;animation-delay: {{ $loop->index * 50 }}ms">
                <div style="position:absolute;top:-0.5rem;right:-0.5rem;font-size:3rem;opacity:0.05;transform:rotate(15deg);">{{ $icon }}</div>
                <p style="font-size:0.7rem;font-weight:800;color:var(--text-light);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.5rem;">{{ $label }}</p>
                <p style="font-size:1.5rem;font-weight:900;color:{{ $color }};font-family:'Outfit',sans-serif;margin:0;">{{ $val }}</p>
            </div>
        </a>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
        {{-- Recent Orders --}}
        <div class="page-card animate-fade-in" style="padding:1.5rem;animation-delay:300ms;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                <h2 style="font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:800;color:var(--text);margin:0;">📦 Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="btn-ghost" style="padding:0.4rem 0.8rem;font-size:0.75rem;">View All</a>
            </div>
            <div style="display:flex;flex-direction:column;gap:0.75rem;">
                @forelse($recentOrders as $order)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:1rem;background:var(--bg-2);border-radius:1rem;border:1px solid var(--border);">
                    <div>
                        <p style="font-size:0.875rem;font-weight:800;color:var(--text);margin:0;">{{ $order->order_number }}</p>
                        <p style="font-size:0.75rem;color:var(--text-muted);margin:0;">{{ $order->user->name ?? 'Deleted User' }}</p>
                    </div>
                    <div style="text-align:right;">
                        <p style="font-size:0.9rem;font-weight:900;color:var(--primary);margin:0;">₱{{ number_format($order->total_amount,2) }}</p>
                        <span class="badge" style="background:{{ $order->statusBg() }};color:{{ $order->statusColor() }};border:none;font-size:0.6rem;">{{ strtoupper($order->status) }}</span>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:2rem;color:var(--text-muted);font-size:0.9rem;">No orders yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Customers --}}
        <div class="page-card animate-fade-in" style="padding:1.5rem;animation-delay:400ms;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                <h2 style="font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:800;color:var(--text);margin:0;">👤 Recent Customers</h2>
                <a href="{{ route('admin.users.index') }}" class="btn-ghost" style="padding:0.4rem 0.8rem;font-size:0.75rem;">View All</a>
            </div>
            <div style="display:flex;flex-direction:column;gap:0.75rem;">
                @foreach($recentUsers as $u)
                <div style="display:flex;align-items:center;gap:1rem;padding:0.75rem;background:var(--bg-2);border-radius:1rem;border:1px solid var(--border);">
                    <div style="width:2.5rem;height:2.5rem;border-radius:9999px;background:var(--primary-faint);display:flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:900;color:var(--primary);flex-shrink:0;">{{ strtoupper(substr($u->name,0,1)) }}</div>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:0.875rem;font-weight:800;color:var(--text);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $u->name }}</p>
                        <p style="font-size:0.75rem;color:var(--text-muted);margin:0;">Joined {{ $u->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
        {{-- Low Stock --}}
        <div class="page-card animate-fade-in" style="padding:1.5rem;animation-delay:500ms;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                <h2 style="font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:800;color:var(--text);margin:0;">⚠️ Low Stock Alert</h2>
                <a href="{{ route('admin.products.index') }}" class="btn-ghost" style="padding:0.4rem 0.8rem;font-size:0.75rem;">Manage Inventory</a>
            </div>
            <div style="display:flex;flex-direction:column;gap:0.75rem;">
                @forelse($lowStock as $product)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:0.85rem 1.25rem;background:var(--bg-2);border-radius:1rem;border:1px solid var(--border);">
                    <div>
                        <p style="font-size:0.875rem;font-weight:800;color:var(--text);margin:0;">{{ $product->name }}</p>
                        <p style="font-size:0.75rem;color:var(--text-muted);margin:0;">{{ $product->category }}</p>
                    </div>
                    <span style="font-size:0.9rem;font-weight:900;color:{{ $product->stock <= 5 ? 'var(--danger)' : 'var(--wheat-2)' }};">{{ $product->stock }} left</span>
                </div>
                @empty
                <div style="text-align:center;padding:2rem;color:var(--text-muted);font-size:0.9rem;">🎉 All stocks healthy!</div>
                @endforelse
            </div>
        </div>

        {{-- Expenses --}}
        <div class="page-card animate-fade-in" style="padding:1.5rem;animation-delay:600ms;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                <h2 style="font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:800;color:var(--text);margin:0;">💰 Expenses This Month</h2>
                <a href="{{ route('admin.expenses') }}" class="btn-ghost" style="padding:0.4rem 0.8rem;font-size:0.75rem;">View All</a>
            </div>
            @php $types = \App\Models\Expense::types(); $total = array_sum($expenseByType->toArray()) ?: 1; @endphp
            <div style="display:flex;flex-direction:column;gap:1.25rem;">
                @forelse($expenseByType as $type => $amount)
                @php $pct = round(($amount/$total)*100); @endphp
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:0.85rem;font-weight:700;color:var(--text-muted);margin-bottom:0.5rem;">
                        <span>{{ $types[$type] ?? $type }}</span>
                        <span style="color:var(--text);">₱{{ number_format($amount,2) }}</span>
                    </div>
                    <div style="height:8px;background:var(--border);border-radius:9999px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;background:var(--primary);border-radius:9999px;"></div>
                    </div>
                </div>
                @empty
                <p style="color:var(--text-muted);font-size:0.9rem;text-align:center;">No expenses this month.</p>
                @endforelse
            </div>
            <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border);text-align:right;">
                <span style="font-size:0.85rem;font-weight:700;color:var(--text-muted);">Total Monthly Expense:</span>
                <span style="font-size:1.25rem;font-weight:900;color:var(--text);margin-left:0.5rem;">₱{{ number_format($expensesThisMonth,2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
