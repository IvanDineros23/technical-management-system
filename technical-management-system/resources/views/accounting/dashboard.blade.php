@extends('layouts.dashboard')

@section('title', 'Accounting Dashboard')
@section('page-title', 'Accounting Dashboard')
@section('page-subtitle', 'Track incoming requests and job order workflow')

@section('sidebar-nav')
    @include('accounting.partials.sidebar')
@endsection

@section('content')
    @php
        $totalWorkflow = ($stats['new_requests'] ?? 0) + ($stats['approved'] ?? 0) + ($stats['ongoing'] ?? 0) + ($stats['completed'] ?? 0);
        $safeTotal = max($totalWorkflow, 1);
        $newPercent = round((($stats['new_requests'] ?? 0) / $safeTotal) * 100);
        $approvedPercent = round((($stats['approved'] ?? 0) / $safeTotal) * 100);
        $ongoingPercent = round((($stats['ongoing'] ?? 0) / $safeTotal) * 100);
        $completedPercent = round((($stats['completed'] ?? 0) / $safeTotal) * 100);
    @endphp

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">New Requests</p>
                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $stats['new_requests'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Approved</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['approved'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Ongoing Job Orders</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['ongoing'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Completed</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['completed'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="xl:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Workflow Snapshot</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Total: {{ $totalWorkflow }}</span>
                </div>

                <div class="space-y-3">
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-amber-700 dark:text-amber-300">New Requests</span>
                            <span class="text-gray-600 dark:text-gray-300">{{ $stats['new_requests'] ?? 0 }} ({{ $newPercent }}%)</span>
                        </div>
                        <div class="h-2 rounded-full bg-amber-100 dark:bg-amber-900/30 overflow-hidden">
                            <div class="h-full bg-amber-500" style="width: {{ $newPercent }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-emerald-700 dark:text-emerald-300">Approved</span>
                            <span class="text-gray-600 dark:text-gray-300">{{ $stats['approved'] ?? 0 }} ({{ $approvedPercent }}%)</span>
                        </div>
                        <div class="h-2 rounded-full bg-emerald-100 dark:bg-emerald-900/30 overflow-hidden">
                            <div class="h-full bg-emerald-500" style="width: {{ $approvedPercent }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-blue-700 dark:text-blue-300">Ongoing</span>
                            <span class="text-gray-600 dark:text-gray-300">{{ $stats['ongoing'] ?? 0 }} ({{ $ongoingPercent }}%)</span>
                        </div>
                        <div class="h-2 rounded-full bg-blue-100 dark:bg-blue-900/30 overflow-hidden">
                            <div class="h-full bg-blue-500" style="width: {{ $ongoingPercent }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-green-700 dark:text-green-300">Completed</span>
                            <span class="text-gray-600 dark:text-gray-300">{{ $stats['completed'] ?? 0 }} ({{ $completedPercent }}%)</span>
                        </div>
                        <div class="h-2 rounded-full bg-green-100 dark:bg-green-900/30 overflow-hidden">
                            <div class="h-full bg-green-500" style="width: {{ $completedPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 space-y-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Quick Actions</h3>

                <a href="{{ route('accounting.timeline') }}" class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Open Timeline</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">View all workflow updates</p>
                    </div>
                    <span class="text-blue-600 dark:text-blue-400">→</span>
                </a>

                <div class="p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Need Attention</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Pending approvals in current queue</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $pendingApprovals->total() }}</p>
                </div>

                <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                    <p>• Review customer request form</p>
                    <p>• Approve if details are complete</p>
                    <p>• Return to marketing if revision needed</p>
                </div>
            </div>
        </div>

        <a href="{{ route('accounting.approvals') }}"
           class="group flex items-center justify-between p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-amber-400 dark:hover:border-amber-500 hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center
                    {{ $pendingApprovals->total() > 0 ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-gray-100 dark:bg-gray-700' }}">
                    <svg class="w-6 h-6 {{ $pendingApprovals->total() > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
                    </svg>
                </div>
                <div>
                    <p class="text-base font-bold text-gray-900 dark:text-white">Pending Approvals</p>
                    @if($pendingApprovals->total() > 0)
                        <p class="text-sm text-amber-600 dark:text-amber-400 font-medium">
                            {{ $pendingApprovals->total() }} job {{ Str::plural('order', $pendingApprovals->total()) }} waiting for your review
                        </p>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No pending job orders — all clear!</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($pendingApprovals->total() > 0)
                    <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 text-sm font-bold rounded-full bg-amber-500 text-white">
                        {{ $pendingApprovals->total() }}
                    </span>
                @endif
                <svg class="w-5 h-5 text-gray-400 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
    </div>
@endsection
