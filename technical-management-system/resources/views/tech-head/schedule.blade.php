@extends('layouts.dashboard')

@section('title', 'Schedule')

@section('page-title', 'Schedule')
@section('page-subtitle', 'Weekly assignments and jobs')

@section('sidebar-nav')
    @include('tech-head.partials.sidebar')
@endsection

@section('content')
    @php
        // Ensure variables exist with defaults
        $unassignedJobs = $unassignedJobs ?? collect([]);
        $technicians = $technicians ?? collect([]);
        $availableTechnicians = $availableTechnicians ?? 0;
        
        // Sample summary data - replace with actual backend calculations
        $summary = [
            'week_total' => $weeklySchedule->flatten()->count(),
            'unassigned' => $unassignedJobs->count(),
            'overdue' => $weeklySchedule->flatten()->filter(fn($a) => optional($a->scheduled_date)->isPast())->count(),
            'available_today' => $availableTechnicians
        ];
        
        // Current filters from request
        $filters = [
            'technician' => request('technician'),
            'status' => request('status'),
            'priority' => request('priority')
        ];
    @endphp

    <div x-data="{ 
        showAdd: false,
        showDetails: false,
        showAssignModal: false,
        showStatusModal: false,
        showMoveModal: false,
        selectedJobId: null,
        selectedJob: null,
        prefilledJobOrderId: null,
        init() {
            this.$watch('showAdd', value => this.handleModalState(value));
            this.$watch('showDetails', value => this.handleModalState(value));
            this.$watch('showAssignModal', value => this.handleModalState(value));
            this.$watch('showStatusModal', value => this.handleModalState(value));
            this.$watch('showMoveModal', value => this.handleModalState(value));
        },
        handleModalState(isOpen) {
            if (isOpen) {
                document.body.style.overflow = 'hidden';
                this.setupEscapeKey();
            } else {
                document.body.style.overflow = 'auto';
            }
        },
        setupEscapeKey() {
            const handler = (e) => {
                if (e.key === 'Escape') {
                    this.closeAllModals();
                    document.removeEventListener('keydown', handler);
                }
            };
            document.addEventListener('keydown', handler);
        },
        closeAllModals() {
            this.showAdd = false;
            this.showDetails = false;
            this.showAssignModal = false;
            this.showStatusModal = false;
            this.showMoveModal = false;
            this.prefilledJobOrderId = null;
        },
        openDetails(assignment) {
            this.selectedAssignment = assignment;
            this.showDetails = true;
        },
        openAssignModal(jobId, job) {
            this.selectedJobId = jobId;
            this.selectedJob = job;
            this.showAssignModal = true;
        },
        openStatusModal(jobId, job) {
            this.selectedJobId = jobId;
            this.selectedJob = job;
            this.showStatusModal = true;
        },
        openMoveModal(jobId, job) {
            this.selectedJobId = jobId;
            this.selectedJob = job;
            this.showMoveModal = true;
        },
        scheduleUnassigned(jobOrderId) {
            this.prefilledJobOrderId = jobOrderId;
            this.showAdd = true;
        }
    }" class="space-y-6">
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <!-- Total Jobs This Week -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-2xl p-5 border border-blue-200 dark:border-blue-700/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide">Total Jobs</p>
                        <p class="text-3xl font-bold text-blue-900 dark:text-blue-100 mt-1">{{ $summary['week_total'] }}</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">This Week</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-500 dark:bg-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Unassigned Jobs -->
            <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-2xl p-5 border border-amber-200 dark:border-amber-700/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wide">Unassigned</p>
                        <p class="text-3xl font-bold text-amber-900 dark:text-amber-100 mt-1">{{ $summary['unassigned'] }}</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Need Assignment</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-500 dark:bg-amber-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Overdue Jobs -->
            <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-2xl p-5 border border-red-200 dark:border-red-700/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wide">Overdue</p>
                        <p class="text-3xl font-bold text-red-900 dark:text-red-100 mt-1">{{ $summary['overdue'] }}</p>
                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">Past Due Date</p>
                    </div>
                    <div class="w-12 h-12 bg-red-500 dark:bg-red-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Available Technicians -->
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 rounded-2xl p-5 border border-emerald-200 dark:border-emerald-700/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Available</p>
                        <p class="text-3xl font-bold text-emerald-900 dark:text-emerald-100 mt-1">{{ $summary['available_today'] }}</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">Technicians Today</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-500 dark:bg-emerald-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" action="{{ route('tech-head.schedule') }}" class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Filters:</span>
                </div>

                <select name="technician" onchange="this.form.submit()" class="min-w-[200px] px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Technicians</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" {{ $filters['technician'] == $tech->id ? 'selected' : '' }}>
                            {{ $tech->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="min-w-[180px] px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="assigned" {{ $filters['status'] == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="in_progress" {{ $filters['status'] == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ $filters['status'] == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>

                <select name="priority" onchange="this.form.submit()" class="min-w-[180px] px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Priorities</option>
                    <option value="urgent" {{ $filters['priority'] == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high" {{ $filters['priority'] == 'high' ? 'selected' : '' }}>High</option>
                    <option value="normal" {{ $filters['priority'] == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="low" {{ $filters['priority'] == 'low' ? 'selected' : '' }}>Low</option>
                </select>

                @if($filters['technician'] || $filters['status'] || $filters['priority'])
                    <a href="{{ route('tech-head.schedule') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Header with Add Button -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Schedule Calendar</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage and monitor all technician schedules</p>
            </div>
            <button 
                @click="showAdd=true" 
                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg text-sm font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Schedule Block</span>
            </button>
        </div>

        <!-- Main Content Grid: Weekly View + Unassigned Panel -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Weekly Schedule (Left/Main) -->
            <div class="xl:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">This Week</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d, Y') }}</p>
                </div>
                <span class="text-sm px-4 py-2 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200 font-semibold">Today: {{ $today->format('M d') }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @forelse($weeklySchedule as $date => $items)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 bg-gradient-to-br from-gray-50 to-white dark:from-gray-700/30 dark:to-gray-800/30 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-base font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($date)->format('l') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</p>
                            </div>
                            <span class="text-xs px-3 py-1.5 rounded-full {{ $items->count() > 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }} font-semibold">{{ $items->count() }} {{ $items->count() == 1 ? 'job' : 'jobs' }}</span>
                        </div>
                        <div class="space-y-3">
                            @foreach($items as $assignment)
                                @php
                                    $hasConflict = $assignment->conflict ?? false;
                                    $isOverloaded = $assignment->overloaded ?? false;
                                @endphp
                                <div 
                                    @click="openDetails({{ json_encode([
                                        'id' => $assignment->id,
                                        'wo_number' => $assignment->jobOrder->job_order_number ?? 'N/A',
                                        'customer' => $assignment->jobOrder->customer->name ?? 'N/A',
                                        'technician' => $assignment->assignedTo->name ?? 'Unassigned',
                                        'scheduled_date' => optional($assignment->scheduled_date)->format('M d, Y'),
                                        'scheduled_time' => optional($assignment->scheduled_time)->format('h:i A') ?? '—',
                                        'status' => $assignment->status ?? 'pending',
                                        'service_type' => $assignment->jobOrder->service_type ?? 'N/A',
                                        'priority' => $assignment->priority ?? 'normal'
                                    ]) }})"
                                    class="relative rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer group"
                                >
                                    <!-- Conflict/Overload Badges -->
                                    <div class="absolute top-2 right-2 flex gap-1">
                                        @if($hasConflict)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300" title="Schedule Conflict">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                Conflict
                                            </span>
                                        @endif
                                        @if($isOverloaded)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300" title="Technician Overloaded">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                Overloaded
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-start justify-between {{ $hasConflict || $isOverloaded ? 'pr-32' : '' }}">
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $assignment->jobOrder->job_order_number ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $assignment->jobOrder->customer->name ?? 'N/A' }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                            {{ $assignment->status === 'assigned' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' : '' }}
                                            {{ $assignment->status === 'in_progress' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200' : '' }}
                                            {{ $assignment->status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $assignment->status ?? 'pending')) }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ optional($assignment->scheduled_time)->format('h:i A') ?? '—' }}</span>
                                            </div>
                                            <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                <span>{{ $assignment->assignedTo->name ?? 'Unassigned' }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Inline Actions Menu -->
                                        <div class="relative" x-data="{ showMenu: false }">
                                            <button 
                                                @click.stop="showMenu = !showMenu" 
                                                class="p-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
                                                title="Actions"
                                            >
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                                </svg>
                                            </button>
                                            
                                            <div 
                                                x-show="showMenu" 
                                                @click.away="showMenu = false"
                                                x-cloak
                                                class="absolute right-0 bottom-full mb-1 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-xl border border-gray-200 dark:border-gray-600 z-10"
                                            >
                                                <button 
                                                    @click.stop="openAssignModal({{ $assignment->id }}, {{ json_encode([
                                                        'wo_number' => $assignment->jobOrder->job_order_number ?? 'N/A',
                                                        'current_tech' => $assignment->assignedTo->name ?? 'Unassigned'
                                                    ]) }}); showMenu = false" 
                                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 flex items-center gap-2 first:rounded-t-lg"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                    Assign/Reassign
                                                </button>
                                                <button 
                                                    @click.stop="openStatusModal({{ $assignment->id }}, {{ json_encode([
                                                        'wo_number' => $assignment->jobOrder->job_order_number ?? 'N/A',
                                                        'current_status' => $assignment->status ?? 'pending'
                                                    ]) }}); showMenu = false" 
                                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 flex items-center gap-2"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Change Status
                                                </button>
                                                <button 
                                                    @click.stop="openMoveModal({{ $assignment->id }}, {{ json_encode([
                                                        'wo_number' => $assignment->jobOrder->job_order_number ?? 'N/A',
                                                        'current_date' => optional($assignment->scheduled_date)->format('Y-m-d'),
                                                        'current_time' => optional($assignment->scheduled_time)->format('H:i')
                                                    ]) }}); showMenu = false" 
                                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 flex items-center gap-2 last:rounded-b-lg"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    Move Date/Time
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No scheduled jobs this week</p>
                    </div>
                @endforelse
            </div>
                </div>
            </div>

            <!-- Unassigned Jobs Panel (Right) -->
            <div class="xl:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6 sticky top-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Unassigned Jobs</h3>
                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200 text-xs font-bold">
                            {{ $unassignedJobs->count() }}
                        </span>
                    </div>
                    
                    <div class="space-y-3 max-h-[600px] overflow-y-auto">
                        @forelse($unassignedJobs as $job)
                            <div class="border border-amber-200 dark:border-amber-700/50 rounded-lg p-4 bg-amber-50/50 dark:bg-amber-900/10">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $job->job_order_number ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $job->customer->name ?? 'N/A' }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                        {{ ($job->priority ?? 'normal') === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200' : '' }}
                                        {{ ($job->priority ?? 'normal') === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-200' : '' }}
                                        {{ ($job->priority ?? 'normal') === 'normal' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' : '' }}
                                        {{ ($job->priority ?? 'normal') === 'low' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}">
                                        {{ ucfirst($job->priority ?? 'normal') }}
                                    </span>
                                </div>
                                
                                <div class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                                    <p><span class="font-semibold">Service:</span> {{ $job->service_type ?? 'N/A' }}</p>
                                    <p><span class="font-semibold">Created:</span> {{ optional($job->created_at)->format('M d, Y') }}</p>
                                </div>
                                
                                <button 
                                    @click.stop="scheduleUnassigned({{ $job->id }})" 
                                    class="w-full px-3 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg text-xs font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center gap-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Schedule Now
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">All jobs assigned!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Add Schedule Modal -->
        <div 
            x-show="showAdd" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showAdd=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add Schedule Block</h3>
                            <button @click="showAdd=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form method="POST" action="{{ route('tech-head.assignments.store') }}" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Job Order</label>
                                <select 
                                    name="job_order_id" 
                                    x-model="prefilledJobOrderId"
                                    required 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Select Job Order</option>
                                    @foreach($unassignedJobs as $job)
                                        <option value="{{ $job->id }}">
                                            {{ $job->job_order_number ?? 'WO-' . $job->id }} - {{ $job->customer->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Assign to Technician</label>
                                <select 
                                    name="assigned_to" 
                                    required 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Select Technician</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Scheduled Date</label>
                                    <input 
                                        type="date" 
                                        name="scheduled_date" 
                                        required 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Scheduled Time</label>
                                    <input 
                                        type="time" 
                                        name="scheduled_time" 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="showAdd=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Create Schedule</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment Details Modal -->
        <div 
            x-show="showDetails" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showDetails=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Schedule Details</h3>
                            <button @click="showDetails=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Work Order Number</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedAssignment?.wo_number"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Customer</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedAssignment?.customer"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Assigned Technician</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedAssignment?.technician"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Service Type</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedAssignment?.service_type"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Scheduled Date</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selectedAssignment?.scheduled_date"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Scheduled Time</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selectedAssignment?.scheduled_time"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200" x-text="selectedAssignment?.status"></span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Priority</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-200" x-text="selectedAssignment?.priority"></span>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button @click="showDetails=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign/Reassign Modal -->
        <div 
            x-show="showAssignModal" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showAssignModal=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Assign Technician</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="'WO: ' + (selectedJob?.wo_number || 'N/A')"></p>
                            </div>
                            <button @click="showAssignModal=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form :action="'/tech-head/assignments/' + selectedJobId + '/reassign'" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Current: <span x-text="selectedJob?.current_tech"></span></label>
                                <select name="assigned_to" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Technician</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="showAssignModal=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Assign</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Status Modal -->
        <div 
            x-show="showStatusModal" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showStatusModal=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Change Status</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="'WO: ' + (selectedJob?.wo_number || 'N/A')"></p>
                            </div>
                            <button @click="showStatusModal=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form :action="'/tech-head/assignments/' + selectedJobId + '/status'" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Current: <span x-text="selectedJob?.current_status"></span></label>
                                <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Status</option>
                                    <option value="assigned">Assigned</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="showStatusModal=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Update Status</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Move Date/Time Modal -->
        <div 
            x-show="showMoveModal" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showMoveModal=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Move Schedule</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="'WO: ' + (selectedJob?.wo_number || 'N/A')"></p>
                            </div>
                            <button @click="showMoveModal=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form :action="'/tech-head/assignments/' + selectedJobId + '/reschedule'" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">New Date</label>
                                    <input 
                                        type="date" 
                                        name="scheduled_date" 
                                        :value="selectedJob?.current_date"
                                        required 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">New Time</label>
                                    <input 
                                        type="time" 
                                        name="scheduled_time"
                                        :value="selectedJob?.current_time"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="showMoveModal=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Move Schedule</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
