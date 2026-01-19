@extends('layouts.dashboard')

@section('title', 'Technician Dashboard')

@section('page-title', 'Technician Dashboard')
@section('page-subtitle', 'Manage your assignments and work orders')

@section('head')
    <script>
        function technicianDashboard() {
            return {
                showJobDetails: false,
                selectedJob: null,
                init() {
                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.showJobDetails) {
                            this.closeJobDetails();
                        }
                    });
                },
                openJobDetails(job) {
                    this.selectedJob = job;
                    this.showJobDetails = true;
                    document.body.style.overflow = 'hidden';
                },
                closeJobDetails() {
                    this.showJobDetails = false;
                    this.selectedJob = null;
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
                        'pending': 'Pending'
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
    <div x-data="technicianDashboard()">
    <!-- ===================== STATS (HORIZONTAL, EQUAL) ===================== -->
    <div class="mb-8">
        <div class="flex gap-4 overflow-x-auto pb-1">

            <!-- Today's Assignments -->
            <div class="flex-1 min-w-[220px] bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-900/20
                        rounded-[20px] shadow-md p-6 border border-purple-200 dark:border-purple-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-purple-600 dark:text-purple-400 font-semibold mb-3">Today's Assignments</p>
                    <h3 class="text-4xl font-bold text-purple-900 dark:text-purple-100 mb-3">{{ $todayAssignments }}</h3>
                    <p class="text-xs text-purple-600 dark:text-purple-400 mt-auto">New today</p>
                </div>
            </div>

            <!-- Pending Jobs -->
            <div class="flex-1 min-w-[220px] bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-900/20
                        rounded-[20px] shadow-md p-6 border border-blue-200 dark:border-blue-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold mb-3">Pending Jobs</p>
                    <h3 class="text-4xl font-bold text-blue-900 dark:text-blue-100 mb-3">{{ $pendingJobs }}</h3>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-auto">Awaiting start</p>
                </div>
            </div>

            <!-- In Progress -->
            <div class="flex-1 min-w-[220px] bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/30 dark:to-yellow-900/20
                        rounded-[20px] shadow-md p-6 border border-yellow-200 dark:border-yellow-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-yellow-600 dark:text-yellow-400 font-semibold mb-3">In Progress</p>
                    <h3 class="text-4xl font-bold text-yellow-900 dark:text-yellow-100 mb-3">{{ $inProgressJobs }}</h3>
                    <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-auto">Active work</p>
                </div>
            </div>

            <!-- Completed -->
            <div class="flex-1 min-w-[220px] bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-900/20
                        rounded-[20px] shadow-md p-6 border border-green-200 dark:border-green-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-green-600 dark:text-green-400 font-semibold mb-3">Completed</p>
                    <h3 class="text-4xl font-bold text-green-900 dark:text-green-100 mb-3">{{ $completedJobs }}</h3>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-auto">All time</p>
                </div>
            </div>

        </div>
    </div>

    <!-- ===================== ALERTS & RECENT ASSIGNMENTS ===================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Alerts -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6 h-full flex flex-col">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Alerts
                </h3>
                <div class="text-center py-4 flex-1 flex items-center justify-center">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No alerts at the moment</p>
                </div>
            </div>
        </div>

        <!-- Recent Assignments -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6 h-full flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Recent Assignments</h3>
                    <a href="{{ route('technician.assignments') }}" 
                       class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        View All →
                    </a>
                </div>

                @if($recentAssignments->count() > 0)
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full">
                            <thead class="border-b border-gray-200 dark:border-gray-700">
                                <tr class="text-left">
                                    <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Job Order</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Customer</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Date</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($recentAssignments as $job)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="py-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $job->job_order_number }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($job->description ?? 'No description', 30) }}</p>
                                    </td>
                                    <td class="py-3">
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $job->customer->name ?? 'N/A' }}</p>
                                    </td>
                                    <td class="py-3">
                                        <span :class="{
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300': '{{ $job->status }}' === 'assigned',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300': '{{ $job->status }}' === 'in_progress',
                                            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': '{{ $job->status }}' === 'completed',
                                            'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300': '{{ $job->status }}' === 'on_hold'
                                        }" class="px-2 py-1 text-xs font-medium rounded-full">
                                            {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $job->created_at->format('M d, Y') }}</p>
                                    </td>
                                    <td class="py-3">
                                        <a href="{{ route('technician.job-details', $job->id) }}" 
                                           class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                                            View Details
                                        </a>
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
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('technician.assignments') }}" 
               class="flex-1 min-w-xs p-4 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-xl transition-colors border border-blue-200 dark:border-blue-800">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">Start Work</p>
            </a>
            <a href="{{ route('technician.reports') }}" 
               class="flex-1 min-w-xs p-4 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-xl transition-colors border border-green-200 dark:border-green-800">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-semibold text-green-900 dark:text-green-100">Submit Report</p>
            </a>
            <a href="{{ route('technician.inventory') }}" 
               class="flex-1 min-w-xs p-4 bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/30 rounded-xl transition-colors border border-orange-200 dark:border-orange-800">
                <svg class="w-8 h-8 text-orange-600 dark:text-orange-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="text-sm font-semibold text-orange-900 dark:text-orange-100">Request Materials</p>
            </a>
            <a href="{{ route('technician.calendar') }}" 
               class="flex-1 min-w-xs p-4 bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-xl transition-colors border border-purple-200 dark:border-purple-800">
                <svg class="w-8 h-8 text-purple-600 dark:text-purple-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm font-semibold text-purple-900 dark:text-purple-100">View Schedule</p>
            </a>
        </div>
    </div>

    </div>
@endsection
