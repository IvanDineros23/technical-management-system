<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customer Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #a855f7;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #a855f7;
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
            background-color: #faf5ff;
            border-left: 3px solid #a855f7;
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
            color: #a855f7;
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
        .status {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status.active {
            background-color: #dcfce7;
            color: #166534;
        }
        .status.inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #faf5ff;
            border-left: 4px solid #a855f7;
        }
        .summary h3 {
            margin-top: 0;
            color: #a855f7;
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
        <h1>Customer Report</h1>
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
            <p><strong>Period:</strong> All Customers</p>
        @endif
        <p><strong>Generated:</strong> {{ $generatedAt->format('F d, Y \a\t g:i A') }}</p>
    </div>
    
    <div class="stats">
        <div class="stat-box">
            <label>Total Customers</label>
            <div class="value">{{ $totalCustomers }}</div>
        </div>
        <div class="stat-box">
            <label>Active Customers</label>
            <div class="value">{{ $activeCustomers }}</div>
        </div>
        <div class="stat-box">
            <label>Total Revenue</label>
            <div class="value">₱{{ number_format($totalRevenue, 2) }}</div>
        </div>
        <div class="stat-box">
            <label>Avg. Customer Value</label>
            <div class="value">₱{{ number_format($totalCustomers > 0 ? $totalRevenue / $totalCustomers : 0, 2) }}</div>
        </div>
    </div>
    
    @if($customers->count() > 0)
        <h3 style="margin-top: 30px; color: #a855f7;">Customer Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Jobs</th>
                    <th style="text-align: right;">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                    <tr>
                        <td>{{ $customer->customer_name }}</td>
                        <td>{{ $customer->contact_person ?? 'N/A' }}</td>
                        <td>{{ $customer->phone ?? 'N/A' }}</td>
                        <td>
                            <span class="status {{ $customer->is_active ? 'active' : 'inactive' }}">
                                {{ $customer->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $customer->job_orders_count }}</td>
                        <td class="amount">₱{{ number_format($customer->jobOrders->sum('grand_total'), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="summary">
            <p>No customers found for the selected period.</p>
        </div>
    @endif
    
    <div class="summary">
        <h3>Summary</h3>
        <p><strong>Total Customers:</strong> {{ $totalCustomers }}</p>
        <p><strong>Active Customers:</strong> {{ $activeCustomers }}</p>
        <p><strong>Inactive Customers:</strong> {{ $totalCustomers - $activeCustomers }}</p>
        <p><strong>Total Revenue from Customers:</strong> ₱{{ number_format($totalRevenue, 2) }}</p>
    </div>
    
    <div class="footer">
        <p>This is an official report generated by GEMARC TMS</p>
        <p>&copy; {{ date('Y') }} GEMARC. All rights reserved.</p>
    </div>
</body>
</html>
