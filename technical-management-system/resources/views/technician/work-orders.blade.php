@extends('layouts.dashboard')

@section('title', 'Work Orders')

@section('page-title', 'Work Orders')
@section('page-subtitle', 'View all work orders')

@section('head')
    <script>
        function workOrdersPage() {
            return {
                filterStatus: '{{ $status ?? "" }}',
                filterPriority: '{{ $priority ?? "" }}',
                searchQuery: '{{ $search ?? "" }}',
                showPreviewModal: false,
                previewOrder: null,
                openPreview(order) {
                    this.previewOrder = order;
                    this.showPreviewModal = true;
                    document.body.style.overflow = 'hidden';
                },
                closePreview() {
                    this.showPreviewModal = false;
                    this.previewOrder = null;
                    document.body.style.overflow = 'auto';
                },
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
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        My Assignments
    </a>

    <a href="{{ route('technician.work-orders') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Work Orders
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
    <div x-data="workOrdersPage()" @keydown.escape.window="if (showPreviewModal) closePreview()">
        @if(session('status'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Work Orders</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">All work orders you're involved with</p>
        </div>

        <!-- Filter Options -->
        <form method="GET" action="{{ route('technician.work-orders') }}" class="mb-12 flex flex-wrap gap-4 items-center">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search work orders..." 
                   class="flex-1 min-w-[260px] px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm placeholder-gray-500 dark:placeholder-gray-400">
            
            <select name="status"
                    class="px-4 py-2 pr-12 min-w-[170px] border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm">
                <option value="">All Status</option>
                <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Waiting for Assignment</option>
                <option value="for_accounting_approval" {{ ($status ?? '') === 'for_accounting_approval' ? 'selected' : '' }}>For Accounting Approval</option>
                <option value="approved" {{ ($status ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="assigned" {{ ($status ?? '') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in_progress" {{ ($status ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="on_hold" {{ ($status ?? '') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="pending_review" {{ ($status ?? '') === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                <option value="rejected" {{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="priority"
                    class="px-4 py-2 pr-12 min-w-[150px] border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm">
                <option value="">All Priority</option>
                <option value="urgent" {{ ($priority ?? '') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="high" {{ ($priority ?? '') === 'high' ? 'selected' : '' }}>High</option>
                <option value="normal" {{ ($priority ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="low" {{ ($priority ?? '') === 'low' ? 'selected' : '' }}>Low</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">Filter</button>
            <a href="{{ route('technician.work-orders') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Clear</a>
        </form>

        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            @if($workOrders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 dark:border-gray-700">
                            <tr class="text-left">
                                <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">WO Number</th>
                                <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Description</th>
                                <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Customer</th>
                                <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Date</th>
                                <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($workOrders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="py-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->job_order_number }}</p>
                                </td>
                                <td class="py-3">
                                    <p class="text-sm text-gray-900 dark:text-white">{{ Str::limit($order->service_description ?? $order->description ?? 'N/A', 40) }}</p>
                                </td>
                                <td class="py-3">
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->customer->name ?? 'N/A' }}</p>
                                </td>
                                <td class="py-3">
                                    @php
                                        $statusValue = $order->technician_status ?? $order->status;
                                        $statusLabel = $order->technician_status_label
                                            ?? (in_array($order->status, ['pending', 'approved'], true) ? 'Waiting for Assignment' : ucfirst(str_replace('_', ' ', $order->status)));
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ in_array($statusValue, ['pending', 'approved'], true) ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' : '' }}
                                        {{ $statusValue === 'assigned' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                        {{ $statusValue === 'in_progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                        {{ $statusValue === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                        {{ $statusValue === 'pending_review' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : '' }}
                                        {{ $statusValue === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</p>
                                </td>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        @if(!$order->is_assigned_to_me && in_array($order->status, ['pending', 'approved'], true))
                                            <button
                                                type="button"
                                                data-id="{{ $order->id }}"
                                                data-job-order-number="{{ $order->job_order_number }}"
                                                data-customer-name="{{ $order->customer->name ?? 'N/A' }}"
                                                data-service-type="{{ $order->service_type ?? 'N/A' }}"
                                                data-service-description="{{ $order->service_description ?? 'N/A' }}"
                                                data-status-label="{{ $statusLabel }}"
                                                data-priority="{{ ucfirst($order->priority ?? 'normal') }}"
                                                data-created-at="{{ $order->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}"
                                                data-assign-url="{{ route('technician.work-orders.assign-to-me', $order) }}"
                                                @click="openPreview({
                                                    id: Number($el.dataset.id),
                                                    job_order_number: $el.dataset.jobOrderNumber,
                                                    customer_name: $el.dataset.customerName,
                                                    service_type: $el.dataset.serviceType,
                                                    service_description: $el.dataset.serviceDescription,
                                                    status_label: $el.dataset.statusLabel,
                                                    priority: $el.dataset.priority,
                                                    created_at: $el.dataset.createdAt,
                                                    assign_url: $el.dataset.assignUrl,
                                                })"
                                                class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium"
                                            >
                                                Preview
                                            </button>
                                        @elseif(($order->is_assigned_to_me ?? false) || ($order->assignment_for_me_count ?? 0) > 0)
                                            <a href="{{ route('technician.job-details', $order->id) }}" 
                                               class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                                                View
                                            </a>
                                        @else
                                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Not Assigned</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $workOrders->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No work orders found</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No work orders available at this time.</p>
                </div>
            @endif
        </div>

        <div
            x-show="showPreviewModal"
            x-cloak
            x-transition.opacity.duration.200ms
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
                x-transition.opacity.duration.200ms
                @click="closePreview()"
            ></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    x-show="showPreviewModal"
                    x-transition:enter="transform transition ease-out duration-250"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                    @click.stop
                    class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700 p-6"
                >
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4 mb-5">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Work Order Preview</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="previewOrder ? previewOrder.job_order_number : ''"></p>
                        </div>
                        <button type="button" @click="closePreview()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Customer</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="previewOrder ? previewOrder.customer_name : 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="previewOrder ? previewOrder.status_label : 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Priority</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="previewOrder ? previewOrder.priority : 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Date Created</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="previewOrder ? previewOrder.created_at : 'N/A'"></p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Service Type</p>
                        <p class="text-sm text-gray-900 dark:text-white" x-text="previewOrder ? previewOrder.service_type : 'N/A'"></p>
                    </div>

                    <div class="mt-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Service Description</p>
                        <p class="text-sm text-gray-900 dark:text-white" x-text="previewOrder ? previewOrder.service_description : 'N/A'"></p>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <button type="button" @click="closePreview()" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Close
                        </button>

                        <form method="POST" :action="previewOrder ? previewOrder.assign_url : '#'" class="m-0">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition-colors">
                                Assign to Me
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
