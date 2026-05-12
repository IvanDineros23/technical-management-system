@extends('layouts.dashboard')

@section('title', 'Job Orders')

@section('head')
    <script>
        function jobOrdersPage() {
            return {
                showJODetails: false,
                selectedJO: null,
                filterStatus: '{{ request("status") ?? "all" }}',
                customerRequestFormUrlBase: '{{ url('/marketing/job-orders') }}',
                init() {
                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.showJODetails) this.closeJODetails();
                        if (e.key === 'Escape') {
                            this.closeAllActionMenus();
                        }
                    });
                },
                openJODetails(jo) {
                    this.closeAllActionMenus();
                    const previewUrl = jo?.id
                        ? `${this.customerRequestFormUrlBase}/${jo.id}/customer-request-form?t=${Date.now()}#page=1&zoom=page-width`
                        : null;

                    this.selectedJO = {
                        ...jo,
                        customer_request_form_url: previewUrl,
                    };
                    this.showJODetails = true;
                    document.body.style.overflow = 'hidden';
                },
                closeJODetails() {
                    this.showJODetails = false;
                    this.selectedJO = null;
                    document.body.style.overflow = 'auto';
                },
                closeAllActionMenus() {
                    document.querySelectorAll('.row-actions[open]').forEach((item) => {
                        item.open = false;
                    });
                },
                closeOtherActionMenus(currentElement) {
                    document.querySelectorAll('.row-actions[open]').forEach((item) => {
                        if (item !== currentElement) {
                            item.open = false;
                        }
                    });
                },
                formatDate(d) {
                    if (!d) return 'N/A';
                    const dt = new Date(d);
                    return isNaN(dt) ? d : dt.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
                },
                formatStatus(status) {
                    if (!status) return 'N/A';
                    return status.replaceAll('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase());
                },
                filterByStatus(status) {
                    const url = new URL(window.location);
                    if (status === 'all') url.searchParams.delete('status');
                    else url.searchParams.set('status', status);
                    // keep q if exists
                    window.location.href = url.toString();
                }
            }
        }
    </script>
@endsection

