<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimelineController extends Controller
{
    /**
     * Display timeline for the authenticated user based on their role
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->role?->slug ?? 'guest';
        $filters = [
            'status' => $request->string('status')->toString(),
            'search' => $request->string('search')->toString(),
        ];
        
        // Get timeline data based on role
        $timelineData = $this->getTimelineDataByRole($role, $filters);
        
        // Get view based on role
        $view = $this->getViewByRole($role);
        
        return view($view, [
            'timelines' => $timelineData['timelines'],
            'stats' => $timelineData['stats'],
            'filters' => $filters
        ]);
    }
    
    /**
     * Get timeline data based on user role
     */
    private function getTimelineDataByRole(string $role, array $filters = []): array
    {
        return match($role) {
            'marketing' => $this->getMarketingTimeline($filters),
            'tech_personnel' => $this->getTechnicianTimeline($filters),
            'tech_head' => $this->getTechHeadTimeline($filters),
            'signatory' => $this->getSignatoryTimeline($filters),
            'accounting' => $this->getAccountingTimeline($filters),
            'admin' => $this->getAdminTimeline($filters),
            default => $this->getDefaultTimeline()
        };
    }
    
    /**
     * Marketing Timeline: Job Orders creation and customer activities
     */
    private function getMarketingTimeline(array $filters = []): array
    {
        $status = $filters['status'] ?? null;
        $search = $filters['search'] ?? null;

        $query = JobOrder::with(['customer'])
            ->select('job_orders.*');

        if (in_array($status, ['pending', 'in_progress', 'completed'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('job_order_number', 'like', "%{$search}%")
                    ->orWhere('service_description', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $timelines = $query
            ->latest('created_at')
            ->limit(50)
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'type' => 'job_order',
                    'title' => "Job Order #{$job->job_order_number}",
                    'description' => $job->service_description,
                    'customer' => $job->customer?->name ?? 'N/A',
                    'status' => $job->status,
                    'priority' => $job->priority_level ?? 'normal',
                    'date' => $job->created_at,
                    'metadata' => [
                        'service_type' => $job->service_type,
                        'expected_start' => $job->expected_start_date,
                        'expected_completion' => $job->expected_completion_date,
                        'grand_total' => $job->grand_total
                    ]
                ];
            });
        
        $stats = [
            'total_jobs' => JobOrder::count(),
            'pending' => JobOrder::where('status', 'pending')->count(),
            'in_progress' => JobOrder::where('status', 'in_progress')->count(),
            'completed' => JobOrder::where('status', 'completed')->count(),
        ];
        
        return ['timelines' => $timelines, 'stats' => $stats];
    }
    
    /**
     * Technician Timeline: Work assignments and maintenance tasks
     */
    private function getTechnicianTimeline(array $filters = []): array
    {
        $status = $filters['status'] ?? null;
        $search = $filters['search'] ?? null;

        $query = JobOrder::with(['customer'])
            ->whereIn('status', ['pending', 'in_progress', 'completed']);

        if (in_array($status, ['pending', 'in_progress', 'completed'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('job_order_number', 'like', "%{$search}%")
                    ->orWhere('service_description', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $timelines = $query
            ->latest('created_at')
            ->limit(50)
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'type' => 'work_assignment',
                    'title' => "Work Order #{$job->job_order_number}",
                    'description' => $job->service_description,
                    'customer' => $job->customer?->name ?? 'N/A',
                    'status' => $job->status,
                    'priority' => $job->priority_level ?? 'normal',
                    'date' => $job->created_at,
                    'metadata' => [
                        'service_type' => $job->service_type,
                        'expected_start' => $job->expected_start_date,
                        'location' => $job->service_address
                    ]
                ];
            });
        
        $stats = [
            'today_tasks' => JobOrder::whereDate('created_at', today())->count(),
            'pending' => JobOrder::where('status', 'pending')->count(),
            'in_progress' => JobOrder::where('status', 'in_progress')->count(),
            'completed_today' => JobOrder::where('status', 'completed')->whereDate('updated_at', today())->count(),
        ];
        
        return ['timelines' => $timelines, 'stats' => $stats];
    }
    
    /**
     * Tech Head Timeline: Team oversight and approvals
     */
    private function getTechHeadTimeline(array $filters = []): array
    {
        $timelines = JobOrder::with(['customer'])
            ->latest('created_at')
            ->limit(50)
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'type' => 'oversight',
                    'title' => "JO #{$job->job_order_number} - {$job->status}",
                    'description' => $job->service_description,
                    'customer' => $job->customer?->name ?? 'N/A',
                    'status' => $job->status,
                    'priority' => $job->priority_level ?? 'normal',
                    'date' => $job->updated_at,
                    'metadata' => [
                        'service_type' => $job->service_type,
                        'requires_approval' => in_array($job->status, ['pending', 'in_progress'])
                    ]
                ];
            });
        
        $stats = [
            'total_active' => JobOrder::whereIn('status', ['pending', 'in_progress'])->count(),
            'pending_approval' => JobOrder::where('status', 'pending')->count(),
            'in_progress' => JobOrder::where('status', 'in_progress')->count(),
            'completed' => JobOrder::where('status', 'completed')->count(),
        ];
        
        return ['timelines' => $timelines, 'stats' => $stats];
    }
    
    /**
     * Signatory Timeline: Documents requiring review/approval
     */
    private function getSignatoryTimeline(array $filters = []): array
    {
        $timelines = JobOrder::with(['customer'])
            ->where('status', 'completed')
            ->latest('updated_at')
            ->limit(50)
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'type' => 'approval',
                    'title' => "Certificate for JO #{$job->job_order_number}",
                    'description' => "Requires signature approval",
                    'customer' => $job->customer?->name ?? 'N/A',
                    'status' => 'pending_signature',
                    'priority' => $job->priority_level ?? 'normal',
                    'date' => $job->updated_at,
                    'metadata' => [
                        'service_type' => $job->service_type,
                        'completion_date' => $job->updated_at
                    ]
                ];
            });
        
        $stats = [
            'pending_signature' => JobOrder::where('status', 'completed')->count(),
            'signed_today' => 0, // Placeholder until signature system is implemented
            'total_signed' => 0,
        ];
        
        return ['timelines' => $timelines, 'stats' => $stats];
    }
    
    /**
     * Accounting Timeline: Financial transactions and billing
     */
    private function getAccountingTimeline(array $filters = []): array
    {
        $status = $filters['status'] ?? null;
        $search = $filters['search'] ?? null;

        $query = JobOrder::with(['customer', 'creator']);

        if ($status === 'pending') {
            $query->whereIn('status', ['for_accounting_approval', 'pending', 'rejected']);
        } elseif ($status === 'in_progress') {
            $query->whereIn('status', ['approved', 'assigned', 'in_progress', 'on_hold']);
        } elseif ($status === 'completed') {
            $query->where('status', 'completed');
        }

        if ($search) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('job_order_number', 'like', "%{$search}%")
                    ->orWhere('service_description', 'like', "%{$search}%")
                    ->orWhere('service_type', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $timelines = $query
            ->latest('updated_at')
            ->limit(50)
            ->get()
            ->map(function ($job) {
                $normalizedStatus = match (true) {
                    in_array($job->status, ['completed']) => 'completed',
                    in_array($job->status, ['approved', 'assigned', 'in_progress', 'on_hold']) => 'in_progress',
                    default => 'pending',
                };

                $title = match (true) {
                    $job->status === 'for_accounting_approval' => "New Request: JO #{$job->job_order_number}",
                    $normalizedStatus === 'completed' => "Completed JO #{$job->job_order_number}",
                    $normalizedStatus === 'in_progress' => "Ongoing JO #{$job->job_order_number}",
                    default => "Job Order #{$job->job_order_number}",
                };

                return [
                    'id' => $job->id,
                    'type' => 'accounting_workflow',
                    'title' => $title,
                    'description' => $job->service_description ?: ($job->service_type ?: 'Job order update'),
                    'customer' => $job->customer?->name ?? 'N/A',
                    'status' => $normalizedStatus,
                    'priority' => $job->priority_level ?? 'normal',
                    'date' => $job->updated_at,
                    'metadata' => [
                        'service_type' => $job->service_type,
                        'workflow_status' => $job->status,
                        'submitted_by' => $job->creator?->name,
                    ]
                ];
            });

        $stats = [
            'total_jobs' => JobOrder::count(),
            'pending' => JobOrder::whereIn('status', ['for_accounting_approval', 'pending', 'rejected'])->count(),
            'in_progress' => JobOrder::whereIn('status', ['approved', 'assigned', 'in_progress', 'on_hold'])->count(),
            'completed' => JobOrder::where('status', 'completed')->count(),
        ];
        
        return ['timelines' => $timelines, 'stats' => $stats];
    }
    
    /**
     * Admin Timeline: System-wide activities
     */
    private function getAdminTimeline(array $filters = []): array
    {
        $timelines = JobOrder::with(['customer'])
            ->latest('created_at')
            ->limit(100)
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'type' => 'system',
                    'title' => "JO #{$job->job_order_number} - {$job->status}",
                    'description' => $job->service_description,
                    'customer' => $job->customer?->name ?? 'N/A',
                    'status' => $job->status,
                    'priority' => $job->priority_level ?? 'normal',
                    'date' => $job->updated_at,
                    'metadata' => [
                        'service_type' => $job->service_type,
                        'grand_total' => $job->grand_total,
                        'created_at' => $job->created_at
                    ]
                ];
            });
        
        $stats = [
            'total_jobs' => JobOrder::count(),
            'pending' => JobOrder::where('status', 'pending')->count(),
            'in_progress' => JobOrder::where('status', 'in_progress')->count(),
            'completed' => JobOrder::where('status', 'completed')->count(),
        ];
        
        return ['timelines' => $timelines, 'stats' => $stats];
    }
    
    /**
     * Default timeline for users without specific roles
     */
    private function getDefaultTimeline(): array
    {
        return [
            'timelines' => collect([]),
            'stats' => []
        ];
    }
    
    /**
     * Get the appropriate view based on role
     */
    private function getViewByRole(string $role): string
    {
        return match($role) {
            'marketing' => 'marketing.timeline',
            'tech_personnel' => 'technician.timeline',
            'tech_head' => 'tech-head.timeline',
            'signatory' => 'signatory.timeline',
            'accounting' => 'accounting.timeline',
            'admin' => 'admin.timeline',
            default => 'timeline'
        };
    }
}
