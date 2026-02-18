<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs Export</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #16a34a;
            padding-bottom: 15px;
        }

        .logo {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo img {
            height: 80px;
            width: auto;
        }
        
        .header h1 {
            font-size: 20px;
            color: #16a34a;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 16px;
            color: #374151;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10px;
            color: #666;
        }
        
        .export-info {
            background: #f3f4f6;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 8px;
        }
        
        .export-info p {
            margin: 3px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        thead {
            background-color: #16a34a;
            color: white;
        }
        
        th {
            padding: 10px 6px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
            border: 1px solid #15803d;
        }
        
        td {
            padding: 8px 6px;
            border: 1px solid #e5e7eb;
            font-size: 8px;
            vertical-align: top;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        tbody tr:hover {
            background-color: #dcfce7;
        }
        
        .action-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 7px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .action-create { background: #dcfce7; color: #166534; }
        .action-update { background: #dbeafe; color: #1e40af; }
        .action-delete { background: #fee2e2; color: #991b1b; }
        .action-login { background: #e0e7ff; color: #4338ca; }
        .action-logout { background: #fef3c7; color: #92400e; }
        .action-register { background: #fae8ff; color: #86198f; }
        .action-default { background: #f3f4f6; color: #374151; }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 7px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('assets/0fae4580-eff0-4ee7-98e2-8ab80dd542cf-removebg-preview.png') }}" alt="Gemarc Logo">
        </div>
        <h1>Gemarc Enterprises Inc</h1>
        <h2>Audit Logs Report</h2>
        <p>Technical Management System - Activity Tracking</p>
    </div>
    
    <div class="export-info">
        <p><strong>Export Date:</strong> {{ now()->timezone('Asia/Manila')->format('F d, Y h:i A') }}</p>
        <p><strong>Total Records:</strong> {{ $auditLogs->count() }}</p>
        <p><strong>Exported By:</strong> {{ auth()->user()->name ?? 'System' }}</p>
    </div>
    
    @if($auditLogs->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 13%;">Timestamp</th>
                <th style="width: 12%;">User</th>
                <th style="width: 10%;">Action</th>
                <th style="width: 12%;">Module</th>
                <th style="width: 53%;">Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach($auditLogs as $log)
            <tr>
                <td>{{ $log->created_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}</td>
                <td>{{ $log->user?->name ?? 'System' }}</td>
                <td>
                    @php
                        $action = strtoupper($log->action);
                        $actionClass = match($action) {
                            'CREATE' => 'action-create',
                            'UPDATE' => 'action-update',
                            'DELETE' => 'action-delete',
                            'LOGIN' => 'action-login',
                            'LOGOUT' => 'action-logout',
                            'REGISTER' => 'action-register',
                            default => 'action-default',
                        };
                    @endphp
                    <span class="action-badge {{ $actionClass }}">{{ $action }}</span>
                </td>
                <td>{{ $log->model_type ?? 'N/A' }}</td>
                <td>{{ $log->description ?? 'No description' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <p>No audit logs found for the selected filters.</p>
    </div>
    @endif
    
    <div class="footer">
        <p>This is a system-generated report from Gemarc Enterprises Inc Technical Management System</p>
        <p>© {{ now()->year }} Gemarc Enterprises Inc. All rights reserved.</p>
    </div>
</body>
</html>
