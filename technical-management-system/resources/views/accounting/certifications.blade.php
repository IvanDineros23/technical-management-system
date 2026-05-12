@extends('layouts.dashboard')

@section('title', 'Job Certifications')
@section('page-title', 'Job Certifications')
@section('page-subtitle', 'Review and approve completed jobs for certification signing')

@section('sidebar-nav')
    @include('accounting.partials.sidebar')
@endsection

@section('content')
<div class="space-y-4 sm:space-y-6">

    {{-- Header Stats --}}
    <div class="flex flex-wrap items-center gap-2 sm:gap-4">
        <div class="flex items-center gap-2 sm:gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl px-3 sm:px-5 py-2 sm:py-3">
            <div class="w-9 h-9 sm:w-10 sm:h-10 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-blue-700 dark:text-blue-400 font-medium">Pending Certification Approval</p>
                <p class="text-lg sm:text-2xl font-bold text-blue-600 dark:text-blue-300">{{ $pendingCount }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-3 sm:p-5">
        <form method="GET" action="{{ route('accounting.certifications') }}" class="space-y-3 sm:space-y-0 sm:flex sm:flex-wrap sm:gap-3 sm:items-end">
            {{-- Search --}}
            <div class="w-full sm:flex-1">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Search</label>
                <input type="text" name="q" value="{{ $search }}"
                       placeholder="JO number, customer name..."
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="w-full sm:w-auto px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                Search
            </button>
        </form>
    </div>

    {{-- Job Certifications Mobile Cards --}}
    <div class="space-y-3 md:hidden">
        @forelse($jobOrders as $jobOrder)
            <div class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm p-3 sm:p-4">
                {{-- Header: JO Number and Customer --}}
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-3">
                    <div>
                        <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">{{ $jobOrder->job_order_number }}</p>
                        <p class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $jobOrder->customer->name ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Details Grid --}}
                <div class="space-y-2 mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:gap-3">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Technician</p>
                            <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">{{ optional($jobOrder->lastAssignment)->technician->name ?? 'Not Assigned' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Service Type</p>
                            <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">{{ $jobOrder->service_type ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Approval Status --}}
                <div class="mb-4">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200">
                        ✓ Approved by Tech Head
                    </span>
                </div>

                {{-- Action Button --}}
                <form action="{{ route('accounting.certifications.approve', $jobOrder->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                        Approve for Signing
                    </button>
                </form>
            </div>
        @empty
            <div class="p-6 sm:p-8 text-center rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">No pending certifications</p>
                <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">All completed jobs requiring certification have been processed.</p>
            </div>
        @endforelse
    </div>

    {{-- Job Certifications Table --}}
    <div class="hidden md:block bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($jobOrders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300">Job Order</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 hidden sm:table-cell">Customer</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 hidden md:table-cell">Technician</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 hidden lg:table-cell">Service Type</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 hidden sm:table-cell">Tech Head Approval</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($jobOrders as $jobOrder)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">{{ $jobOrder->job_order_number }}</span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 hidden sm:table-cell">
                                    {{ $jobOrder->customer->name ?? 'N/A' }}
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 hidden md:table-cell">
                                    {{ optional($jobOrder->lastAssignment)->technician->name ?? 'Not Assigned' }}
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 hidden lg:table-cell">
                                    {{ $jobOrder->service_type ?? 'N/A' }}
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs hidden sm:table-cell">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200">
                                        ✓ Approved
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-right">
                                    <form action="{{ route('accounting.certifications.approve', $jobOrder->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2 sm:px-4 py-1 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition-colors whitespace-nowrap">
                                            Approve
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200 dark:border-gray-700 text-xs sm:text-sm">
                {{ $jobOrders->links() }}
            </div>
        @else
            <div class="p-6 sm:p-8 text-center">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">No pending certifications</p>
                <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">All completed jobs requiring certification have been processed.</p>
            </div>
        @endif
    </div>

</div>
@endsection
