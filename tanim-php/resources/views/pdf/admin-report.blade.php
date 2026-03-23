<!DOCTYPE html>
<html>
<head>
    <title>Tanim Analytics Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #2e7d32; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #2e7d32; margin: 0; text-transform: uppercase; font-size: 24px; }
        .header p { color: #666; margin: 5px 0 0; font-size: 14px; }
        
        .summary-grid { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .stat-card { background: #f9f9f9; padding: 15px; border: 1px solid #eee; text-align: center; width: 25%; }
        .stat-label { font-size: 11px; text-transform: uppercase; color: #666; font-weight: bold; margin-bottom: 5px; }
        .stat-value { font-size: 18px; font-weight: bold; color: #2e7d32; }

        .section-title { font-size: 16px; font-weight: bold; color: #333; border-left: 4px solid #2e7d32; padding-left: 10px; margin: 30px 0 15px; background: #f0f4f0; padding-top: 5px; padding-bottom: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }
        th { background: #f4f4f4; text-align: left; padding: 10px; border-bottom: 2px solid #ddd; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 10px; color: #999; text-align: center; padding: 10px 0; border-top: 1px solid #eee; }
        
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Tanim Marketplace</h1>
        <p>Business Intelligence Report & Analytics Summary</p>
        <p>Period: {{ $fromDate->format('M d, Y') }} - {{ $toDate->format('M d, Y') }}</p>
    </div>

    <div class="section-title">Executive Summary</div>
    <table class="summary-grid">
        <tr>
            <td class="stat-card">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">₱{{ number_format($totalRevenue, 2) }}</div>
            </td>
            <td class="stat-card">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value">{{ $totalOrders }}</div>
            </td>
            <td class="stat-card">
                <div class="stat-label">Total Customers</div>
                <div class="stat-value">{{ $totalCustomers }}</div>
            </td>
            <td class="stat-card">
                <div class="stat-label">Avg. Order Value</div>
                <div class="stat-value">₱{{ number_format($avgOrderValue, 2) }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Sales Performance by Month</div>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Orders</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyData as $data)
            <tr>
                <td>{{ $data['month'] }}</td>
                <td>{{ $data['count'] }}</td>
                <td>₱{{ number_format($data['total'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Top Selling Products</div>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Product Name</th>
                <th>Revenue Contribution</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $index => $product)
            <tr>
                <td>#{{ $index + 1 }}</td>
                <td>{{ $product->product_name }}</td>
                <td>₱{{ number_format($product->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="section-title">Order Status Distribution</div>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Order Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordersByStatus as $status => $count)
            <tr>
                <td>{{ ucfirst($status) }}</td>
                <td>{{ $count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Category Performance</div>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categorySales as $cat)
            <tr>
                <td>{{ $cat->category }}</td>
                <td>₱{{ number_format($cat->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F d, Y h:i A') }} | Tanim Agricultural Marketplace Admin Panel
    </div>
</body>
</html>
