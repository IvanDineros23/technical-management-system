<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Performance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f97316;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #f97316;
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
            background-color: #fff7ed;
            border-left: 3px solid #f97316;
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
            color: #f97316;
        }
        .status-breakdown {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .status-item {
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .status-item.pending {
            background-color: #fef3c7;
            border-left: 3px solid #f59e0b;
        }
        .status-item.in-progress {
            background-color: #dbeafe;
            border-left: 3px solid #3b82f6;
        }
        .status-item.completed {
            background-color: #dcfce7;
            border-left: 3px solid #10b981;
        }
        .status-item .label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .status-item .count {
            font-size: 24px;
            font-weight: bold;
        }
        .progress-bar {
            margin: 20px 0;
            padding: 15px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        .progress-bar .label {
            font-size: 12px;
            color: #666;
            margin-bottom: 8px;
        }
        .bar {
            height: 20px;
            background-color: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
        }
        .bar-fill {
            height: 100%;
            background-color: #f97316;
        }
        .bar-value {
            text-align: right;
            margin-top: 5px;
            font-weight: bold;
            font-size: 13px;
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
            background-color: #fff7ed;
            border-left: 4px solid #f97316;
        }
        .summary h3 {
            margin-top: 0;
            color: #f97316;
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
        <h1>Performance Report</h1>
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
            <label>Total Jobs</label>
            <div class="value">{{ $totalJobs }}</div>
        </div>
        <div class="stat-box">
            <label>Completed Jobs</label>
            <div class="value">{{ $completedJobs }}</div>
        </div>
        <div class="stat-box">
            <label>Completion Rate</label>
            <div class="value">{{ number_format($completionRate, 1) }}%</div>
        </div>
        <div class="stat-box">
            <label>Total Revenue</label>
            <div class="value">₱{{ number_format($totalRevenue, 2) }}</div>
        </div>
    </div>
    
    <div class="status-breakdown">
        <div class="status-item pending">
            <div class="label">Pending Jobs</div>
            <div class="count">{{ $statusBreakdown['pending'] }}</div>
        </div>
        <div class="status-item in-progress">
            <div class="label">In Progress</div>
            <div class="count">{{ $statusBreakdown['in_progress'] }}</div>
        </div>
        <div class="status-item completed">
            <div class="label">Completed</div>
            <div class="count">{{ $statusBreakdown['completed'] }}</div>
        </div>
    </div>
    
    <div class="progress-bar">
        <div class="label">Job Completion Progress</div>
        <div class="bar">
            <div class="bar-fill" style="width: {{ $completionRate }}%;"></div>
        </div>
        <div class="bar-value">{{ number_format($completionRate, 1) }}% Completed</div>
    </div>
    
    <div class="summary">
        <h3>Key Performance Indicators</h3>
        <p><strong>Total Jobs Processed:</strong> {{ $totalJobs }}</p>
        <p><strong>Completed Jobs:</strong> {{ $completedJobs }}</p>
        <p><strong>In-Progress Jobs:</strong> {{ $inProgressJobs }}</p>
        <p><strong>Pending Jobs:</strong> {{ $pendingJobs }}</p>
        <p><strong>Completion Rate:</strong> {{ number_format($completionRate, 2) }}%</p>
        <p><strong>Total Revenue Generated:</strong> ₱{{ number_format($totalRevenue, 2) }}</p>
        <p><strong>Average Revenue per Job:</strong> ₱{{ number_format($averageRevenue, 2) }}</p>
    </div>
    
    <div class="footer">
        <p>This is an official report generated by GEMARC TMS</p>
        <p>&copy; {{ date('Y') }} GEMARC. All rights reserved.</p>
    </div>
</body>
</html>
