<?php

use App\Http\Controllers\{AdminController, ApprovalController, AuditLogController, CalibrationController, EquipmentController, InventoryController, ProfileController, ReportController, RoleController, SettingsController, SignatoryController, TimelineController, VerificationController};
use App\Models\{Assignment, Calibration, Certificate, Customer, Equipment, JobOrder, Report, Role, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public Certificate Verification
Route::prefix('verify')->name('verification.')->group(function () {
    // Search page (public)
    Route::get('/', [VerificationController::class, 'verify'])->name('verify');
    // Show certificate verification details
    Route::get('/certificate/{certificateNumber}', [VerificationController::class, 'show'])->name('show');
    // Lightweight status endpoint (JSON)
    Route::get('/status/{certificateNumber}', [VerificationController::class, 'getStatus'])->name('status');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Redirect based on role
    if ($user->role) {
        switch ($user->role->slug) {
            case 'marketing':
                return redirect()->route('marketing.dashboard');
            case 'tech_personnel':
                return redirect()->route('technician.dashboard');
            case 'tech_head':
                return redirect()->route('tech-head.dashboard');
            case 'signatory':
                return redirect()->route('signatory.dashboard');
            case 'accounting':
                return redirect()->route('accounting.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
        }
    }
    
    $stats = [
        'jobOrders' => JobOrder::count(),
        'customers' => Customer::count(),
        'users' => User::count(),
    ];

    return view('dashboard', compact('stats'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Marketing Routes
Route::middleware(['auth', 'verified', 'role:marketing'])->prefix('marketing')->name('marketing.')->group(function () {
    Route::get('/dashboard', function () {
        $totalJobOrders = \App\Models\JobOrder::count();
        $pendingJobOrders = \App\Models\JobOrder::where('status', 'pending')->count();
        $inProgressJobOrders = \App\Models\JobOrder::where('status', 'in_progress')->count();
        $completedJobOrders = \App\Models\JobOrder::where('status', 'completed')->count();
        $recentJobOrders = \App\Models\JobOrder::with('customer')
            ->latest()
            ->take(3)
            ->get();
        
        return view('marketing.dashboard', compact(
            'totalJobOrders',
            'pendingJobOrders',
            'inProgressJobOrders',
            'completedJobOrders',
            'recentJobOrders'
        ));
    })->name('dashboard');
    
    Route::get('/job-orders', function () {
        $query = \App\Models\JobOrder::with('customer')->latest();
        
        // Filter by status if provided
        if (request()->has('status') && request('status') != '') {
            $query->where('status', request('status'));
        }
        
        $jobOrders = $query->paginate(10);
        return view('marketing.job-orders', compact('jobOrders'));
    })->name('job-orders');
    
    Route::get('/create-job-order', function () {
        $customers = \App\Models\Customer::where('is_active', true)->get();
        return view('marketing.create-job-order', compact('customers'));
    })->name('create-job-order');

    // Customer details page
    Route::get('/customers/{customer}', function (\App\Models\Customer $customer) {
        $customer->loadCount('jobOrders');
        return view('marketing.customer-details', compact('customer'));
    })->name('customers.show');
    
    Route::post('/job-orders', function (Illuminate\Http\Request $request) {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'service_type' => 'required|string',
                'priority_level' => 'required|string',
                'service_description' => 'required|string',
                'expected_start_date' => 'nullable|date',
                'expected_completion_date' => 'nullable|date|after_or_equal:expected_start_date',
                'service_address' => 'required|string',
                'city' => 'nullable|string|max:100',
                'province' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'contact_person' => 'nullable|string|max:255',
            ]);

            // Get the customer
            $customer = \App\Models\Customer::findOrFail($validated['customer_id']);

            // Generate job order number
            $lastJobOrder = \App\Models\JobOrder::latest('id')->first();
            $nextNumber = $lastJobOrder ? (intval(substr($lastJobOrder->job_order_number, 3)) + 1) : 1;
            $jobOrderNumber = 'JO-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Map priority level
            $priorityMap = [
                'Normal' => 'normal',
                'High' => 'high',
                'Urgent' => 'urgent'
            ];
            $priority = $priorityMap[$validated['priority_level']] ?? 'normal';

            // Create job order
            $jobOrder = \App\Models\JobOrder::create([
                'job_order_number' => $jobOrderNumber,
                'customer_id' => $customer->id,
                'service_type' => $validated['service_type'],
                'service_description' => $validated['service_description'],
                'expected_start_date' => $validated['expected_start_date'] ?? null,
                'expected_completion_date' => $validated['expected_completion_date'] ?? null,
                'service_address' => $validated['service_address'],
                'city' => $validated['city'] ?? null,
                'province' => $validated['province'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'requested_by' => $validated['contact_person'] ?? $customer->name,
                'request_date' => now(),
                'required_date' => $validated['expected_completion_date'] ?? null,
                'priority' => $priority,
                'status' => 'pending',
                'created_by' => auth()->id() ?? 1, // Use auth user or default to 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Job Order created successfully!',
                'job_order_id' => $jobOrder->id
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating job order: ' . $e->getMessage()
            ], 500);
        }
    })->name('job-orders.store');
    
    Route::get('/customers', function () {
        $query = \App\Models\Customer::withCount('jobOrders');
        
        // Search functionality
        if (request()->has('search') && request('search') != '') {
            $searchTerm = request('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                  ->orWhere('address', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $customers = $query->get();
        return view('marketing.customers', compact('customers'));
    })->name('customers');
    
    Route::post('/customers', function () {
        try {
            $validated = request()->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'required|string|max:50',
                'address' => 'required|string',
                'city' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'contact_person' => 'nullable|string|max:255',
                'tax_id' => 'nullable|string|max:100',
            ]);
            
            $validated['is_active'] = true;
            \App\Models\Customer::create($validated);
            
            return response()->json(['success' => true, 'message' => 'Customer added successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('customers.store');
    
    Route::put('/customers/{customer}', function (\App\Models\Customer $customer) {
        try {
            $validated = request()->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email,' . $customer->id,
                'phone' => 'required|string|max:50',
                'address' => 'required|string',
                'city' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'contact_person' => 'nullable|string|max:255',
                'tax_id' => 'nullable|string|max:100',
            ]);
            
            $customer->update($validated);
            
            return response()->json(['success' => true, 'message' => 'Customer updated successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('customers.update');
    
    Route::delete('/customers/{customer}', function (\App\Models\Customer $customer) {
        try {
            $customer->delete();
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error deleting customer'
            ], 500);
        }
    })->name('customers.destroy');
    
    Route::get('/reports', function () {
        $totalRevenue = \App\Models\JobOrder::sum('grand_total');
        $completedJobs = \App\Models\JobOrder::where('status', 'completed')->count();
        $activeCustomers = \App\Models\Customer::where('is_active', true)->count();
        $avgJobValue = $completedJobs > 0 ? $totalRevenue / $completedJobs : 0;
        
        // Job Orders Trend Data (Last 30 days)
        $jobOrdersTrendData = \App\Models\JobOrder::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Revenue Overview Data (Last 30 days)
        $revenueOverviewData = \App\Models\JobOrder::selectRaw('DATE(created_at) as date, SUM(grand_total) as revenue')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return view('marketing.reports', compact(
            'totalRevenue',
            'completedJobs',
            'activeCustomers',
            'avgJobValue',
            'jobOrdersTrendData',
            'revenueOverviewData'
        ));
    })->name('reports');
    
    // Report generation routes
    Route::post('/reports/job-orders', [ReportController::class, 'generateJobOrdersReport'])->name('reports.job-orders');
    Route::post('/reports/revenue', [ReportController::class, 'generateRevenueReport'])->name('reports.revenue');
    Route::post('/reports/customer', [ReportController::class, 'generateCustomerReport'])->name('reports.customer');
    Route::post('/reports/performance', [ReportController::class, 'generatePerformanceReport'])->name('reports.performance');
    
    // Timeline route for Marketing
    Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline');
});

// Technician Routes
Route::middleware(['auth', 'verified', 'role:tech_personnel'])->prefix('technician')->name('technician.')->group(function () {
    Route::get('/dashboard', function () {
        // Temporarily show all job orders until technician assignment is implemented
        $user = auth()->user();
        $todayAssignments = \App\Models\JobOrder::whereDate('created_at', today())->count();
        $pendingJobs = \App\Models\JobOrder::where('status', 'pending')->count();
        $inProgressJobs = \App\Models\JobOrder::where('status', 'in_progress')->count();
        $completedJobs = \App\Models\JobOrder::where('status', 'completed')->count();
        $recentAssignments = \App\Models\JobOrder::with('customer')
            ->latest()
            ->take(5)
            ->get();

        $overdueAssignments = \App\Models\Assignment::with(['jobOrder.customer'])
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->whereNotNull('scheduled_date')
            ->whereDate('scheduled_date', '<', today())
            ->orderBy('scheduled_date')
            ->take(3)
            ->get();

        $dueTodayAssignments = \App\Models\Assignment::with(['jobOrder.customer'])
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->whereDate('scheduled_date', today())
            ->orderBy('scheduled_time')
            ->take(3)
            ->get();

        $inventoryRequests = \App\Models\InventoryRequest::with(['inventoryItem'])
            ->where('requested_by', $user->id)
            ->latest('updated_at')
            ->take(5)
            ->get();

        $newAssignments = \App\Models\Assignment::with(['jobOrder.customer'])
            ->where('assigned_to', $user->id)
            ->where('status', 'assigned')
            ->whereDate('assigned_at', '>=', now()->subDays(7))
            ->orderByDesc('assigned_at')
            ->take(3)
            ->get();

        $onHoldAssignments = \App\Models\Assignment::with(['jobOrder.customer'])
            ->where('assigned_to', $user->id)
            ->where('status', 'on_hold')
            ->orderByDesc('updated_at')
            ->take(3)
            ->get();

        $highPriorityAssignments = \App\Models\Assignment::with(['jobOrder.customer'])
            ->where('assigned_to', $user->id)
            ->whereIn('priority', ['high', 'urgent'])
            ->whereIn('status', ['assigned', 'in_progress'])
            ->orderByDesc('assigned_at')
            ->take(3)
            ->get();
        
        return view('technician.dashboard', compact(
            'todayAssignments',
            'pendingJobs',
            'inProgressJobs',
            'completedJobs',
            'recentAssignments',
            'overdueAssignments',
            'dueTodayAssignments',
            'inventoryRequests',
            'newAssignments',
            'onHoldAssignments',
            'highPriorityAssignments'
        ));
    })->name('dashboard');
    
    Route::get('/assignments', function () {
        // Show only job orders assigned to the current technician
        $currentUser = auth()->user();
        $assignments = \App\Models\Assignment::with(['jobOrder.customer', 'jobOrder'])
            ->where('assigned_to', $currentUser->id)
            ->whereHas('jobOrder', function($query) {
                $query->whereIn('status', ['assigned', 'in_progress', 'on_hold', 'completed']);
            })
            ->latest()
            ->paginate(20)
            ->through(function($assignment) {
                // Return the job order with assignment info
                $job = $assignment->jobOrder;
                $job->assignment_id = $assignment->id;
                $job->assigned_at = $assignment->created_at;
                return $job;
            });
        return view('technician.assignments', compact('assignments'));
    })->name('assignments');
    
    Route::get('/work-orders', function () {
        // Show all work orders (broader view for reference)
        $workOrders = \App\Models\JobOrder::with('customer')
            ->whereIn('status', ['pending', 'assigned', 'in_progress', 'on_hold', 'completed'])
            ->latest()
            ->paginate(20);
        return view('technician.work-orders', compact('workOrders'));
    })->name('work-orders');
    
    Route::get('/job-details/{id}', function ($id) {
        $job = \App\Models\JobOrder::with([
            'customer',
            'assignments.calibrations.measurementPoints',
            'attachments',
            'items',
            'checklistItems.creator',
            'checklistItems.completer',
            'crewMembers.user',
            'crewMembers.addedBy'
        ])->findOrFail($id);
        $assignment = \App\Models\Assignment::with('report')
            ->where('job_order_id', $id)
            ->where('assigned_to', auth()->id())
            ->first();
        
        // Automatically add assigned technician to crew if not already added
        if ($assignment) {
            $alreadyInCrew = $job->crewMembers()->where('user_id', auth()->id())->exists();
            if (!$alreadyInCrew) {
                $crewMember = $job->crewMembers()->create([
                    'user_id' => auth()->id(),
                    'added_by' => auth()->id()
                ]);
                // Log the auto-addition
                \App\Helpers\AuditLogHelper::log(
                    'CREATE',
                    'JobOrderCrewMember',
                    $crewMember->id,
                    auth()->id(),
                    'Auto-added assigned technician to crew',
                    null,
                    ['user_id' => auth()->id(), 'job_order_id' => $job->id]
                );
            }
            // Reload crew members to include the newly added one
            $job->load('crewMembers.user', 'crewMembers.addedBy');
        }
        
        $checklistItems = $job->checklistItems->map(function ($item) {
            return [
                'id' => $item->id,
                'description' => $item->description,
                'is_completed' => (bool) $item->is_completed,
                'completed_at' => optional($item->completed_at)->toDateTimeString(),
                'created_by' => optional($item->creator)->name,
                'completed_by' => optional($item->completer)->name,
            ];
        })->values();

        $crewMembers = $job->crewMembers->map(function ($member) {
            return [
                'id' => $member->id,
                'user_id' => $member->user_id,
                'name' => $member->user ? $member->user->name : $member->name,
                'added_by' => optional($member->addedBy)->name
            ];
        })->values();

        $technicianRoleId = \App\Models\Role::where('slug', 'tech_personnel')->value('id');
        $technicians = \App\Models\User::where('role_id', $technicianRoleId)
            ->where('id', '!=', auth()->id()) // Exclude current user since they're auto-added
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('technician.job-details', compact('job', 'assignment', 'checklistItems', 'crewMembers', 'technicians'));
    })->name('job-details');

    Route::post('/job/{jobId}/crew-members', function (\Illuminate\Http\Request $request, $jobId) {
        $job = \App\Models\JobOrder::findOrFail($jobId);
        $assignment = \App\Models\Assignment::where('job_order_id', $job->id)
            ->where('assigned_to', auth()->id())
            ->first();
        abort_if(!$assignment, 403, 'Not authorized to update crew members.');

        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'nullable|string|max:255'
        ]);

        if (!$data['user_id'] && !$data['name']) {
            return response()->json(['success' => false, 'message' => 'Select a technician or provide a name.'], 422);
        }

        if (!empty($data['user_id'])) {
            $existing = \App\Models\JobOrderCrewMember::where('job_order_id', $job->id)
                ->where('user_id', $data['user_id'])
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => true,
                    'item' => [
                        'id' => $existing->id,
                        'user_id' => $existing->user_id,
                        'name' => optional($existing->user)->name,
                        'added_by' => optional($existing->addedBy)->name
                    ]
                ]);
            }
        }

        $member = $job->crewMembers()->create([
            'user_id' => $data['user_id'] ?? null,
            'name' => $data['user_id'] ? null : $data['name'],
            'added_by' => auth()->id()
        ]);

        \App\Helpers\AuditLogHelper::log(
            'CREATE',
            'JobOrderCrewMember',
            $member->id,
            "Crew member added for Job Order {$job->job_order_number}",
            null,
            $member->toArray(),
            ['user_id', 'name']
        );

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $member->id,
                'user_id' => $member->user_id,
                'name' => $member->user ? $member->user->name : $member->name,
                'added_by' => optional($member->addedBy)->name
            ]
        ]);
    })->name('job.crew-members.store');

    Route::delete('/crew-members/{member}', function (\App\Models\JobOrderCrewMember $member) {
        $assignment = \App\Models\Assignment::where('job_order_id', $member->job_order_id)
            ->where('assigned_to', auth()->id())
            ->first();
        abort_if(!$assignment, 403, 'Not authorized to update crew members.');
        
        // Prevent assigned technician from removing themselves
        if ($member->user_id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot remove yourself. You are assigned to this job.'
            ], 403);
        }

        $snapshot = $member->toArray();
        $member->delete();

        \App\Helpers\AuditLogHelper::log(
            'DELETE',
            'JobOrderCrewMember',
            $snapshot['id'],
            'Crew member removed',
            $snapshot,
            null,
            ['user_id', 'name']
        );

        return response()->json(['success' => true]);
    })->name('crew-members.delete');

    Route::post('/job/{jobId}/checklist', function (\Illuminate\Http\Request $request, $jobId) {
        $job = \App\Models\JobOrder::findOrFail($jobId);
        $assignment = \App\Models\Assignment::where('job_order_id', $job->id)
            ->where('assigned_to', auth()->id())
            ->first();
        abort_if(!$assignment, 403, 'Not authorized to update checklist.');

        $data = $request->validate([
            'description' => 'required|string|max:1000'
        ]);

        $item = $job->checklistItems()->create([
            'description' => $data['description'],
            'is_completed' => false,
            'created_by' => auth()->id(),
        ]);

        \App\Helpers\AuditLogHelper::log(
            'CREATE',
            'ChecklistItem',
            $item->id,
            'Checklist item created',
            null,
            $item->toArray(),
            ['description', 'is_completed']
        );

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $item->id,
                'description' => $item->description,
                'is_completed' => $item->is_completed,
                'completed_at' => null,
                'created_by' => optional($item->creator)->name,
                'completed_by' => null
            ]
        ]);
    })->name('job.checklist.store');

    Route::patch('/checklist-items/{item}', function (\Illuminate\Http\Request $request, \App\Models\ChecklistItem $item) {
        $assignment = \App\Models\Assignment::where('job_order_id', $item->job_order_id)
            ->where('assigned_to', auth()->id())
            ->first();
        abort_if(!$assignment, 403, 'Not authorized to update checklist.');

        $data = $request->validate([
            'is_completed' => 'required|boolean'
        ]);

        $oldValues = $item->only(['is_completed', 'completed_by', 'completed_at']);

        if ($data['is_completed']) {
            $item->update([
                'is_completed' => true,
                'completed_by' => auth()->id(),
                'completed_at' => now()
            ]);
        } else {
            $item->update([
                'is_completed' => false,
                'completed_by' => null,
                'completed_at' => null
            ]);
        }

        \App\Helpers\AuditLogHelper::log(
            'UPDATE',
            'ChecklistItem',
            $item->id,
            'Checklist item updated',
            $oldValues,
            $item->only(['is_completed', 'completed_by', 'completed_at']),
            ['is_completed', 'completed_by', 'completed_at']
        );

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $item->id,
                'description' => $item->description,
                'is_completed' => $item->is_completed,
                'completed_at' => optional($item->completed_at)->toDateTimeString(),
                'created_by' => optional($item->creator)->name,
                'completed_by' => optional($item->completer)->name
            ]
        ]);
    })->name('checklist.update');

    Route::delete('/checklist-items/{item}', function (\App\Models\ChecklistItem $item) {
        $assignment = \App\Models\Assignment::where('job_order_id', $item->job_order_id)
            ->where('assigned_to', auth()->id())
            ->first();
        abort_if(!$assignment, 403, 'Not authorized to update checklist.');

        $snapshot = $item->toArray();
        $item->delete();

        \App\Helpers\AuditLogHelper::log(
            'DELETE',
            'ChecklistItem',
            $snapshot['id'],
            'Checklist item deleted',
            $snapshot,
            null,
            ['description', 'is_completed']
        );

        return response()->json(['success' => true]);
    })->name('checklist.delete');

    // Technician report submission
    Route::post('/job/{job}/submit-report', function (\Illuminate\Http\Request $request, \App\Models\JobOrder $job) {
        $assignment = \App\Models\Assignment::where('job_order_id', $job->id)
            ->where('assigned_to', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'work_summary' => 'required|string|max:2000',
            'parts_used' => 'nullable|string|max:2000',
            'remarks' => 'nullable|string|max:2000',
        ]);

        $report = $assignment->report;
        if ($report && $report->status === 'approved') {
            return redirect()->back()->with('error', 'Report already approved and cannot be edited.');
        }

        if ($report) {
            $report->update([
                'work_summary' => $validated['work_summary'],
                'parts_used' => $validated['parts_used'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ]);
        } else {
            $report = \App\Models\Report::create([
                'assignment_id' => $assignment->id,
                'submitted_by' => auth()->id(),
                'work_summary' => $validated['work_summary'],
                'parts_used' => $validated['parts_used'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'status' => 'pending',
            ]);
        }

        $assignment->update([
            'status' => 'completed',
            'completed_at' => $assignment->completed_at ?? now(),
        ]);

        $job->update([
            'status' => 'pending',
        ]);

        return redirect()->back()->with('status', 'Report submitted for review.');
    })->name('job.submit-report');

    // Technician report submission (from reports modal)
    Route::post('/reports/{assignment}/submit', function (\Illuminate\Http\Request $request, \App\Models\Assignment $assignment) {
        abort_unless($assignment->assigned_to === auth()->id(), 403);

        $validated = $request->validate([
            'work_summary' => 'required|string|max:2000',
            'parts_used' => 'nullable|string|max:2000',
            'remarks' => 'nullable|string|max:2000',
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ]);

        $report = $assignment->report;
        if ($report && $report->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Report already approved and cannot be edited.'
            ], 422);
        }

        $job = $assignment->jobOrder;
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $path = $file->store('job-attachments/' . $job->id, 'public');
                $photoPaths[] = $path;
                $job->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'description' => 'Report photo',
                    'uploaded_by' => auth()->id(),
                    'uploaded_at' => now(),
                ]);
            }
        }

        if ($report) {
            $report->update([
                'work_summary' => $validated['work_summary'],
                'parts_used' => $validated['parts_used'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'photos' => array_values(array_filter(array_merge($report->photos ?? [], $photoPaths))),
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ]);
        } else {
            $report = \App\Models\Report::create([
                'assignment_id' => $assignment->id,
                'submitted_by' => auth()->id(),
                'work_summary' => $validated['work_summary'],
                'parts_used' => $validated['parts_used'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'photos' => $photoPaths,
                'status' => 'pending',
            ]);
        }

        $assignment->update([
            'status' => 'completed',
            'completed_at' => $assignment->completed_at ?? now(),
        ]);

        $job->update([
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report submitted for review.'
        ]);
    })->name('reports.submit');
    
    // Attachments upload route
    Route::post('/job/{jobId}/attachments', function ($jobId, \Illuminate\Http\Request $request) {
        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,webp,xls,xlsx|max:10240', // 10MB max
        ]);
        
        $job = \App\Models\JobOrder::findOrFail($jobId);
        
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('job-attachments/' . $jobId, 'public');
                
                $job->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
            
            \App\Helpers\AuditLogHelper::log(
                'CREATE',
                'JobAttachment',
                $jobId,
                auth()->id(),
                "Technician uploaded " . count($request->file('files')) . " attachment(s) for Job Order {$job->job_order_number}",
                null,
                null
            );
            
            return redirect()->back()->with('success', 'Files uploaded successfully.');
        }
        
        return redirect()->back()->with('error', 'No files were uploaded.');
    })->name('job.attachments.upload');
    
    // Job status update routes
    Route::post('/job/{jobId}/start', function ($jobId) {
        $job = \App\Models\JobOrder::findOrFail($jobId);
        
        // Update job status to in_progress
        $job->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);
        
        \App\Helpers\AuditLogHelper::log(
            'UPDATE',
            'JobOrder',
            $jobId,
            auth()->id(),
            "Technician started work on Job Order {$job->job_order_number}",
            ['status' => 'assigned'],
            ['status' => 'in_progress']
        );
        
        return response()->json(['success' => true, 'message' => 'Job started successfully']);
    })->name('job.start');
    
    Route::post('/job/{jobId}/pause', function ($jobId) {
        $job = \App\Models\JobOrder::findOrFail($jobId);
        
        // Update job status to on_hold
        $job->update([
            'status' => 'on_hold',
            'paused_at' => now()
        ]);
        
        \App\Helpers\AuditLogHelper::log(
            'UPDATE',
            'JobOrder',
            $jobId,
            auth()->id(),
            "Technician paused Job Order {$job->job_order_number}",
            ['status' => $job->getOriginal('status')],
            ['status' => 'on_hold']
        );
        
        return response()->json(['success' => true, 'message' => 'Job paused successfully']);
    })->name('job.pause');
    
    Route::get('/maintenance', function () {
        $maintenanceTasks = \App\Models\EquipmentMaintenance::with('equipment')
            ->latest('next_maintenance_date')
            ->paginate(20);
        
        // Calculate stats
        $today = now();
        $stats = [
            'scheduled_today' => \App\Models\EquipmentMaintenance::whereDate('next_maintenance_date', $today)->count(),
            'overdue' => \App\Models\EquipmentMaintenance::where('next_maintenance_date', '<', $today)
                ->where('status', '!=', 'completed')
                ->count(),
            'this_week' => \App\Models\EquipmentMaintenance::whereBetween('next_maintenance_date', [$today->startOfWeek(), $today->endOfWeek()])->count(),
            'completed' => \App\Models\EquipmentMaintenance::where('status', 'completed')->count(),
        ];
        
        return view('technician.maintenance', compact('maintenanceTasks', 'stats'));
    })->name('maintenance');
    
    Route::get('/equipment', function () {
        $equipment = Equipment::latest()->paginate(20);
        $equipmentStats = [
            'total' => Equipment::count(),
            'available' => Equipment::where('status', 'available')->count(),
            'in_use' => Equipment::where('status', 'in_use')->count(),
            'maintenance' => Equipment::where('status', 'maintenance')->count(),
        ];
        return view('technician.equipment', compact('equipment', 'equipmentStats'));
    })->name('equipment');
    
    Route::get('/inventory', [InventoryController::class, 'technicianIndex'])->name('inventory');
    Route::post('/inventory/request', [InventoryController::class, 'requestItem'])->name('inventory.request');
    
    Route::get('/reports', function () {
        $user = auth()->user();
        
        // Get pending reports (completed assignments without reports)
        $pendingReports = \App\Models\Assignment::where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->whereDoesntHave('report')
            ->with(['jobOrder.customer'])
            ->get();
        
        // Get submitted reports
        $submittedReports = \App\Models\Assignment::where('assigned_to', $user->id)
            ->whereHas('report')
            ->with(['jobOrder.customer', 'report'])
            ->latest()
            ->get();
        
        // Stats
        $pendingCount = $pendingReports->count();
        $todayCount = $submittedReports->filter(function($assignment) {
            return $assignment->report && $assignment->report->created_at->isToday();
        })->count();
        $totalCount = $submittedReports->count();
        
        return view('technician.reports', compact('pendingReports', 'submittedReports', 'pendingCount', 'todayCount', 'totalCount'));
    })->name('reports');

    Route::get('/certificates', function () {
        $userId = auth()->id();

        $query = Certificate::with(['jobOrder.customer', 'issuedBy', 'releasedBy'])
            ->whereHas('jobOrder.assignments', function ($q) use ($userId) {
                $q->where('assigned_to', $userId);
            });

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $certificates = $query->orderBy('created_at', 'desc')->get();

        $jobOrders = JobOrder::with('customer')
            ->where('status', 'approved')
            ->whereDoesntHave('certificates')
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('assigned_to', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('technician.certificates', compact('certificates', 'jobOrders'));
    })->name('certificates');

    Route::post('/certificates', function (Request $request) {
        $data = $request->validate([
            'job_order_id' => 'nullable|exists:job_orders,id',
            'customer_name' => 'required_without:job_order_id|string|max:255',
            'equipment_description' => 'required_without:job_order_id|string|max:500',
            'service_type' => 'required_without:job_order_id|string',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date|after:issue_date',
            'status' => 'required|in:pending,generated',
            'template_used' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($data['job_order_id']) {
            $hasAssignment = Assignment::where('assigned_to', auth()->id())
                ->where('job_order_id', $data['job_order_id'])
                ->exists();

            abort_unless($hasAssignment, 403);
        }

        $certificateNotes = $data['notes'] ?? '';
        if (!$data['job_order_id']) {
            $manualDetails = "Customer: {$data['customer_name']}\n";
            $manualDetails .= "Equipment: {$data['equipment_description']}\n";
            $manualDetails .= "Service Type: {$data['service_type']}\n";
            if ($certificateNotes) {
                $manualDetails .= "\nAdditional Notes:\n{$certificateNotes}";
            }
            $certificateNotes = $manualDetails;
        }

        $certificate = Certificate::create([
            'certificate_number' => Certificate::generateCertificateNumber(),
            'job_order_id' => $data['job_order_id'] ?? null,
            'job_order_item_id' => null,
            'calibration_id' => null,
            'issue_date' => $data['issue_date'],
            'valid_until' => $data['valid_until'],
            'status' => $data['status'],
            'template_used' => $data['template_used'] ?? 'default',
            'notes' => $certificateNotes,
            'issued_by' => auth()->id(),
            'approved_by' => auth()->id(),
            'generated_at' => $data['status'] === 'generated' ? now() : null,
            'version' => 1,
            'revision_number' => 0,
            'is_current' => true,
        ]);

        return redirect()->route('technician.certificates')->with('status', 'Certificate created successfully: ' . $certificate->certificate_number);
    })->name('certificates.store');

    Route::get('/certificates/{certificate}/download', function (Certificate $certificate) {
        $userId = auth()->id();
        $hasAccess = ($certificate->jobOrder && $certificate->jobOrder->assignments()->where('assigned_to', $userId)->exists())
            || $certificate->issued_by === $userId;

        abort_unless($hasAccess, 403);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate->load(['jobOrder.customer', 'issuedBy', 'approvedBy'])
        ]);

        return $pdf->download($certificate->certificate_number . '.pdf');
    })->name('certificates.download');

    Route::post('/certificates/{certificate}/generate', function (Certificate $certificate) {
        $userId = auth()->id();
        $hasAccess = ($certificate->jobOrder && $certificate->jobOrder->assignments()->where('assigned_to', $userId)->exists())
            || $certificate->issued_by === $userId;

        abort_unless($hasAccess, 403);

        if ($certificate->status === 'pending') {
            $certificate->update([
                'status' => 'generated',
                'generated_at' => now(),
                'issue_date' => now(),
            ]);

            return redirect()->route('technician.certificates')->with('status', 'Certificate generated successfully');
        }

        return redirect()->route('technician.certificates')->with('error', 'Certificate has already been generated');
    })->name('certificates.generate');
    
    // Timeline route for Technician
    Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline');
    
    Route::get('/notifications', function () {
        return view('technician.notifications');
    })->name('notifications');
    
    Route::get('/calendar', function () {
        $user = auth()->user();
        
        // Get all assignments for the technician
        $assignments = \App\Models\Assignment::where('assigned_to', $user->id)
            ->whereNotNull('scheduled_date')
            ->with(['jobOrder'])
            ->get()
            ->map(function($assignment) {
                return [
                    'id' => $assignment->id,
                    'date' => $assignment->scheduled_date,
                    'time' => $assignment->scheduled_time ? \Carbon\Carbon::parse($assignment->scheduled_time)->format('h:i A') : null,
                    'title' => $assignment->jobOrder->service_type ?? 'Job Assignment',
                    'location' => $assignment->location,
                    'status' => $assignment->status,
                    'priority' => $assignment->priority,
                ];
            });
        
        return view('technician.calendar', compact('assignments'));
    })->name('calendar');

});

