@extends('layouts.dashboard')

@section('title', 'My Assignments')

@section('page-title', 'My Assignments')
@section('page-subtitle', 'Manage your assigned job orders')

@section('head')
    <script>
        function assignmentsPage() {
            return {
                selectedStatus: 'all',
                searchQuery: '',
                assignments: [],
                filteredAssignments: [],
                showModal: false,
                selectedJob: null,
                showConfirmModal: false,
                confirmAction: null,
                confirmJobId: null,
                init() {
                    // Load all assignments from DOM
                    this.loadAssignments();
                    this.filterAssignments();
                },
                loadAssignments() {
                    // Load assignments from data attributes
                    const jobCards = document.querySelectorAll('[data-job-number]');
                    this.assignments = Array.from(jobCards).map(card => ({
                        id: card.id.replace('job-', ''),
                        jobNumber: card.getAttribute('data-job-number'),
                        status: card.getAttribute('data-status'),
                        priority: card.getAttribute('data-priority'),
                        description: card.getAttribute('data-description'),
                        customer: card.getAttribute('data-customer'),
                        createdAt: card.getAttribute('data-created-at'),
                        location: card.getAttribute('data-location') || 'N/A',
                        equipment: card.getAttribute('data-equipment') || 'N/A',
                        notes: card.getAttribute('data-notes') || 'No notes available',
                        element: card
                    }));
                },
                filterAssignments() {
                    // Filter assignments based on status and search query
                    this.filteredAssignments = this.assignments.filter(job => {
                        const statusMatch = this.selectedStatus === 'all' || job.status === this.selectedStatus;
                        const searchMatch = this.searchQuery === '' || 
                            job.jobNumber.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            job.description.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            job.customer.toLowerCase().includes(this.searchQuery.toLowerCase());
                        
                        return statusMatch && searchMatch;
                    });
                    
                    // Show/hide assignment elements based on filter
                    this.assignments.forEach(job => {
                        const isVisible = this.filteredAssignments.some(filtered => filtered.id === job.id);
                        if (job.element) {
                            job.element.style.display = isVisible ? '' : 'none';
                        }
                    });
                },
                clearSearch() {
                    this.searchQuery = '';
                    this.filterAssignments();
                },
                openDetails(jobId) {
                    // Find the job from the assignments data
                    const jobElement = document.getElementById(`job-${jobId}`);
                    if (jobElement) {
                        this.selectedJob = {
                            id: jobId,
                            jobNumber: jobElement.getAttribute('data-job-number'),
                            status: jobElement.getAttribute('data-status'),
                            priority: jobElement.getAttribute('data-priority'),
                            description: jobElement.getAttribute('data-description'),
                            customer: jobElement.getAttribute('data-customer'),
                            createdAt: jobElement.getAttribute('data-created-at'),
                            location: jobElement.getAttribute('data-location') || 'N/A',
                            equipment: jobElement.getAttribute('data-equipment') || 'N/A',
                            notes: jobElement.getAttribute('data-notes') || 'No notes available'
                        };
                        this.showModal = true;
                    }
                },
                closeModal() {
                    this.showModal = false;
                    this.selectedJob = null;
                },
                closeConfirmModal() {
                    this.showConfirmModal = false;
                    this.confirmAction = null;
                    this.confirmJobId = null;
                },
                handleKeydown(event) {
                    if (event.key === 'Escape') {
                        if (this.showModal) {
                            this.closeModal();
                        } else if (this.showConfirmModal) {
                            this.closeConfirmModal();
                        }
                    }
                },
                openConfirmStart(jobId) {
                    this.confirmAction = 'start';
                    this.confirmJobId = jobId;
                    this.showConfirmModal = true;
                },
                openConfirmComplete(jobId) {
                    this.confirmAction = 'complete';
                    this.confirmJobId = jobId;
                    this.showConfirmModal = true;
                },
                openConfirmPause(jobId) {
                    this.confirmAction = 'pause';
                    this.confirmJobId = jobId;
                    this.showConfirmModal = true;
                },
                confirmStartJob() {
                    // Use fetch to update job status
                    fetch(`/technician/job/${this.confirmJobId}/start`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload page to show updated status
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to start job. Please try again.');
                        this.closeConfirmModal();
                    });
                },
                confirmCompleteJob() {
                    window.location.href = `/technician/reports?job_id=${this.confirmJobId}`;
                },
                confirmPauseJob() {
                    // Use fetch to update job status
                    fetch(`/technician/job/${this.confirmJobId}/pause`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload page to show updated status
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to pause job. Please try again.');
                        this.closeConfirmModal();
                    });
                }
            }
        }
    </script>
@endsection

