<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Models\{Calibration, Certificate, JobOrder, Report, Timeline, User};
use Illuminate\Http\Request;

class SignatoryController extends Controller
{
    /**
     * Signatory Dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();

        // Stats
        $stats = [
            'for_review' => Calibration::where('status', 'approved')
                ->whereDoesntHave('certificate')
                ->count(),
            'approved' => Calibration::where('status', 'approved')->count(),
            'returned' => Calibration::where('status', 'returned_for_revision')->count(),
            'signed' => Certificate::where('signed_by', $user->id)->count(),
        ];

        // Recent submissions for review
        $recentSubmissions = Calibration::where('status', 'approved')
            ->whereDoesntHave('certificate')
            ->with(['assignment.jobOrder.customer', 'performedBy', 'measurementPoints'])
            ->latest()
            ->take(5)
            ->get();

        // Pending count for sidebar
        $pendingCount = $stats['for_review'];

        return view('signatory.dashboard', compact('stats', 'recentSubmissions', 'pendingCount'));
    }

    /**
     * For Review - List of calibration submissions
     */
    public function forReview(Request $request)
    {
        $query = Calibration::where('status', 'approved')
            ->whereDoesntHave('certificate')
            ->with(['assignment.jobOrder.customer', 'performedBy', 'measurementPoints']);

        // Filters
        if ($request->filled('date_from')) {
            $query->whereDate('calibration_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('calibration_date', '<=', $request->date_to);
        }
        if ($request->filled('job_order')) {
            $query->whereHas('assignment.jobOrder', function($q) {
                $q->where('job_order_number', 'like', '%' . request('job_order') . '%');
            });
        }
        if ($request->filled('technician')) {
            $query->where('performed_by', $request->technician);
        }

        $calibrations = $query->latest()->paginate(15);

        return view('signatory.for-review', compact('calibrations'));
    }

    /**
     * Review workspace - Full calibration data review
     */
    public function review(Calibration $calibration)
    {
        // Authorization
        if ($calibration->status !== 'approved') {
            abort(403, 'Only approved calibrations can be reviewed for signing.');
        }

        $calibration->load([
            'assignment.jobOrder.customer',
            'assignment.technician',
            'performedBy',
            'measurementPoints',
            'technicalReview.reviewedBy',
        ]);

        // Timeline for this calibration
        $timeline = Timeline::where('calibration_id', $calibration->id)
            ->orWhere('job_order_id', $calibration->assignment->job_order_id)
            ->latest()
            ->get();

        return view('signatory.review', compact('calibration', 'timeline'));
    }

    /**
     * Approve and prepare for signature
     */
    public function approve(Request $request, Calibration $calibration)
    {
        $validated = $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $calibration->update([
            'signatory_remarks' => $validated['remarks'] ?? null,
            'signatory_id' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Update timeline
        Timeline::create([
            'calibration_id' => $calibration->id,
            'job_order_id' => $calibration->assignment->job_order_id,
            'event' => 'approved_by_signatory',
            'description' => 'Calibration approved by signatory: ' . auth()->user()->name,
            'created_by' => auth()->id(),
        ]);

        // Audit logging
        AuditLogHelper::log(
            'APPROVE',
            'Calibration',
            $calibration->id,
            "Approved calibration {$calibration->id} for signing"
        );

        return redirect()->route('signatory.sign', $calibration)
            ->with('status', 'Calibration approved. Now sign the certificate.');
    }

    /**
     * Digital signature workflow
     */
    public function signatureForm(Calibration $calibration)
    {
        if ($calibration->signatory_id !== auth()->id() || !$calibration->reviewed_at) {
            abort(403, 'This calibration is not ready for signing.');
        }

        $calibration->load([
            'assignment.jobOrder.customer',
            'performedBy',
            'measurementPoints',
        ]);

        return view('signatory.sign', compact('calibration'));
    }

    /**
     * Create digital signature
     */
    public function sign(Request $request, Calibration $calibration)
    {
        $validated = $request->validate([
            'signature_password' => 'required|string|min:6',
            'certificate_number' => 'required|string|unique:certificates,certificate_number',
        ]);

        // TODO: Verify signature password (implement actual signature mechanism)
        // For now, we'll just create a record

        // Create certificate record
        $certificate = Certificate::create([
            'calibration_id' => $calibration->id,
            'job_order_id' => $calibration->assignment->job_order_id,
            'certificate_number' => $validated['certificate_number'],
            'signed_by' => auth()->id(),
            'signed_at' => now(),
            'status' => 'approved',
            'data' => $calibration->toJson(), // Store calibration data snapshot
        ]);

        // Update calibration status
        $calibration->update([
            'status' => 'signed',
            'certificate_id' => $certificate->id,
        ]);

        // Update timeline
        Timeline::create([
            'calibration_id' => $calibration->id,
            'job_order_id' => $calibration->assignment->job_order_id,
            'event' => 'signed',
            'description' => 'Certificate signed by: ' . auth()->user()->name,
            'created_by' => auth()->id(),
        ]);

        // Update job order status
        $calibration->assignment->jobOrder->update(['status' => 'completed']);

        // Audit logging
        AuditLogHelper::log(
            'SIGN',
            'Certificate',
            $certificate->id,
            "Signed certificate {$certificate->certificate_number} for calibration {$calibration->id}"
        );

        return redirect()->route('signatory.certificates')
            ->with('status', 'Certificate signed successfully! Certificate #' . $certificate->certificate_number);
    }

    /**
     * Request revision - return to tech head
     */
    public function requestRevision(Request $request, Calibration $calibration)
    {
        $validated = $request->validate([
            'revision_remarks' => 'required|string|max:1000',
        ]);

        $calibration->update([
            'status' => 'returned_for_revision',
            'signatory_remarks' => $validated['revision_remarks'],
            'signatory_id' => auth()->id(),
        ]);

        // Update timeline
        Timeline::create([
            'calibration_id' => $calibration->id,
            'job_order_id' => $calibration->assignment->job_order_id,
            'event' => 'returned_for_revision',
            'description' => 'Returned for revision: ' . $validated['revision_remarks'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('signatory.for-review')
            ->with('status', 'Calibration returned for revision.');
    }

    /**
     * Signed certificates list
     */
    public function certificates(Request $request)
    {
        $query = Certificate::with([
            'jobOrder.customer',
            'calibration.assignment.jobOrder.customer',
            'signedBy',
        ]);

        // Filters
        if ($request->filled('date_from')) {
            $query->whereDate('signed_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('signed_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $certificates = $query->latest('signed_at')->paginate(15);

        return view('signatory.certificates', compact('certificates'));
    }

    /**
     * Preview certificate
     */
    public function previewCertificate(Certificate $certificate)
    {
        $certificate->load([
            'calibration.assignment.jobOrder.customer',
            'calibration.performedBy',
            'calibration.measurementPoints',
            'signedBy',
            'issuedBy',
        ]);

        return view('signatory.certificate-preview', compact('certificate'));
    }

    /**
     * Timeline view
     */
    public function timeline(JobOrder $jobOrder, Request $request)
    {
        $jobOrder->load([
            'calibrations.performedBy',
            'calibrations.signatory', 
            'calibrations.technicalReviewer',
            'customer', 
            'assignments.assignedTo',
            'creator'
        ]);

        // Build timeline from actual events instead of Timeline model
        $timeline = collect();
        
        // Job Order created event
        $timeline->push([
            'event' => 'Job Order Created',
            'description' => $jobOrder->service_type . ' - ' . $jobOrder->service_description,
            'created_at' => $jobOrder->created_at,
            'created_by' => $jobOrder->creator,
            'status' => 'info'
        ]);
        
        // Assignment events
        foreach ($jobOrder->assignments as $assignment) {
            if ($assignment->assigned_at) {
                $timeline->push([
                    'event' => 'Technician Assigned',
                    'description' => 'Assigned to ' . ($assignment->assignedTo->name ?? 'N/A'),
                    'created_at' => $assignment->assigned_at,
                    'created_by' => null,
                    'status' => 'info'
                ]);
            }
            
            if ($assignment->started_at) {
                $timeline->push([
                    'event' => 'Work Started',
                    'description' => 'Technician started working on the job',
                    'created_at' => $assignment->started_at,
                    'created_by' => $assignment->assignedTo,
                    'status' => 'info'
                ]);
            }
            
            if ($assignment->completed_at) {
                $timeline->push([
                    'event' => 'Work Completed',
                    'description' => 'Technician completed the assigned work',
                    'created_at' => $assignment->completed_at,
                    'created_by' => $assignment->assignedTo,
                    'status' => 'success'
                ]);
            }
        }
        
        // Calibration events
        foreach ($jobOrder->calibrations as $calibration) {
            $timeline->push([
                'event' => 'Calibration Performed',
                'description' => 'Calibration completed by ' . ($calibration->performedBy->name ?? 'N/A'),
                'created_at' => $calibration->calibration_date ?? $calibration->created_at,
                'created_by' => $calibration->performedBy,
                'status' => 'info'
            ]);
            
            if ($calibration->status === 'approved') {
                $timeline->push([
                    'event' => 'Calibration Approved',
                    'description' => 'Calibration approved for signature',
                    'created_at' => $calibration->reviewed_at ?? $calibration->updated_at,
                    'created_by' => $calibration->technicalReviewer,
                    'status' => 'success'
                ]);
            }
            
            if ($calibration->signatory_id) {
                $timeline->push([
                    'event' => 'Document Signed',
                    'description' => 'Document signed by signatory',
                    'created_at' => $calibration->reviewed_at ?? $calibration->updated_at,
                    'created_by' => $calibration->signatory,
                    'status' => 'success'
                ]);
            }
        }
        
        // Certificate events
        $certificates = Certificate::where('job_order_id', $jobOrder->id)->get();
        foreach ($certificates as $certificate) {
            $timeline->push([
                'event' => 'Certificate Generated',
                'description' => 'Certificate #' . $certificate->certificate_number,
                'created_at' => $certificate->created_at,
                'created_by' => $certificate->signedBy,
                'status' => 'success'
            ]);
            
            if ($certificate->released_at) {
                $timeline->push([
                    'event' => 'Certificate Released',
                    'description' => 'Certificate released to customer',
                    'created_at' => $certificate->released_at,
                    'created_by' => $certificate->releasedBy,
                    'status' => 'success'
                ]);
            }
        }
        
        // Sort by date descending
        $timeline = $timeline->sortByDesc('created_at')->values();

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'jobOrder' => $jobOrder,
                'timeline' => $timeline
            ]);
        }

        return view('signatory.timeline', compact('jobOrder', 'timeline'));
    }

    /**
     * Unified timeline - View all job orders with their timelines
     */
    public function allTimelines(Request $request)
    {
        $query = JobOrder::with(['customer', 'calibrations']);
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('job_order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($custQ) use ($search) {
                      $custQ->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $jobOrders = $query->latest()->paginate(15);
        
        return view('signatory.all-timelines', compact('jobOrders'));
    }

    /**
     * Reports - View submitted reports for review
     */
    public function reports(Request $request)
    {
        $query = Report::with(['assignment.jobOrder.customer', 'assignment.assignedTo', 'submittedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by job order
        if ($request->filled('job_order')) {
            $query->whereHas('assignment.jobOrder', function($q) {
                $q->where('job_order_number', 'like', '%' . request('job_order') . '%');
            });
        }

        // Filter by technician
        if ($request->filled('technician')) {
            $query->where('submitted_by', $request->technician);
        }

        // Date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $reports = $query->latest('created_at')->paginate(15);

        return view('signatory.reports', compact('reports'));
    }

    /**
     * View report details
     */
    public function viewReport(Report $report)
    {
        $report->load(['assignment.jobOrder.customer', 'assignment.assignedTo', 'submittedBy', 'reviewedBy']);

        return view('signatory.report-detail', compact('report'));
    }

    /**
     * Profile settings
     */
    public function profile()
    {
        $user = auth()->user();

        return view('signatory.profile', compact('user'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'nullable|string',
            'title' => 'nullable|string|max:100',
        ]);

        auth()->user()->update($validated);

        return back()->with('status', 'Profile updated successfully.');
    }
}
