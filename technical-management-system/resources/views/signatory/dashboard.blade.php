@extends('layouts.dashboard')

@section('title', 'Signatory Dashboard')
@section('page-title', 'Signatory Dashboard')
@section('page-subtitle', 'Review, approve, and sign calibration certificates')

@section('sidebar-nav')
    @include('signatory.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['for_review'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">For Review</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['approved'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Approved</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['returned'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Returned</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4.242 4.242a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4.242-4.242a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['signed'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Signed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Submissions for Review -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">📋 Recent Submissions for Review</h3>
                <a href="{{ route('signatory.for-review') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-medium">View all →</a>
            </div>

            @if($recentSubmissions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 dark:border-gray-700">
                            <tr class="text-left text-xs">
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">WO Number</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Customer</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Technician</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Calibration Date</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="pb-3 text-right font-semibold text-gray-600 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($recentSubmissions as $calibration)
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
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">
                                            ⏳ For Review
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('signatory.review', $calibration) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-medium">
                                            Review →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No calibrations pending review</p>
                </div>
            @endif
        </div>

        <!-- Quick Info -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-900/30 rounded-[20px] border border-purple-200 dark:border-purple-800 p-6">
            <h3 class="text-sm font-bold text-purple-900 dark:text-purple-200 mb-2">⚡ Quick Action</h3>
            <a href="{{ route('signatory.for-review') }}" class="inline-block text-xs font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 transition-colors">
                View pending calibrations →
            </a>
        </div>
    </div>
@endsection
