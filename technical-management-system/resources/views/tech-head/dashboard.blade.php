@extends('layouts.dashboard')

@section('title', 'Technical Head Dashboard')

@section('page-title', 'Technical Head Dashboard')
@section('page-subtitle', 'Team and operations overview')

@section('head')
    <script>
        function techHeadDashboard() {
            return {
                showWorkOrderDetails: false,
                selectedWorkOrder: null,
                init() {
                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.showWorkOrderDetails) {
                            this.closeWorkOrderDetails();
                        }
                    });
                    
                    // Scroll to timeline if pagination parameter exists
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('timeline_page')) {
                        setTimeout(() => {
                            document.getElementById('activity-timeline')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 100);
                    }
                },
                openWorkOrderDetails(order) {
                    this.selectedWorkOrder = order;
                    this.showWorkOrderDetails = true;
                    document.body.style.overflow = 'hidden';
                },
                closeWorkOrderDetails() {
                    this.showWorkOrderDetails = false;
                    this.selectedWorkOrder = null;
                    document.body.style.overflow = 'auto';
                },
                formatDate(d) {
                    if (!d) return 'N/A';
                    const dt = new Date(d);
                    return isNaN(dt) ? d : dt.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
                },
                getStatusColor(status) {
                    const colors = {
                        'pending': 'amber',
                        'approved': 'emerald',
                        'in_progress': 'blue',
                        'on_hold': 'orange',
                        'completed': 'green',
                        'overdue': 'red'
                    };
                    return colors[status] || 'gray';
                },
                getStatusLabel(status) {
                    const labels = {
                        'pending': 'Pending',
                        'approved': 'Approved',
                        'in_progress': 'In Progress',
                        'on_hold': 'On Hold',
                        'completed': 'Completed',
                        'overdue': 'Overdue'
                    };
                    return labels[status] || status;
                }
            }
        }
    </script>
@endsection

@section('sidebar-nav')
    @include('tech-head.partials.sidebar')
@endsection

@section('sidebar-nav')
    <a href="{{ route('tech-head.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>


@section('sidebar-nav')
    <a href="{{ route('tech-head.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('tech-head.work-orders') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Work Orders
    </a>

    <a href="{{ route('tech-head.technicians') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Technicians
    </a>

    <a href="{{ route('tech-head.reports') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Reports & Approvals
    </a>

    <a href="{{ route('tech-head.equipment') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
        </svg>
        Equipment
    </a>

    <a href="{{ route('tech-head.calibration-approvals') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Calibration Approvals
    </a>

    <a href="{{ route('tech-head.maintenance') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Maintenance Tasks
    </a>

    <a href="{{ route('tech-head.schedule') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Schedule / Calendar
    </a>

    <a href="{{ route('tech-head.timeline') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Activity Timeline
    </a>

    <a href="{{ route('tech-head.analytics') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Analytics & Reports
    </a>
@endsection


