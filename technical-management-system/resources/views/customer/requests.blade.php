    @extends('layouts.dashboard')

    @section('title', 'My Requests')

    @section('page-title', 'My Requests')
    @section('page-subtitle', 'Track your job order requests')

    @section('sidebar-nav')
        <a href="{{ route('customer.dashboard') }}"
        class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('customer.requests') }}"
        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Requests
        </a>

        <a href="{{ route('customer.certificates') }}"
        class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
            Certificates
        </a>

        <a href="{{ route('certificate-verification.verify') }}"
        class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Verify Certificate
        </a>
    @endsection

    @section('content')
        @if(!$customer)
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                <p class="text-sm font-semibold">No customer profile linked to this account yet.</p>
                <p class="text-xs mt-1">Please contact the administrator to link your customer record.</p>
            </div>
        @endif

        @if(session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">{{ session('status') }}</p>
                        @if(session('pdf_url'))
                            <p class="text-xs mt-2">
                                <a href="{{ session('pdf_url') }}" download class="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-900 font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download your PDF form
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div
            x-data="{
                showCreateModal: false,
                showViewModal: false,
                selectedOrder: null,
                search: '',
                openView(order) { this.selectedOrder = order; this.showViewModal = true; },
                closeView() { this.showViewModal = false; this.selectedOrder = null; },
                matchRow(order) {
                    if (!this.search) return true;
                    const q = this.search.toLowerCase();
                    return (order.job_order_number || '').toLowerCase().includes(q)
                        || (order.service_type || '').toLowerCase().includes(q)
                        || (order.status || '').toLowerCase().includes(q);
                },
                statusSteps: ['pending','approved','in_progress','completed'],
                stepIndex(status) {
                    const idx = this.statusSteps.indexOf(status);
                    return idx === -1 ? 0 : idx;
                },
                statusLabel(s) {
                    return (s || '').replaceAll('_',' ').replace(/\b\w/g, c => c.toUpperCase());
                },
                priorityLabel(p) {
                    if (!p) return 'Normal';
                    return p.replace(/\b\w/g, c => c.toUpperCase());
                }
            }"
            class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6"
        >
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Job Requests</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Track your submitted service requests</p>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18.0a7.5 7.5 0 006.15-3.35z"/>
                        </svg>
                        <input
                            x-model="search"
                            type="text"
                            placeholder="Search job order / service / status..."
                            class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    @if($customer)
                        <button
                            @click="showCreateModal = true"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Request
                        </button>
                    @endif
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('customer.requests') }}"
                class="px-4 py-2 rounded-lg text-xs font-semibold {{ $status === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                    All
                </a>
                <a href="{{ route('customer.requests', ['status' => 'pending']) }}"
                class="px-4 py-2 rounded-lg text-xs font-semibold {{ $status === 'pending' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Pending
                </a>
                <a href="{{ route('customer.requests', ['status' => 'approved']) }}"
                class="px-4 py-2 rounded-lg text-xs font-semibold {{ $status === 'approved' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Approved
                </a>
                <a href="{{ route('customer.requests', ['status' => 'in_progress']) }}"
                class="px-4 py-2 rounded-lg text-xs font-semibold {{ $status === 'in_progress' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700' }}">
                    In Progress
                </a>
                <a href="{{ route('customer.requests', ['status' => 'completed']) }}"
                class="px-4 py-2 rounded-lg text-xs font-semibold {{ $status === 'completed' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Completed
                </a>
                <a href="{{ route('customer.requests', ['status' => 'cancelled']) }}"
                class="px-4 py-2 rounded-lg text-xs font-semibold {{ $status === 'cancelled' ? 'bg-rose-500 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Cancelled
                </a>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white/95 dark:bg-gray-800/95 backdrop-blur z-10">
                        <tr>
                            <th class="pb-3 text-xs font-semibold text-gray-500 text-center w-[140px]">Job Order</th>
                            <th class="pb-3 text-xs font-semibold text-gray-500 text-center w-[160px]">Service</th>
                            <th class="pb-3 text-xs font-semibold text-gray-500 text-center w-[160px]">Requested</th>
                            <th class="pb-3 text-xs font-semibold text-gray-500 text-center w-[120px]">Priority</th>
                            <th class="pb-3 text-xs font-semibold text-gray-500 text-center w-[140px]">Status</th>
                            <th class="pb-3 text-xs font-semibold text-gray-500 text-center w-[320px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($jobOrders as $order)
                            <tr
                                x-show="matchRow({ job_order_number: @js($order->job_order_number), service_type: @js($order->service_type), status: @js($order->status) })"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="py-3 text-sm font-semibold text-slate-900 dark:text-white text-center align-middle">
                                    {{ $order->job_order_number }}
                                </td>
                                <td class="py-3 text-sm text-gray-600 dark:text-gray-300 text-center align-middle">
                                    {{ $order->service_type ?? 'N/A' }}
                                </td>
                                <td class="py-3 text-sm text-gray-600 dark:text-gray-300 text-center align-middle">
                                    {{ optional($order->request_date)->format('M d, Y') ?? 'N/A' }}
                                </td>
                                <td class="py-3 text-center align-middle">
                                    @php
                                        $priority = $order->priority ?? 'normal';
                                        $priorityStyles = [
                                            'normal' => 'bg-slate-100 text-slate-700',
                                            'high' => 'bg-amber-100 text-amber-700',
                                            'urgent' => 'bg-rose-100 text-rose-700',
                                        ];
                                        $priorityClass = $priorityStyles[$priority] ?? 'bg-slate-100 text-slate-700';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $priorityClass }}">
                                        {{ ucfirst($priority) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center align-middle">
                                    @php
                                        $statusStyles = [
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'approved' => 'bg-emerald-100 text-emerald-700',
                                            'in_progress' => 'bg-blue-100 text-blue-700',
                                            'completed' => 'bg-emerald-100 text-emerald-700',
                                            'cancelled' => 'bg-rose-100 text-rose-700',
                                        ];
                                        $statusClass = $statusStyles[$order->status] ?? 'bg-slate-100 text-slate-700';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center align-middle">
                                    <div class="flex items-center justify-center gap-2 flex-nowrap whitespace-nowrap">
                                        <button
                                            type="button"
                                            @click="openView({
                                                job_order_number: @js($order->job_order_number),
                                                service_type: @js($order->service_type),
                                                request_date: @js(optional($order->request_date)->format('M d, Y')),
                                                status: @js($order->status),
                                                priority: @js($order->priority ?? 'normal'),
                                                service_address: @js($order->service_address),
                                                city: @js($order->city),
                                                province: @js($order->province),
                                                postal_code: @js($order->postal_code),
                                                expected_completion_date: @js(optional($order->expected_completion_date)->format('M d, Y')),
                                                notes: @js($order->notes),
                                                service_description: @js($order->service_description),
                                                pdf_filename: @js($order->pdf_filename),
                                            })"
                                            class="inline-flex items-center justify-center gap-1 h-8 px-3 rounded-md text-xs font-semibold text-slate-700 hover:text-slate-900 dark:text-gray-200 dark:hover:text-white"
                                            title="View details"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </button>
                                        @if($order->pdf_filename)
                                            <a href="{{ asset('storage/generated/' . $order->pdf_filename) }}"
                                               download
                                               class="inline-flex items-center justify-center gap-1 h-8 px-3 rounded-md text-xs font-semibold text-blue-600 hover:text-blue-700"
                                               title="Download PDF form">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                Download
                                            </a>
                                        @else
                                            <button
                                                class="inline-flex items-center justify-center gap-1 h-8 px-3 rounded-md text-xs font-semibold text-blue-300 cursor-not-allowed"
                                                disabled>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                Download
                                            </button>
                                        @endif

                                        @if($order->status === 'pending')
                                            <form method="POST"
                                                  action="{{ route('customer.requests.cancel', $order) }}{{ $status !== '' ? '?status=' . $status : '' }}"
                                                  onsubmit="return confirm('Cancel this request?');"
                                                  class="flex items-center m-0">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center h-8 px-3 rounded-md text-xs font-semibold text-rose-600 hover:text-rose-700">
                                                    Cancel
                                                </button>
                                            </form>
                                        @else
                                            <button class="inline-flex items-center justify-center h-8 px-3 rounded-md text-xs font-semibold text-rose-300 cursor-not-allowed"
                                                    disabled>
                                                Cancel
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-sm text-gray-500">No requests found.</td>
                                    <!-- View Details Modal -->
                                    <div x-show="showViewModal" x-cloak
                                         x-on:keydown.escape.window="closeView()"
                                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                                         @click.self="closeView()">

                                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[92vh] overflow-y-auto"
                                             @click.stop>
                                            <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                                                <div>
                                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="selectedOrder?.job_order_number || 'Job Order'"></h3>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        <span x-text="selectedOrder?.service_type || 'N/A'"></span>
                                                        <span class="mx-2">•</span>
                                                        Requested: <span x-text="selectedOrder?.request_date || 'N/A'"></span>
                                                    </p>
                                                </div>
                                                <button @click="closeView()" class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="p-6 space-y-6">
                                                <!-- Badges -->
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700"
                                                          x-text="'Priority: ' + priorityLabel(selectedOrder?.priority)"></span>

                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700"
                                                          x-text="'Status: ' + statusLabel(selectedOrder?.status)"></span>

                                                    <template x-if="selectedOrder?.expected_completion_date">
                                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700"
                                                              x-text="'ETA: ' + selectedOrder.expected_completion_date"></span>
                                                    </template>
                                                </div>

                                                <!-- Timeline -->
                                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                                                    <p class="text-sm font-semibold text-slate-900 dark:text-white mb-3">Progress</p>

                                                    <div class="flex items-center justify-between gap-2">
                                                        <template x-for="(step, i) in statusSteps" :key="step">
                                                            <div class="flex-1">
                                                                <div class="flex items-center gap-2">
                                                                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold"
                                                                         :class="i <= stepIndex(selectedOrder?.status) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'">
                                                                        <span x-text="i+1"></span>
                                                                    </div>
                                                                    <div class="text-xs font-semibold"
                                                                         :class="i <= stepIndex(selectedOrder?.status) ? 'text-slate-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                                                                         x-text="statusLabel(step)"></div>
                                                                </div>
                                                                <div class="mt-2 h-1 rounded"
                                                                     :class="i < stepIndex(selectedOrder?.status) ? 'bg-blue-600' : 'bg-gray-200'"></div>
                                                            </div>
                                                        </template>
                                                    </div>

                                                    <template x-if="selectedOrder?.status === 'cancelled'">
                                                        <p class="mt-3 text-xs text-rose-600 font-semibold">This request was cancelled.</p>
                                                    </template>
                                                </div>

                                                <!-- Details grid -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                                                        <p class="text-xs font-semibold text-gray-500 mb-2">Service Description</p>
                                                        <p class="text-sm text-slate-900 dark:text-white whitespace-pre-line" x-text="selectedOrder?.service_description || 'N/A'"></p>
                                                    </div>

                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                                                        <p class="text-xs font-semibold text-gray-500 mb-2">Address</p>
                                                        <p class="text-sm text-slate-900 dark:text-white whitespace-pre-line">
                                                            <span x-text="selectedOrder?.service_address || 'N/A'"></span>
                                                            <template x-if="selectedOrder?.city || selectedOrder?.province || selectedOrder?.postal_code">
                                                                <span class="block text-xs text-gray-500 dark:text-gray-400 mt-2"
                                                                      x-text="[(selectedOrder?.city||''),(selectedOrder?.province||''),(selectedOrder?.postal_code||'')].filter(Boolean).join(', ')"></span>
                                                            </template>
                                                        </p>
                                                    </div>

                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 md:col-span-2">
                                                        <p class="text-xs font-semibold text-gray-500 mb-2">Special Instructions / Notes</p>
                                                        <p class="text-sm text-slate-900 dark:text-white whitespace-pre-line" x-text="selectedOrder?.notes || 'None'"></p>
                                                    </div>
                                                </div>

                                                <!-- Footer actions -->
                                                <div class="flex flex-wrap items-center justify-end gap-2 pt-2">
                                                    <template x-if="selectedOrder?.pdf_filename">
                                                        <a
                                                            :href="`{{ asset('storage/generated') }}/${selectedOrder.pdf_filename}`"
                                                            download
                                                            class="inline-flex items-center justify-center gap-1 h-9 px-4 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700"
                                                        >
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                            </svg>
                                                            Download PDF
                                                        </a>
                                                    </template>

                                                    <button type="button" @click="closeView()"
                                                            class="inline-flex items-center justify-center h-9 px-4 rounded-lg text-sm font-semibold bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-500">
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($jobOrders, 'links'))
                <div class="mt-4">{{ $jobOrders->links() }}</div>
            @endif

            <!-- Create Request Modal -->
            <div x-show="showCreateModal" x-cloak
                x-on:keydown.escape.window="showCreateModal = false"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
                @click.self="showCreateModal = false">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[92vh] overflow-y-auto"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                    @click.stop>
                    <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Create Service Request</h3>
                        <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('customer.requests.store') }}" class="p-6 space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Service Type *</label>
                                <select name="service_type" required
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                                    <option value="">Select service type</option>
                                    <option value="Calibration">Calibration</option>
                                    <option value="Repair">Repair</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Installation">Installation</option>
                                    <option value="Consultation">Consultation</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Priority Level *</label>
                                <select name="priority" required
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                                    <option value="normal">Normal</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Service Description *</label>
                            <textarea name="service_description" rows="4" required
                                    placeholder="Describe what you need..."
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Service Address *</label>
                            <input type="text" name="service_address" required
                                placeholder="Where will the service be performed?"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">City</label>
                                <input type="text" name="city"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Province</label>
                                <input type="text" name="province"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Postal Code</label>
                                <input type="text" name="postal_code"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Expected Completion Date</label>
                            <input type="date" name="expected_completion_date"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Special Instructions</label>
                            <textarea name="notes" rows="3"
                                    placeholder="Any special requirements or notes..."
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm"></textarea>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="submit"
                                    class="flex-1 px-6 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                Submit Request
                            </button>
                            <button type="button" @click="showCreateModal = false"
                                    class="px-6 py-2.5 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-semibold hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    <style>
        [x-cloak] { display: none !important; }
    </style>
