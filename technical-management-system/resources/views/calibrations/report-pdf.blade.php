<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Calibration Report</title>
    <style>
        body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; color:#0f172a; }
        .container { width: 100%; padding: 24px; }
        .header { text-align:center; margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: 700; }
        .subtitle { font-size: 12px; color:#475569; }
        .section { margin-top: 12px; }
        .section h3 { font-size: 14px; margin-bottom: 8px; color:#1e293b; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; font-size: 12px; }
        th { background: #f1f5f9; text-align: left; }
        .grid { display: table; width: 100%; }
        .col { display: table-cell; width: 50%; vertical-align: top; }
        .muted { color:#64748b; font-size: 11px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">Detailed Calibration Report</div>
            <div class="subtitle">Calibration #: {{ $calibration->calibration_number }} · Date: {{ optional($calibration->calibration_date)->format('M d, Y') }}</div>
        </div>

        <div class="section">
            <h3>Job & Customer</h3>
            <div class="grid">
                <div class="col">
                    <strong>Job Order:</strong> {{ optional($calibration->assignment->jobOrder)->job_order_number }}<br>
                    <strong>Customer:</strong> {{ optional($calibration->assignment->jobOrder->customer)->name }}<br>
                    <strong>Service Type:</strong> {{ optional($calibration->assignment->jobOrder)->service_type }}
                </div>
                <div class="col">
                    <strong>Technician:</strong> {{ optional($calibration->performedBy)->name }}<br>
                    <strong>Location:</strong> {{ $calibration->location ?? 'N/A' }}<br>
                    <strong>Procedure Ref:</strong> {{ $calibration->procedure_reference ?? 'N/A' }}
                </div>
            </div>
        </div>

        <div class="section">
            <h3>Measurement Points</h3>
            <table>
                <thead>
                    <tr>
                        <th>Point #</th>
                        <th>Reference Value</th>
                        <th>UUT Reading</th>
                        <th>Error</th>
                        <th>Uncertainty</th>
                        <th>Acceptance</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($calibration->measurementPoints as $p)
                    <tr>
                        <td>{{ $p->point_number }}</td>
                        <td>{{ number_format($p->reference_value, 4) }}</td>
                        <td>{{ number_format($p->uut_reading, 4) }}</td>
                        <td>{{ number_format($p->error, 4) }}</td>
                        <td>{{ $p->uncertainty ? number_format($p->uncertainty, 4) : 'N/A' }}</td>
                        <td>{{ $p->acceptance_criteria ?? 'N/A' }}</td>
                        <td>{{ strtoupper($p->status) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="muted">Error = UUT Reading − Reference Value. Pass if |Error| ≤ Uncertainty.</div>
        </div>

        <div class="section">
            <h3>Summary</h3>
            <div class="grid">
                <div class="col">
                    <strong>Total Points:</strong> {{ $calibration->measurementPoints->count() }}<br>
                    <strong>Passed:</strong> {{ $calibration->measurementPoints->where('status','pass')->count() }}<br>
                    <strong>Failed:</strong> {{ $calibration->measurementPoints->where('status','fail')->count() }}
                </div>
                <div class="col">
                    <strong>Start:</strong> {{ $calibration->start_time ?? 'N/A' }}<br>
                    <strong>End:</strong> {{ $calibration->end_time ?? 'N/A' }}<br>
                    <strong>Remarks:</strong> {{ $calibration->remarks ?? 'None' }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>