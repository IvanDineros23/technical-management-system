@extends('layouts.dashboard')

@section('title', 'Technician Dashboard')

@section('page-title', 'Technician Dashboard')
@section('page-subtitle', 'Manage your assignments and job orders')

@section('head')
    <script>
        function technicianDashboard() {
            return {
                showJobDetails: false,
                selectedJob: null,
                isLoading: false,
                init() {
                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.showJobDetails) {
                            this.closeJobDetails();
                        }
                    });
                },
                openJobDetails(job) {
                    this.isLoading = true;
                    this.showJobDetails = true;
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => {
                        this.selectedJob = job;
                        this.isLoading = false;
                    }, 400);
                },
                closeJobDetails() {
                    this.showJobDetails = false;
                    this.selectedJob = null;
                    this.isLoading = false;
                    document.body.style.overflow = 'auto';
                },
                formatDate(d) {
                    if (!d) return 'N/A';
                    const dt = new Date(d);
                    return isNaN(dt) ? d : dt.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
                },
                getStatusColor(status) {
                    const colors = {
                        'assigned': 'blue',
                        'in_progress': 'yellow',
                        'on_hold': 'orange',
                        'completed': 'green',
                        'pending': 'gray'
                    };
                    return colors[status] || 'gray';
                },
                getStatusLabel(status) {
                    const labels = {
                        'assigned': 'Assigned',
                        'in_progress': 'In Progress',
                        'on_hold': 'On Hold',
                        'completed': 'Completed',
                        'pending': 'Waiting for Assignment'
                    };
                    return labels[status] || status;
                }
            }
        }
    </script>
@endsection

@section('sidebar-nav')
    <a href="{{ route('technician.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('technician.assignments') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        My Assignments
    </a>

    <a href="{{ route('technician.work-orders') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Job Orders
    </a>

    <a href="{{ route('technician.equipment') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
        </svg>
        Equipment
    </a>

    <a href="{{ route('technician.reports') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Reports
    </a>

    <a href="{{ route('technician.certificates') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
        </svg>
        Certificates
    </a>

    <a href="{{ route('technician.calendar') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Calendar
    </a>

    <a href="{{ route('technician.timeline') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Timeline
    </a>
@endsection

