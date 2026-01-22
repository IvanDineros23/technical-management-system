<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Calibration Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        @page {
            margin: 40px 50px;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #1e40af;
            font-size: 28pt;
            margin: 0 0 10px 0;
            font-weight: bold;
        }
        
        .header h2 {
            color: #3b82f6;
            font-size: 18pt;
            margin: 0;
            font-weight: normal;
        }
        
        .cert-number {
            background: #dbeafe;
            padding: 10px;
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        .info-section h3 {
            color: #1e40af;
            font-size: 13pt;
            margin: 0 0 10px 0;
            border-bottom: 2px solid #93c5fd;
            padding-bottom: 5px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            color: #4b5563;
            padding: 5px 10px 5px 0;
        }
        
        .info-value {
            display: table-cell;
            width: 60%;
            padding: 5px 0;
            color: #1f2937;
        }
        
        .calibration-data {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 15px;
            margin: 20px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        table th {
            background: #2563eb;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        
        table td {
            border: 1px solid #d1d5db;
            padding: 8px;
        }
        
        table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        
        .signature-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            margin: 20px 2% 0 0;
        }
        
        .signature-line {
            border-top: 2px solid #333;
            margin: 50px auto 10px;
            width: 80%;
        }
        
        .signature-box p {
            margin: 5px 0;
            font-size: 10pt;
        }
        
        .signature-box .name {
            font-weight: bold;
            color: #1e40af;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding: 10px 0;
        }
        
        .validity-notice {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            margin: 20px 0;
            font-size: 10pt;
        }
        
        .qr-code {
            float: right;
            margin: 0 0 10px 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CALIBRATION CERTIFICATE</h1>
        <h2>Certificate of Calibration</h2>
    </div>
    
    <div class="cert-number">
        Certificate No: {{ $certificate->certificate_number }}
    </div>
    
    <div class="info-section">
        <h3>Customer Information</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Customer Name:</div>
                <div class="info-value">{{ $certificate->jobOrder?->customer?->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Work Order No:</div>
                <div class="info-value">{{ $certificate->jobOrder?->job_order_number ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Service Type:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $certificate->jobOrder?->service_type ?? 'N/A')) }}</div>
            </div>
        </div>
    </div>
    
    <div class="info-section">
        <h3>Certificate Details</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Issue Date:</div>
                <div class="info-value">{{ optional($certificate->issue_date)->setTimezone('Asia/Manila')->format('F d, Y') ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Valid Until:</div>
                <div class="info-value">{{ optional($certificate->valid_until)->setTimezone('Asia/Manila')->format('F d, Y') ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Certificate Version:</div>
                <div class="info-value">{{ $certificate->version }}.{{ $certificate->revision_number }}</div>
            </div>
        </div>
    </div>
    
    <div class="calibration-data">
        <h3 style="margin-top: 0;">Calibration Information</h3>
        
        @if($certificate->jobOrder)
            <p><strong>Service Description:</strong></p>
            <p>{{ $certificate->jobOrder->service_description ?? 'No description provided.' }}</p>
            
            @if($certificate->jobOrder->special_instructions)
                <p><strong>Special Instructions:</strong></p>
                <p>{{ $certificate->jobOrder->special_instructions }}</p>
            @endif
        @else
            {{-- Manual entry certificate - show notes --}}
            <p><strong>Certificate Details:</strong></p>
            <p style="white-space: pre-line;">{{ $certificate->notes ?? 'No details provided.' }}</p>
        @endif
        
        <!-- Placeholder for actual calibration data -->
        <table>
            <thead>
                <tr>
                    <th>Parameter</th>
                    <th>Standard Value</th>
                    <th>Measured Value</th>
                    <th>Uncertainty</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" style="text-align: center; color: #6b7280; padding: 20px;">
                        Calibration data will be populated from calibration records
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="validity-notice">
        <strong>⚠ Notice:</strong> This certificate is valid until {{ optional($certificate->valid_until)->setTimezone('Asia/Manila')->format('F d, Y') ?? 'N/A' }}. 
        Recalibration is recommended before this date to ensure continued accuracy and compliance.
    </div>
    
    <div class="signature-section">
        <h3 style="color: #1e40af; margin-bottom: 20px;">Authorized Signatures</h3>
        
        <div class="signature-box">
            <div class="signature-line"></div>
            <p class="name">{{ $certificate->issuedBy->name ?? 'N/A' }}</p>
            <p>Issued By</p>
            <p>{{ optional($certificate->generated_at)->setTimezone('Asia/Manila')->format('F d, Y') ?? 'N/A' }}</p>
        </div>
        
        <div class="signature-box">
            <div class="signature-line"></div>
            <p class="name">{{ $certificate->approvedBy->name ?? 'N/A' }}</p>
            <p>Approved By</p>
            <p>{{ optional($certificate->jobOrder?->approved_at ?? $certificate->generated_at)->setTimezone('Asia/Manila')->format('F d, Y') ?? 'N/A' }}</p>
        </div>
    </div>
    
    <div class="footer">
        <p>This certificate is computer-generated and is valid without signature.</p>
        <p>For verification, please visit our website or scan the QR code.</p>
        <p>Generated on {{ now()->setTimezone('Asia/Manila')->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>
