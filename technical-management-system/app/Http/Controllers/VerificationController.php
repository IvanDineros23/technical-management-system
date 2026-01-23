<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;

class VerificationController extends Controller
{
    // Public search page and quick lookup
    public function verify(Request $request)
    {
        $certificateNumber = trim((string) $request->query('certificate_number', ''));
        $searchPerformed = $certificateNumber !== '';
        $error = null;
        $certificate = null;

        if ($searchPerformed) {
            $certificate = Certificate::with([
                'jobOrder.customer',
                'jobOrderItem',
                'calibration.measurementPoints',
                'signedBy'
            ])->where('certificate_number', $certificateNumber)->first();

            if (!$certificate) {
                $error = 'No certificate found for that number.';
            }
        }

        return view('verification.verify', compact('searchPerformed', 'error', 'certificate'));
    }

    // Detailed certificate verification page
    public function show(string $certificateNumber)
    {
        $certificate = Certificate::with([
            'jobOrder.customer',
            'jobOrderItem',
            'calibration.measurementPoints',
            'issuedBy', 'reviewedBy', 'approvedBy', 'signedBy'
        ])->where('certificate_number', $certificateNumber)->firstOrFail();

        return view('verification.show', compact('certificate'));
    }

    // Lightweight JSON status endpoint
    public function getStatus(string $certificateNumber)
    {
        $certificate = Certificate::with(['signedBy', 'jobOrder.customer'])
            ->where('certificate_number', $certificateNumber)
            ->first();

        if (!$certificate) {
            return response()->json(['found' => false, 'message' => 'Certificate not found'], 404);
        }

        return response()->json([
            'found' => true,
            'certificate_number' => $certificate->certificate_number,
            'status' => $certificate->signed_at ? 'signed' : 'issued',
            'signed_at' => $certificate->signed_at,
            'signed_by' => optional($certificate->signedBy)->name,
            'customer' => optional(optional($certificate->jobOrder)->customer)->name,
        ]);
    }
}