@section('content')
    <div x-data="technicianDashboard()">
    <!-- ===================== STATS (HORIZONTAL, EQUAL) ===================== -->
    <div class="mb-6 sm:mb-8">
        <div class="flex gap-2 sm:gap-4 overflow-x-auto pb-2 sm:pb-1">

            <!-- Today's Assignments -->
            <div class="flex-1 min-w-[160px] sm:min-w-[220px] bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-900/20
                        rounded-[20px] shadow-md p-3 sm:p-6 border border-purple-200 dark:border-purple-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-purple-600 dark:text-purple-400 font-semibold mb-2 sm:mb-3">Today's Assignments</p>
                    <h3 class="text-2xl sm:text-4xl font-bold text-purple-900 dark:text-purple-100 mb-2 sm:mb-3">{{ $todayAssignments }}</h3>
                    <p class="text-xs text-purple-600 dark:text-purple-400 mt-auto">New today</p>
                </div>
            </div>

            <!-- Waiting for Assignment -->
            <div class="flex-1 min-w-[160px] sm:min-w-[220px] bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-900/20
                        rounded-[20px] shadow-md p-3 sm:p-6 border border-blue-200 dark:border-blue-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold mb-2 sm:mb-3">Waiting for Assignment</p>
                    <h3 class="text-2xl sm:text-4xl font-bold text-blue-900 dark:text-blue-100 mb-2 sm:mb-3">{{ $pendingJobs }}</h3>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-auto">Awaiting assignment</p>
                </div>
            </div>

            <!-- In Progress -->
            <div class="flex-1 min-w-[160px] sm:min-w-[220px] bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/30 dark:to-yellow-900/20
                        rounded-[20px] shadow-md p-3 sm:p-6 border border-yellow-200 dark:border-yellow-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-yellow-600 dark:text-yellow-400 font-semibold mb-2 sm:mb-3">In Progress</p>
                    <h3 class="text-2xl sm:text-4xl font-bold text-yellow-900 dark:text-yellow-100 mb-2 sm:mb-3">{{ $inProgressJobs }}</h3>
                    <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-auto">Active work</p>
                </div>
            </div>

            <!-- Completed -->
            <div class="flex-1 min-w-[160px] sm:min-w-[220px] bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-900/20
                        rounded-[20px] shadow-md p-3 sm:p-6 border border-green-200 dark:border-green-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-green-600 dark:text-green-400 font-semibold mb-2 sm:mb-3">Completed</p>
                    <h3 class="text-2xl sm:text-4xl font-bold text-green-900 dark:text-green-100 mb-2 sm:mb-3">{{ $completedJobs }}</h3>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-auto">All time</p>
                </div>
            </div>

        </div>
    </div>

    <!-- ===================== ALERTS & RECENT ASSIGNMENTS ===================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <!-- Alerts -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-4 sm:p-6 h-full flex flex-col">
                <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Alerts
                </h3>
                <div class="space-y-2 sm:space-y-3 flex-1">
                    @if($overdueAssignments->count() > 0)
                        <div>
                            <p class="text-xs font-semibold text-red-600 dark:text-red-400 mb-2">Overdue</p>
                            <div class="space-y-2">
                                @foreach($overdueAssignments as $assignment)
                                    <div class="p-2 sm:p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/40">
                                        <p class="text-xs sm:text-sm font-semibold text-red-900 dark:text-red-100">
                                            {{ $assignment->jobOrder->job_order_number ?? 'Job Order' }}
                                        </p>
                                        <p class="text-xs text-red-700 dark:text-red-300">
                                            {{ $assignment->jobOrder->customer->name ?? 'N/A' }}
                                        </p>
                                        @if($assignment->scheduled_date)
                                            <p class="text-xs text-red-600 dark:text-red-300">Due: {{ \Carbon\Carbon::parse($assignment->scheduled_date)->setTimezone('Asia/Manila')->format('M d, Y') }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($dueTodayAssignments->count() > 0)
                        <div>
                            <p class="text-xs font-semibold text-yellow-600 dark:text-yellow-400 mb-2">Due Today</p>
                            <div class="space-y-2">
                                @foreach($dueTodayAssignments as $assignment)
                                    <div class="p-2 sm:p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800/40">
                                        <p class="text-xs sm:text-sm font-semibold text-yellow-900 dark:text-yellow-100">
                                            {{ $assignment->jobOrder->job_order_number ?? 'Job Order' }}
                                        </p>
                                        <p class="text-xs text-yellow-700 dark:text-yellow-300">
                                            {{ $assignment->jobOrder->customer->name ?? 'N/A' }}
                                        </p>
                                        @if($assignment->scheduled_time)
                                            <p class="text-xs text-yellow-600 dark:text-yellow-300">Time: {{ \Carbon\Carbon::parse($assignment->scheduled_time)->format('h:i A') }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(($newAssignments ?? collect())->count() > 0)
                        <div>
                            <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 mb-2">New Assignments</p>
                            <div class="space-y-2">
                                @foreach($newAssignments as $assignment)
                                    <div class="p-2 sm:p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/40">
                                        <p class="text-xs sm:text-sm font-semibold text-blue-900 dark:text-blue-100">
                                            {{ $assignment->jobOrder->job_order_number ?? 'Job Order' }}
                                        </p>
                                        <p class="text-xs text-blue-700 dark:text-blue-300">
                                            {{ $assignment->jobOrder->customer->name ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-blue-600 dark:text-blue-300">Assigned: {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('M d, Y') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(($onHoldAssignments ?? collect())->count() > 0)
                        <div>
                            <p class="text-xs font-semibold text-orange-600 dark:text-orange-400 mb-2">On Hold</p>
                            <div class="space-y-2">
                                @foreach($onHoldAssignments as $assignment)
                                    <div class="p-2 sm:p-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800/40">
                                        <p class="text-xs sm:text-sm font-semibold text-orange-900 dark:text-orange-100">
                                            {{ $assignment->jobOrder->job_order_number ?? 'Job Order' }}
                                        </p>
                                        <p class="text-xs text-orange-700 dark:text-orange-300">
                                            {{ $assignment->jobOrder->customer->name ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-orange-600 dark:text-orange-300">Status: On Hold</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(($highPriorityAssignments ?? collect())->count() > 0)
                        <div>
                            <p class="text-xs font-semibold text-red-600 dark:text-red-400 mb-2">High Priority</p>
                            <div class="space-y-2">
                                @foreach($highPriorityAssignments as $assignment)
                                    <div class="p-2 sm:p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/40">
                                        <p class="text-xs sm:text-sm font-semibold text-red-900 dark:text-red-100">
                                            {{ $assignment->jobOrder->job_order_number ?? 'Job Order' }}
                                        </p>
                                        <p class="text-xs text-red-700 dark:text-red-300">
                                            {{ $assignment->jobOrder->customer->name ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-red-600 dark:text-red-300">Priority: {{ ucfirst($assignment->priority) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($overdueAssignments->count() === 0 && $dueTodayAssignments->count() === 0 && ($newAssignments ?? collect())->count() === 0 && ($onHoldAssignments ?? collect())->count() === 0 && ($highPriorityAssignments ?? collect())->count() === 0)
                        <div class="text-center py-4 flex items-center justify-center">
                            <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm">No alerts at the moment</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Assignments -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-4 sm:p-6 h-full flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">Recent Assignments</h3>
                    <a href="{{ route('technician.assignments') }}" 
                       class="text-xs sm:text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        View All →
                    </a>
                </div>

                @if($recentAssignments->count() > 0)
                    <div class="sm:hidden space-y-3 flex-1">
                        @foreach($recentAssignments as $assignment)
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-3 hover:border-blue-300 dark:hover:border-blue-500 transition-colors">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $assignment->jobOrder->job_order_number ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $assignment->jobOrder->customer->name ?? 'N/A' }}</p>
                                    </div>
                                    <span :class="{
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300': '{{ $assignment->status }}' === 'assigned',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300': '{{ $assignment->status }}' === 'in_progress',
                                        'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': '{{ $assignment->status }}' === 'completed',
                                        'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300': '{{ $assignment->status }}' === 'on_hold'
                                    }" class="px-2 py-1 text-xs font-medium rounded-full">
                                        {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $assignment->assigned_at ? $assignment->assigned_at->setTimezone('Asia/Manila')->format('M d, Y') : $assignment->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}</p>
                                <button @click='openJobDetails({{ json_encode([
                                    "id" => $assignment->jobOrder->id ?? null,
                                    "job_order_number" => $assignment->jobOrder->job_order_number ?? "N/A",
                                    "customer" => $assignment->jobOrder->customer->name ?? "N/A",
                                    "service_type" => $assignment->jobOrder->service_type ?? "N/A",
                                    "service_description" => $assignment->jobOrder->service_description ?? ($assignment->jobOrder->description ?? "No description"),
                                    "service_address" => $assignment->jobOrder->service_address ?? "N/A",
                                    "priority" => $assignment->priority ?? ($assignment->jobOrder->priority ?? "normal"),
                                    "status" => $assignment->status,
                                    "scheduled_date" => $assignment->scheduled_date ? $assignment->scheduled_date->setTimezone("Asia/Manila")->format("M d, Y") : (($assignment->jobOrder && $assignment->jobOrder->expected_start_date) ? $assignment->jobOrder->expected_start_date->setTimezone("Asia/Manila")->format("M d, Y") : "Not scheduled"),
                                    "scheduled_time" => "--",
                                    "notes" => $assignment->jobOrder->notes ?? "No notes",
                                    "created_at" => ($assignment->assigned_at ?? $assignment->created_at)->setTimezone("Asia/Manila")->format("M d, Y h:i A")
                                ]) }})' class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-medium">
                                    View Details
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div class="hidden sm:block overflow-x-auto flex-1">
                        <table class="w-full">
                            <thead class="border-b-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50">
                                <tr class="text-left">
                                    <th class="px-4 py-4 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Job Order</th>
                                    <th class="px-4 py-4 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Customer</th>
                                    <th class="px-4 py-4 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Status</th>
                                    <th class="px-4 py-4 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Date</th>
                                    <th class="px-4 py-4 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($recentAssignments as $assignment)
                                <tr class="hover:bg-blue-50 dark:hover:bg-gray-700/80 transition-colors">
                                    <td class="px-4 py-4">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $assignment->jobOrder->job_order_number ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($assignment->jobOrder->description ?? 'No description', 35) }}</p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $assignment->jobOrder->customer->name ?? 'N/A' }}</p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span :class="{
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300': '{{ $assignment->status }}' === 'assigned',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300': '{{ $assignment->status }}' === 'in_progress',
                                            'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300': '{{ $assignment->status }}' === 'completed',
                                            'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300': '{{ $assignment->status }}' === 'on_hold',
                                            'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-300': !['assigned','in_progress','completed','on_hold'].includes('{{ $assignment->status }}')
                                        }" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full">
                                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignment->assigned_at ? $assignment->assigned_at->setTimezone('Asia/Manila')->format('M d, Y') : $assignment->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}</p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <button @click='openJobDetails({{ json_encode([
                                            "id" => $assignment->jobOrder->id ?? null,
                                            "job_order_number" => $assignment->jobOrder->job_order_number ?? "N/A",
                                            "customer" => $assignment->jobOrder->customer->name ?? "N/A",
                                            "service_type" => $assignment->jobOrder->service_type ?? "N/A",
                                            "service_description" => $assignment->jobOrder->service_description ?? ($assignment->jobOrder->description ?? "No description"),
                                            "service_address" => $assignment->jobOrder->service_address ?? "N/A",
                                            "priority" => $assignment->priority ?? ($assignment->jobOrder->priority ?? "normal"),
                                            "status" => $assignment->status,
                                            "scheduled_date" => $assignment->scheduled_date ? $assignment->scheduled_date->setTimezone("Asia/Manila")->format("M d, Y") : (($assignment->jobOrder && $assignment->jobOrder->expected_start_date) ? $assignment->jobOrder->expected_start_date->setTimezone("Asia/Manila")->format("M d, Y") : "Not scheduled"),
                                            "scheduled_time" => "--",
                                            "notes" => $assignment->jobOrder->notes ?? "No notes",
                                            "created_at" => ($assignment->assigned_at ?? $assignment->created_at)->setTimezone("Asia/Manila")->format("M d, Y h:i A")
                                        ]) }})' 
                                           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 dark:hover:bg-blue-500 transition-colors">
                                            View
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12 flex-1 flex items-center justify-center">
                        <div>
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-4 text-gray-500 dark:text-gray-400">No assignments found</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6 sm:mb-8">
        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 sm:gap-4">
            <a href="{{ route('technician.assignments') }}" 
               class="p-3 sm:p-4 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-xl transition-colors border border-blue-200 dark:border-blue-800 flex flex-col items-center">
                <svg class="w-6 sm:w-8 h-6 sm:h-8 text-blue-600 dark:text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <p class="text-xs sm:text-sm font-semibold text-blue-900 dark:text-blue-100 text-center">Start Work</p>
            </a>
            <a href="{{ route('technician.reports') }}" 
               class="p-3 sm:p-4 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-xl transition-colors border border-green-200 dark:border-green-800 flex flex-col items-center">
                <svg class="w-6 sm:w-8 h-6 sm:h-8 text-green-600 dark:text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-xs sm:text-sm font-semibold text-green-900 dark:text-green-100 text-center">Submit Report</p>
            </a>
            <a href="{{ route('technician.calendar') }}" 
               class="p-3 sm:p-4 bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-xl transition-colors border border-purple-200 dark:border-purple-800 flex flex-col items-center">
                <svg class="w-6 sm:w-8 h-6 sm:h-8 text-purple-600 dark:text-purple-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-xs sm:text-sm font-semibold text-purple-900 dark:text-purple-100 text-center">View Schedule</p>
            </a>
            <a href="{{ route('technician.work-orders') }}" 
               class="p-3 sm:p-4 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30 rounded-xl transition-colors border border-amber-200 dark:border-amber-800 flex flex-col items-center">
                <svg class="w-6 sm:w-8 h-6 sm:h-8 text-amber-600 dark:text-amber-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-xs sm:text-sm font-semibold text-amber-900 dark:text-amber-100 text-center">Job Orders</p>
            </a>
            <a href="{{ route('technician.equipment') }}" 
               class="p-3 sm:p-4 bg-teal-50 dark:bg-teal-900/20 hover:bg-teal-100 dark:hover:bg-teal-900/30 rounded-xl transition-colors border border-teal-200 dark:border-teal-800 flex flex-col items-center">
                <svg class="w-6 sm:w-8 h-6 sm:h-8 text-teal-600 dark:text-teal-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                </svg>
                <p class="text-xs sm:text-sm font-semibold text-teal-900 dark:text-teal-100 text-center">Equipment</p>
            </a>
            <a href="{{ route('technician.timeline') }}" 
               class="p-3 sm:p-4 bg-slate-50 dark:bg-slate-900/20 hover:bg-slate-100 dark:hover:bg-slate-900/30 rounded-xl transition-colors border border-slate-200 dark:border-slate-700 flex flex-col items-center">
                <svg class="w-6 sm:w-8 h-6 sm:h-8 text-slate-600 dark:text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-slate-100 text-center">Timeline</p>
            </a>
        </div>
    </div>

    <!-- Job Details Modal -->
    <div 
        x-show="showJobDetails" 
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
    >
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeJobDetails()"></div>
        <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
            <div 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
            >
                <div class="p-4 sm:p-6">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4 mb-4 sm:mb-6">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">Job Order Details</h3>
                        <button @click="closeJobDetails()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Modal Content -->
                    <div class="space-y-4 sm:space-y-6 min-h-[400px]">
                        <!-- Loading Spinner -->
                        <div x-show="isLoading" class="flex items-center justify-center h-96">
                            <div class="relative w-12 h-12">
                                <div class="absolute inset-0 rounded-full border-4 border-gray-300 dark:border-gray-600"></div>
                                <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-blue-600 dark:border-t-blue-400 animate-spin"></div>
                            </div>
                        </div>

                        <!-- Details Content -->
                        <div x-show="!isLoading" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="space-y-4 sm:space-y-6">
                        <!-- Job Order Info -->
                        <div class="grid grid-cols-2 gap-2 sm:gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Job Order Number</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedJob?.job_order_number"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                                <span x-text="getStatusLabel(selectedJob?.status)" :class="{
                                    'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300': selectedJob?.status === 'pending',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300': selectedJob?.status === 'assigned',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300': selectedJob?.status === 'in_progress',
                                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': selectedJob?.status === 'completed',
                                    'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300': selectedJob?.status === 'on_hold'
                                }" class="inline-block px-2 py-1 text-xs font-medium rounded-full capitalize"></span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Customer</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedJob?.customer"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Priority</p>
                                <span x-text="selectedJob?.priority" :class="{
                                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300': selectedJob?.priority === 'urgent',
                                    'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300': selectedJob?.priority === 'high',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300': selectedJob?.priority === 'normal',
                                    'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300': selectedJob?.priority === 'low'
                                }" class="inline-block px-2 py-1 text-xs font-medium rounded-full capitalize"></span>
                            </div>
                        </div>

                        <!-- Service Details -->
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Service Type</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedJob?.service_type"></p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Service Description</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selectedJob?.service_description"></p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Service Address</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selectedJob?.service_address"></p>
                        </div>

                        <!-- Schedule Info -->
                        <div class="grid grid-cols-2 gap-2 sm:gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Scheduled Date</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedJob?.scheduled_date"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Scheduled Time</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedJob?.scheduled_time"></p>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notes</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selectedJob?.notes || 'No notes'"></p>
                        </div>

                        <!-- Created At -->
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Created At</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400" x-text="selectedJob?.created_at"></p>
                        </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="flex justify-end gap-3 pt-4 sm:pt-6 border-t border-gray-200 dark:border-gray-700 mt-4 sm:mt-6">
                        <button @click="closeJobDetails()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection
