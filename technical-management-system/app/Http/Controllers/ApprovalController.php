<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Models\{Calibration, TechnicalReview};
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    /**
     * Show pending calibrations for technical review
     */
    public function index()
    {
        // Only tech head can approve
        if (auth()->user()->role->slug !== 'tech_head') {
            abort(403, 'Only Technical Head can approve calibrations');
        }

        $pendingCalibrations = Calibration::with([
            'jobOrderItem.jobOrder.customer',
            'assignment.technician',
            'performedBy',
            'measurementPoints'
        ])
            ->where('status', 'submitted_for_review')
            ->latest()
            ->get();

        return view('tech-head.calibration-approvals', compact('pendingCalibrations'));
    }

    /**
     * Show detailed view for approving a calibration
     */
    public function show(Calibration $calibration)
    {
        // Only tech head can approve
        if (auth()->user()->role->slug !== 'tech_head') {
            abort(403, 'Unauthorized');
        }

        $calibration->load([
            'jobOrderItem.jobOrder.customer',
            'assignment.technician',
            'performedBy',
            'measurementPoints',
            'technicalReview'
        ]);

        return view('tech-head.calibration-review', compact('calibration'));
    }

    /**
     * Approve or reject calibration
     */
    public function approve(Request $request, Calibration $calibration)
    {
        // Only tech head can approve
        if (auth()->user()->role->slug !== 'tech_head') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'result' => 'required|in:approved,rejected,conditional',
            'findings' => 'required|string',
            'recommendations' => 'nullable|string',
            'data_reviewed' => 'boolean',
            'calculations_verified' => 'boolean',
            'standards_checked' => 'boolean',
        ]);

        // Check if technical review already exists
        $review = $calibration->technicalReview;

        if (!$review) {
            $review = new TechnicalReview();
        }

        // Update review record
        $review->update([
            'calibration_id' => $calibration->id,
            'reviewer_id' => auth()->id(),
            'review_date' => now()->date(),
            'review_time' => now()->format('H:i:s'),
            'result' => $validated['result'],
            'findings' => $validated['findings'],
            'recommendations' => $validated['recommendations'] ?? null,
            'data_reviewed' => $validated['data_reviewed'] ?? false,
            'calculations_verified' => $validated['calculations_verified'] ?? false,
            'standards_checked' => $validated['standards_checked'] ?? false,
            'status' => $validated['result'],
        ]);

        // Update calibration status based on review result
        $newStatus = match($validated['result']) {
            'approved' => 'approved',
            'rejected' => 'rejected',
            'conditional' => 'conditional_approval',
            default => 'submitted_for_review'
        };

        $calibration->update([
            'status' => $newStatus,
        ]);

        $message = match($validated['result']) {
            'approved' => 'Calibration approved successfully!',
            'rejected' => 'Calibration rejected. Technician will be notified.',
            'conditional' => 'Conditional approval sent. Awaiting corrections.',
            default => 'Review saved.'
        };

        // Audit logging
        AuditLogHelper::log(
            strtoupper($validated['result']),
            'Calibration',
            $calibration->id,
            "Technical review completed: {$validated['result']}. Findings: {$validated['findings']}"
        );

        return redirect()->route('tech-head.reports')
            ->with('status', $message);
    }

    /**
     * Get measurement points summary for a calibration
     */
    public function getMeasurementSummary(Calibration $calibration)
    {
        $points = $calibration->measurementPoints->map(function ($point) {
            return [
                'id' => $point->id,
                'point_number' => $point->point_number,
                'reference_value' => $point->reference_value,
                'uut_reading' => $point->uut_reading,
                'error' => $point->error,
                'uncertainty' => $point->uncertainty,
                'status' => $point->status,
            ];
        });

        $passCount = $points->where('status', 'pass')->count();
        $failCount = $points->where('status', 'fail')->count();
        $totalPoints = $points->count();

        return response()->json([
            'points' => $points,
            'summary' => [
                'total_points' => $totalPoints,
                'pass_count' => $passCount,
                'fail_count' => $failCount,
                'pass_percentage' => $totalPoints > 0 ? round(($passCount / $totalPoints) * 100, 2) : 0,
            ]
        ]);
    }
}
