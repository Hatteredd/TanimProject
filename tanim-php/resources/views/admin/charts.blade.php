@extends('layouts.admin')
@section('title','Sales Charts')
@section('page-title','&#128202; Sales Analytics')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="glass" style="border-radius:1.25rem;padding:1.5rem;margin-bottom:1.5rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:1.25rem;">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1rem;font-weight:800;color:var(--text);margin:0;">Sales by Date Range</h2>
        <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
            <input type="date" id="fromDate" value="{{ now()->startOfMonth()->toDateString() }}" class="input" style="width:auto;" />
            <span style="color:var(--text-muted);font-size:0.875rem;">to</span>
            <input type="date" id="toDate" value="{{ now()->toDateString() }}" class="input" style="width:auto;" />
            <button onclick="loadRangeChart()" class="btn-primary" style="padding:0.5rem 1rem;font-size:0.875rem;border-radius:0.6rem;">Apply</button>
        </div>
    </div>
    <canvas id="rangeChart" height="80"></canvas>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
    <div class="glass" style="border-radius:1.25rem;padding:1.5rem;">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;">Yearly Sales &mdash; {{ now()->year }}</h2>
        <canvas id="yearlyChart" height="160"></canvas>
    </div>

    <div class="glass" style="border-radius:1.25rem;padding:1.5rem;">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;">Sales Share by Product (Top 10)</h2>
        <canvas id="pieChart" height="160"></canvas>
    </div>
</div>

<script>
const monthlyData = @json($monthlyData);
const productSales = @json($productSales);

// Detect dark mode
const isDark = () => document.documentElement.classList.contains('dark');
const gridColor = () => isDark() ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.07)';
const tickColor = () => isDark() ? '#8fad96' : '#52636b';
const primaryColor = () => isDark() ? '#4ade80' : '#16a34a';

const chartDefaults = () => ({
    scales: {
        x: { grid: { color: gridColor() }, ticks: { color: tickColor() } },
        y: { beginAtZero: true, grid: { color: gridColor() }, ticks: { color: tickColor(), callback: v => '&#8369;' + v.toLocaleString() } }
    }
});

// Yearly chart
new Chart(document.getElementById('yearlyChart'), {
    type: 'line',
    data: {
        labels: monthlyData.map(d => d.month),
        datasets: [{
            label: 'Sales',
            data: monthlyData.map(d => d.total),
            borderColor: primaryColor(),
            backgroundColor: isDark() ? 'rgba(74,222,128,0.08)' : 'rgba(22,163,74,0.08)',
            borderWidth: 2.5,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: primaryColor(),
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, ...chartDefaults() }
});

// Pie chart
const colors = ['#16a34a','#0d9488','#2563eb','#7c3aed','#dc2626','#ea580c','#f59e0b','#84cc16','#06b6d4','#ec4899'];
new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: productSales.map(p => p.product_name),
        datasets: [{ data: productSales.map(p => p.total), backgroundColor: colors }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 }, color: tickColor() } }
        }
    }
});

// Range bar chart
let rangeChart = null;
async function loadRangeChart() {
    const from = document.getElementById('fromDate').value;
    const to = document.getElementById('toDate').value;
    const res = await fetch(`{{ route('admin.charts.sales-range') }}?from=${from}&to=${to}`);
    const data = await res.json();
    if (rangeChart) rangeChart.destroy();
    rangeChart = new Chart(document.getElementById('rangeChart'), {
        type: 'bar',
        data: {
            labels: data.map(d => d.date),
            datasets: [{
                label: 'Daily Sales',
                data: data.map(d => d.total),
                backgroundColor: isDark() ? 'rgba(74,222,128,0.55)' : 'rgba(22,163,74,0.65)',
                borderColor: primaryColor(),
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, ...chartDefaults() }
    });
}
loadRangeChart();
</script>
@endsection
