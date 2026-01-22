@extends('layouts.dashboard')

@section('title', 'Job Order Timeline')

@section('sidebar-nav')
    <a href="{{ route('accounting.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.dashboard') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('accounting.payments') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.payments') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Payment Verification
    </a>

    <a href="{{ route('accounting.certificates.for-release') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.certificates.for-release') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        For Release
    </a>

    <a href="{{ route('accounting.certificates.released') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.certificates.released') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        Released History
    </a>

    <a href="{{ route('accounting.reports') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.reports') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Reports
    </a>

    <a href="{{ route('accounting.timelines') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.timelines', 'accounting.timeline') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Timeline
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Job Order Info Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $jobOrder->job_order_number }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $jobOrder->customer->name ?? 'N/A' }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                    {{ $jobOrder->status === 'completed' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 
                       ($jobOrder->status === 'in_progress' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : 
                        'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300') }}">
                    {{ ucfirst(str_replace('_', ' ', $jobOrder->status)) }}
                </span>
            </div>
        </div>

        <!-- Financial Stats -->
        <div class="grid grid-cols-4 gap-4 mt-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/10 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold">Payment Status</p>
                <p class="text-lg font-bold text-blue-900 dark:text-blue-100 mt-1 capitalize">{{ $stats['payment_status'] }}</p>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/10 rounded-lg p-4 border border-green-200 dark:border-green-800">
                <p class="text-xs text-green-600 dark:text-green-400 font-semibold">Payment Amount</p>
                <p class="text-lg font-bold text-green-900 dark:text-green-100 mt-1">₱{{ number_format($stats['payment_amount'], 2) }}</p>
            </div>
            <div class="bg-gradient-to-br from-violet-50 to-violet-100 dark:from-violet-900/20 dark:to-violet-900/10 rounded-lg p-4 border border-violet-200 dark:border-violet-800">
                <p class="text-xs text-violet-600 dark:text-violet-400 font-semibold">Certificates Generated</p>
                <p class="text-lg font-bold text-violet-900 dark:text-violet-100 mt-1">{{ $stats['certificates_generated'] }}</p>
            </div>
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-900/10 rounded-lg p-4 border border-emerald-200 dark:border-emerald-800">
                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold">Certificates Released</p>
                <p class="text-lg font-bold text-emerald-900 dark:text-emerald-100 mt-1">{{ $stats['certificates_released'] }}</p>
            </div>
        </div>
    </div>

    <!-- Timeline Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Job Order Timeline</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Complete workflow history and events</p>
        </div>
        
        <div class="space-y-4">
            @forelse($timeline as $event)
                <div class="flex gap-4">
                    <!-- Timeline dot -->
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 rounded-full {{ $event['status'] === 'success' ? 'bg-green-500' : ($event['status'] === 'warning' ? 'bg-yellow-500' : 'bg-blue-500') }}"></div>
                        @if(!$loop->last)
                            <div class="w-0.5 h-full bg-gray-200 dark:bg-gray-700 mt-1"></div>
                        @endif
                    </div>
                    
                    <!-- Event content -->
                    <div class="flex-1 pb-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $event['event'] }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $event['description'] }}</p>
                                @if($event['created_by'])
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                        by {{ $event['created_by']->name ?? 'System' }}
                                    </p>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-500 whitespace-nowrap">
                                {{ is_object($event['created_at']) ? $event['created_at']->setTimezone('Asia/Manila')->format('M d, Y h:i A') : $event['created_at'] }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No timeline events</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Timeline events will appear here as the job progresses.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
