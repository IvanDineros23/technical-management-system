@extends('layouts.dashboard')

@section('title', 'Job Completion Certificates')
@section('page-title', 'Job Completion Certificates')
@section('page-subtitle', 'Review and sign certificates for completed jobs')

@section('sidebar-nav')
    @include('signatory.partials.sidebar')
@endsection

@section('content')
<div class="space-y-4 sm:space-y-6">

    {{-- Header Stats --}}
    <div class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-3 sm:gap-4">
        <div class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl px-4 sm:px-5 py-3 w-full sm:w-auto">
            <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="block w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke-width="2" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 12.5l2.5 2.5 4.5-5" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-emerald-700 dark:text-emerald-400 font-medium">Ready for Signing</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-300 leading-none">{{ $pendingCount }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 sm:p-5">
        <form method="GET" action="{{ route('signatory.job-certifications') }}" class="flex flex-col sm:flex-row gap-3 sm:items-end">
            {{-- Search --}}
            <div class="flex-1 min-w-0">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Search</label>
                <input type="text" name="q" value="{{ $search }}"
                       placeholder="JO number, customer name..."
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors">
                Search
            </button>
        </form>
    </div>

    {{-- Job Certifications Grid --}}
    @if($jobOrders->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
            @foreach($jobOrders as $jobOrder)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-4 sm:p-6">
                        {{-- Header --}}
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $jobOrder->job_order_number }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 break-words">
                                    {{ $jobOrder->customer->name ?? 'N/A' }}
                                </p>
                            </div>
                            <span class="inline-block self-start px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200 whitespace-nowrap">
                                Ready to Sign
                            </span>
                        </div>

                        {{-- Details --}}
                        <div class="space-y-3 mb-5 sm:mb-6 text-sm">
                            <div class="flex flex-col sm:flex-row sm:justify-between gap-1 sm:gap-3">
                                <span class="text-gray-600 dark:text-gray-400">Assigned Technician:</span>
                                <span class="font-medium text-gray-900 dark:text-white text-left sm:text-right break-words">
                                    {{ optional($jobOrder->lastAssignment)->technician->name ?? 'Not Assigned' }}
                                </span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:justify-between gap-1 sm:gap-3">
                                <span class="text-gray-600 dark:text-gray-400">Service Type:</span>
                                <span class="font-medium text-gray-900 dark:text-white text-left sm:text-right break-words">
                                    {{ $jobOrder->service_type ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:justify-between gap-1 sm:gap-3">
                                <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                <span class="inline-block px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200">
                                    {{ ucwords(str_replace('_', ' ', $jobOrder->status)) }}
                                </span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-col sm:flex-row gap-3 pt-5 sm:pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('signatory.job-certifications.review', $jobOrder->id) }}" class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium text-center transition-colors">
                                Review & Sign
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6 flex justify-center sm:justify-end overflow-x-auto">
            {{ $jobOrders->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6 sm:p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="block w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke-width="2" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 12.5l2.5 2.5 4.5-5" />
                </svg>
            </div>
            <p class="text-gray-500 dark:text-gray-400 text-sm">No certificates ready for signing</p>
            <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Waiting for Accounting to approve completed jobs.</p>
        </div>
    @endif

</div>
@endsection
