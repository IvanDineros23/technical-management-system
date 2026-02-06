<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Job Orders Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #2563eb;
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
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f0f0f0;
            border-left: 4px solid #2563eb;
        }
        .summary h3 {
            margin-top: 0;
            color: #2563eb;
        }
        .summary p {
            margin: 5px 0;
            font-size: 13px;
        }
        .status {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status.pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status.in_progress {
            background-color: #dbeafe;
            color: #0c2340;
        }
        .status.completed {
            background-color: #dcfce7;
            color: #166534;
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
        <h1>Job Orders Report</h1>
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
        <p><strong>Total Records:</strong> {{ $jobOrders->count() }}</p>
    </div>
    
    @if($jobOrders->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>JO Number</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Date Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobOrders as $jo)
                    <tr>
                        <td>{{ $jo->job_order_number }}</td>
                        <td>{{ $jo->customer->customer_name ?? 'N/A' }}</td>
                        <td>
                            <span class="status {{ str_replace('_', '-', $jo->status) }}">
                                {{ ucfirst(str_replace('_', ' ', $jo->status)) }}
                            </span>
                        </td>
                        <td>₱{{ number_format($jo->grand_total, 2) }}</td>
                        <td>{{ $jo->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="summary">
            <h3>Summary</h3>
            <p><strong>Total Job Orders:</strong> {{ $jobOrders->count() }}</p>
            <p><strong>Total Amount:</strong> ₱{{ number_format($jobOrders->sum('grand_total'), 2) }}</p>
            <p><strong>Average Amount:</strong> ₱{{ number_format($jobOrders->avg('grand_total'), 2) }}</p>
            <p><strong>Completed:</strong> {{ $jobOrders->where('status', 'completed')->count() }}</p>
            <p><strong>In Progress:</strong> {{ $jobOrders->where('status', 'in_progress')->count() }}</p>
            <p><strong>Pending:</strong> {{ $jobOrders->where('status', 'pending')->count() }}</p>
        </div>
    @else
        <div class="summary">
            <p>No job orders found for the selected period.</p>
        </div>
    @endif
    
    <div class="footer">
        <p>This is an official report generated by GEMARC TMS</p>
        <p>&copy; {{ date('Y') }} GEMARC. All rights reserved.</p>
    </div>
</body>
</html>
