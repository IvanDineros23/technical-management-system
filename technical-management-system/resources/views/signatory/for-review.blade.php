@extends('layouts.dashboard')

@section('title', 'Calibrations for Review')
@section('page-title', 'For Review')
@section('page-subtitle', 'Approved calibrations awaiting signature')

@section('sidebar-nav')
    @include('signatory.partials.sidebar', ['pendingCount' => $calibrations->total()])
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">🔍 Filters</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Job Order</label>
                    <input type="text" name="job_order" placeholder="WO-xxx" value="{{ request('job_order') }}"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Calibrations Table -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    📋 Calibrations for Review
                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ $calibrations->total() }} total)</span>
                </h3>
            </div>

            @if($calibrations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 dark:border-gray-700">
                            <tr class="text-left text-xs">
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">WO Number</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Customer</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Technician</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Cal Date</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Points</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Pass/Fail</th>
                                <th class="pb-3 text-right font-semibold text-gray-600 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($calibrations as $calibration)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="py-3 text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $calibration->assignment->jobOrder->job_order_number }}
                                    </td>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $calibration->assignment->jobOrder->customer->name }}
                                    </td>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $calibration->performedBy->name }}
                                    </td>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $calibration->calibration_date->setTimezone('Asia/Manila')->format('M d, Y') }}
                                    </td>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $calibration->measurementPoints->count() }}
                                    </td>
                                    <td class="py-3">
                                        @if($calibration->pass_fail === 'pass')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">✓ Pass</span>
                                        @elseif($calibration->pass_fail === 'fail')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200">✗ Fail</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">~ Conditional</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('signatory.review', $calibration) }}" class="inline-block px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $calibrations->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No calibrations for review</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All pending calibrations have been reviewed</p>
                </div>
            @endif
        </div>
    </div>
@endsection