// Tech Head Routes
Route::middleware(['auth', 'verified', 'role:tech_head'])->prefix('tech-head')->name('tech-head.')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $statusFilter = $request->string('status')->toString();
        $statusFilter = in_array($statusFilter, ['pending', 'in_progress', 'overdue', 'high_priority', 'completed']) ? $statusFilter : null;


        $today = now();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $summary = [
            'activeWorkOrders' => JobOrder::whereIn('status', ['pending', 'in_progress'])->count(),
            'pendingApprovals' => JobOrder::where('status', 'pending')->count(),
            'inProgressJobs' => JobOrder::where('status', 'in_progress')->count(),
            'overdueJobs' => JobOrder::whereIn('status', ['pending', 'in_progress'])
                ->where(function ($query) {
                    $query->whereDate('required_date', '<', today())
                        ->orWhereDate('expected_completion_date', '<', today());
                })
                ->count(),
            'completedToday' => JobOrder::where('status', 'completed')->whereDate('updated_at', today())->count(),
            'completedThisWeek' => JobOrder::where('status', 'completed')
                ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                ->count(),
        ];

        $techniciansAvailable = 0;
        $techniciansOnTask = 0;
        $technicianOverview = collect();

        $technicianRoleId = Role::where('slug', 'tech_personnel')->value('id');
        if ($technicianRoleId) {
            $technicians = User::where('role_id', $technicianRoleId)->orderBy('name')->get();
            $technicianIds = $technicians->pluck('id');

            $activeTechIds = Assignment::whereIn('status', ['assigned', 'in_progress'])
                ->whereIn('assigned_to', $technicianIds)
                ->pluck('assigned_to')
                ->unique();

            $techniciansOnTask = $activeTechIds->count();
            $techniciansAvailable = max($technicians->count() - $techniciansOnTask, 0);

            $technicianStats = Assignment::select(
                'assigned_to',
                DB::raw('SUM(CASE WHEN status IN ("assigned","in_progress") THEN 1 ELSE 0 END) as active_jobs'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_jobs'),
                DB::raw('SUM(CASE WHEN status != "completed" AND DATE(scheduled_date) = CURDATE() THEN 1 ELSE 0 END) as jobs_today'),
                DB::raw('SUM(CASE WHEN status != "completed" AND scheduled_date < CURDATE() THEN 1 ELSE 0 END) as overdue_jobs')
            )
                ->whereIn('assigned_to', $technicianIds)
                ->groupBy('assigned_to')
                ->get()
                ->keyBy('assigned_to');

            $activeAssignments = Assignment::with(['jobOrder.customer'])
                ->whereIn('assigned_to', $technicianIds)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->latest('scheduled_date')
                ->get()
                ->keyBy('assigned_to');

            $technicianOverview = $technicians->map(function ($technician) use ($technicianStats, $activeAssignments) {
                return [
                    'user' => $technician,
                    'stats' => $technicianStats[$technician->id] ?? null,
                    'activeAssignment' => $activeAssignments[$technician->id] ?? null,
                ];
            });
        }

        // Fetch overdue work orders (past required_date or expected_completion_date, not completed/cancelled)
        $overdueWorkOrders = JobOrder::with('customer')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where(function ($query) {
                $today = today();
                $query->where(function ($q) use ($today) {
                    $q->where('required_date', '<', $today)
                      ->where('required_date', '!=', null);
                })
                ->orWhere(function ($q) use ($today) {
                    $q->where('expected_completion_date', '<', $today)
                      ->where('expected_completion_date', '!=', null);
                });
            })
            ->orderBy('required_date', 'asc')
            ->orderBy('expected_completion_date', 'asc')
            ->take(10)
            ->get();

        // Fetch unassigned jobs (pending/in_progress with no active assignments)
        $unassignedJobs = JobOrder::with('customer')
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereDoesntHave('assignments', function ($query) {
                $query->whereIn('status', ['assigned', 'in_progress']);
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Fetch pending approvals waiting longest (aging approval queue)
        $agingPendingApprovals = JobOrder::with('customer')
            ->where('status', 'pending')
            ->whereNull('approved_at')
            ->orderBy('request_date', 'asc')
            ->take(10)
            ->get();

        $criticalEquipment = Equipment::where(function ($query) {
            $query->where('status', 'maintenance')
                ->orWhere('calibration_required', true);
        })
            ->orderBy('next_maintenance')
            ->take(5)
            ->get();

        $highPriorityJobs = JobOrder::with('customer')
            ->whereIn('priority', ['urgent', 'high'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->take(5)
            ->get();

        $workOrderQuery = JobOrder::with('customer')->latest('created_at');
        if ($statusFilter === 'pending') {
            $workOrderQuery->where('status', 'pending');
        } elseif ($statusFilter === 'in_progress') {
            $workOrderQuery->where('status', 'in_progress');
        } elseif ($statusFilter === 'completed') {
            $workOrderQuery->where('status', 'completed');
        } elseif ($statusFilter === 'overdue') {
            $workOrderQuery->whereIn('status', ['pending', 'in_progress'])
                ->where(function ($query) {
                    $query->whereDate('required_date', '<', today())
                        ->orWhereDate('expected_completion_date', '<', today());
                });
        } elseif ($statusFilter === 'high_priority') {
            $workOrderQuery->whereIn('priority', ['urgent', 'high']);
        }

        $workOrders = $workOrderQuery->take(12)->get();
        $assignmentsByJob = Assignment::with('assignedTo')
            ->whereIn('job_order_id', $workOrders->pluck('id'))
            ->latest('created_at')
            ->get()
            ->keyBy('job_order_id');

        $workOrderCounts = [
            'pending' => JobOrder::where('status', 'pending')->count(),
            'in_progress' => JobOrder::where('status', 'in_progress')->count(),
            'overdue' => $summary['overdueJobs'],
            'high_priority' => JobOrder::whereIn('priority', ['urgent', 'high'])
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
            'completed' => JobOrder::where('status', 'completed')->count(),
        ];

        $activityTimeline = collect();

        $activityTimeline = $activityTimeline->merge(
            JobOrder::with('customer')
                ->latest('created_at')
                ->take(8)
                ->get()
                ->map(function ($job) {
                    return [
                        'timestamp' => $job->created_at,
                        'type' => 'job_created',
                        'title' => "Job created: {$job->job_order_number}",
                        'user' => $job->requested_by ?? 'Client',
                        'job' => $job,
                        'status' => $job->status,
                    ];
                })
        );

        $assignmentEvents = Assignment::with(['jobOrder.customer', 'assignedTo'])
            ->latest('created_at')
            ->take(10)
            ->get()
            ->flatMap(function ($assignment) {
                $events = collect([
                    [
                        'timestamp' => $assignment->assigned_at ?? $assignment->created_at,
                        'type' => 'technician_assigned',
                        'title' => 'Technician assigned',
                        'user' => $assignment->assignedTo?->name,
                        'job' => $assignment->jobOrder,
                        'status' => $assignment->status,
                    ],
                ]);

                if ($assignment->started_at) {
                    $events->push([
                        'timestamp' => $assignment->started_at,
                        'type' => 'job_started',
                        'title' => 'Job started',
                        'user' => $assignment->assignedTo?->name,
                        'job' => $assignment->jobOrder,
                        'status' => 'in_progress',
                    ]);
                }

                if ($assignment->completed_at) {
                    $events->push([
                        'timestamp' => $assignment->completed_at,
                        'type' => 'job_completed',
                        'title' => 'Job completed',
                        'user' => $assignment->assignedTo?->name,
                        'job' => $assignment->jobOrder,
                        'status' => 'completed',
                    ]);
                }

                return $events;
            });

        $reportEvents = Report::with(['assignment.jobOrder.customer', 'submittedBy'])
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(function ($report) {
                return [
                    'timestamp' => $report->created_at,
                    'type' => 'report_submitted',
                    'title' => 'Report submitted',
                    'user' => $report->submittedBy?->name,
                    'job' => $report->assignment?->jobOrder,
                    'status' => $report->status ?? 'submitted',
                ];
            });

        // Paginate activity timeline - 3 items per page
        $timelinePage = request()->get('timeline_page', 1);
        $perPage = 3;
        
        $allActivities = $activityTimeline
            ->merge($assignmentEvents)
            ->merge($reportEvents)
            ->sortByDesc('timestamp')
            ->values();
        
        $activityTimeline = new \Illuminate\Pagination\LengthAwarePaginator(
            $allActivities->forPage($timelinePage, $perPage),
            $allActivities->count(),
            $perPage,
            $timelinePage,
            [
                'path' => request()->url(), 
                'query' => request()->except('timeline_page'),
                'pageName' => 'timeline_page'
            ]
        );
        $activityTimeline->setPageName('timeline_page');

        $schedule = [
            'today' => Assignment::with(['jobOrder.customer', 'assignedTo'])
                ->whereDate('scheduled_date', $today)
                ->orderBy('scheduled_time')
                ->get(),
            'tomorrow' => Assignment::with(['jobOrder.customer', 'assignedTo'])
                ->whereDate('scheduled_date', $today->copy()->addDay())
                ->orderBy('scheduled_time')
                ->get(),
            'upcoming' => Assignment::with(['jobOrder.customer', 'assignedTo'])
                ->whereDate('scheduled_date', '>', $today->copy()->addDays(1))
                ->orderBy('scheduled_date')
                ->limit(8)
                ->get(),
        ];

        $equipmentSummary = [
            'total' => Equipment::count(),
            'underMaintenance' => Equipment::where('status', 'maintenance')->count(),
            'critical' => Equipment::where(function ($query) {
                $query->where('status', 'maintenance')
                    ->orWhere('calibration_required', true);
            })->count(),
        ];

        return view('tech-head.dashboard', [
            'summary' => $summary,
            'techniciansAvailable' => $techniciansAvailable,
            'techniciansOnTask' => $techniciansOnTask,
            'technicianOverview' => $technicianOverview,
            'overdueWorkOrders' => $overdueWorkOrders,
            'unassignedJobs' => $unassignedJobs,
            'agingPendingApprovals' => $agingPendingApprovals,
            'criticalEquipment' => $criticalEquipment,
            'highPriorityJobs' => $highPriorityJobs,
            'workOrders' => $workOrders,
            'assignmentsByJob' => $assignmentsByJob,
            'workOrderCounts' => $workOrderCounts,
            'statusFilter' => $statusFilter,
            'activityTimeline' => $activityTimeline,
            'schedule' => $schedule,
            'equipmentSummary' => $equipmentSummary,
        ]);
    })->name('dashboard');
    
    Route::get('/work-orders', function (\Illuminate\Http\Request $request) {
        $search = $request->get('search');
        $status = $request->get('status');
        $priority = $request->get('priority');
        
        $query = JobOrder::with([
            'customer',
            'attachments',
            'checklistItems.creator',
            'checklistItems.completer'
        ])->withCount('certificates');
        
        // Exclude work orders that already have assignments (they should appear in Assignments page only)
        $query->whereDoesntHave('assignments');
        
        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('job_order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($custQ) use ($search) {
                      $custQ->where('name', 'like', "%{$search}%");
                  })
                  ->orWhere('service_type', 'like', "%{$search}%")
                  ->orWhere('service_description', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($status) {
            $query->where('status', $status);
        }
        
        // Priority filter
        if ($priority) {
            $query->where('priority', $priority);
        }
        
        $workOrders = $query->latest()->paginate(20)->appends([
            'search' => $search,
            'status' => $status,
            'priority' => $priority
        ]);
        
        // Get all active technicians for assignment
        $technicianRoleId = \App\Models\Role::where('slug', 'tech_personnel')->value('id');
        $technicians = \App\Models\User::where('role_id', $technicianRoleId)
            ->orderBy('name')
            ->get();
        
        return view('tech-head.work-orders', compact('workOrders', 'search', 'technicians'));
    })->name('work-orders');

    // Work Orders CRUD & Management
    Route::post('/work-orders', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'priority' => 'required|in:low,normal,high,urgent',
            'status' => 'required|string|max:50',
            'required_date' => 'nullable|date',
            'service_type' => 'nullable|string',
            'service_description' => 'nullable|string',
            'service_address' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $number = 'WO-' . now()->format('YmdHis');
        $order = JobOrder::create(array_merge($data, [
            'job_order_number' => $number,
            'request_date' => now()->toDateString(),
            'created_by' => auth()->id(),
        ]));
        return redirect()->route('tech-head.work-orders')->with('status', 'Work order created: ' . $order->job_order_number);
    })->name('work-orders.store');

    Route::put('/work-orders/{jobOrder}', function (\Illuminate\Http\Request $request, JobOrder $jobOrder) {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'priority' => 'required|in:low,normal,high,urgent',
            'status' => 'required|string|max:50',
            'required_date' => 'nullable|date',
            'service_type' => 'nullable|string',
            'service_description' => 'nullable|string',
            'service_address' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $jobOrder->update($data);
        return redirect()->route('tech-head.work-orders')->with('status', 'Work order updated');
    })->name('work-orders.update');

    Route::delete('/work-orders/{jobOrder}', function (JobOrder $jobOrder) {
        $jobOrder->delete();
        return redirect()->route('tech-head.work-orders')->with('status', 'Work order deleted');
    })->name('work-orders.destroy');

    Route::post('/work-orders/{jobOrder}/cancel', function (JobOrder $jobOrder) {
        $jobOrder->update(['status' => 'cancelled']);
        return redirect()->route('tech-head.work-orders')->with('status', 'Work order cancelled');
    })->name('work-orders.cancel');

    Route::post('/work-orders/{jobOrder}/status', function (\Illuminate\Http\Request $request, JobOrder $jobOrder) {
        $data = $request->validate(['status' => 'required|string|max:50']);
        $jobOrder->update(['status' => $data['status']]);
        return redirect()->route('tech-head.work-orders')->with('status', 'Status updated');
    })->name('work-orders.status');

    Route::post('/work-orders/{jobOrder}/assign', function (\Illuminate\Http\Request $request, JobOrder $jobOrder) {
        $data = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'scheduled_date' => 'nullable|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'notes' => 'nullable|string',
        ]);
        Assignment::create([
            'job_order_id' => $jobOrder->id,
            'assigned_to' => $data['assigned_to'],
            'assigned_by' => auth()->id(),
            'scheduled_date' => $data['scheduled_date'] ?? null,
            'scheduled_time' => $data['scheduled_time'] ?? null,
            'priority' => $data['priority'] ?? ($jobOrder->priority ?? 'normal'),
            'status' => 'assigned',
            'notes' => $data['notes'] ?? null,
        ]);
        $jobOrder->update(['status' => 'assigned']);
        return redirect()->route('tech-head.work-orders')->with('status', 'Technician assigned');
    })->name('work-orders.assign');
    
    // View job order calibration data and attachments
    Route::get('/work-orders/{jobOrder}/calibration-data', function (JobOrder $jobOrder) {
        $jobOrder->load(['assignments.calibrations.measurementPoints', 'attachments', 'customer']);
        return view('tech-head.calibration-data', compact('jobOrder'));
    })->name('work-orders.calibration-data');
    
    // Approval & Signature Route
    Route::patch('/work-orders/{jobOrder}/approve', function (\Illuminate\Http\Request $request, JobOrder $jobOrder) {
        $data = $request->validate([
            'action' => 'required|in:approve,reject',
            'signature' => 'nullable|string',
            'comments' => 'nullable|string|max:1000',
        ]);
        
        if ($data['action'] === 'approve') {
            $jobOrder->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_signature' => $data['signature'] ?? null,
                'approval_comments' => $data['comments'] ?? null,
            ]);
            
            // Auto-generate certificate after approval
            $certificate = Certificate::create([
                'certificate_number' => Certificate::generateCertificateNumber(),
                'job_order_id' => $jobOrder->id,
                'job_order_item_id' => null, // Set if needed
                'calibration_id' => null, // Set if needed
                'issue_date' => now(),
                'valid_until' => now()->addYear(),
                'status' => 'generated',
                'issued_by' => auth()->id(),
                'approved_by' => auth()->id(),
                'generated_at' => now(),
                'template_used' => 'default',
                'version' => 1,
                'revision_number' => 0,
                'is_current' => true,
            ]);
            
            return redirect()->route('tech-head.work-orders')->with('status', 'Work order approved, signed, and certificate generated successfully');
        } else {
            $jobOrder->update([
                'status' => 'rejected',
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'rejection_reason' => $data['comments'] ?? null,
            ]);
            
            return redirect()->route('tech-head.work-orders')->with('status', 'Work order rejected');
        }
    })->name('work-orders.approve');
    
    // Certificate Routes
    Route::get('/certificates', function () {
        $query = Certificate::with(['jobOrder.customer', 'issuedBy', 'releasedBy']);
        
        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        $certificates = $query->orderBy('created_at', 'desc')->get();
        
        // Get approved job orders for manual certificate creation
        $jobOrders = JobOrder::with('customer')
            ->where('status', 'approved')
            ->whereDoesntHave('certificates')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('tech-head.certificates', compact('certificates', 'jobOrders'));
    })->name('certificates');
    
    Route::post('/certificates', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'job_order_id' => 'nullable|exists:job_orders,id',
            'customer_name' => 'required_without:job_order_id|string|max:255',
            'equipment_description' => 'required_without:job_order_id|string|max:500',
            'service_type' => 'required_without:job_order_id|string',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date|after:issue_date',
            'status' => 'required|in:pending,generated',
            'template_used' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Build notes with manual entry details if no work order
        $certificateNotes = $data['notes'] ?? '';
        if (!$data['job_order_id']) {
            $manualDetails = "Customer: {$data['customer_name']}\n";
            $manualDetails .= "Equipment: {$data['equipment_description']}\n";
            $manualDetails .= "Service Type: {$data['service_type']}\n";
            if ($certificateNotes) {
                $manualDetails .= "\nAdditional Notes:\n{$certificateNotes}";
            }
            $certificateNotes = $manualDetails;
        }
        
        $certificate = Certificate::create([
            'certificate_number' => Certificate::generateCertificateNumber(),
            'job_order_id' => $data['job_order_id'] ?? null,
            'job_order_item_id' => null,
            'calibration_id' => null,
            'issue_date' => $data['issue_date'],
            'valid_until' => $data['valid_until'],
            'status' => $data['status'],
            'template_used' => $data['template_used'] ?? 'default',
            'notes' => $certificateNotes,
            'issued_by' => auth()->id(),
            'approved_by' => auth()->id(),
            'generated_at' => $data['status'] === 'generated' ? now() : null,
            'version' => 1,
            'revision_number' => 0,
            'is_current' => true,
        ]);
        
        return redirect()->route('tech-head.certificates')->with('status', 'Certificate created successfully: ' . $certificate->certificate_number);
    })->name('certificates.store');
    
    Route::get('/certificates/{certificate}/download', function (Certificate $certificate) {
        // Generate PDF using DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate->load(['jobOrder.customer', 'issuedBy', 'approvedBy'])
        ]);
        
        return $pdf->download($certificate->certificate_number . '.pdf');
    })->name('certificates.download');
    
    Route::post('/certificates/{certificate}/generate', function (Certificate $certificate) {
        if ($certificate->status === 'pending') {
            $certificate->update([
                'status' => 'generated',
                'generated_at' => now(),
                'issue_date' => now(),
            ]);
            
            return redirect()->route('tech-head.certificates')->with('status', 'Certificate generated successfully');
        }
        
        return redirect()->route('tech-head.certificates')->with('error', 'Certificate has already been generated');
    })->name('certificates.generate');
    
    Route::patch('/certificates/{certificate}/release', function (\Illuminate\Http\Request $request, Certificate $certificate) {
        $data = $request->validate([
            'released_to' => 'required|string|max:255',
            'delivery_method' => 'required|in:email,hand_delivery,courier',
            'release_notes' => 'nullable|string|max:1000',
        ]);
        
        $certificate->update([
            'status' => 'released',
            'released_at' => now(),
            'released_to' => $data['released_to'],
            'released_by' => auth()->id(),
            'delivery_method' => $data['delivery_method'],
            'release_notes' => $data['release_notes'] ?? null,
        ]);
        
        // Update job order status to completed
        $certificate->jobOrder->update(['status' => 'completed']);
        
        return redirect()->route('tech-head.certificates')->with('status', 'Certificate released successfully');
    })->name('certificates.release');
    
    Route::delete('/certificates/{certificate}', function (Certificate $certificate) {
        $certificateNumber = $certificate->certificate_number;
        
        // Delete the certificate
        $certificate->delete();
        
        return redirect()->route('tech-head.certificates')->with('status', 'Certificate ' . $certificateNumber . ' deleted successfully');
    })->name('certificates.destroy');
    
    // Calibration Approval Routes
    Route::get('/calibration-approvals', [ApprovalController::class, 'index'])->name('calibration-approvals');
    Route::get('/calibration/{calibration}/review', [ApprovalController::class, 'show'])->name('calibration.show');
    Route::post('/calibration/{calibration}/approve', [ApprovalController::class, 'approve'])->name('calibration.approve');
    Route::get('/calibration/{calibration}/measurement-summary', [ApprovalController::class, 'getMeasurementSummary'])->name('calibration.measurement-summary');
    
    Route::get('/technicians', function () {
        $technicianRoleId = Role::where('slug', 'tech_personnel')->value('id');
        
        // Build query
        $query = User::where('role_id', $technicianRoleId)->with(['role']);
        
        // Apply search filter if present
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('department', 'like', '%' . $search . '%')
                  ->orWhere('skills', 'like', '%' . $search . '%');
            });
        }
        
        $technicians = $query->get();
        
        // Get stats for each technician
        $technicianIds = $technicians->pluck('id');
        $technicianStats = Assignment::select(
            'assigned_to',
            DB::raw('COUNT(*) as total_assignments'),
            DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'),
            DB::raw('SUM(CASE WHEN status IN ("assigned", "in_progress") THEN 1 ELSE 0 END) as active')
        )
            ->whereIn('assigned_to', $technicianIds)
            ->groupBy('assigned_to')
            ->get()
            ->keyBy('assigned_to');
        
        return view('tech-head.technicians', compact('technicians', 'technicianStats'));
    })->name('technicians');

    // Technicians management
    Route::post('/technicians', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'department' => 'nullable|string|max:100',
            'skills' => 'nullable|array',
        ]);
        $roleId = Role::where('slug', 'tech_personnel')->value('id');
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role_id' => $roleId,
            'department' => $data['department'] ?? null,
            'is_active' => true,
            'availability' => 'available',
            'skills' => isset($data['skills']) ? json_encode($data['skills']) : null,
        ]);
        return redirect()->route('tech-head.technicians')->with('status', 'Technician added: ' . $user->name);
    })->name('technicians.store');

    Route::post('/technicians/{user}/disable', function (User $user) {
        $user->update(['is_active' => false]);
        return redirect()->route('tech-head.technicians')->with('status', 'Technician disabled');
    })->name('technicians.disable');

    Route::post('/technicians/{user}/availability', function (\Illuminate\Http\Request $request, User $user) {
        $data = $request->validate(['availability' => 'required|in:available,on_leave,unavailable']);
        $user->update(['availability' => $data['availability']]);
        return redirect()->route('tech-head.technicians')->with('status', 'Availability updated');
    })->name('technicians.availability');

    Route::post('/technicians/{user}/skills', function (\Illuminate\Http\Request $request, User $user) {
        $data = $request->validate(['skills' => 'nullable|array']);
        $user->update(['skills' => isset($data['skills']) ? json_encode($data['skills']) : null]);
        return redirect()->route('tech-head.technicians')->with('status', 'Skills updated');
    })->name('technicians.skills');
    
    Route::get('/assignments', function (\Illuminate\Http\Request $request) {
        $search = $request->get('search');
        $status = $request->get('status');
        $priority = $request->get('priority');
        
        $query = Assignment::with(['jobOrder.customer', 'assignedTo']);
        
        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('jobOrder', function ($subQ) use ($search) {
                    $subQ->where('job_order_number', 'like', "%{$search}%")
                         ->orWhereHas('customer', function ($custQ) use ($search) {
                             $custQ->where('name', 'like', "%{$search}%");
                         });
                })
                ->orWhereHas('assignedTo', function ($techQ) use ($search) {
                    $techQ->where('name', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($status) {
            $query->where('status', $status);
        }
        
        // Priority filter
        if ($priority) {
            $query->where('priority', $priority);
        }
        
        $assignments = $query->latest()->paginate(20)->appends([
            'search' => $search,
            'status' => $status,
            'priority' => $priority
        ]);
        
        return view('tech-head.assignments', compact('assignments', 'search'));
    })->name('assignments');

    // Assignments control
    Route::post('/assignments', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'job_order_id' => 'required|exists:job_orders,id',
            'assigned_to' => 'required|exists:users,id',
            'scheduled_date' => 'nullable|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'notes' => 'nullable|string',
        ]);
        Assignment::create(array_merge($data, [
            'assigned_by' => auth()->id(),
            'status' => 'assigned',
        ]));
        return redirect()->route('tech-head.assignments')->with('status', 'Assignment created');
    })->name('assignments.store');

    Route::post('/assignments/{assignment}/reassign', function (\Illuminate\Http\Request $request, Assignment $assignment) {
        $data = $request->validate(['assigned_to' => 'required|exists:users,id']);
        $assignment->update(['assigned_to' => $data['assigned_to'], 'status' => 'assigned']);
        return redirect()->route('tech-head.assignments')->with('status', 'Assignment reassigned');
    })->name('assignments.reassign');

    Route::post('/assignments/{assignment}/unassign', function (Assignment $assignment) {
        $assignment->update(['assigned_to' => null, 'status' => 'pending']);
        return redirect()->route('tech-head.assignments')->with('status', 'Assignment unassigned');
    })->name('assignments.unassign');

    Route::post('/assignments/{assignment}/schedule', function (\Illuminate\Http\Request $request, Assignment $assignment) {
        $data = $request->validate([
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
        ]);
        $assignment->update($data);
        return redirect()->route('tech-head.assignments')->with('status', 'Assignment scheduled');
    })->name('assignments.schedule');
    
    Route::get('/reports', function (\Illuminate\Http\Request $request) {
        $filter = $request->get('filter', 'all');
        
        // Get all reports with relationships
        $query = Report::with(['assignment.jobOrder.customer', 'assignment.jobOrder.attachments', 'submittedBy', 'reviewedBy'])
            ->latest();
        
        // Apply filter
        if ($filter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($filter === 'approved') {
            $query->where('status', 'approved');
        } elseif ($filter === 'rejected') {
            $query->where('status', 'rejected');
        }
        
        $reports = $query->paginate(15);
        
        // Get pending reports for the card
        $pendingReports = Report::with(['assignment.jobOrder.customer', 'assignment.jobOrder.attachments', 'submittedBy'])
            ->where('status', 'pending')
            ->latest()
            ->get();
        
        // Report stats
        $stats = [
            'total' => Report::count(),
            'pending' => Report::where('status', 'pending')->count(),
            'approved' => Report::where('status', 'approved')->count(),
            'rejected' => Report::where('status', 'rejected')->count(),
        ];

        // Calibration stats
        $calStats = [
            'total' => Calibration::count(),
            'pending' => Calibration::where('status', 'submitted_for_review')->count(),
            'approved' => Calibration::where('status', 'approved')->count(),
            'rejected' => Calibration::where('status', 'rejected')->count(),
        ];

        // Calibrations filter
        $calFilter = request('cal_filter', 'pending');
        $calibrationQuery = Calibration::with([
            'jobOrderItem.jobOrder.customer',
            'assignment.technician',
            'performedBy',
            'measurementPoints',
        ]);

        $calibrationQuery = match($calFilter) {
            'approved' => $calibrationQuery->where('status', 'approved'),
            'rejected' => $calibrationQuery->where('status', 'rejected'),
            default => $calibrationQuery->where('status', 'submitted_for_review'),
        };

        $calibrations = $calibrationQuery->latest()->paginate(10)->withQueryString();
        $pendingCalibrations = Calibration::with([
            'jobOrderItem.jobOrder.customer',
            'assignment.technician',
            'performedBy',
            'measurementPoints',
        ])->where('status', 'submitted_for_review')->latest()->take(6)->get();
        
        return view('tech-head.reports', compact('reports', 'pendingReports', 'stats', 'filter', 'calStats', 'calFilter', 'calibrations', 'pendingCalibrations'));
    })->name('reports');

    // Reports approvals
    Route::post('/reports/{report}/approve', function (Report $report) {
        $report->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        // lock job as completed
        $report->assignment->jobOrder->update(['status' => 'completed']);
        return redirect()->route('tech-head.reports')->with('status', 'Report approved');
    })->name('reports.approve');

    Route::post('/reports/{report}/reject', function (\Illuminate\Http\Request $request, Report $report) {
        $data = $request->validate(['review_notes' => 'nullable|string']);
        $report->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $data['review_notes'] ?? null,
        ]);
        return redirect()->route('tech-head.reports')->with('status', 'Report rejected');
    })->name('reports.reject');

    Route::post('/reports/{report}/revise', function (\Illuminate\Http\Request $request, Report $report) {
        $data = $request->validate(['review_notes' => 'required|string']);
        $report->update([
            'status' => 'pending',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $data['review_notes'],
        ]);
        return redirect()->route('tech-head.reports')->with('status', 'Revision requested');
    })->name('reports.revise');
    
    Route::get('/equipment', function () {
        $equipment = Equipment::latest()->paginate(20);
        $equipmentStats = [
            'total' => Equipment::count(),
            'available' => Equipment::where('status', 'available')->count(),
            'in_use' => Equipment::where('status', 'in_use')->count(),
            'maintenance' => Equipment::where('status', 'maintenance')->count(),
            'retired' => Equipment::where('status', 'retired')->count(),
        ];
        return view('tech-head.equipment', compact('equipment', 'equipmentStats'));
    })->name('equipment');

    Route::get('/inventory', [InventoryController::class, 'techHeadIndex'])->name('inventory');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('/inventory/{inventoryItem}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{inventoryItem}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::patch('/inventory/requests/{inventoryRequest}', [InventoryController::class, 'updateRequestStatus'])->name('inventory.requests.update');

    // Equipment CRUD
    Route::post('/equipment', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'equipment_code' => 'required|string|unique:equipment,equipment_code',
            'name' => 'required|string',
            'category' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'model' => 'nullable|string',
            'status' => 'nullable|in:available,in_use,maintenance,retired',
            'location' => 'nullable|string',
            'calibration_required' => 'nullable|boolean',
        ]);
        Equipment::create($data);
        return redirect()->route('tech-head.equipment')->with('status', 'Equipment added successfully');
    })->name('equipment.store');

    Route::put('/equipment/{equipment}', function (\Illuminate\Http\Request $request, Equipment $equipment) {
        $data = $request->validate([
            'name' => 'required|string',
            'status' => 'nullable|in:available,in_use,maintenance,retired',
            'location' => 'nullable|string',
        ]);
        $equipment->update($data);
        return redirect()->route('tech-head.equipment')->with('status', 'Equipment updated');
    })->name('equipment.update');

    Route::delete('/equipment/{equipment}', function (Equipment $equipment) {
        $equipment->delete();
        return redirect()->route('tech-head.equipment')->with('status', 'Equipment deleted');
    })->name('equipment.destroy');

    Route::post('/equipment/{equipment}/status', function (\Illuminate\Http\Request $request, Equipment $equipment) {
        $data = $request->validate(['status' => 'required|in:available,in_use,maintenance,retired']);
        $equipment->update(['status' => $data['status']]);
        return redirect()->route('tech-head.equipment')->with('status', 'Status updated');
    })->name('equipment.status');

    Route::post('/equipment/{equipment}/location', function (\Illuminate\Http\Request $request, Equipment $equipment) {
        $data = $request->validate(['location' => 'required|string']);
        $equipment->update(['location' => $data['location']]);
        return redirect()->route('tech-head.equipment')->with('status', 'Location updated');
    })->name('equipment.location');
    
    Route::get('/maintenance', function () {
        $maintenanceTasks = Equipment::where('status', 'maintenance')
            ->orWhere('calibration_required', true)
            ->with(['maintenanceRecords' => function($query) {
                $query->latest()->limit(5);
            }])
            ->paginate(20);
        
        // Additional stats
        $recentRecords = DB::table('equipment_maintenance')
            ->whereMonth('performed_at', now()->month)
            ->whereYear('performed_at', now()->year)
            ->count();
            
        $overdueCount = Equipment::where('status', 'maintenance')
            ->where('next_maintenance', '<', now())
            ->count();
        
        return view('tech-head.maintenance', compact('maintenanceTasks', 'recentRecords', 'overdueCount'));
    })->name('maintenance');

    // Maintenance records
    Route::post('/maintenance', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'maintenance_type' => 'nullable|in:preventive,corrective,calibration,repair',
            'performed_at' => 'required|date',
            'description' => 'nullable|string',
        ]);
        DB::table('equipment_maintenance')->insert(array_merge($data, [
            'performed_by' => auth()->id(),
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]));
        return redirect()->route('tech-head.maintenance')->with('status', 'Maintenance record added');
    })->name('maintenance.store');
    
    Route::get('/schedule', function () {
        $today = now();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        
        // Get weekly schedule with filters
        $query = Assignment::with(['jobOrder.customer', 'assignedTo'])
            ->whereBetween('scheduled_date', [$weekStart, $weekEnd])
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time');
        
        // Apply filters if present
        if (request('technician')) {
            $query->where('assigned_to', request('technician'));
        }
        if (request('status')) {
            $query->where('status', request('status'));
        }
        if (request('priority')) {
            $query->where('priority', request('priority'));
        }
        
        $assignments = $query->get();
        
        $weeklySchedule = $assignments->groupBy(function($assignment) {
            return $assignment->scheduled_date->format('Y-m-d');
        });
        
        // Get unassigned jobs (jobs without assignments or with null assigned_to)
        $unassignedJobs = JobOrder::with('customer')
            ->whereDoesntHave('assignments')
            ->orWhereHas('assignments', function($q) {
                $q->whereNull('assigned_to');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get all active technicians - use same logic as technicians page
        $technicianRoleId = Role::where('slug', 'tech_personnel')->value('id');
        $technicians = User::where('role_id', $technicianRoleId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $assignmentsByTechnician = $assignments->groupBy('assigned_to');
        $technicianSchedules = $technicians->map(function ($technician) use ($assignmentsByTechnician) {
            $items = $assignmentsByTechnician->get($technician->id, collect())->values();

            return [
                'technician' => $technician,
                'assignments' => $items,
            ];
        });
        
        return view('tech-head.schedule', compact('weeklySchedule', 'today', 'weekStart', 'weekEnd', 'unassignedJobs', 'technicians', 'technicianSchedules'));
    })->name('schedule');
    
    Route::get('/analytics', function () {
        $stats = [
            'totalJobOrders' => JobOrder::count(),
            'completedJobs' => JobOrder::where('status', 'completed')->count(),
            'avgCompletionTime' => Assignment::whereNotNull('completed_at')
                ->whereNotNull('started_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, started_at, completed_at)) as avg_hours')
                ->value('avg_hours'),
            'technicianPerformance' => User::whereHas('role', function($q) {
                $q->where('slug', 'tech_personnel');
            })
                ->withCount([
                    'assignmentsAsAssignee as total_jobs',
                    'assignmentsAsAssignee as completed_jobs' => function($query) {
                        $query->where('status', 'completed');
                    }
                ])
                ->get(),
        ];
        
        return view('tech-head.analytics', compact('stats'));
    })->name('analytics');
    
    Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline');
});

// Signatory Routes
Route::middleware(['auth', 'verified', 'role:signatory'])->prefix('signatory')->name('signatory.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [SignatoryController::class, 'dashboard'])->name('dashboard');
    
    // For Review - Calibrations awaiting signature
    Route::get('/for-review', [SignatoryController::class, 'forReview'])->name('for-review');
    
    // Review workspace
    Route::get('/review/{calibration}', [SignatoryController::class, 'review'])->name('review');
    
    // Approve calibration (before signature)
    Route::post('/review/{calibration}/approve', [SignatoryController::class, 'approve'])->name('approve');
    
    // Request revision
    Route::post('/review/{calibration}/request-revision', [SignatoryController::class, 'requestRevision'])->name('request-revision');
    
    // Digital Signature workflow
    Route::get('/sign/{calibration}', [SignatoryController::class, 'signatureForm'])->name('sign.form');
    Route::post('/sign/{calibration}', [SignatoryController::class, 'sign'])->name('sign');
    
    // Signed Certificates
    Route::get('/certificates', [SignatoryController::class, 'certificates'])->name('certificates');
    Route::get('/certificates/{certificate}/preview', [SignatoryController::class, 'previewCertificate'])->name('certificate.preview');

    // Calibration Report PDF (for review and archival)
    Route::get('/calibration/{calibration}/report-pdf', [CalibrationController::class, 'reportPdf'])->name('calibration.report-pdf');
    
    // Reports - View uploaded reports
    Route::get('/reports', [SignatoryController::class, 'reports'])->name('reports');
    Route::get('/reports/{report}', [SignatoryController::class, 'viewReport'])->name('report.view');
    
    // Timeline - Unified view
    Route::get('/timelines', [SignatoryController::class, 'allTimelines'])->name('timelines');
    Route::get('/timeline/{jobOrder}', [SignatoryController::class, 'timeline'])->name('timeline');
    
    // Profile
    Route::get('/profile', [SignatoryController::class, 'profile'])->name('profile');
    Route::patch('/profile', [SignatoryController::class, 'updateProfile'])->name('profile.update');
});

