<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\JobOrder;
use App\Models\JobOrderAttachment;
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
            'pagination' => $timelineData['pagination'] ?? null,
            'stats' => $timelineData['stats'],
            'filters' => $filters,
            'pendingCount' => $timelineData['pendingCount'] ?? 0
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
     * Format audit log entry to timeline format with color coordination
     */
    private function formatAuditLogToTimeline($auditLog)
    {
        $user = $auditLog->user;
        $userName = $user?->name ?? 'Unknown User';
        $userRole = $user?->role?->name ?? 'Unknown Role';
        $userDept = $user?->department ?? '';
        $modelType = $auditLog->model_type ?? 'Unknown';
        $modelId = $auditLog->model_id ?? 0;
        
        // Initialize customer name and job order reference
        $customerName = 'N/A';
        $joNumber = '';
        $jobStatus = 'pending';
        $jobOrder = null;
        
        // Resolve the job order for direct or related audit log records.
        $jobOrder = $this->resolveJobOrderFromAuditLog($auditLog);
        if ($jobOrder) {
            $joNumber = $jobOrder->job_order_number;
            $customerName = $jobOrder->customer?->name ?? 'Unknown Customer';
            $jobStatus = $jobOrder->status ?? 'pending';
        }
        
        // Map action to human-readable format
        $actionMap = [
            'CREATE' => 'created',
            'UPDATE' => 'updated',
            'DELETE' => 'deleted',
        ];
        
        $action = $actionMap[$auditLog->action] ?? strtolower($auditLog->action);
        
        // Build description with audit log format
        $description = $auditLog->description;
        if (!$description) {
            $description = "{$userRole} {$userName}";
            if ($userDept) {
                $description .= " ({$userDept})";
            }
            $description .= " {$action} ";
            
            if ($modelType === 'JobOrder' && $joNumber) {
                $description .= "request JO-{$joNumber}";
            } else {
                $description .= "{$modelType}";
            }
        }
        
        // Determine status and type from action
        $status = 'pending';
        $type = 'system';
        
        if ($jobOrder) {
            $type = 'job_order';
            $status = $jobStatus;
        }
        
        // Determine color based on action type
        $actionColor = $this->getActionColor($auditLog->action, $modelType, $jobStatus);
        
        // Extract JO number for title
        $title = "{$userName} - {$userRole}";
        if ($joNumber) {
            $title = "JO-{$joNumber}";
        }
        
        return [
            'id' => $auditLog->id,
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'customer' => $customerName,
            'status' => $status,
            'priority' => 'normal',
            'date' => $auditLog->created_at,
            'action_color' => $actionColor,
            'job_order' => $jobOrder,
            'metadata' => [
                'user_name' => $userName,
                'user_role' => $userRole,
                'user_dept' => $userDept,
                'action' => $auditLog->action,
                'model_type' => $modelType,
                'model_id' => $modelId,
            ]
        ];
    }

    /**
     * Resolve the related job order for an audit log record.
     */
    private function resolveJobOrderFromAuditLog(AuditLog $auditLog): ?JobOrder
    {
        $modelType = $auditLog->model_type ?? '';
        $modelId = $auditLog->model_id ?? 0;

        if ($modelType === 'JobOrder' && $modelId) {
            return JobOrder::find($modelId);
        }

        if ($modelType === 'Assignment' && $modelId) {
            $assignment = \App\Models\Assignment::with('jobOrder')->find($modelId);
            if ($assignment?->jobOrder) {
                return $assignment->jobOrder;
            }
        }

        if ($modelType === 'Calibration' && $modelId) {
            $calibration = \App\Models\Calibration::with('assignment.jobOrder')->find($modelId);
            if ($calibration?->assignment?->jobOrder) {
                return $calibration->assignment->jobOrder;
            }
        }

        if ($modelType === 'Report' && $modelId) {
            $report = \App\Models\Report::with('assignment.jobOrder')->find($modelId);
            if ($report?->assignment?->jobOrder) {
                return $report->assignment->jobOrder;
            }
        }

        if ($modelType === 'Certificate' && $modelId) {
            $certificate = \App\Models\Certificate::with('jobOrder')->find($modelId);
            if ($certificate?->jobOrder) {
                return $certificate->jobOrder;
            }
        }

        if (in_array($modelType, ['JobAttachment', 'JobOrderAttachment'], true)) {
            $jobOrderId = data_get($auditLog->new_values, 'job_order_id')
                ?? data_get($auditLog->old_values, 'job_order_id');

            if (! $jobOrderId && $modelId) {
                $jobOrderId = JobOrderAttachment::find($modelId)?->job_order_id;
            }

            return $jobOrderId ? JobOrder::find($jobOrderId) : null;
        }

        // Fallback: extract JO number from description (e.g., JO-00018).
        $description = (string) ($auditLog->description ?? '');
        if ($description !== '' && preg_match('/\b(JO-[A-Za-z0-9-]+)\b/i', $description, $matches)) {
            $joNumber = strtoupper($matches[1]);

            return JobOrder::whereRaw('UPPER(job_order_number) = ?', [$joNumber])->first();
        }

        return null;
    }

    /**
     * Get color code based on action type and model type
     */
    private function getActionColor(string $action, string $modelType, string $jobStatus = 'pending'): string
    {
        // Color mapping based on action
        if ($action === 'CREATE') {
            return 'create'; // Green - new creation
        } elseif ($action === 'DELETE') {
            return 'delete'; // Red - deletion
        } elseif ($action === 'UPDATE') {
            // Further differentiation based on model type
            if ($modelType === 'JobOrder') {
                if ($jobStatus === 'completed') {
                    return 'completed'; // Green - job completed
                } elseif ($jobStatus === 'in_progress') {
                    return 'in_progress'; // Blue - job in progress
                } elseif ($jobStatus === 'pending') {
                    return 'pending'; // Yellow - pending
                }
            } elseif ($modelType === 'Certificate') {
                return 'approved'; // Purple - approval/certification
            } elseif ($modelType === 'Payment') {
                return 'payment'; // Green - payment processed
            }
            return 'update'; // Orange - general update
        }
        
        return 'default'; // Gray
    }
    
    /**
     * Marketing Timeline: Customer requests and JO creation activities
     */
    private function getMarketingTimeline(array $filters = []): array
    {
        $search = $filters['search'] ?? null;

        // Get audit logs for customer request activities (JobOrder creation, updates)
        $query = AuditLog::with(['user' => function($q) { $q->with('role'); }])
            ->where('model_type', 'JobOrder')
            ->whereNotIn('description', ['User logged in', 'User logged out']);

        if ($search) {
            // Search by JO number through related JobOrder
            if (is_numeric($search)) {
                $jobOrders = JobOrder::where('job_order_number', 'like', "%{$search}%")
                    ->pluck('id');
                if ($jobOrders->isNotEmpty()) {
                    $query->whereIn('model_id', $jobOrders);
                } else {
                    $query->where('description', 'like', "%{$search}%");
                }
            } else {
                $query->where('description', 'like', "%{$search}%");
            }
        }

        $auditLogs = $query
            ->latest('created_at')
            ->get();

        $timelines = $auditLogs->map(function ($auditLog) {
            return $this->formatAuditLogToTimeline($auditLog);
        });
        
        $stats = [
            'total_jobs' => JobOrder::count(),
            'pending' => JobOrder::where('status', 'pending')->count(),
            'in_progress' => JobOrder::where('status', 'in_progress')->count(),
            'completed' => JobOrder::where('status', 'completed')->count(),
        ];
        
        return [
            'timelines' => $timelines,
            'pagination' => null,
            'stats' => $stats,
            'pendingCount' => $stats['pending'] ?? 0
        ];
    }
    
    /**
     * Technician Timeline: Work assignments and technical activities
     */
    private function getTechnicianTimeline(array $filters = []): array
    {
        $search = $filters['search'] ?? null;

        // Get audit logs for technical activities - only job/assignment related
        $query = AuditLog::with(['user' => function($q) { $q->with('role'); }])
            ->whereIn('model_type', ['JobOrder', 'Assignment', 'Calibration', 'Equipment', 'JobAttachment', 'JobOrderAttachment'])
            ->whereNotIn('description', ['User logged in', 'User logged out']);

        if ($search) {
            if (is_numeric($search)) {
                $jobOrders = JobOrder::where('job_order_number', 'like', "%{$search}%")
                    ->pluck('id');
                if ($jobOrders->isNotEmpty()) {
                    $query->wherein('model_id', $jobOrders);
                } else {
                    $query->where('description', 'like', "%{$search}%");
                }
            } else {
                $query->where('description', 'like', "%{$search}%");
            }
        }

        $auditLogs = $query
            ->latest('created_at')
            ->get();

        $timelines = $auditLogs->map(function ($auditLog) {
            return $this->formatAuditLogToTimeline($auditLog);
        });
        
        $stats = [
            'today_tasks' => AuditLog::whereDate('created_at', today())
                ->whereIn('model_type', ['JobOrder', 'Assignment', 'JobAttachment', 'JobOrderAttachment'])
                ->whereNotIn('description', ['User logged in', 'User logged out'])
                ->count(),
            'pending' => JobOrder::where('status', 'pending')->count(),
            'in_progress' => JobOrder::where('status', 'in_progress')->count(),
            'completed_today' => JobOrder::where('status', 'completed')
                ->whereDate('updated_at', today())
                ->count(),
        ];
        
        return [
            'timelines' => $timelines,
            'pagination' => null,
            'stats' => $stats,
            'pendingCount' => $stats['pending'] ?? 0
        ];
    }
    
    /**
     * Tech Head Timeline: Team oversight and approval activities
     */
    private function getTechHeadTimeline(array $filters = []): array
    {
        $search = $filters['search'] ?? null;

        // Tech Head should only see job lifecycle activities (assignment, execution, approvals, reports).
        $query = AuditLog::with(['user' => function($q) { $q->with('role'); }])
            ->whereIn('action', ['CREATE', 'UPDATE', 'DELETE', 'ASSIGN', 'APPROVE', 'REJECT', 'GENERATE', 'SUBMIT'])
            ->whereNotIn('model_type', ['Backup', 'Cache', 'Settings', 'AuditLog'])
            ->where(function ($q) {
                $q->whereIn('model_type', [
                    'JobOrder',
                    'Assignment',
                    'Calibration',
                    'Report',
                    'Invoice',
                    'Payment',
                    'AccountingRelease',
                    'SignatoryApproval',
                    'JobAttachment',
                    'JobOrderAttachment',
                ])
                // Some legacy records are saved as Unknown but still include JO references.
                ->orWhere('description', 'like', '%JO-%')
                ->orWhere('description', 'like', '%Job Order%');
            })
            ->whereNotIn('description', ['User logged in', 'User logged out']);

        if ($search) {
            if (is_numeric($search)) {
                $jobOrders = JobOrder::where('job_order_number', 'like', "%{$search}%")
                    ->pluck('id');
                if ($jobOrders->isNotEmpty()) {
                    $query->wherein('model_id', $jobOrders);
                } else {
                    $query->where('description', 'like', "%{$search}%");
                }
            } else {
                $query->where('description', 'like', "%{$search}%");
            }
        }

        $auditLogs = $query
            ->latest('created_at')
            ->get();

        $timelines = $auditLogs
            ->map(function ($auditLog) {
                return $this->formatAuditLogToTimeline($auditLog);
            })
            // Final guard: keep only entries tied to a concrete Job Order context.
            ->filter(function ($timeline) {
                return !empty($timeline['job_order'])
                    || str_starts_with((string) ($timeline['title'] ?? ''), 'JO-')
                    || str_contains((string) ($timeline['description'] ?? ''), 'Job Order')
                    || str_contains((string) ($timeline['description'] ?? ''), 'JO-');
            })
            ->values();
        
        $stats = [
            'total_active' => JobOrder::whereIn('status', ['pending', 'in_progress'])->count(),
            'pending_approval' => JobOrder::where('status', 'pending')->count(),
            'in_progress' => JobOrder::where('status', 'in_progress')->count(),
            'completed' => JobOrder::where('status', 'completed')->count(),
        ];
        
        return [
            'timelines' => $timelines,
            'pagination' => null,
            'stats' => $stats
        ];
    }
    
    /**
     * Signatory Timeline: Document signature and approval activities
     */
    private function getSignatoryTimeline(array $filters = []): array
    {
        // Keep signatory timeline behavior aligned with tech head/accounting timeline behavior.
        return $this->getTechHeadTimeline($filters);
    }
    
    /**
     * Accounting Timeline: Financial transactions and billing activities
     */
    private function getAccountingTimeline(array $filters = []): array
    {
        // Keep accounting timeline behavior aligned with tech head timeline behavior.
        return $this->getTechHeadTimeline($filters);
    }
    
    /**
     * Admin Timeline: System-wide activities and all department actions
     */
    private function getAdminTimeline(array $filters = []): array
    {
        $search = $filters['search'] ?? null;

        // Get all audit logs for system-wide view - only job-related activities
        $query = AuditLog::with(['user' => function($q) { $q->with('role'); }])
            ->whereIn('model_type', ['JobOrder', 'Assignment', 'Calibration', 'Certificate', 'Payment', 'Invoice', 'Equipment', 'SignatoryApproval', 'AccountingRelease', 'JobAttachment', 'JobOrderAttachment'])
            ->whereNotIn('description', ['User logged in', 'User logged out']);

        if ($search) {
            if (is_numeric($search)) {
                $jobOrders = JobOrder::where('job_order_number', 'like', "%{$search}%")
                    ->pluck('id');
                if ($jobOrders->isNotEmpty()) {
                    $query->wherein('model_id', $jobOrders);
                } else {
                    $query->where('description', 'like', "%{$search}%");
                }
            } else {
                $query->where('description', 'like', "%{$search}%");
            }
        }

        $auditLogs = $query
            ->latest('created_at')
            ->get();

        $timelines = $auditLogs->map(function ($auditLog) {
            return $this->formatAuditLogToTimeline($auditLog);
        });
        
        $stats = [
            'total_jobs' => JobOrder::count(),
            'pending' => JobOrder::where('status', 'pending')->count(),
            'in_progress' => JobOrder::where('status', 'in_progress')->count(),
            'completed' => JobOrder::where('status', 'completed')->count(),
        ];
        
        return [
            'timelines' => $timelines,
            'pagination' => null,
            'stats' => $stats
        ];
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
     * API endpoint: Get job audit trail as JSON for modal popup
     */
    public function getJobAuditTrailJson(JobOrder $jobOrder)
    {
        $attachmentIds = JobOrderAttachment::where('job_order_id', $jobOrder->id)->pluck('id');

        // Get all activities related to this job order
        $allActivities = AuditLog::with(['user' => function($q) { $q->with('role'); }])
            ->where(function($query) use ($jobOrder) {
                // Direct job order activities
                $query->where(function($q) use ($jobOrder) {
                    $q->where('model_type', 'JobOrder')
                      ->where('model_id', $jobOrder->id);
                });
                
                // Activities related to this job (assignments, calculations, certificates, payments)
                $query->orWhere(function($q) use ($jobOrder) {
                    $q->whereIn('model_type', ['Assignment', 'Calibration', 'Report', 'Certificate', 'Payment', 'Invoice', 'AccountingRelease', 'SignatoryApproval'])
                      ->where('model_id', '>', 0);
                });
            })
            ->where(function ($query) use ($attachmentIds) {
                if ($attachmentIds->isNotEmpty()) {
                    $query->where(function ($q) use ($attachmentIds) {
                        $q->whereIn('model_type', ['JobAttachment', 'JobOrderAttachment'])
                          ->whereIn('model_id', $attachmentIds);
                    })->orWhere(function ($q) {
                        $q->whereNotIn('model_type', ['JobAttachment', 'JobOrderAttachment']);
                    });
                } else {
                    $query->whereNotIn('model_type', ['JobAttachment', 'JobOrderAttachment']);
                }
            })
            ->whereNotIn('description', ['User logged in', 'User logged out'])
            ->latest('created_at')
            ->get()
            ->filter(function (AuditLog $auditLog) use ($jobOrder) {
                $resolvedJobOrder = $this->resolveJobOrderFromAuditLog($auditLog);

                return $resolvedJobOrder && (int) $resolvedJobOrder->id === (int) $jobOrder->id;
            })
            ->values();

        // Format each activity
        $formattedActivities = $allActivities->map(function ($auditLog) use ($jobOrder) {
            $formatted = $this->formatAuditLogToTimeline($auditLog);
            $formatted['job_order'] = $jobOrder;
            return $formatted;
        });

        // Get stats for this specific job
        $stats = [
            'customer' => $jobOrder->customer?->name ?? 'N/A',
            'total_activities' => $formattedActivities->count(),
            'current_status' => $jobOrder->status,
            'started_date' => AuditLog::where('model_type', 'JobOrder')
                ->where('model_id', $jobOrder->id)
                ->where('action', 'CREATE')
                ->first()?->created_at,
            'last_activity' => AuditLog::where('model_type', 'JobOrder')
                ->where('model_id', $jobOrder->id)
                ->latest('created_at')
                ->first()?->created_at,
        ];

        return response()->json([
            'jobOrder' => $jobOrder,
            'activities' => $formattedActivities,
            'stats' => $stats
        ]);
    }

    /**
     * Show complete audit trail for a specific job order
     * Shows all activities from all departments for that job
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
