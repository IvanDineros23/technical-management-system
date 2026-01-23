<?php

namespace App\Http\Controllers;

use App\Models\{Assignment, Calibration, JobOrder, JobOrderItem, MeasurementPoint, UncertaintyCalculation};
use Illuminate\Http\Request;

class CalibrationController extends Controller
{
    /**
     * Show technician's calibration assignments
     */
    public function index()
    {
        $technician = auth()->user();
        
        // Get assignments for this technician that need calibration entry
        $assignments = Assignment::with([
            'jobOrder.customer',
            'jobOrder.items',
            'technician'
        ])
            ->where('assigned_to', $technician->id)
            ->where(function($query) {
                $query->where('status', 'assigned')
                    ->orWhere('status', 'in_progress');
            })
            ->get();

        return view('technician.calibration-assignments', compact('assignments'));
    }

    /**
     * Show calibration entry form for a specific assignment
     */
    public function show(Assignment $assignment)
    {
        // Check authorization
        if ($assignment->assigned_to !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Load related data
        $assignment->load([
            'jobOrder.customer',
            'jobOrderItems',
            'technician'
        ]);

        // Check if calibration record exists
        $calibration = Calibration::where('assignment_id', $assignment->id)->first();

        if (!$calibration) {
            // Create new calibration record
            $calibration = Calibration::create([
                'assignment_id' => $assignment->id,
                'job_order_item_id' => $assignment->jobOrderItems()->first()->id ?? null,
                'performed_by' => auth()->id(),
                'calibration_number' => 'CAL-' . date('YmdHis') . '-' . auth()->id(),
                'status' => 'pending',
                'calibration_date' => now()->date(),
            ]);
        }

        $calibration->load('measurementPoints');

        return view('technician.calibration-entry', compact('assignment', 'calibration'));
    }

    /**
     * Store calibration measurement points
     */
    public function storeMeasurementPoints(Request $request, Calibration $calibration)
    {
        // Check authorization
        if ($calibration->performed_by !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'calibration_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'procedure_reference' => 'nullable|string|max:255',
            'environmental_conditions' => 'nullable|array',
            'measurement_points' => 'required|array',
            'measurement_points.*.point_number' => 'required|integer|min:1',
            'measurement_points.*.reference_value' => 'required|numeric',
            'measurement_points.*.uut_reading' => 'required|numeric',
            'measurement_points.*.uncertainty' => 'nullable|numeric|min:0',
            'measurement_points.*.acceptance_criteria' => 'nullable|string',
        ]);

        // Update calibration record
        $calibration->update([
            'calibration_date' => $validated['calibration_date'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'location' => $validated['location'] ?? null,
            'procedure_reference' => $validated['procedure_reference'] ?? null,
            'environmental_conditions' => $validated['environmental_conditions'] ?? null,
        ]);

        // Clear existing measurement points
        $calibration->measurementPoints()->delete();

        // Create new measurement points
        foreach ($validated['measurement_points'] as $point) {
            $error = $point['uut_reading'] - $point['reference_value'];
            
            MeasurementPoint::create([
                'calibration_id' => $calibration->id,
                'point_number' => $point['point_number'],
                'reference_value' => $point['reference_value'],
                'uut_reading' => $point['uut_reading'],
                'error' => $error,
                'uncertainty' => $point['uncertainty'] ?? null,
                'acceptance_criteria' => $point['acceptance_criteria'] ?? null,
                'status' => isset($point['uncertainty']) && abs($error) <= $point['uncertainty'] ? 'pass' : 'fail',
            ]);
        }

        return redirect()->route('technician.calibration.show', $calibration->assignment_id)
            ->with('status', 'Measurement points saved successfully!');
    }

    /**
     * Submit calibration for technical review
     */
    public function submitForReview(Request $request, Calibration $calibration)
    {
        // Check authorization
        if ($calibration->performed_by !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'remarks' => 'nullable|string',
            'pass_fail' => 'required|in:pass,fail,conditional',
        ]);

        // Verify measurement points exist
        if ($calibration->measurementPoints()->count() === 0) {
            return back()->withErrors(['measurement_points' => 'At least one measurement point is required.']);
        }

        // Update calibration
        $calibration->update([
            'status' => 'submitted_for_review',
            'pass_fail' => $validated['pass_fail'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('technician.dashboard')
            ->with('status', 'Calibration data submitted for technical review!');
    }

    /**
     * Store uncertainty calculation for a measurement point
     */
    public function storeUncertainty(Request $request, MeasurementPoint $measurementPoint)
    {
        $validated = $request->validate([
            'standard_uncertainty' => 'required|numeric|min:0',
            'coverage_factor' => 'required|numeric|min:1',
            'confidence_level' => 'required|string|in:68%,95%,99%',
            'method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Delete existing uncertainty calculation if exists
        $measurementPoint->uncertaintyCalculation()->delete();

        // Calculate expanded uncertainty
        $expandedUncertainty = $validated['standard_uncertainty'] * $validated['coverage_factor'];

        // Create new uncertainty calculation
        UncertaintyCalculation::create([
            'calibration_id' => $measurementPoint->calibration_id,
            'measurement_point_id' => $measurementPoint->id,
            'standard_uncertainty' => $validated['standard_uncertainty'],
            'coverage_factor' => $validated['coverage_factor'],
            'expanded_uncertainty' => $expandedUncertainty,
            'confidence_level' => $validated['confidence_level'],
            'method' => $validated['method'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update measurement point uncertainty and status
        $error = $measurementPoint->error;
        $status = abs($error) <= $expandedUncertainty ? 'pass' : 'fail';

        $measurementPoint->update([
            'uncertainty' => $expandedUncertainty,
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Uncertainty calculation saved',
            'expanded_uncertainty' => $expandedUncertainty,
            'status' => $status,
        ]);
    }

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
