@extends('layouts.dashboard')
@section('title', 'Report Details')
@section('page-title', 'Report Details')
@section('page-subtitle', 'Review submitted report')

@section('sidebar-nav')
    @include('signatory.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('signatory.reports') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Reports
        </a>
    </div>

    <!-- Report Header -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Report Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Job Order: <span class="font-semibold">{{ $report->assignment?->jobOrder?->job_order_number ?? 'N/A' }}</span></p>
            </div>
            @php
                $statusColors = [
                    'submitted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200',
                    'reviewed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-200',
                    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200',
                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200',
                ];
                $statusColor = $statusColors[$report->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/50 dark:text-gray-200';
            @endphp
            <span class="px-4 py-2 text-lg font-semibold rounded-full {{ $statusColor }}">
                {{ ucfirst(str_replace('_', ' ', $report->status)) }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Submitted By</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ $report->submittedBy?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Assigned Technician</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ $report->assignment?->assignedTo?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Submitted Date</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ $report->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Service Type</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ $report->assignment?->jobOrder?->service_type ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Work Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Work Summary</h2>
        
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-6">
            @if($report->work_summary)
                <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                    {{ nl2br(e($report->work_summary)) }}
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 italic">No work summary provided</p>
            @endif
        </div>
    </div>

    <!-- Parts Used -->
    @if($report->parts_used)
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Parts Used</h2>
            
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-6">
                <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                    {{ nl2br(e($report->parts_used)) }}
                </div>
            </div>
        </div>
    @endif

    <!-- Remarks -->
    @if($report->remarks)
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Remarks</h2>
            
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-6">
                <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                    {{ nl2br(e($report->remarks)) }}
                </div>
            </div>
        </div>
    @endif

    <!-- Photos -->
    @if($report->photos && count($report->photos ?? []) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Photos</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($report->photos ?? [] as $photo)
                    <div class="relative group overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-900">
                        <img src="{{ $photo }}" alt="Report photo" class="w-full h-48 object-cover group-hover:opacity-75 transition-opacity">
                        <a href="{{ $photo }}" target="_blank"
                           class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-colors">
                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Review Notes (if reviewed) -->
    @if($report->reviewed_at)
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-[20px] border border-blue-200 dark:border-blue-800 p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">Review Information</h3>
                    <div class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                        <p><strong>Reviewed by:</strong> {{ $report->reviewedBy?->name ?? 'N/A' }}</p>
                        <p><strong>Reviewed at:</strong> {{ $report->reviewed_at->format('M d, Y H:i') }}</p>
                        @if($report->review_notes)
                            <p><strong>Review Notes:</strong></p>
                            <div class="mt-2 bg-white dark:bg-gray-800 rounded p-3">
                                {{ nl2br(e($report->review_notes)) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Job Order Details -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Job Order Information</h2>
        
        @php $jobOrder = $report->assignment?->jobOrder; @endphp
        
        @if($jobOrder)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Job Order Number</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ $jobOrder->job_order_number }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Customer</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ $jobOrder->customer?->name ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Service Type</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ $jobOrder->service_type }}</p>
                    </div>
                </div>
                <div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        @php
                            $jStatusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200',
                                'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200',
                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200',
                            ];
                            $jStatusColor = $jStatusColors[$jobOrder->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/50 dark:text-gray-200';
                        @endphp
                        <span class="inline-block mt-1 px-3 py-1 text-sm font-semibold rounded-full {{ $jStatusColor }}">
                            {{ ucfirst(str_replace('_', ' ', $jobOrder->status)) }}
                        </span>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Created Date</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ $jobOrder->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Expected Completion</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ $jobOrder->expected_completion_date?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400 italic">Job order information not available</p>
        @endif
    </div>
</div>
@endsection
