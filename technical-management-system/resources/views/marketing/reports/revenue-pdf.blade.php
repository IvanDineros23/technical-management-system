<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Revenue Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #10b981;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .meta {
            margin: 20px 0;
            font-size: 12px;
            color: #666;
        }
        .meta p {
            margin: 3px 0;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .stat-box {
            padding: 15px;
            background-color: #f0fdf4;
            border-left: 3px solid #10b981;
            border-radius: 3px;
        }
        .stat-box label {
            display: block;
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-box .value {
            font-size: 18px;
            font-weight: bold;
            color: #10b981;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead {
            background-color: #f0f0f0;
        }
        table th {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
        }
        table td {
            border: 1px solid #ddd;
            padding: 10px;
            font-size: 12px;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .amount {
            text-align: right;
            font-weight: bold;
        }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f0fdf4;
            border-left: 4px solid #10b981;
        }
        .summary h3 {
            margin-top: 0;
            color: #10b981;
        }
        .summary p {
            margin: 5px 0;
            font-size: 13px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Revenue Report</h1>
        <p>GEMARC - Technical Management System</p>
    </div>
    
    <div class="meta">
        @if($fromDate && $toDate)
            <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}</p>
        @elseif($fromDate)
            <p><strong>From Date:</strong> {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }}</p>
        @elseif($toDate)
            <p><strong>To Date:</strong> {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}</p>
        @else
            <p><strong>Period:</strong> All Records</p>
        @endif
        <p><strong>Generated:</strong> {{ $generatedAt->format('F d, Y \a\t g:i A') }}</p>
    </div>
    
    <div class="stats">
        <div class="stat-box">
            <label>Total Revenue</label>
            <div class="value">₱{{ number_format($totalRevenue, 2) }}</div>
        </div>
        <div class="stat-box">
            <label>Total Jobs</label>
            <div class="value">{{ $totalJobs }}</div>
        </div>
        <div class="stat-box">
            <label>Average Revenue</label>
            <div class="value">₱{{ number_format($averageRevenue, 2) }}</div>
        </div>
        <div class="stat-box">
            <label>High Value Job</label>
            <div class="value">₱{{ number_format($jobOrders->max('grand_total') ?? 0, 2) }}</div>
        </div>
    </div>
    
    @if($revenueByCustomer->count() > 0)
        <h3 style="margin-top: 30px; color: #10b981;">Revenue by Customer</h3>
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Jobs Count</th>
                    <th style="text-align: right;">Total Revenue</th>
                    <th style="text-align: right;">Average per Job</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenueByCustomer as $item)
                    <tr>
                        <td>{{ $item['customer']->customer_name }}</td>
                        <td>{{ $item['count'] }}</td>
                        <td class="amount">₱{{ number_format($item['total'], 2) }}</td>
                        <td class="amount">₱{{ number_format($item['total'] / $item['count'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
    <div class="summary">
        <h3>Key Metrics</h3>
        <p><strong>Total Revenue:</strong> ₱{{ number_format($totalRevenue, 2) }}</p>
        <p><strong>Number of Jobs:</strong> {{ $totalJobs }}</p>
        <p><strong>Average Revenue per Job:</strong> ₱{{ number_format($averageRevenue, 2) }}</p>
        <p><strong>Clients with Revenue:</strong> {{ $revenueByCustomer->count() }}</p>
    </div>
    
    <div class="footer">
        <p>This is an official report generated by GEMARC TMS</p>
        <p>&copy; {{ date('Y') }} GEMARC. All rights reserved.</p>
    </div>
</body>
</html>
