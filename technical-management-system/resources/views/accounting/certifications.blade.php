@extends('layouts.dashboard')

@section('title', 'Job Certifications')
@section('page-title', 'Job Certifications')
@section('page-subtitle', 'Review and approve completed jobs for certification signing')

@section('sidebar-nav')
    @include('accounting.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header Stats --}}
    <div class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl px-5 py-3">
            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-blue-700 dark:text-blue-400 font-medium">Pending Certification Approval</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-300">{{ $pendingCount }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
        <form method="GET" action="{{ route('accounting.certifications') }}" class="flex flex-wrap gap-3 items-end">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Search</label>
                <input type="text" name="q" value="{{ $search }}"
                       placeholder="JO number, customer name..."
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                Search
            </button>
        </form>
    </div>

    {{-- Job Certifications Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($jobOrders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300">Job Order</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300">Technician</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300">Service Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300">Tech Head Approval</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($jobOrders as $jobOrder)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $jobOrder->job_order_number }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $jobOrder->customer->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ optional($jobOrder->lastAssignment)->technician->name ?? 'Not Assigned' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $jobOrder->service_type ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200">
                                        ✓ Approved
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('accounting.certifications.approve', $jobOrder->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition-colors">
                                            Approve for Signing
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $jobOrders->links() }}
            </div>
        @else
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">No pending certifications</p>
                <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">All completed jobs requiring certification have been processed.</p>
            </div>
        @endif
    </div>

</div>
@endsection