@section('sidebar-nav')
    <a href="{{ route('marketing.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
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
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Job Orders
    </a>

    <a href="{{ route('marketing.customer-request-forms') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Request Forms
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
    <div x-data="jobOrdersPage()" class="w-full min-h-screen">
        <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 sm:px-0">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Job Orders</h2>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">View and manage all job orders</p>
            </div>
            <a href="{{ route('marketing.create-job-order') }}"
               class="inline-flex w-full sm:w-auto justify-center items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Manual Create JO
            </a>
        </div>

        <!-- Filters -->
        <div class="mb-4 sm:mb-6 flex flex-wrap gap-2 sm:gap-3 px-4 sm:px-0">
            <button @click="filterByStatus('all')" :class="filterStatus === 'all' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600'" class="px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                All Orders
            </button>
            <button @click="filterByStatus('pending')" :class="filterStatus === 'pending' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600'" class="px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                Pending
            </button>
            <button @click="filterByStatus('for_accounting_approval')" :class="filterStatus === 'for_accounting_approval' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600'" class="px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                For Accounting
            </button>
            <button @click="filterByStatus('approved')" :class="filterStatus === 'approved' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600'" class="px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                Approved
            </button>
            <button @click="filterByStatus('in_progress')" :class="filterStatus === 'in_progress' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600'" class="px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                In Progress
            </button>
            <button @click="filterByStatus('completed')" :class="filterStatus === 'completed' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600'" class="px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                Completed
            </button>
            <button @click="filterByStatus('rejected')" :class="filterStatus === 'rejected' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600'" class="px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                Rejected
            </button>
        </div>

        <!-- Search Bar -->
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 sm:px-0">
                    <form method="GET" action="{{ route('marketing.job-orders') }}" class="w-full sm:w-96">
              {{-- keep current status when searching --}}
              @if(request('status'))
                  <input type="hidden" name="status" value="{{ request('status') }}">
              @endif

              <div class="relative">
                  <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18.0a7.5 7.5 0 006.15-3.35z"/>
                  </svg>

                  <input
                      name="q"
                      value="{{ request('q') }}"
                      type="text"
                      placeholder="Search JO / customer / email / service / status..."
                      class="w-full pl-9 pr-20 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >

                  <button type="submit"
                      class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 text-xs font-semibold rounded-md bg-blue-600 text-white hover:bg-blue-700">
                      Search
                  </button>
              </div>
          </form>

          <div class="text-xs text-gray-500 dark:text-gray-400">
              Tip: Try “JO-00008” or “Repair"
          </div>
        </div>

    <!-- Job Orders Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg sm:rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden mx-4 sm:mx-0">
        <!-- Mobile Cards -->
        <div class="sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($jobOrders as $jobOrder)
                @php
                    $isCustomerRequest = $jobOrder->creator && $jobOrder->creator->role && $jobOrder->creator->role->slug === 'customer';
                @endphp
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $jobOrder->job_order_number }}</div>
                            @if($isCustomerRequest)
                                <span class="mt-1 inline-flex items-center px-2 py-0.5 text-[11px] font-semibold rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200">
                                    Customer Request
                                </span>
                            @endif
                        </div>
                        <div>
                            @if($jobOrder->status === 'pending')
                                <span class="px-2.5 py-1 text-xs font-semibold bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded-full">Pending</span>
                            @elseif($jobOrder->status === 'for_accounting_approval')
                                <span class="px-2.5 py-1 text-xs font-semibold bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 rounded-full">For Accounting</span>
                            @elseif($jobOrder->status === 'approved')
                                <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full">Approved</span>
                            @elseif($jobOrder->status === 'in_progress')
                                <span class="px-2.5 py-1 text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full">In Progress</span>
                            @elseif($jobOrder->status === 'completed')
                                <span class="px-2.5 py-1 text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full">Completed</span>
                            @elseif($jobOrder->status === 'rejected')
                                <span class="px-2.5 py-1 text-xs font-semibold bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 rounded-full">Rejected</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full">{{ ucfirst($jobOrder->status) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $jobOrder->customer->name ?? 'N/A' }}</p>
                        <p class="break-all">{{ $jobOrder->customer->email ?? '' }}</p>
                        <p><span class="font-medium">Service:</span> {{ $jobOrder->service_type ?? 'N/A' }}</p>
                        <p><span class="font-medium">Created:</span> {{ $jobOrder->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button @click='openJODetails(@json($jobOrder))' class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                            View
                        </button>
                        <details class="row-actions relative text-left" @toggle="if ($el.open) closeOtherActionMenus($el)" @click.outside="$el.open = false">
                            <summary class="list-none cursor-pointer inline-flex items-center gap-1 text-slate-600 hover:text-slate-800 dark:text-gray-300 dark:hover:text-white text-sm font-medium">
                                Actions
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            @if($jobOrder->status === 'pending')
                                @if($isCustomerRequest)
                                    <div class="absolute right-0 top-full mt-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 p-2 space-y-1 min-w-[170px] shadow-lg z-30">
                                        <form method="POST" action="{{ route('marketing.job-orders.approve', $jobOrder) }}" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full text-left text-emerald-600 hover:text-emerald-700 font-semibold">
                                                Accept
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('marketing.job-orders.reject', $jobOrder) }}" class="m-0" onsubmit="return confirm('Decline this customer request?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full text-left text-rose-600 hover:text-rose-700 font-semibold">
                                                Decline
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="absolute right-0 top-full mt-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 p-2 space-y-1 min-w-[190px] shadow-lg z-30">
                                        <a href="{{ route('marketing.job-orders.edit', $jobOrder) }}" class="block text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 font-medium">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('marketing.job-orders.submit-accounting', $jobOrder) }}" class="m-0" onsubmit="return confirm('Submit this pending JO to accounting?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full text-left text-emerald-600 hover:text-emerald-700 font-semibold">
                                                Submit to Accounting
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @else
                                <div class="absolute right-0 top-full mt-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 p-2 space-y-1 min-w-[190px] shadow-lg z-30">
                                    <a href="{{ route('marketing.job-orders.edit', $jobOrder) }}" class="block text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 font-medium">
                                        Edit
                                    </a>
                                    @if($jobOrder->pdf_filename)
                                        <a href="{{ route('marketing.job-orders.download', $jobOrder) }}" class="block text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                                            Download PDF
                                        </a>
                                    @else
                                        <span class="block text-indigo-300 dark:text-indigo-500 font-medium cursor-not-allowed" title="No PDF available yet">
                                            Download PDF
                                        </span>
                                    @endif
                                    <form method="POST" action="{{ route('marketing.job-orders.destroy', $jobOrder) }}" class="m-0" onsubmit="return confirm('Delete this job order?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-left text-rose-600 hover:text-rose-700 font-semibold">
                                            Delete
                                        </button>
                                    </form>
                                    @if(in_array($jobOrder->status, ['rejected', 'cancelled'], true))
                                        <form method="POST" action="{{ route('marketing.job-orders.submit-accounting', $jobOrder) }}" class="m-0" onsubmit="return confirm('Resubmit this job order to accounting?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full text-left text-emerald-600 hover:text-emerald-700 font-semibold">
                                                Submit to Accounting
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </details>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No job orders found</div>
            @endforelse
        </div>

        <div class="hidden sm:block overflow-x-auto max-w-full">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">JO Number</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Service Type</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Date Created</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($jobOrders as $jobOrder)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-center align-middle">
                                @php
                                    $isCustomerRequest = $jobOrder->creator && $jobOrder->creator->role && $jobOrder->creator->role->slug === 'customer';
                                @endphp
                                <div class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $jobOrder->job_order_number }}</div>
                                @if($isCustomerRequest)
                                    <span class="mt-1 inline-flex items-center px-2 py-0.5 text-[11px] font-semibold rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200">
                                        Customer Request
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center align-middle">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $jobOrder->customer->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $jobOrder->customer->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center align-middle text-sm text-gray-700 dark:text-gray-300">{{ $jobOrder->service_type ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center align-middle text-sm text-gray-700 dark:text-gray-300">{{ $jobOrder->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center align-middle">
                                @if($jobOrder->status === 'pending')
                                    <span class="px-3 py-1 text-xs font-semibold bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded-full">Pending</span>
                                @elseif($jobOrder->status === 'for_accounting_approval')
                                    <span class="px-3 py-1 text-xs font-semibold bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 rounded-full">For Accounting Approval</span>
                                @elseif($jobOrder->status === 'approved')
                                    <span class="px-3 py-1 text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full">Approved</span>
                                @elseif($jobOrder->status === 'in_progress')
                                    <span class="px-3 py-1 text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full">In Progress</span>
                                @elseif($jobOrder->status === 'completed')
                                    <span class="px-3 py-1 text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full">Completed</span>
                                @elseif($jobOrder->status === 'rejected')
                                    <span class="px-3 py-1 text-xs font-semibold bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 rounded-full">Rejected</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full">{{ ucfirst($jobOrder->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center align-middle">
                                <div class="flex items-center justify-center gap-2 min-w-[180px]">
                                    <button
                                        @click='openJODetails(@json($jobOrder))'
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium"
                                    >
                                        View
                                    </button>

                                    <details class="row-actions relative text-left" @toggle="if ($el.open) closeOtherActionMenus($el)" @click.outside="$el.open = false">
                                        <summary class="list-none cursor-pointer inline-flex items-center gap-1 text-slate-600 hover:text-slate-800 dark:text-gray-300 dark:hover:text-white font-medium">
                                            Actions
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </summary>

                                        @if($jobOrder->status === 'pending')
                                            @if($isCustomerRequest)
                                                <div class="absolute right-0 top-full mt-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 p-2 space-y-1 min-w-[170px] shadow-lg z-30">
                                                    <form method="POST" action="{{ route('marketing.job-orders.approve', $jobOrder) }}" class="m-0">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-full text-left text-emerald-600 hover:text-emerald-700 font-semibold">
                                                            Accept
                                                        </button>
                                                    </form>

                                                    <form method="POST" action="{{ route('marketing.job-orders.reject', $jobOrder) }}" class="m-0"
                                                          onsubmit="return confirm('Decline this customer request?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-full text-left text-rose-600 hover:text-rose-700 font-semibold">
                                                            Decline
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <div class="absolute right-0 top-full mt-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 p-2 space-y-1 min-w-[190px] shadow-lg z-30">
                                                    <a
                                                        href="{{ route('marketing.job-orders.edit', $jobOrder) }}"
                                                        class="block text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 font-medium"
                                                    >
                                                        Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('marketing.job-orders.submit-accounting', $jobOrder) }}" class="m-0"
                                                          onsubmit="return confirm('Submit this pending JO to accounting?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-full text-left text-emerald-600 hover:text-emerald-700 font-semibold">
                                                            Submit to Accounting
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        @else
                                            <div class="absolute right-0 top-full mt-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 p-2 space-y-1 min-w-[190px] shadow-lg z-30">
                                                <a
                                                    href="{{ route('marketing.job-orders.edit', $jobOrder) }}"
                                                    class="block text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 font-medium"
                                                >
                                                    Edit
                                                </a>

                                                @if($jobOrder->pdf_filename)
                                                    <a
                                                        href="{{ route('marketing.job-orders.download', $jobOrder) }}"
                                                        class="block text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium"
                                                    >
                                                        Download PDF
                                                    </a>
                                                @else
                                                    <span class="block text-indigo-300 dark:text-indigo-500 font-medium cursor-not-allowed" title="No PDF available yet">
                                                        Download PDF
                                                    </span>
                                                @endif

                                                <form method="POST" action="{{ route('marketing.job-orders.destroy', $jobOrder) }}" class="m-0"
                                                      onsubmit="return confirm('Delete this job order?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full text-left text-rose-600 hover:text-rose-700 font-semibold">
                                                        Delete
                                                    </button>
                                                </form>

                                                @if(in_array($jobOrder->status, ['rejected', 'cancelled'], true))
                                                    <form method="POST" action="{{ route('marketing.job-orders.submit-accounting', $jobOrder) }}" class="m-0"
                                                          onsubmit="return confirm('Resubmit this job order to accounting?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-full text-left text-emerald-600 hover:text-emerald-700 font-semibold">
                                                            Submit to Accounting
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </details>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                No job orders found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                Showing {{ $jobOrders->firstItem() ?? 0 }} to {{ $jobOrders->lastItem() ?? 0 }} of {{ $jobOrders->total() }} results
            </div>
            <div class="flex gap-2">
                {{ $jobOrders->appends(request()->query())->links() }}
            </div>
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
                                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all w-[95vw] max-w-7xl max-h-[92vh] overflow-y-auto">

                                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-3 sm:py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-white" x-text="selectedJO?.job_order_number || 'Job Order Details'"></h3>
                        <button @click="closeJODetails()" class="text-white hover:text-gray-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-4">
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
                                      'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300': selectedJO?.status === 'for_accounting_approval',
                                      'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300': selectedJO?.status === 'approved',
                                      'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300': selectedJO?.status === 'in_progress',
                                      'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300': selectedJO?.status === 'completed',
                                      'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300': selectedJO?.status === 'rejected',
                                      'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300': !['pending','for_accounting_approval','approved','in_progress','completed','rejected'].includes(selectedJO?.status)
                                  }" x-text="(selectedJO?.status || 'N/A').replace('_',' ')"></span>
                            <p x-show="selectedJO?.status === 'for_accounting_approval'" class="mt-2 text-xs text-violet-700 dark:text-violet-300">
                                Waiting for Accounting Approval
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Service Type</p>
                            <p class="font-medium text-gray-900 dark:text-white" x-text="selectedJO?.service_type || 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Created</p>
                            <p class="font-medium text-gray-900 dark:text-white" x-text="formatDate(selectedJO?.created_at)"></p>
                        </div>
                    </div>

                    <div x-show="selectedJO?.status === 'pending'" x-cloak class="mt-2 rounded-xl border border-amber-200 dark:border-amber-700/40 bg-amber-50/60 dark:bg-amber-900/10 p-4 space-y-4">
                        <h4 class="text-sm font-semibold text-amber-800 dark:text-amber-300">Customer Request Form</h4>

                        <div class="rounded-lg border border-amber-200/80 dark:border-amber-700/40 bg-white dark:bg-gray-900 overflow-hidden">
                            <div class="px-3 py-2 border-b border-amber-200/80 dark:border-amber-700/40 flex items-center justify-between">
                                <p class="text-xs font-medium text-gray-600 dark:text-gray-300">Customer Request Form PDF Preview</p>
                                <a :href="selectedJO?.customer_request_form_url"
                                   target="_blank"
                                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline"
                                >
                                    Open PDF
                                </a>
                            </div>
                            <iframe
                                :src="selectedJO?.customer_request_form_url"
                                class="w-full h-[520px] bg-white"
                                title="Customer Request Form PDF Preview"
                            ></iframe>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Requested By</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="selectedJO?.requested_by || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Priority</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="formatStatus(selectedJO?.priority)"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Expected Completion</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="formatDate(selectedJO?.expected_completion_date)"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Client PO Ctrl No</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="selectedJO?.client_po_ctrl_no || 'N/A'"></p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Service Address</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="selectedJO?.service_address || 'N/A'"></p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Service Description</p>
                            <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line" x-text="selectedJO?.service_description || 'N/A'"></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Service Invoice Number</p>
                                <p class="text-sm text-gray-800 dark:text-gray-200" x-text="selectedJO?.service_invoice_number || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Terms</p>
                                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line" x-text="selectedJO?.terms || 'N/A'"></p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Notes</p>
                            <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line" x-text="selectedJO?.notes || 'N/A'"></p>
                        </div>

                        <div x-show="selectedJO?.items && selectedJO.items.length > 0" x-cloak>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Requested Items</p>
                            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-white dark:bg-gray-800">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-300">#</th>
                                            <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-300">Equipment</th>
                                            <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-300">Model</th>
                                            <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-300">Serial</th>
                                            <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-300">Capacity</th>
                                            <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-300">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(item, idx) in (selectedJO?.items || [])" :key="item.id || idx">
                                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                                <td class="px-3 py-2 text-gray-800 dark:text-gray-200" x-text="item.item_number || (idx + 1)"></td>
                                                <td class="px-3 py-2 text-gray-800 dark:text-gray-200" x-text="item.equipment_type || 'N/A'"></td>
                                                <td class="px-3 py-2 text-gray-800 dark:text-gray-200" x-text="item.model || 'N/A'"></td>
                                                <td class="px-3 py-2 text-gray-800 dark:text-gray-200" x-text="item.serial_number || 'N/A'"></td>
                                                <td class="px-3 py-2 text-gray-800 dark:text-gray-200" x-text="item.range || 'N/A'"></td>
                                                <td class="px-3 py-2 text-gray-800 dark:text-gray-200" x-text="item.quantity || 1"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 sm:px-6 py-4 flex justify-end gap-3">
                    <button @click="closeJODetails()" class="w-full sm:w-auto px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    [x-cloak] { display: none !important; }
</style>