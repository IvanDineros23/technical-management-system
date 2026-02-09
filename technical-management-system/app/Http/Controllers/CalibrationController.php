<?php

namespace App\Http\Controllers;

use App\Models\Calibration;

class CalibrationController extends Controller
{
    /**
     * Generate detailed calibration report PDF
     */
    public function reportPdf(Calibration $calibration)
    {
        $calibration->load(['assignment.jobOrder.customer', 'measurementPoints', 'performedBy']);

        $data = [
            'calibration' => $calibration,
        ];

        // Generate PDF using DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('calibrations.report-pdf', $data)->setPaper('a4');

        $filename = 'calibration-report-' . ($calibration->calibration_number ?? $calibration->id) . '.pdf';
        return $pdf->download($filename);
    }
}