@section('sidebar-nav')
    <a href="{{ route('technician.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('technician.assignments') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
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
        Work Orders
    </a>

    <a href="{{ route('technician.maintenance') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Maintenance Tasks
    </a>

    <a href="{{ route('technician.equipment') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
        </svg>
        Equipment
    </a>

    <a href="{{ route('technician.inventory') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        Inventory
    </a>

    <a href="{{ route('technician.reports') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Reports
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
    <div x-data="assignmentsPage()" @keydown.window="handleKeydown($event)">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">My Assignments</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Track and manage your assigned job orders</p>
        </div>

        <!-- Stats Summary -->
        <div class="flex flex-wrap gap-4 mb-6">
            <div class="flex-1 min-w-xs bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-200 dark:border-blue-800">
                <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold mb-1">Assigned</p>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $assignments->where('status', 'assigned')->count() }}</p>
            </div>
            <div class="flex-1 min-w-xs bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-xl border border-yellow-200 dark:border-yellow-800">
                <p class="text-xs text-yellow-600 dark:text-yellow-400 font-semibold mb-1">In Progress</p>
                <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $assignments->where('status', 'in_progress')->count() }}</p>
            </div>
            <div class="flex-1 min-w-xs bg-orange-50 dark:bg-orange-900/20 p-4 rounded-xl border border-orange-200 dark:border-orange-800">
                <p class="text-xs text-orange-600 dark:text-orange-400 font-semibold mb-1">On Hold</p>
                <p class="text-2xl font-bold text-orange-900 dark:text-orange-100">{{ $assignments->where('status', 'on_hold')->count() }}</p>
            </div>
            <div class="flex-1 min-w-xs bg-green-50 dark:bg-green-900/20 p-4 rounded-xl border border-green-200 dark:border-green-800">
                <p class="text-xs text-green-600 dark:text-green-400 font-semibold mb-1">Completed</p>
                <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $assignments->where('status', 'completed')->count() }}</p>
            </div>
        </div>

        <!-- Filter and Search Options -->
        <div class="mb-6 flex flex-col sm:flex-row gap-3">
            <!-- Status Filter -->
            <div class="flex items-center gap-2">
                <select x-model="selectedStatus" @change="filterAssignments()" 
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm font-medium hover:border-blue-400 dark:hover:border-blue-500 transition-colors">
                    <option value="all">All Status</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="on_hold">On Hold</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <!-- Search Bar -->
            <div class="flex-1 relative">
                <div class="relative">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           x-model="searchQuery" 
                           @input="filterAssignments()"
                           placeholder="Search by job number, description, or customer..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm placeholder-gray-500 dark:placeholder-gray-400 hover:border-blue-400 dark:hover:border-blue-500 focus:outline-none focus:border-blue-500 dark:focus:border-blue-500 transition-colors">
                    <button x-show="searchQuery.length > 0" 
                            @click="clearSearch()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Results Count -->
            <div class="flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                <span x-text="`${filteredAssignments.length} result${filteredAssignments.length !== 1 ? 's' : ''}`"></span>
            </div>
        </div>

        <!-- Assignments List -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            @if($assignments->count() > 0)
                <div class="space-y-4">
                    @foreach($assignments as $job)
                    <div id="job-{{ $job->id }}"
                         data-job-number="{{ $job->job_order_number }}"
                         data-status="{{ $job->status }}"
                         data-priority="{{ $job->priority }}"
                         data-description="{{ $job->description ?? 'No description' }}"
                         data-customer="{{ $job->customer->name ?? 'N/A' }}"
                         data-created-at="{{ $job->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}"
                         data-location="{{ $job->location ?? 'N/A' }}"
                         data-equipment="{{ $job->equipment ?? 'N/A' }}"
                         data-notes="{{ $job->notes ?? 'No notes available' }}"
                         class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $job->job_order_number }}</h3>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        {{ $job->status === 'pending' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' : '' }}
                                        {{ $job->status === 'assigned' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                        {{ $job->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                        {{ $job->status === 'on_hold' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' : '' }}
                                        {{ $job->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                    </span>
                                    @if($job->priority === 'urgent')
                                    <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                        URGENT
                                    </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $job->description ?? 'No description' }}</p>
                                <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ $job->customer->name ?? 'N/A' }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $job->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 ml-4">
                                @if($job->status === 'assigned')
                                <button @click="openConfirmStart({{ $job->id }})" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                    Start Work
                                </button>
                                @elseif($job->status === 'in_progress')
                                <button @click="openConfirmComplete({{ $job->id }})" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                                    Complete
                                </button>
                                <button @click="openConfirmPause({{ $job->id }})" 
                                        class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700 transition-colors">
                                    Pause
                                </button>
                                @endif
                                <button @click="openDetails({{ $job->id }})"
                                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $assignments->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No assignments found</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">You don't have any job orders assigned yet.</p>
                </div>
            @endif
        </div>

        <!-- Job Details Modal -->
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 z-50 flex items-center justify-center p-4 transition-opacity"
             @click.self="closeModal()"
             @keydown="handleKeydown($event)">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white dark:bg-gray-800 rounded-[20px] shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700 transform transition-all sm:align-middle"
                 @click.stop>
                <!-- Modal Header -->
                <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="selectedJob?.jobNumber"></h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Complete job details</p>
                    </div>
                    <button @click="closeModal()"
                            class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 space-y-6">
                    <!-- Status & Priority Row -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">Status</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white" x-text="selectedJob?.status?.replace(/_/g, ' ')"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">Priority</p>
                            <span x-show="selectedJob?.priority === 'urgent'" class="inline-block px-3 py-1 text-sm font-bold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                URGENT
                            </span>
                            <span x-show="selectedJob?.priority !== 'urgent'" class="text-lg font-semibold text-gray-900 dark:text-white" x-text="selectedJob?.priority?.replace(/_/g, ' ')"></span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">Description</p>
                        <p class="text-gray-700 dark:text-gray-300 text-base" x-text="selectedJob?.description"></p>
                    </div>

                    <!-- Customer & Date Row -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">Customer</p>
                            <p class="text-gray-900 dark:text-white" x-text="selectedJob?.customer"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">Created Date</p>
                            <p class="text-gray-900 dark:text-white" x-text="selectedJob?.createdAt"></p>
                        </div>
                    </div>

                    <!-- Location & Equipment Row -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">Location</p>
                            <p class="text-gray-900 dark:text-white" x-text="selectedJob?.location"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">Equipment</p>
                            <p class="text-gray-900 dark:text-white" x-text="selectedJob?.equipment"></p>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">Notes</p>
                        <p class="text-gray-700 dark:text-gray-300 text-base" x-text="selectedJob?.notes"></p>
                    </div>

                    <!-- Divider -->
                    <hr class="border-gray-200 dark:border-gray-700">

                    <!-- Action Buttons -->
                    <div class="flex gap-3 justify-end">
                        <button @click="closeModal()"
                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Close
                        </button>
                        <template x-if="selectedJob?.status === 'assigned'">
                            <button @click="openConfirmStart(selectedJob.id); closeModal();"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                Start Work
                            </button>
                        </template>
                        <template x-if="selectedJob?.status === 'in_progress'">
                            <button @click="openConfirmPause(selectedJob.id)"
                                    class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700 transition-colors">
                                Pause Job
                            </button>
                        </template>
                        <template x-if="selectedJob?.status === 'in_progress'">
                            <button @click="openConfirmComplete(selectedJob.id);"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                                Complete Job
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 z-50 flex items-center justify-center p-4 transition-opacity"
             @click.self="closeConfirmModal()"
             @keydown="handleKeydown($event)">
            <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white dark:bg-gray-800 rounded-[20px] shadow-xl max-w-md w-full border border-gray-200 dark:border-gray-700 transform transition-all sm:align-middle p-6"
                 @click.stop>
                
                <!-- Icon -->
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full"
                     :class="{
                         'bg-blue-100 dark:bg-blue-900/30': confirmAction === 'start',
                         'bg-green-100 dark:bg-green-900/30': confirmAction === 'complete',
                         'bg-orange-100 dark:bg-orange-900/30': confirmAction === 'pause'
                     }">
                    <svg x-show="confirmAction === 'start'" class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg x-show="confirmAction === 'complete'" class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg x-show="confirmAction === 'pause'" class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </div>

                <!-- Content -->
                <div class="text-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                        <span x-show="confirmAction === 'start'">Start Working on Job?</span>
                        <span x-show="confirmAction === 'complete'">Complete Job Order?</span>
                        <span x-show="confirmAction === 'pause'">Put Job On Hold?</span>
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span x-show="confirmAction === 'start'">Are you ready to start working on this job order?</span>
                        <span x-show="confirmAction === 'complete'">Are you sure you want to complete this job order? You'll be redirected to the reports page.</span>
                        <span x-show="confirmAction === 'pause'">Are you sure you want to pause this job order?</span>
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button @click="closeConfirmModal()"
                            class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button @click="confirmAction === 'start' ? confirmStartJob() : (confirmAction === 'complete' ? confirmCompleteJob() : confirmPauseJob())"
                            :class="{
                                'bg-blue-600 hover:bg-blue-700': confirmAction === 'start',
                                'bg-green-600 hover:bg-green-700': confirmAction === 'complete',
                                'bg-orange-600 hover:bg-orange-700': confirmAction === 'pause'
                            }"
                            class="flex-1 px-4 py-2 text-white rounded-lg text-sm font-medium transition-colors">
                        <span x-show="confirmAction === 'start'">Start Work</span>
                        <span x-show="confirmAction === 'complete'">Complete</span>
                        <span x-show="confirmAction === 'pause'">Pause</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
