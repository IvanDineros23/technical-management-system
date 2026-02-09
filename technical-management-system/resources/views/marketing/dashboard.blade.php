@extends('layouts.dashboard')

@section('title', 'Marketing Dashboard')

@section('page-title', 'Marketing Dashboard')
@section('page-subtitle', 'Manage job orders and customer requests')

@section('head')
    <script>
        function marketingDashboard() {
            return {
                showJODetails: false,
                selectedJO: null,
                init() {
                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.showJODetails) {
                            this.closeJODetails();
                        }
                    });
                },
                openJODetails(jo) {
                    this.selectedJO = jo;
                    this.showJODetails = true;
                    document.body.style.overflow = 'hidden';
                },
                closeJODetails() {
                    this.showJODetails = false;
                    this.selectedJO = null;
                    document.body.style.overflow = 'auto';
                },
                formatDate(d) {
                    if (!d) return 'N/A';
                    const dt = new Date(d);
                    return isNaN(dt) ? d : dt.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
                }
            }
        }
    </script>
@endsection

@section('sidebar-nav')
    <a href="{{ route('marketing.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('marketing.create-job-order') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4v16m8-8H4"/>
        </svg>
        Create New JO
    </a>

    <a href="{{ route('marketing.customers') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Customers
    </a>

    <a href="{{ route('marketing.job-orders') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Job Orders
    </a>

    <a href="{{ route('marketing.reports') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Reports
    </a>

    <a href="{{ route('marketing.timeline') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Timeline
    </a>
@endsection

