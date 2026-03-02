@extends('layouts.dashboard')

@section('title', 'Accounting Dashboard')
@section('page-title', 'Accounting Approvals')
@section('page-subtitle', 'Review job orders endorsed by marketing')

@section('sidebar-nav')
    <a href="{{ route('accounting.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Pending Approvals
    </a>
@endsection

@section('content')
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Job Orders for Approval</h2>

            <form method="GET" action="{{ route('accounting.dashboard') }}" class="w-full sm:w-80">
                <div class="relative">
                    <input name="q" value="{{ $search }}" type="text" placeholder="Search JO / customer / service"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 pl-3 pr-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">JO Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Submitted By</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pendingApprovals as $jobOrder)
                        <tr>
                            <td class="px-6 py-4 text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $jobOrder->job_order_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">{{ $jobOrder->customer->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $jobOrder->service_type ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $jobOrder->creator->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('accounting.job-orders.customer-request-form', $jobOrder) }}" target="_blank"
                                       class="text-blue-600 hover:text-blue-700 font-semibold">
                                        View
                                    </a>

                                    <form method="POST" action="{{ route('accounting.job-orders.approve', $jobOrder) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-700 font-semibold">Approve</button>
                                    </form>

                                    <form method="POST" action="{{ route('accounting.job-orders.reject', $jobOrder) }}"
                                          onsubmit="return confirm('Return this JO to marketing for revision?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold">Return</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No job orders waiting for accounting approval.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $pendingApprovals->links() }}
            </div>
        </div>
    </div>
@endsection