@section('content')
    <div x-data="techHeadDashboard()">
    <!-- ===================== SUMMARY CARDS ===================== -->
    <div class="mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Active Work Orders -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-900/20 rounded-[20px] shadow-md p-5 border border-blue-200 dark:border-blue-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold mb-2">Active Work Orders</p>
                    <h3 class="text-3xl font-bold text-blue-900 dark:text-blue-100 mb-2">{{ $summary['activeWorkOrders'] }}</h3>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-auto">Approved + Pending + In Progress</p>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/30 dark:to-amber-900/20 rounded-[20px] shadow-md p-5 border border-amber-200 dark:border-amber-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-amber-600 dark:text-amber-400 font-semibold mb-2">Pending Approvals</p>
                    <h3 class="text-3xl font-bold text-amber-900 dark:text-amber-100 mb-2">{{ $summary['pendingApprovals'] }}</h3>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-auto">Awaiting review</p>
                </div>
            </div>

            <!-- Overdue Jobs -->
            <div class="bg-gradient-to-br from-rose-50 to-rose-100 dark:from-rose-900/30 dark:to-rose-900/20 rounded-[20px] shadow-md p-5 border border-rose-200 dark:border-rose-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold mb-2">Overdue Jobs</p>
                    <h3 class="text-3xl font-bold text-rose-900 dark:text-rose-100 mb-2">{{ $summary['overdueJobs'] }}</h3>
                    <p class="text-xs text-rose-600 dark:text-rose-400 mt-auto">Past deadline</p>
                </div>
            </div>

            <!-- Completed Today -->
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/30 dark:to-emerald-900/20 rounded-[20px] shadow-md p-5 border border-emerald-200 dark:border-emerald-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mb-2">Completed Today</p>
                    <h3 class="text-3xl font-bold text-emerald-900 dark:text-emerald-100 mb-2">{{ $summary['completedToday'] }}</h3>
                    <p class="text-xs text-emerald-600 dark:text-emerald-200 mt-auto">{{ $summary['completedThisWeek'] }} this week</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== ALERTS & CRITICAL ISSUES ===================== -->
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Alerts & Critical Issues
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Overdue Work Orders -->
                <div class="rounded-lg bg-rose-50 dark:bg-rose-900/20 border-l-4 border-rose-500 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-semibold text-rose-900 dark:text-rose-100">Overdue Work Orders</p>
                        <span class="inline-block px-2.5 py-0.5 bg-rose-600 text-white text-xs font-bold rounded-full">{{ $overdueWorkOrders->count() }}</span>
                    </div>
                    @forelse($overdueWorkOrders->take(3) as $job)
                        <div class="mb-2 pb-2 border-b border-rose-200 dark:border-rose-800/30 last:border-0">
                            <p class="text-xs font-medium text-rose-800 dark:text-rose-200">{{ $job->job_order_number }}</p>
                            <p class="text-xs text-rose-700 dark:text-rose-300">{{ $job->customer->name ?? 'N/A' }}</p>
                            @if($job->required_date)
                                <p class="text-xs text-rose-600 dark:text-rose-400">Due: {{ $job->required_date->setTimezone('Asia/Manila')->format('M d, Y') }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-rose-700 dark:text-rose-300">No overdue orders</p>
                    @endforelse
                </div>

                <!-- Unassigned Jobs -->
                <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">Jobs Without Technician</p>
                        <span class="inline-block px-2.5 py-0.5 bg-amber-600 text-white text-xs font-bold rounded-full">{{ $unassignedJobs->count() }}</span>
                    </div>
                    @forelse($unassignedJobs->take(3) as $job)
                        <div class="mb-2 pb-2 border-b border-amber-200 dark:border-amber-800/30 last:border-0">
                            <p class="text-xs font-medium text-amber-800 dark:text-amber-200">{{ $job->job_order_number }}</p>
                            <p class="text-xs text-amber-700 dark:text-amber-300">{{ $job->customer->name ?? 'N/A' }}</p>
                            <p class="text-xs text-amber-600 dark:text-amber-400">{{ ucfirst($job->priority) }} Priority</p>
                        </div>
                    @empty
                        <p class="text-xs text-amber-700 dark:text-amber-300">All jobs assigned</p>
                    @endforelse
                </div>

                <!-- Aging Pending Approvals -->
                <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">Pending Approvals (Aging)</p>
                        <span class="inline-block px-2.5 py-0.5 bg-blue-600 text-white text-xs font-bold rounded-full">{{ $agingPendingApprovals->count() }}</span>
                    </div>
                    @forelse($agingPendingApprovals->take(3) as $job)
                        <div class="mb-2 pb-2 border-b border-blue-200 dark:border-blue-800/30 last:border-0">
                            <p class="text-xs font-medium text-blue-800 dark:text-blue-200">{{ $job->job_order_number }}</p>
                            <p class="text-xs text-blue-700 dark:text-blue-300">{{ $job->customer->name ?? 'N/A' }}</p>
                            @php
                                $days = now()->setTimezone('Asia/Manila')->diffInDays($job->request_date);
                            @endphp
                            <p class="text-xs text-blue-600 dark:text-blue-400">Pending {{ $days }} days</p>
                        </div>
                    @empty
                        <p class="text-xs text-blue-700 dark:text-blue-300">No pending approvals</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== TECHNICIAN OVERVIEW & WORK ORDERS ===================== -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <!-- Technician Overview -->
        <div class="xl:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6 h-full">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Technician Overview</h3>
                    <a href="{{ route('tech-head.technicians') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($technicianOverview as $tech)
                        @php
                            $assignment = $tech['activeAssignment'] ?? null;
                            $stats = $tech['stats'] ?? null;
                            $statusLabel = $assignment ? 'On Task' : 'Available';
                            $statusColor = $assignment ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200';
                        @endphp
                        <div class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30 p-3">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $tech['user']->name }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
                                </div>
                                <a href="{{ route('tech-head.technicians') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">View</a>
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1 mt-2">
                                <p><span class="font-medium">Current:</span> {{ $assignment?->jobOrder?->job_order_number ?? 'No active job' }}</p>
                                <div class="flex gap-4 pt-1">
                                    <p><span class="font-medium">Today:</span> {{ $stats->jobs_today ?? 0 }}</p>
                                    <p><span class="font-medium">Overdue:</span> {{ $stats->overdue_jobs ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No technicians found</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Work Orders Overview -->
        <div class="xl:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6 h-full">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Work Orders Overview</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('tech-head.dashboard', ['status' => '']) }}" class="px-3 py-1 text-xs font-medium rounded-full {{ empty($statusFilter) ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">All</a>
                        <a href="{{ route('tech-head.dashboard', ['status' => 'pending']) }}" class="px-3 py-1 text-xs font-medium rounded-full {{ $statusFilter === 'pending' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">Pending</a>
                        <a href="{{ route('tech-head.dashboard', ['status' => 'overdue']) }}" class="px-3 py-1 text-xs font-medium rounded-full {{ $statusFilter === 'overdue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">Overdue</a>
                        <a href="{{ route('tech-head.dashboard', ['status' => 'high_priority']) }}" class="px-3 py-1 text-xs font-medium rounded-full {{ $statusFilter === 'high_priority' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">High Priority</a>
                    </div>
                </div>
                @if($workOrders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-gray-200 dark:border-gray-700">
                                <tr class="text-left text-xs text-gray-600 dark:text-gray-400">
                                    <th class="pb-2 font-semibold">Work Order</th>
                                    <th class="pb-2 font-semibold">Client</th>
                                    <th class="pb-2 font-semibold">Type</th>
                                    <th class="pb-2 font-semibold">Technician</th>
                                    <th class="pb-2 font-semibold">Status</th>
                                    <th class="pb-2 font-semibold">Priority</th>
                                    <th class="pb-2 font-semibold">Due</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($workOrders->take(6) as $job)
                                @php
                                    $assignment = $assignmentsByJob[$job->id] ?? null;
                                    $statusColors = [
                                        'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                        'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                        'completed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                                        'on_hold' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300'
                                    ];
                                    $priorityColors = [
                                        'urgent' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200',
                                        'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-200',
                                        'normal' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200',
                                        'low' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                    ];
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-2 text-gray-900 dark:text-white font-medium">{{ $job->job_order_number }}</td>
                                    <td class="py-2 text-gray-900 dark:text-white">{{ $job->customer->name ?? 'N/A' }}</td>
                                    <td class="py-2 text-gray-700 dark:text-gray-300">{{ Str::limit($job->service_type ?? '—', 15) }}</td>
                                    <td class="py-2 text-gray-700 dark:text-gray-300">{{ $assignment?->assignedTo?->name ?? 'Unassigned' }}</td>
                                    <td class="py-2">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $statusColors[$job->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200' }}">
                                            {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-2">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $priorityColors[$job->priority ?? 'normal'] ?? $priorityColors['normal'] }}">
                                            {{ ucfirst($job->priority ?? 'Normal') }}
                                        </span>
                                    </td>
                                    <td class="py-2 text-gray-600 dark:text-gray-400">{{ (($job->required_date ?? $job->expected_completion_date)?->setTimezone('Asia/Manila')->format('M d')) ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-gray-500 dark:text-gray-400 py-8">No work orders found</p>
                @endif
            </div>
        </div>
    </div>

    <!-- ===================== ACTIVITY TIMELINE & SCHEDULE ===================== -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        <!-- Unified Activity Timeline -->
        <div x-data="{ 
            loading: false,
            loadPage(url) {
                this.loading = true;
                const urlObj = new URL(url);
                const timelinePage = urlObj.searchParams.get('timeline_page');
                urlObj.searchParams.delete('timeline_page');
                urlObj.searchParams.set('timeline_page', timelinePage);
                window.location.href = urlObj.toString();
            }
        }" class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6" id="activity-timeline">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Unified Activity Timeline</h3>
                <a href="{{ route('tech-head.timeline') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">View All</a>
            </div>
            <div class="space-y-3" :class="{ 'opacity-50': loading }">
                @forelse($activityTimeline as $event)
                    <div class="flex gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 p-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 {{ $event['type'] === 'job_created' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-200' : ($event['type'] === 'technician_assigned' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-200' : ($event['type'] === 'job_completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300')) }}">
                            <span class="text-xs font-bold">{{ Str::upper(substr($event['type'], 0, 2)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $event['title'] }}</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ optional($event['timestamp'])->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ optional($event['job'])->job_order_number ?? 'Work order' }} • {{ optional(optional($event['job'])->customer)->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">No recent activity</p>
                @endforelse
            </div>
            
            @if($activityTimeline->hasPages())
                <div class="mt-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        Showing {{ $activityTimeline->firstItem() ?? 0 }} to {{ $activityTimeline->lastItem() ?? 0 }} of {{ $activityTimeline->total() }} activities
                    </div>
                    <div class="flex gap-2">
                        @if($activityTimeline->onFirstPage())
                            <span class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 rounded-lg cursor-not-allowed">Previous</span>
                        @else
                            <button @click="loadPage('{{ $activityTimeline->previousPageUrl() }}')" class="px-3 py-1 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700">Previous</button>
                        @endif
                        
                        <span class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg">
                            Page {{ $activityTimeline->currentPage() }} of {{ $activityTimeline->lastPage() }}
                        </span>
                        
                        @if($activityTimeline->hasMorePages())
                            <button @click="loadPage('{{ $activityTimeline->nextPageUrl() }}')" class="px-3 py-1 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700">Next</button>
                        @else
                            <span class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 rounded-lg cursor-not-allowed">Next</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Schedule Snapshot -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Schedule Snapshot</h3>
            <div class="space-y-3">
                @php
                    $scheduleBlocks = [
                        'Today' => $schedule['today'] ?? collect(),
                        'Tomorrow' => $schedule['tomorrow'] ?? collect(),
                        'Upcoming' => $schedule['upcoming'] ?? collect(),
                    ];
                @endphp
                @foreach($scheduleBlocks as $label => $items)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700/40 p-3">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $label }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200 font-semibold">{{ $items->count() }}</span>
                        </div>
                        @forelse($items->take(2) as $assignment)
                            <p class="text-xs text-gray-700 dark:text-gray-300 mb-1">{{ $assignment->jobOrder?->job_order_number ?? 'N/A' }} • {{ $assignment->scheduled_time ? \Carbon\Carbon::createFromFormat('H:i:s', $assignment->scheduled_time)->setTimezone('Asia/Manila')->format('h:i A') : '--' }}</p>
                        @empty
                            <p class="text-xs text-gray-500 dark:text-gray-400">No jobs scheduled</p>
                        @endforelse
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ===================== EQUIPMENT STATUS ===================== -->
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Equipment Status</h3>
                <a href="{{ route('tech-head.equipment') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Manage</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-lg bg-white dark:bg-gray-700/40 p-4 text-center border-2 border-gray-200 dark:border-gray-600">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">Total</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $equipmentSummary['total'] ?? 0 }}</p>
                </div>
                <div class="rounded-lg bg-amber-50 dark:bg-amber-900/30 p-4 text-center border-2 border-amber-300 dark:border-amber-700">
                    <p class="text-xs font-semibold text-amber-900 dark:text-amber-200 mb-2">Maintenance</p>
                    <p class="text-3xl font-bold text-amber-900 dark:text-amber-100">{{ $equipmentSummary['underMaintenance'] ?? 0 }}</p>
                </div>
                <div class="rounded-lg bg-rose-50 dark:bg-rose-900/30 p-4 text-center border-2 border-rose-300 dark:border-rose-700">
                    <p class="text-xs font-semibold text-rose-900 dark:text-rose-200 mb-2">Critical</p>
                    <p class="text-3xl font-bold text-rose-900 dark:text-rose-100">{{ $equipmentSummary['critical'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== QUICK ACTIONS ===================== -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <a href="{{ route('tech-head.work-orders') }}" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-xl transition-colors border-2 border-blue-200 dark:border-blue-700">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <p class="text-xs font-semibold text-blue-900 dark:text-blue-200 text-center">Create Work Order</p>
            </a>
            <a href="{{ route('tech-head.work-orders') }}" class="flex flex-col items-center p-4 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-xl transition-colors border-2 border-indigo-200 dark:border-indigo-700">
                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-xs font-semibold text-indigo-900 dark:text-indigo-200 text-center">Assign Technician</p>
            </a>
            <a href="{{ route('tech-head.reports') }}" class="flex flex-col items-center p-4 bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 rounded-xl transition-colors border-2 border-emerald-200 dark:border-emerald-700">
                <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs font-semibold text-emerald-900 dark:text-emerald-200 text-center">Approve Reports</p>
            </a>
            <a href="{{ route('tech-head.reports') }}" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 rounded-xl transition-colors border-2 border-purple-200 dark:border-purple-700">
                <svg class="w-8 h-8 text-purple-600 dark:text-purple-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-xs font-semibold text-purple-900 dark:text-purple-200 text-center">View Reports</p>
            </a>
            <a href="{{ route('tech-head.maintenance') }}" class="flex flex-col items-center p-4 bg-orange-50 dark:bg-orange-900/30 hover:bg-orange-100 dark:hover:bg-orange-900/50 rounded-xl transition-colors border-2 border-orange-200 dark:border-orange-700">
                <svg class="w-8 h-8 text-orange-600 dark:text-orange-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-xs font-semibold text-orange-900 dark:text-orange-200 text-center">Maintenance Task</p>
            </a>
        </div>
    </div>

    </div>
@endsection
                <a href="{{ route('tech-head.work-orders') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 text-sm font-semibold shadow hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors border-2 border-blue-200 dark:border-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Work Order
                </a>
                <a href="{{ route('tech-head.work-orders') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 dark:bg-indigo-600 text-indigo-700 dark:text-white text-sm font-semibold shadow hover:bg-indigo-100 dark:hover:bg-indigo-700 transition-colors border-2 border-indigo-200 dark:border-indigo-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Assign Technician
                </a>
                <a href="{{ route('tech-head.reports') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-200 text-sm font-semibold shadow hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors border-2 border-emerald-200 dark:border-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Approve Reports
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wide">Active Work Orders</p>
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-blue-900 dark:text-blue-100 mb-1">{{ number_format($summary['activeWorkOrders']) }}</p>
            <p class="text-xs text-blue-600 dark:text-blue-300">Pending + In Progress</p>
        </div>

        <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/30 dark:to-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-amber-700 dark:text-amber-300 uppercase tracking-wide">Pending Approvals</p>
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-amber-900 dark:text-amber-100 mb-1">{{ number_format($summary['pendingApprovals']) }}</p>
            <p class="text-xs text-amber-600 dark:text-amber-300">Awaiting sign-off</p>
        </div>

        <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/30 dark:to-cyan-900/20 border border-cyan-200 dark:border-cyan-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-cyan-700 dark:text-cyan-300 uppercase tracking-wide">In Progress</p>
                <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-cyan-900 dark:text-cyan-100 mb-1">{{ number_format($summary['inProgressJobs']) }}</p>
            <p class="text-xs text-cyan-600 dark:text-cyan-300">Currently active</p>
        </div>

        <div class="bg-gradient-to-br from-rose-50 to-rose-100 dark:from-rose-900/30 dark:to-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-rose-700 dark:text-rose-300 uppercase tracking-wide">Overdue Jobs</p>
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-rose-900 dark:text-rose-100 mb-1">{{ number_format($summary['overdueJobs']) }}</p>
            <p class="text-xs text-rose-600 dark:text-rose-300">Past required date</p>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/30 dark:to-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-200 uppercase tracking-wide">Completed Today</p>
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-emerald-900 dark:text-emerald-100 mb-1">{{ number_format($summary['completedToday']) }}</p>
            <p class="text-xs text-emerald-600 dark:text-emerald-200">{{ number_format($summary['completedThisWeek']) }} this week</p>
        </div>

        <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/40 dark:to-slate-900/30 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wide">Technicians</p>
                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-900 dark:text-white mb-1">{{ number_format($techniciansAvailable) }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-200">{{ number_format($techniciansOnTask) }} on task</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200">!</span>
                        Alerts & Critical Issues
                    </h3>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Top risks to unblock</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-xl border-2 border-rose-300 dark:border-rose-700 bg-rose-50 dark:bg-rose-900/30 p-4">
                        <p class="text-sm font-semibold text-rose-900 dark:text-rose-100 mb-3">Overdue work orders</p>
                        <div class="space-y-3">
                            @forelse($overdueWorkOrders as $job)
                                <div class="flex items-start justify-between bg-white dark:bg-gray-700/50 rounded-lg p-3 border border-rose-200 dark:border-rose-800/50">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $job->job_order_number }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300">{{ $job->customer->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[11px] px-2 py-1 rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200">{{ ucfirst($job->status) }}</span>
                                        <p class="text-[11px] text-rose-700 dark:text-rose-300 font-medium mt-1">Due {{ ($job->required_date ?? $job->expected_completion_date) ? ($job->required_date ?? $job->expected_completion_date)->setTimezone('Asia/Manila')->format('M d') : 'N/A' }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-600 dark:text-gray-300">No overdue work orders</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="rounded-xl border-2 border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/30 p-4">
                            <p class="text-sm font-semibold text-amber-900 dark:text-amber-100 mb-3">Jobs without technician</p>
                            <div class="space-y-2">
                                @forelse($unassignedJobs as $job)
                                    <div class="flex items-center justify-between text-sm bg-white dark:bg-gray-700/50 rounded-lg p-2 border border-amber-200 dark:border-amber-800/50">
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ $job->job_order_number }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-300">{{ $job->customer->name ?? 'N/A' }}</p>
                                        </div>
                                        <span class="text-[11px] px-2 py-1 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">{{ ucfirst($job->status) }}</span>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-600 dark:text-gray-300">All jobs assigned</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="rounded-xl border-2 border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/30 p-4">
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-3">Pending approvals (aging)</p>
                            <div class="space-y-2">
                                @forelse($agingPendingApprovals as $job)
                                    <div class="flex items-center justify-between text-sm bg-white dark:bg-gray-700/50 rounded-lg p-2 border border-blue-200 dark:border-blue-800/50">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $job->job_order_number }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300">{{ optional($job->request_date)->setTimezone('Asia/Manila')->format('M d') ?? 'N/A' }}</p>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-600 dark:text-gray-300">No pending approvals</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="rounded-xl border-2 border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 p-4">
                            <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100 mb-3">Critical equipment</p>
                            <div class="space-y-2">
                                @forelse($criticalEquipment as $equipment)
                                    <div class="flex items-center justify-between text-sm bg-white dark:bg-gray-700/50 rounded-lg p-2 border border-emerald-200 dark:border-emerald-800/50">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $equipment->name }}</p>
                                        <span class="text-[11px] px-2 py-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">{{ ucfirst($equipment->status ?? 'check') }}</span>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-600 dark:text-gray-300">No flagged equipment</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Work Orders Overview</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Quick filters and latest jobs</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $filters = [
                                '' => 'All',
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'in_progress' => 'In Progress',
                                'overdue' => 'Overdue',
                                'high_priority' => 'High Priority',
                                'completed' => 'Completed',
                            ];
                        @endphp
                        @foreach($filters as $key => $label)
                            @php $isActive = ($statusFilter ?? '') === $key; @endphp
                            <a href="{{ route('tech-head.dashboard', ['status' => $key]) }}"
                                    class="px-3 py-1.5 rounded-full text-xs font-semibold border-2 transition-colors {{ $isActive ? 'bg-blue-600 dark:bg-blue-600 text-white border-blue-600 dark:border-blue-500' : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                                {{ $label }}
                                @php
                                    $mapKey = $key === '' ? 'all' : $key;
                                @endphp
                                @if(isset($workOrderCounts[$mapKey]))
                                    <span class="ml-2 text-[11px] px-2 py-0.5 rounded-full font-bold {{ $isActive ? 'bg-blue-500 dark:bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">{{ $workOrderCounts[$mapKey] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="text-left text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900/40 border-b-2 border-gray-200 dark:border-gray-700 uppercase tracking-wide">
                            <th class="px-3 py-3">Work Order</th>
                            <th class="px-3 py-3">Client / Location</th>
                            <th class="px-3 py-3">Type</th>
                            <th class="px-3 py-3">Technician</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Priority</th>
                            <th class="px-3 py-3 text-right">Due</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($workOrders as $job)
                            @php
                                $assignment = $assignmentsByJob[$job->id] ?? null;
                                $priorityColors = [
                                    'urgent' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200',
                                    'high' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200',
                                    'normal' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200',
                                    'low' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
                                ];
                                $statusColors = [
                                    'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200',
                                    'approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200',
                                    'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200',
                                    'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200',
                                    'on_hold' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
                                ];
                                $priority = $job->priority ?? 'normal';
                                $status = $job->status ?? 'pending';
                                $dueDate = $job->required_date ?? $job->expected_completion_date;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-100 dark:border-gray-700">
                                <td class="px-3 py-3">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $job->job_order_number }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($job->service_type ?? 'Service', 28) }}</p>
                                </td>
                                <td class="px-3 py-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $job->customer->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($job->service_address ?? '—', 32) }}</p>
                                </td>
                                <td class="px-3 py-3 text-sm text-gray-800 dark:text-gray-200">{{ $job->service_type ?? '—' }}</td>
                                <td class="px-3 py-3 text-sm text-gray-800 dark:text-gray-200">{{ $assignment?->assignedTo?->name ?? 'Unassigned' }}</td>
                                <td class="py-3">
                                    <span class="text-[11px] px-2 py-1 rounded-full font-semibold {{ $statusColors[$status] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200' }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="text-[11px] px-2 py-1 rounded-full font-semibold {{ $priorityColors[$priority] ?? $priorityColors['normal'] }}">{{ ucfirst($priority) }}</span>
                                </td>
                                <td class="py-3 text-right text-sm font-medium text-slate-800 dark:text-slate-200">{{ $dueDate ? $dueDate->setTimezone('Asia/Manila')->format('M d') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-8 text-center text-gray-600 dark:text-gray-400 font-medium">No work orders found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Technician Overview</h3>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Roster status</span>
                </div>
                <div class="space-y-3">
                    @forelse($technicianOverview as $tech)
                        @php
                            $assignment = $tech['activeAssignment'] ?? null;
                            $stats = $tech['stats'] ?? null;
                            $statusLabel = $assignment ? 'On Task' : (($stats && ($stats->jobs_today ?? 0) > 0) ? 'On Task' : 'Available');
                            $statusColor = $assignment ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200';
                        @endphp
                        <div class="flex items-start justify-between rounded-xl border-2 border-gray-200 dark:border-gray-600 px-4 py-3 bg-white dark:bg-gray-700/50 hover:shadow-md transition-shadow">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $tech['user']->name }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $tech['user']->department ?? 'Technical' }}</p>
                                <p class="text-xs text-gray-700 dark:text-gray-300 font-medium mt-2">Current: {{ $assignment?->jobOrder?->job_order_number ?? 'No active job' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 text-[11px] font-semibold rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
                                <div class="mt-2 text-[11px] text-gray-600 dark:text-gray-300 space-y-1">
                                    <p class="font-medium">Jobs today: {{ $stats->jobs_today ?? 0 }}</p>
                                    <p class="font-medium">Overdue: {{ $stats->overdue_jobs ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600 dark:text-gray-300 text-center py-4">No technicians found.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Schedule Snapshot</h3>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Today, tomorrow, upcoming</span>
                </div>
                <div class="space-y-4">
                    @php
                        $scheduleBlocks = [
                            'Today' => $schedule['today'],
                            'Tomorrow' => $schedule['tomorrow'],
                            'Upcoming' => $schedule['upcoming'],
                        ];
                    @endphp
                    @foreach($scheduleBlocks as $label => $items)
                        <div class="rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700/50 p-3">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $label }}</p>
                                <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-100 font-semibold">{{ $items->count() }} jobs</span>
                            </div>
                            <div class="space-y-2">
                                @forelse($items as $assignment)
                                    <div class="flex items-center justify-between text-sm bg-gray-50 dark:bg-gray-900/40 rounded-lg p-2 border border-gray-200 dark:border-gray-700">
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $assignment->jobOrder?->job_order_number ?? 'Work Order' }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $assignment->jobOrder?->customer?->name ?? 'N/A' }} • {{ $assignment->location ?? 'On-site' }}</p>
                                        </div>
                                        <p class="text-xs text-gray-700 dark:text-gray-300 font-semibold">{{ $assignment->scheduled_time ? \Carbon\Carbon::parse($assignment->scheduled_time)->setTimezone('Asia/Manila')->format('h:i A') : '--' }}</p>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-600 dark:text-gray-400 text-center py-2">No jobs scheduled</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Equipment Status</h3>
                    <a href="{{ route('tech-head.equipment') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold">Equipment management</a>
                </div>
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="rounded-lg bg-gray-100 dark:bg-gray-900/40 border-2 border-gray-300 dark:border-gray-700 p-3 text-center">
                        <p class="text-xs text-gray-700 dark:text-gray-300 font-semibold">Total</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($equipmentSummary['total']) }}</p>
                    </div>
                    <div class="rounded-lg bg-amber-50 dark:bg-amber-900/30 border-2 border-amber-300 dark:border-amber-700 p-3 text-center">
                        <p class="text-xs text-amber-800 dark:text-amber-200 font-semibold">Maintenance</p>
                        <p class="text-2xl font-bold text-amber-900 dark:text-amber-100">{{ number_format($equipmentSummary['underMaintenance']) }}</p>
                    </div>
                    <div class="rounded-lg bg-rose-50 dark:bg-rose-900/30 border-2 border-rose-300 dark:border-rose-700 p-3 text-center">
                        <p class="text-xs text-rose-800 dark:text-rose-200 font-semibold">Critical</p>
                        <p class="text-2xl font-bold text-rose-900 dark:text-rose-100">{{ number_format($equipmentSummary['critical']) }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">Monitor flagged equipment and manage inventory.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Unified Activity Timeline</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-300 font-medium">Recent job, assignment, and report events</p>
                </div>
                <a href="{{ route('tech-head.timeline') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold">Open Timeline</a>
            </div>
            <div class="space-y-3">
                @forelse($activityTimeline as $event)
                    <div class="flex items-start gap-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-3 bg-white dark:bg-gray-900/40 hover:shadow-md transition-shadow">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $event['type'] === 'job_created' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-200' : ($event['type'] === 'technician_assigned' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-200' : ($event['type'] === 'job_completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300')) }}">
                            <span class="text-xs font-bold">{{ Str::upper(substr($event['type'], 0, 2)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $event['title'] }}</p>
                                <span class="text-[11px] text-gray-600 dark:text-gray-400 font-medium whitespace-nowrap">{{ optional($event['timestamp'])->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mt-1">{{ optional($event['job'])->job_order_number ?? 'Work order' }} • {{ optional(optional($event['job'])->customer)->name ?? 'N/A' }}</p>
                            <div class="mt-1.5 flex items-center gap-2 flex-wrap text-[11px] text-gray-600 dark:text-gray-400">
                                <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-semibold">{{ ucfirst(str_replace('_', ' ', $event['status'] ?? '')) }}</span>
                                @if(!empty($event['user']))
                                    <span class="font-medium">by {{ $event['user'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center py-8">No recent activity recorded.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Quick Actions</h3>
                <span class="text-xs text-slate-600 dark:text-slate-400 font-semibold">Fast handoffs</span>
            </div>
            <div class="grid grid-cols-1 gap-3">
                <button type="button" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-50 dark:bg-blue-600 text-blue-700 dark:text-white font-semibold shadow hover:bg-blue-100 dark:hover:bg-blue-700 transition-colors border-2 border-blue-600 dark:border-blue-500">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500 text-blue-700 dark:text-white flex items-center justify-center font-bold">+</span>
                    Create Work Order
                </button>
                <button type="button" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-600 text-amber-700 dark:text-white font-semibold shadow hover:bg-amber-100 dark:hover:bg-amber-700 transition-colors border-2 border-amber-600 dark:border-amber-500">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-500 text-amber-700 dark:text-white flex items-center justify-center font-bold">⇄</span>
                    Assign Technician
                </button>
                <button type="button" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-600 text-emerald-700 dark:text-white font-semibold shadow hover:bg-emerald-100 dark:hover:bg-emerald-700 transition-colors border-2 border-emerald-600 dark:border-emerald-500">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500 text-emerald-700 dark:text-white flex items-center justify-center font-bold">✓</span>
                    Approve Reports
                </button>
                <button type="button" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white font-semibold shadow hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors border-2 border-slate-600 dark:border-slate-500">
                    <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-white flex items-center justify-center font-bold">👁</span>
                    View Reports
                </button>
                <button type="button" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-cyan-50 dark:bg-cyan-600 text-cyan-700 dark:text-white font-semibold shadow hover:bg-cyan-100 dark:hover:bg-cyan-700 transition-colors border-2 border-cyan-600 dark:border-cyan-500">
                    <span class="w-8 h-8 rounded-lg bg-cyan-100 dark:bg-cyan-500 text-cyan-700 dark:text-white flex items-center justify-center font-bold">⚙</span>
                    Create Maintenance Task
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