// Accounting Routes
Route::middleware(['auth', 'verified', 'role:accounting'])->prefix('accounting')->name('accounting.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\AccountingController::class, 'dashboard'])->name('dashboard');
    
    // Payment Verification
    Route::get('/payments', [\App\Http\Controllers\AccountingController::class, 'payments'])->name('payments');
    Route::patch('/payments/{jobOrder}/verify', [\App\Http\Controllers\AccountingController::class, 'verifyPayment'])->name('payments.verify');
    Route::post('/payments/{jobOrder}/mark-paid', [\App\Http\Controllers\AccountingController::class, 'markAsPaid'])->name('payments.mark-paid');
    
    // Certificates For Release
    Route::get('/certificates/for-release', [\App\Http\Controllers\AccountingController::class, 'certificatesForRelease'])->name('certificates.for-release');
    Route::get('/certificates/{certificate}/preview', [\App\Http\Controllers\AccountingController::class, 'previewCertificate'])->name('certificates.preview');
    Route::patch('/certificates/{certificate}/release', [\App\Http\Controllers\AccountingController::class, 'releaseCertificate'])->name('certificates.release');
    Route::patch('/certificates/{certificate}/hold', [\App\Http\Controllers\AccountingController::class, 'holdCertificate'])->name('certificates.hold');
    
    // Released Certificates (History)
    Route::get('/certificates/released', [\App\Http\Controllers\AccountingController::class, 'releasedCertificates'])->name('certificates.released');
    Route::get('/certificates/{certificate}/download', [\App\Http\Controllers\AccountingController::class, 'downloadCertificate'])->name('certificates.download');
    
    // Timeline (Read-only)
    Route::get('/timelines', [\App\Http\Controllers\AccountingController::class, 'allTimelines'])->name('timelines');
    Route::get('/timeline/{jobOrder}', [\App\Http\Controllers\AccountingController::class, 'timeline'])->name('timeline');
    
    // Reports Export
    Route::get('/reports', [\App\Http\Controllers\AccountingController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [\App\Http\Controllers\AccountingController::class, 'exportReports'])->name('reports.export');
    
    // Invoices (unified with admin)
    Route::get('/invoices', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices');
    Route::post('/invoices', [\App\Http\Controllers\InvoiceController::class, 'store'])->name('invoices.store');
    Route::patch('/invoices/{invoice}/pay', [\App\Http\Controllers\InvoiceController::class, 'markAsPaid'])->name('invoices.pay');
    Route::delete('/invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'destroy'])->name('invoices.destroy');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // SYSTEM ADMINISTRATION SECTION (Admin-only pages)
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::post('/users/{user}/deactivate', [AdminController::class, 'deactivateUser'])->name('users.deactivate');
    
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    Route::post('/equipment', [EquipmentController::class, 'store'])->name('equipment.store');
    Route::post('/equipment/{equipment}/calibrate', [EquipmentController::class, 'calibrate'])->name('equipment.calibrate');
    Route::delete('/equipment/{equipment}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');
    
    Route::get('/inventory', [InventoryController::class, 'adminIndex'])->name('inventory.index');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('/inventory/{inventoryItem}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{inventoryItem}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::patch('/inventory/requests/{inventoryRequest}', [InventoryController::class, 'updateRequestStatus'])->name('inventory.requests.update');
    
    Route::get('/accounting', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('accounting.index');
    Route::post('/accounting/invoices', [\App\Http\Controllers\InvoiceController::class, 'store'])->name('invoices.store');
    Route::patch('/accounting/invoices/{invoice}/pay', [\App\Http\Controllers\InvoiceController::class, 'markAsPaid'])->name('invoices.pay');
    Route::delete('/accounting/invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'destroy'])->name('invoices.destroy');
    
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/general', [\App\Http\Controllers\SettingsController::class, 'updateGeneral'])->name('settings.general.update');
    Route::post('/settings/backup/create', [\App\Http\Controllers\SettingsController::class, 'createBackup'])->name('settings.backup.create');
    Route::get('/settings/backup/download/{filename}', [\App\Http\Controllers\SettingsController::class, 'downloadBackup'])->name('settings.backup.download');
    Route::delete('/settings/backup/delete/{filename}', [\App\Http\Controllers\SettingsController::class, 'deleteBackup'])->name('settings.backup.delete');
    Route::post('/settings/backup/restore', [\App\Http\Controllers\SettingsController::class, 'restoreBackup'])->name('settings.backup.restore');
    Route::post('/settings/security', [\App\Http\Controllers\SettingsController::class, 'updateSecurity'])->name('settings.security.update');
    Route::post('/settings/cache/clear', [\App\Http\Controllers\SettingsController::class, 'clearCache'])->name('settings.cache.clear');
    Route::post('/settings/database/optimize', [\App\Http\Controllers\SettingsController::class, 'optimizeDatabase'])->name('settings.database.optimize');
    Route::post('/settings/logs/clear', [\App\Http\Controllers\SettingsController::class, 'clearLogs'])->name('settings.logs.clear');
    Route::post('/settings/sessions/terminate', [\App\Http\Controllers\SettingsController::class, 'terminateAllSessions'])->name('settings.sessions.terminate');
    
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    
    // Workflow Tracking
    Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