@section('content')
    <div x-data="marketingDashboard()">
    <!-- ===================== STATS (HORIZONTAL, EQUAL) ===================== -->
    <div class="mb-8">
        <div class="flex gap-4 overflow-x-auto pb-1">

            <!-- Total JO -->
            <div class="flex-1 min-w-[220px] bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-900/20
                        rounded-[20px] shadow-md p-6 border border-blue-200 dark:border-blue-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold mb-3">Total Job Orders</p>
                    <h3 class="text-4xl font-bold text-blue-900 dark:text-blue-100 mb-3">{{ $totalJobOrders }}</h3>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-auto">All time</p>
                </div>
            </div>

            <!-- Waiting for Assignment -->
            <div class="flex-1 min-w-[220px] bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/30 dark:to-orange-900/20
                        rounded-[20px] shadow-md p-6 border border-orange-200 dark:border-orange-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-orange-600 dark:text-orange-400 font-semibold mb-3">Waiting for Assignment</p>
                    <h3 class="text-4xl font-bold text-orange-900 dark:text-orange-100 mb-3">{{ $pendingJobOrders }}</h3>
                    <p class="text-xs text-orange-600 dark:text-orange-400 mt-auto">Awaiting assignment</p>
                </div>
            </div>

            <!-- In Progress -->
            <div class="flex-1 min-w-[220px] bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/30 dark:to-cyan-900/20
                        rounded-[20px] shadow-md p-6 border border-cyan-200 dark:border-cyan-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-cyan-600 dark:text-cyan-400 font-semibold mb-3">In Progress</p>
                    <h3 class="text-4xl font-bold text-cyan-900 dark:text-cyan-100 mb-3">{{ $inProgressJobOrders }}</h3>
                    <p class="text-xs text-cyan-600 dark:text-cyan-400 mt-auto">Being processed</p>
                </div>
            </div>

            <!-- Completed -->
            <div class="flex-1 min-w-[220px] bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-900/20
                        rounded-[20px] shadow-md p-6 border border-green-200 dark:border-green-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-green-600 dark:text-green-400 font-semibold mb-3">Completed</p>
                    <h3 class="text-4xl font-bold text-green-900 dark:text-green-100 mb-3">{{ $completedJobOrders }}</h3>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-auto">All time</p>
                </div>
            </div>

        </div>
    </div>

    <!-- ===================== MAIN GRID ===================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- Recent Job Orders -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-[20px] shadow-md dark:shadow-gray-900/50 border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-slate-100 dark:border-gray-700">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Recent Job Orders</h3>
            </div>

            <div class="p-4 space-y-2">
                @forelse($recentJobOrders as $jobOrder)
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-gray-700 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-colors cursor-pointer" @click='openJODetails(@json($jobOrder))'>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                                <span class="text-sm font-bold text-blue-600 dark:text-blue-400">JO</span>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $jobOrder->job_order_number }}</p>
                                <p class="text-sm text-slate-500 dark:text-gray-400">{{ $jobOrder->customer->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($jobOrder->status === 'pending')
                            <span class="px-3 py-1 text-xs font-semibold bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded-full">Waiting for Assignment</span>
                        @elseif($jobOrder->status === 'in_progress')
                            <span class="px-3 py-1 text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full">In Progress</span>
                        @elseif($jobOrder->status === 'completed')
                            <span class="px-3 py-1 text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full">Completed</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full">{{ ucfirst($jobOrder->status) }}</span>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                        <p>No job orders found</p>
                    </div>
                @endforelse

                <a href="{{ route('marketing.job-orders') }}" class="mt-4 w-full block text-center py-2.5 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold rounded-xl hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                    View All Job Orders →
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md dark:shadow-gray-900/50 border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-slate-100 dark:border-gray-700">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Quick Actions</h3>
            </div>

            <div class="p-4 space-y-2">
                <a href="{{ route('marketing.create-job-order') }}" class="w-full flex items-center gap-3 p-3 bg-blue-600 dark:bg-blue-700 text-white rounded-xl hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors shadow-md shadow-blue-200 dark:shadow-blue-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="font-semibold">Create Job Order</span>
                </a>

                <a href="{{ route('marketing.customers') }}" class="w-full flex items-center gap-3 p-3 bg-slate-50 dark:bg-gray-700 text-slate-700 dark:text-gray-200 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="font-semibold">Add Customer</span>
                </a>

                <a href="{{ route('marketing.reports') }}" class="w-full flex items-center gap-3 p-3 bg-slate-50 dark:bg-gray-700 text-slate-700 dark:text-gray-200 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="font-semibold">View Reports</span>
                </a>
            </div>
        </div>

        <!-- JO Details Modal -->
        <div x-show="showJODetails" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showJODetails"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="closeJODetails()"
                     class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showJODetails"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-white" x-text="selectedJO?.job_order_number || 'Job Order Details'"></h3>
                            <button @click="closeJODetails()" class="text-white hover:text-gray-200 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Customer</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedJO?.customer?.name || 'N/A'"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedJO?.customer?.email || ''"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full"
                                      :class="{
                                          'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300': selectedJO?.status === 'pending',
                                          'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300': selectedJO?.status === 'in_progress',
                                          'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300': selectedJO?.status === 'completed',
                                          'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300': !['pending','in_progress','completed'].includes(selectedJO?.status)
                                      }" x-text="selectedJO?.status === 'pending' ? 'Waiting for Assignment' : (selectedJO?.status || 'N/A').replace('_',' ')"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Service Type</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedJO?.service_type || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Priority</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedJO?.priority || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Expected Start</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="formatDate(selectedJO?.expected_start_date)"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Expected Completion</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="formatDate(selectedJO?.expected_completion_date)"></p>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Service Description</p>
                            <p class="mt-1 text-gray-900 dark:text-white" x-text="selectedJO?.service_description || 'N/A'"></p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Service Address</p>
                            <p class="mt-1 text-gray-900 dark:text-white" x-text="[selectedJO?.service_address, selectedJO?.city, selectedJO?.province, selectedJO?.postal_code].filter(Boolean).join(', ') || 'N/A'"></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Created</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="formatDate(selectedJO?.created_at)"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Requested By</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedJO?.requested_by || 'N/A'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 flex justify-end gap-3">
                        <button @click="closeJODetails()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Close</button>
                        <a href="{{ route('marketing.job-orders') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">View All</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<style>
    [x-cloak] { display: none !important; }
</style>