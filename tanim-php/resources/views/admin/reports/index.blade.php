@extends('layouts.admin')
@section('title','Reports')
@section('page-title','📈 Reports & Analytics')
@section('content')

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

{{-- Summary KPIs --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;margin-bottom:2rem;">
@foreach([
    ['Total Revenue','₱'.number_format($totalRevenue,2),'var(--primary)','💰'],
    ['Total Orders',$totalOrders,'var(--sky)','📦'],
    ['Customers',$totalCustomers,'var(--earth)','👤'],
    ['Avg Order Value','₱'.number_format($avgOrderValue,2),'var(--wheat-2)','📊'],
] as [$label,$val,$color,$icon])
<div class="stat-card">
    <span style="font-size:1.4rem;display:block;margin-bottom:.4rem;">{{ $icon }}</span>
    <p style="font-size:.68rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin:0 0 .2rem;">{{ $label }}</p>
    <p style="font-size:1.15rem;font-weight:900;color:{{ $color }};font-family:'Outfit',sans-serif;margin:0;">{{ $val }}</p>
</div>
@endforeach
</div>

{{-- Row 1: Monthly Sales Line + Customer Registrations Bar --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

    <div class="glass" style="border-radius:1.25rem;padding:1.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h2 style="font-family:'Outfit',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);margin:0;">📅 Monthly Sales — {{ $year }}</h2>
            <span style="font-size:.72rem;font-weight:700;color:var(--primary);">₱{{ number_format($totalRevenue,0) }} total</span>
        </div>
        <canvas id="monthlySalesChart" height="220"></canvas>
    </div>

    <div class="glass" style="border-radius:1.25rem;padding:1.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h2 style="font-family:'Outfit',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);margin:0;">👤 New Customers — {{ $year }}</h2>
            <span style="font-size:.72rem;font-weight:700;color:var(--earth);">{{ $totalCustomers }} total</span>
        </div>
        <canvas id="customerRegChart" height="220"></canvas>
    </div>
</div>

{{-- Row 2: Top Products + Orders by Status + Category Sales --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

    <div class="glass" style="border-radius:1.25rem;padding:1.25rem;">
        <h2 style="font-family:'Outfit',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);margin:0 0 1rem;">🏆 Top Products by Revenue</h2>
        <canvas id="topProductsChart" height="260"></canvas>
    </div>

    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        <div class="glass" style="border-radius:1.25rem;padding:1.25rem;">
            <h2 style="font-family:'Outfit',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);margin:0 0 .85rem;">�� Orders by Status</h2>
            <canvas id="orderStatusChart" height="160"></canvas>
        </div>

        <div class="glass" style="border-radius:1.25rem;padding:1.25rem;">
            <h2 style="font-family:'Outfit',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);margin:0 0 .85rem;">🌾 Sales by Category</h2>
            <canvas id="categoryChart" height="160"></canvas>
        </div>
    </div>
</div>

<script>
(function () {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor  = isDark ? '#c9d1d9' : '#374151';
    const gridColor  = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.07)';
    const tooltipBg  = isDark ? '#1e2a1e' : '#ffffff';
    const tooltipTxt = isDark ? '#e2e8f0' : '#1f2937';

    Chart.defaults.color = textColor;
    Chart.defaults.font.family = "'Outfit', sans-serif";
    Chart.defaults.font.size = 11;

    const months      = @json(array_column($monthlyData, 'month'));
    const salesTotals = @json(array_column($monthlyData, 'total'));
    const salesCounts = @json(array_column($monthlyData, 'count'));
    const regCounts   = @json(array_column($userRegData, 'count'));

    const topLabels = @json($topProducts->pluck('product_name')->map(fn($n) => strlen($n) > 22 ? substr($n,0,22).'…' : $n)->values());
    const topTotals = @json($topProducts->pluck('total')->map(fn($v) => round($v, 2))->values());

    const statusLabels = @json(array_keys($ordersByStatus->toArray()));
    const statusCounts = @json(array_values($ordersByStatus->toArray()));

    const catLabels = @json($categorySales->pluck('category')->values());
    const catTotals = @json($categorySales->pluck('total')->map(fn($v) => round($v, 2))->values());

    const statusPalette = {
        pending:'#d97706', confirmed:'#2563eb', processing:'#7c3aed',
        shipped:'#0891b2', delivered:'#16a34a', cancelled:'#dc2626',
    };

    function tip() {
        return { backgroundColor:tooltipBg, titleColor:tooltipTxt, bodyColor:tooltipTxt,
                 borderColor:gridColor, borderWidth:1, padding:10, cornerRadius:8 };
    }

    // 1. Monthly Sales — Line
    new Chart(document.getElementById('monthlySalesChart'), {
        type: 'line',
        data: { labels: months, datasets: [
            { label:'Revenue (₱)', data:salesTotals, borderColor:'#4ade80',
              backgroundColor:'rgba(74,222,128,0.15)', borderWidth:2.5,
              pointBackgroundColor:'#4ade80', pointRadius:4, tension:0.4, fill:true },
            { label:'Orders', data:salesCounts, borderColor:'#38bdf8',
              backgroundColor:'rgba(56,189,248,0.10)', borderWidth:2,
              pointBackgroundColor:'#38bdf8', pointRadius:3, tension:0.4,
              fill:false, yAxisID:'y2' }
        ]},
        options: {
            responsive:true, interaction:{mode:'index',intersect:false},
            plugins:{ legend:{position:'bottom',labels:{boxWidth:12,padding:12}}, tooltip:tip() },
            scales:{
                x:{grid:{color:gridColor}},
                y:{grid:{color:gridColor}, ticks:{callback:v=>'₱'+v.toLocaleString()}},
                y2:{position:'right', grid:{drawOnChartArea:false}, ticks:{stepSize:1}},
            }
        }
    });

    // 2. Customer Registrations — Bar
    new Chart(document.getElementById('customerRegChart'), {
        type: 'bar',
        data: { labels:months, datasets:[{
            label:'New Customers', data:regCounts,
            backgroundColor:'rgba(161,110,60,0.75)', borderColor:'#a16e3c',
            borderWidth:1.5, borderRadius:6
        }]},
        options:{
            responsive:true,
            plugins:{ legend:{display:false}, tooltip:tip() },
            scales:{ x:{grid:{color:gridColor}}, y:{grid:{color:gridColor},ticks:{stepSize:1}} }
        }
    });

    // 3. Top Products — Horizontal bar
    new Chart(document.getElementById('topProductsChart'), {
        type: 'bar',
        data: { labels:topLabels, datasets:[{
            label:'Revenue (₱)', data:topTotals, borderRadius:5,
            backgroundColor:['rgba(74,222,128,.75)','rgba(56,189,248,.75)','rgba(161,110,60,.75)',
                'rgba(250,204,21,.75)','rgba(167,139,250,.75)','rgba(251,146,60,.75)',
                'rgba(34,197,94,.75)','rgba(14,165,233,.75)','rgba(239,68,68,.75)','rgba(99,102,241,.75)'],
        }]},
        options:{
            indexAxis:'y', responsive:true,
            plugins:{ legend:{display:false},
                tooltip:{...tip(), callbacks:{label:ctx=>' ₱'+ctx.parsed.x.toLocaleString()}} },
            scales:{
                x:{grid:{color:gridColor}, ticks:{callback:v=>'₱'+v.toLocaleString()}},
                y:{grid:{color:gridColor}}
            }
        }
    });

    // 4. Orders by Status — Doughnut
    const sBg = statusLabels.map(s => statusPalette[s] ?? '#6b7280');
    new Chart(document.getElementById('orderStatusChart'), {
        type: 'doughnut',
        data: { labels:statusLabels.map(s=>s.charAt(0).toUpperCase()+s.slice(1)),
            datasets:[{ data:statusCounts, backgroundColor:sBg.map(c=>c+'cc'),
                borderColor:sBg, borderWidth:2, hoverOffset:6 }]},
        options:{
            responsive:true, cutout:'62%',
            plugins:{ legend:{position:'right',labels:{boxWidth:12,padding:10}}, tooltip:tip() }
        }
    });

    // 5. Category Sales — Bar
    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: { labels:catLabels, datasets:[{
            label:'Revenue (₱)', data:catTotals,
            backgroundColor:'rgba(74,222,128,0.75)', borderColor:'#4ade80',
            borderWidth:1.5, borderRadius:6
        }]},
        options:{
            responsive:true,
            plugins:{ legend:{display:false},
                tooltip:{...tip(), callbacks:{label:ctx=>' ₱'+ctx.parsed.y.toLocaleString()}} },
            scales:{
                x:{grid:{color:gridColor}},
                y:{grid:{color:gridColor}, ticks:{callback:v=>'₱'+v.toLocaleString()}}
            }
        }
    });
})();
</script>
@endsection
