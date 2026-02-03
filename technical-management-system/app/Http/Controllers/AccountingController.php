<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Models\{Certificate, JobOrder, Payment, CertificateRelease};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    /**
     * Accounting Dashboard - Overview
     */
    public function dashboard(Request $request)
    {
        $today = now();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        // Summary stats
        $stats = [
            'pending_for_release' => Certificate::where('status', 'approved')
                ->whereNotNull('generated_at')
                ->whereNull('released_at')
                ->count(),
            
            'unpaid_jobs' => JobOrder::whereDoesntHave('payment', function($q) {
                $q->where('status', 'verified');
            })
                ->whereIn('status', ['completed', 'in_progress'])
                ->count(),
            
            'released_today' => Certificate::whereDate('released_at', $today)->count(),
            
            'released_this_week' => Certificate::whereBetween('released_at', [$startOfWeek, $endOfWeek])->count(),
        ];

        // Top 10 Pending Releases
        $pendingReleases = Certificate::with(['jobOrder.customer', 'jobOrder.payment'])
            ->where('status', 'approved')
            ->whereNotNull('generated_at')
            ->whereNull('released_at')
            ->latest('generated_at')
            ->take(10)
            ->get();

        // Top 10 Pending Payment Verification
        $pendingPayments = JobOrder::with(['customer', 'payment'])
            ->whereHas('certificate', function($q) {
                $q->where('status', 'approved')->whereNotNull('generated_at');
            })
            ->where(function($q) {
                $q->whereDoesntHave('payment')
                    ->orWhereHas('payment', function($p) {
                        $p->where('status', '!=', 'verified');
                    });
            })
            ->latest()
            ->take(10)
            ->get();

        return view('accounting.dashboard', compact('stats', 'pendingReleases', 'pendingPayments'));
    }

    /**
     * Payment Verification Page
     */
    public function payments(Request $request)
    {
        $query = JobOrder::with(['customer', 'payment', 'certificate']);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('job_order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by payment status
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'unpaid') {
                $query->whereDoesntHave('payment');
            } elseif ($status === 'paid') {
                $query->whereHas('payment', function($q) {
                    $q->where('status', 'paid');
                });
            } elseif ($status === 'verified') {
                $query->whereHas('payment', function($q) {
                    $q->where('status', 'verified');
                });
            }
        }

        $jobOrders = $query->whereIn('status', ['completed', 'in_progress'])
            ->latest()
            ->paginate(20);

        return view('accounting.payments', compact('jobOrders'));
    }

    /**
     * Verify Payment
     */
    public function verifyPayment(Request $request, JobOrder $jobOrder)
    {
        $validated = $request->validate([
            'payment_code' => 'required|string|max:255',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Get or create payment record
            $payment = $jobOrder->payment()->firstOrNew([
                'job_order_id' => $jobOrder->id,
            ]);

            $payment->fill([
                'payment_code' => $validated['payment_code'],
                'amount_paid' => $validated['amount_paid'],
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $payment->save();

            DB::commit();

            return redirect()->route('accounting.payments')
                ->with('status', 'Payment verified successfully for ' . $jobOrder->job_order_number);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to verify payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark as Paid (if no payment record exists)
     */
    public function markAsPaid(Request $request, JobOrder $jobOrder)
    {
        $validated = $request->validate([
            'payment_code' => 'required|string|max:255',
            'amount_paid' => 'required|numeric|min:0',
            'paid_at' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            Payment::create([
                'job_order_id' => $jobOrder->id,
                'payment_code' => $validated['payment_code'],
                'amount_paid' => $validated['amount_paid'],
                'paid_at' => $validated['paid_at'] ?? now(),
                'status' => 'paid',
            ]);

            DB::commit();

            return redirect()->route('accounting.payments')
                ->with('status', 'Payment recorded for ' . $jobOrder->job_order_number);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to record payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Certificates For Release
     */
    public function certificatesForRelease(Request $request)
    {
        $query = Certificate::with(['jobOrder.customer', 'jobOrder.payment'])
            ->where('status', 'approved')
            ->whereNotNull('generated_at')
            ->whereNull('released_at');

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhereHas('jobOrder', function($jo) use ($search) {
                        $jo->where('job_order_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jobOrder.customer', function($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $status = $request->payment_status;
            if ($status === 'verified') {
                $query->whereHas('jobOrder.payment', function($q) {
                    $q->where('status', 'verified');
                });
            } elseif ($status === 'unverified') {
                $query->where(function($q) {
                    $q->whereDoesntHave('jobOrder.payment')
                        ->orWhereHas('jobOrder.payment', function($p) {
                            $p->where('status', '!=', 'verified');
                        });
                });
            }
        }

        $certificates = $query->latest('generated_at')->paginate(20);

        return view('accounting.certificates-for-release', compact('certificates'));
    }

    /**
     * Release Certificate
     */
    public function releaseCertificate(Request $request, Certificate $certificate)
    {
        // Check if payment is verified
        if (!$certificate->jobOrder->payment || $certificate->jobOrder->payment->status !== 'verified') {
            return back()->withErrors(['error' => 'Payment must be verified before releasing certificate.']);
        }

        $validated = $request->validate([
            'released_to' => 'required|string|max:255',
            'delivery_method' => 'required|in:pickup,courier,email,hand_delivery',
            'release_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update certificate
            $certificate->update([
                'status' => 'released',
                'released_at' => now(),
                'released_to' => $validated['released_to'],
                'delivery_method' => $validated['delivery_method'],
                'release_notes' => $validated['release_notes'] ?? null,
            ]);

            // Create release log
            CertificateRelease::create([
                'certificate_id' => $certificate->id,
                'released_by' => auth()->id(),
                'released_at' => now(),
                'released_to' => $validated['released_to'],
                'delivery_method' => $validated['delivery_method'],
                'notes' => $validated['release_notes'] ?? null,
            ]);

            DB::commit();

            // Audit logging
            AuditLogHelper::log(
                'RELEASE',
                'Certificate',
                $certificate->id,
                "Released certificate {$certificate->certificate_number} to {$validated['released_to']} via {$validated['delivery_method']}"
            );

            return redirect()->route('accounting.certificates.for-release')
                ->with('status', 'Certificate ' . $certificate->certificate_number . ' released successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to release certificate: ' . $e->getMessage()]);
        }
    }

    /**
     * Hold/Unhold Certificate
     */
    public function holdCertificate(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'hold_reason' => 'required_if:action,hold|nullable|string',
            'action' => 'required|in:hold,unhold',
        ]);

        $isHolding = $validated['action'] === 'hold';

        $certificate->update([
            'is_on_hold' => $isHolding,
            'hold_reason' => $isHolding ? $validated['hold_reason'] : null,
            'held_by' => $isHolding ? auth()->id() : null,
            'held_at' => $isHolding ? now() : null,
        ]);

        $message = $isHolding 
            ? 'Certificate placed on hold' 
            : 'Certificate hold removed';

        // Audit logging
        AuditLogHelper::log(
            $isHolding ? 'HOLD' : 'UNHOLD',
            'Certificate',
            $certificate->id,
            $isHolding 
                ? "Placed hold on certificate {$certificate->certificate_number}: {$validated['hold_reason']}" 
                : "Removed hold from certificate {$certificate->certificate_number}"
        );

        return redirect()->route('accounting.certificates.for-release')
            ->with('status', $message);
    }

    /**
     * Released Certificates (History)
     */
    public function releasedCertificates(Request $request)
    {
        $query = Certificate::with(['jobOrder.customer', 'releasedBy'])
            ->where('status', 'released')
            ->whereNotNull('released_at');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('released_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('released_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhereHas('jobOrder', function($jo) use ($search) {
                        $jo->where('job_order_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jobOrder.customer', function($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $certificates = $query->latest('released_at')->paginate(20);

        return view('accounting.certificates-released', compact('certificates'));
    }

    /**
     * All Timelines - List view for accounting
     */
    public function allTimelines(Request $request)
    {
        $query = JobOrder::with(['customer', 'payment', 'certificates']);
        
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
        
        return view('accounting.timelines', compact('jobOrders'));
    }

    /**
     * Timeline - Job Order Workflow (Read-only for Accounting)
     */
    public function timeline(JobOrder $jobOrder, Request $request)
    {
        $jobOrder->load([
            'calibrations.performedBy',
            'calibrations.signatory', 
            'calibrations.technicalReviewer',
            'customer', 
            'assignments.assignedTo',
            'creator',
            'payment',
            'certificates'
        ]);

        // Build timeline from actual events
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
            if ($assignment->scheduled_date) {
                $timeline->push([
                    'event' => 'Technician Assigned',
                    'description' => 'Assigned to ' . ($assignment->assignedTo->name ?? 'N/A'),
                    'created_at' => $assignment->scheduled_date,
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
        
        // Payment events (accounting-specific)
        if ($jobOrder->payment) {
            $timeline->push([
                'event' => 'Payment Recorded',
                'description' => 'Payment of ₱' . number_format($jobOrder->payment->amount_paid, 2) . ' recorded',
                'created_at' => $jobOrder->payment->paid_at ?? $jobOrder->payment->created_at,
                'created_by' => null,
                'status' => $jobOrder->payment->status === 'verified' ? 'success' : 'warning'
            ]);
            
            if ($jobOrder->payment->status === 'verified') {
                $timeline->push([
                    'event' => 'Payment Verified',
                    'description' => 'Payment verified by accounting',
                    'created_at' => $jobOrder->payment->verified_at,
                    'created_by' => $jobOrder->payment->verifiedBy,
                    'status' => 'success'
                ]);
            }
        }
        
        // Certificate events
        foreach ($jobOrder->certificates as $certificate) {
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

        // Stats for accounting view
        $stats = [
            'payment_status' => $jobOrder->payment ? $jobOrder->payment->status : 'unpaid',
            'payment_amount' => $jobOrder->payment ? $jobOrder->payment->amount_paid : 0,
            'certificates_generated' => $jobOrder->certificates->count(),
            'certificates_released' => $jobOrder->certificates->where('released_at', '!=', null)->count(),
        ];

        return view('accounting.timeline', compact('jobOrder', 'timeline', 'stats'));
    }

    /**
     * Reports Export
     */
    public function reports(Request $request)
    {
        $reportType = $request->input('report_type', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Payments (verified)
        $paymentsQuery = Payment::with(['jobOrder.customer'])
            ->where('status', 'verified');

        if ($startDate) {
            $paymentsQuery->whereDate('verified_at', '>=', $startDate);
        }
        if ($endDate) {
            $paymentsQuery->whereDate('verified_at', '<=', $endDate);
        }

        $payments = $paymentsQuery
            ->orderByDesc('verified_at')
            ->limit(50)
            ->get();

        // Released certificates
        $releasesQuery = Certificate::with(['jobOrder.customer'])
            ->whereNotNull('released_at');

        if ($startDate) {
            $releasesQuery->whereDate('released_at', '>=', $startDate);
        }
        if ($endDate) {
            $releasesQuery->whereDate('released_at', '<=', $endDate);
        }

        $releasedCertificates = $releasesQuery
            ->orderByDesc('released_at')
            ->limit(50)
            ->get();

        // Monthly summary (last 6 months)
        $monthlySummary = Payment::selectRaw('DATE_FORMAT(verified_at, "%Y-%m") as period, COUNT(*) as total_payments, SUM(amount_paid) as total_amount')
            ->where('status', 'verified')
            ->whereNotNull('verified_at')
            ->when($startDate, fn ($q) => $q->whereDate('verified_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('verified_at', '<=', $endDate))
            ->groupBy('period')
            ->orderByDesc('period')
            ->limit(6)
            ->get();

        return view('accounting.reports', compact(
            'reportType',
            'startDate',
            'endDate',
            'payments',
            'releasedCertificates',
            'monthlySummary'
        ));
    }

    public function exportReports(Request $request)
    {
        // Implement CSV/Excel export logic here
        return back()->with('status', 'Export feature coming soon');
    }
}
