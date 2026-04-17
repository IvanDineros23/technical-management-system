@extends('layouts.dashboard')

@section('title', 'Reports')

@section('page-title', 'Reports')
@section('page-subtitle', 'Review and manage technician reports')

@section('sidebar-nav')
    @include('tech-head.partials.sidebar')
@endsection

@section('content')
    <div
        x-data="{
            showApprove: false,
            showReject: false,
            showDetails: false,
            selectedId: null,
            selectedReport: null,
            openReportDetails(report) {
                this.selectedReport = report;
                this.showDetails = true;
                document.body.style.overflow = 'hidden';
            },
            closeReportDetails() {
                this.showDetails = false;
                this.selectedReport = null;
                if (!this.showApprove && !this.showReject) {
                    document.body.style.overflow = 'auto';
                }
            },
            openApprove(id) {
                this.selectedId = id;
                this.showApprove = true;
                document.body.style.overflow = 'hidden';
            },
            closeApprove() {
                this.showApprove = false;
                if (!this.showDetails && !this.showReject) {
                    document.body.style.overflow = 'auto';
                }
            },
            openReject(id) {
                this.selectedId = id;
                this.showReject = true;
                document.body.style.overflow = 'hidden';
            },
            closeReject() {
                this.showReject = false;
                if (!this.showDetails && !this.showApprove) {
                    document.body.style.overflow = 'auto';
                }
            },
            closeAllModals() {
                this.showDetails = false;
                this.showApprove = false;
                this.showReject = false;
                this.selectedReport = null;
                document.body.style.overflow = 'auto';
            },
            reportStatusLabel(status) {
                if (!status) return 'N/A';
                return status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            }
        }"
        @keydown.escape.window="closeAllModals()"
        class="space-y-6"
    >
        
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Reports</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pending'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pending Review</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['rejected'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Rejected</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pending Reports - Quick Action Cards -->
        @if($pendingReports->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">⚡ Pending Reports - Needs Your Review</h3>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">{{ $pendingReports->count() }} waiting</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($pendingReports as $report)
                    @php
                        $startedAt = $report->assignment?->started_at
                            ? $report->assignment->started_at->copy()->setTimezone('Asia/Manila')
                            : null;
                        $completedAt = $report->assignment?->completed_at
                            ? $report->assignment->completed_at->copy()->setTimezone('Asia/Manila')
                            : null;
                        $durationText = 'N/A';
                        if ($startedAt && $completedAt && $completedAt->greaterThanOrEqualTo($startedAt)) {
                            $minutes = $startedAt->diffInMinutes($completedAt);
                            $hours = intdiv($minutes, 60);
                            $remainingMinutes = $minutes % 60;
                            $durationText = $hours > 0 ? "{$hours}h {$remainingMinutes}m" : "{$remainingMinutes}m";
                        }

                        $reportPayload = [
                            'id' => $report->id,
                            'status' => $report->status,
                            'work_summary' => $report->work_summary,
                            'parts_used' => $report->parts_used,
                            'remarks' => $report->remarks,
                            'review_notes' => $report->review_notes,
                            'submitted_at' => $report->created_at
                                ? $report->created_at->copy()->setTimezone('Asia/Manila')->format('M d, Y h:i A')
                                : null,
                            'submitted_by' => optional($report->submittedBy)->name,
                            'reviewed_by' => optional($report->reviewedBy)->name,
                            'reviewed_at' => $report->reviewed_at
                                ? $report->reviewed_at->copy()->setTimezone('Asia/Manila')->format('M d, Y h:i A')
                                : null,
                            'job_order' => [
                                'number' => optional($report->assignment?->jobOrder)->job_order_number,
                                'customer' => optional($report->assignment?->jobOrder?->customer)->name,
                                'service_type' => optional($report->assignment?->jobOrder)->service_type,
                                'service_description' => optional($report->assignment?->jobOrder)->service_description,
                            ],
                            'work_tracker' => [
                                'started_at' => $startedAt?->format('M d, Y h:i A'),
                                'completed_at' => $completedAt?->format('M d, Y h:i A'),
                                'duration' => $durationText,
                            ],
                            'attachments' => $report->assignment?->jobOrder?->attachments
                                ? $report->assignment->jobOrder->attachments->map(function ($attachment) {
                                    return [
                                        'id' => $attachment->id,
                                        'file_name' => $attachment->file_name,
                                        'file_path' => $attachment->file_path,
                                        'created_at' => optional($attachment->created_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A'),
                                    ];
                                })->values()->all()
                                : [],
                        ];
                    @endphp
                    <div class="border border-amber-200 dark:border-amber-800 rounded-xl p-4 bg-amber-50/50 dark:bg-amber-900/20 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $report->assignment->jobOrder->job_order_number ?? 'N/A' }}</p>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200">⏳ Pending</span>
                        </div>
                        <div class="space-y-1 mb-3">
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                <span class="font-semibold">Customer:</span> {{ $report->assignment->jobOrder->customer->name ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                <span class="font-semibold">Technician:</span> {{ $report->submittedBy->name ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                <span class="font-semibold">Submitted:</span> {{ optional($report->created_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}
                            </p>
                        </div>
                        @if($report->work_summary)
                            <p class="text-xs text-gray-700 dark:text-gray-300 mb-3 line-clamp-2">{{ \Illuminate\Support\Str::limit($report->work_summary, 100) }}</p>
                        @endif
                        <div class="flex gap-2">
                            <button @click="openReportDetails(@js($reportPayload))" class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition-colors">View Details</button>
                            <button @click="openApprove({{ $report->id }})" class="flex-1 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition-colors">✓ Approve</button>
                        </div>
                        <div class="flex gap-2 mt-2">
                            <button @click="openReject({{ $report->id }})" class="w-full px-3 py-2 border border-rose-300 dark:border-rose-700 text-rose-700 dark:text-rose-300 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-lg text-xs font-medium transition-colors">✗ Reject</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Pending Calibrations - Quick Action Cards -->
        @if($pendingCalibrations->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">🧪 Calibration Approvals - Needs Your Review</h3>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">{{ $pendingCalibrations->count() }} waiting</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($pendingCalibrations as $calibration)
                    <div class="border border-amber-200 dark:border-amber-800 rounded-xl p-4 bg-amber-50/50 dark:bg-amber-900/20 hover:shadow-md transition-shadow" x-data="{ open:false }">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $calibration->assignment->jobOrder->job_order_number ?? 'N/A' }}</p>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200">⏳ Pending</span>
                        </div>
                        <div class="space-y-1 mb-3">
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                <span class="font-semibold">Customer:</span> {{ $calibration->assignment->jobOrder->customer->name ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                <span class="font-semibold">Technician:</span> {{ $calibration->performedBy->name ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                <span class="font-semibold">Calibration Date:</span> {{ optional($calibration->calibration_date)->setTimezone('Asia/Manila')->format('M d, Y') ?? 'N/A' }}
                            </p>
                        </div>
                        <button @click="open=!open" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mb-2">{{ __('View measurement points') }}</button>
                        <div x-show="open" x-cloak class="mb-3 space-y-1 text-xs text-gray-700 dark:text-gray-300">
                            @forelse($calibration->measurementPoints as $point)
                                <div class="p-2 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                    <div class="flex justify-between text-[11px] font-semibold">
                                        <span>Point {{ $point->point_number }}</span>
                                        <span class="text-gray-500">Ref: {{ $point->reference_value }}</span>
                                    </div>
                                    <div class="text-[11px] text-gray-600 dark:text-gray-400">UUT: {{ $point->uut_reading }} | Unc: {{ $point->uncertainty }} | {{ $point->acceptance_criteria }}</div>
                                </div>
                            @empty
                                <p class="text-[11px] text-gray-500">No points</p>
                            @endforelse
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('tech-head.calibration.approve', $calibration) }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="result" value="approved">
                                <input type="hidden" name="findings" value="Approved via reports">
                                <input type="hidden" name="data_reviewed" value="1">
                                <input type="hidden" name="calculations_verified" value="1">
                                <input type="hidden" name="standards_checked" value="1">
                                <button type="submit" class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition-colors">✓ Approve</button>
                            </form>
                            <form method="POST" action="{{ route('tech-head.calibration.approve', $calibration) }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="result" value="rejected">
                                <input type="hidden" name="findings" value="Rejected via reports">
                                <button type="submit" class="w-full px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-medium transition-colors">✗ Reject</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- All Reports Table -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">All Reports</h3>
                
                <!-- Filter Buttons -->
                <div class="flex gap-2">
                    <a href="{{ route('tech-head.reports', ['filter' => 'all']) }}" 
                       class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        All ({{ $stats['total'] }})
                    </a>
                    <a href="{{ route('tech-head.reports', ['filter' => 'pending']) }}" 
                       class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $filter === 'pending' ? 'bg-amber-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Pending ({{ $stats['pending'] }})
                    </a>
                    <a href="{{ route('tech-head.reports', ['filter' => 'approved']) }}" 
                       class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $filter === 'approved' ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Approved ({{ $stats['approved'] }})
                    </a>
                    <a href="{{ route('tech-head.reports', ['filter' => 'rejected']) }}" 
                       class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $filter === 'rejected' ? 'bg-rose-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Rejected ({{ $stats['rejected'] }})
                    </a>
                </div>
            </div>
            
            @if($reports->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr class="text-left">
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Status</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">WO Number</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Customer</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Technician</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Submitted</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Reviewed By</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($reports as $report)
                            @php
                                $startedAt = $report->assignment?->started_at
                                    ? $report->assignment->started_at->copy()->setTimezone('Asia/Manila')
                                    : null;
                                $completedAt = $report->assignment?->completed_at
                                    ? $report->assignment->completed_at->copy()->setTimezone('Asia/Manila')
                                    : null;
                                $durationText = 'N/A';
                                if ($startedAt && $completedAt && $completedAt->greaterThanOrEqualTo($startedAt)) {
                                    $minutes = $startedAt->diffInMinutes($completedAt);
                                    $hours = intdiv($minutes, 60);
                                    $remainingMinutes = $minutes % 60;
                                    $durationText = $hours > 0 ? "{$hours}h {$remainingMinutes}m" : "{$remainingMinutes}m";
                                }

                                $reportPayload = [
                                    'id' => $report->id,
                                    'status' => $report->status,
                                    'work_summary' => $report->work_summary,
                                    'parts_used' => $report->parts_used,
                                    'remarks' => $report->remarks,
                                    'review_notes' => $report->review_notes,
                                    'submitted_at' => $report->created_at
                                        ? $report->created_at->copy()->setTimezone('Asia/Manila')->format('M d, Y h:i A')
                                        : null,
                                    'submitted_by' => optional($report->submittedBy)->name,
                                    'reviewed_by' => optional($report->reviewedBy)->name,
                                    'reviewed_at' => $report->reviewed_at
                                        ? $report->reviewed_at->copy()->setTimezone('Asia/Manila')->format('M d, Y h:i A')
                                        : null,
                                    'job_order' => [
                                        'number' => optional($report->assignment?->jobOrder)->job_order_number,
                                        'customer' => optional($report->assignment?->jobOrder?->customer)->name,
                                        'service_type' => optional($report->assignment?->jobOrder)->service_type,
                                        'service_description' => optional($report->assignment?->jobOrder)->service_description,
                                    ],
                                    'work_tracker' => [
                                        'started_at' => $startedAt?->format('M d, Y h:i A'),
                                        'completed_at' => $completedAt?->format('M d, Y h:i A'),
                                        'duration' => $durationText,
                                    ],
                                    'attachments' => $report->assignment?->jobOrder?->attachments
                                        ? $report->assignment->jobOrder->attachments->map(function ($attachment) {
                                            return [
                                                'id' => $attachment->id,
                                                'file_name' => $attachment->file_name,
                                                'file_path' => $attachment->file_path,
                                                'created_at' => optional($attachment->created_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A'),
                                            ];
                                        })->values()->all()
                                        : [],
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="py-3">
                                    @if($report->status === 'pending')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">Pending</span>
                                    @elseif($report->status === 'approved')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">Approved</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200">Rejected</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $report->assignment->jobOrder->job_order_number ?? 'N/A' }}</p>
                                </td>
                                <td class="py-3">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $report->assignment->jobOrder->customer->name ?? 'N/A' }}</p>
                                </td>
                                <td class="py-3">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $report->submittedBy->name ?? 'N/A' }}</p>
                                </td>
                                <td class="py-3">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ optional($report->created_at)->setTimezone('Asia/Manila')->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ optional($report->created_at)->setTimezone('Asia/Manila')->format('h:i A') }}</p>
                                </td>
                                <td class="py-3">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $report->reviewedBy->name ?? '-' }}</p>
                                    @if($report->reviewed_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ optional($report->reviewed_at)->setTimezone('Asia/Manila')->format('M d, Y') }}</p>
                                    @endif
                                </td>
                                <td class="py-3 text-right">
                                    <button @click="openReportDetails(@js($reportPayload))" class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-medium">View Details</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6">
                {{ $reports->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No reports found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($filter !== 'all')
                        No {{ $filter }} reports at the moment.
                    @else
                        Technicians haven't submitted any reports yet.
                    @endif
                </p>
            </div>
            @endif
        </div>
        
        <!-- Report Details Modal -->
        <div
            x-show="showDetails"
            x-cloak
            x-transition.opacity.duration.200ms
            class="fixed inset-0 z-[70] overflow-y-auto"
            style="display:none;"
        >
            <div class="fixed inset-0 bg-gray-900/65 backdrop-blur-sm z-0" @click="closeReportDetails()"></div>
            <div class="flex min-h-full items-center justify-center p-4 relative z-10">
                <div
                    x-show="showDetails"
                    x-transition:enter="transform transition ease-out duration-250"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                    @click.stop
                    class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700 max-h-[90vh] overflow-y-auto"
                >
                    <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 rounded-t-[20px] z-10">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Report Details</h3>
                            <button @click="closeReportDetails()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 space-y-4" x-show="selectedReport">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Job Order Information</h4>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">WO Number:</p>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="selectedReport?.job_order?.number || 'N/A'"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Customer:</p>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="selectedReport?.job_order?.customer || 'N/A'"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Service Type:</p>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="selectedReport?.job_order?.service_type || 'N/A'"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Report Information</h4>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Submitted By:</p>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="selectedReport?.submitted_by || 'N/A'"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Submitted At:</p>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="selectedReport?.submitted_at || 'N/A'"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Status:</p>
                                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full"
                                              :class="{
                                                  'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200': selectedReport?.status === 'pending',
                                                  'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200': selectedReport?.status === 'approved',
                                                  'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200': selectedReport?.status === 'rejected'
                                              }"
                                              x-text="reportStatusLabel(selectedReport?.status)">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Work Tracker</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">Started At</p>
                                    <p class="font-medium text-gray-900 dark:text-white" x-text="selectedReport?.work_tracker?.started_at || 'Not set'"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">Completed At</p>
                                    <p class="font-medium text-gray-900 dark:text-white" x-text="selectedReport?.work_tracker?.completed_at || 'Not set'"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">Duration</p>
                                    <p class="font-medium text-gray-900 dark:text-white" x-text="selectedReport?.work_tracker?.duration || 'N/A'"></p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Work Summary</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4" x-text="selectedReport?.work_summary || 'No summary provided'"></p>
                        </div>

                        <div x-show="selectedReport?.parts_used">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Parts Used</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4" x-text="selectedReport?.parts_used || 'None'"></p>
                        </div>

                        <div x-show="selectedReport?.remarks">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Remarks</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4" x-text="selectedReport?.remarks || 'None'"></p>
                        </div>

                        <div x-show="selectedReport?.job_order?.service_description">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Service Description</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4" x-text="selectedReport?.job_order?.service_description || 'N/A'"></p>
                        </div>

                        <div x-show="selectedReport?.attachments?.length">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Attachments</h4>
                            <div class="space-y-2">
                                <template x-for="attachment in selectedReport.attachments" :key="attachment.id">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="attachment.file_name"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="attachment.created_at || ''"></p>
                                        </div>
                                        <a class="text-blue-600 dark:text-blue-400 hover:underline text-sm"
                                           :href="'{{ rtrim(Storage::url(''), '/') }}/' + attachment.file_path"
                                           target="_blank">
                                            View
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div x-show="selectedReport?.review_notes">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Review Notes</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4 border border-amber-200 dark:border-amber-800" x-text="selectedReport?.review_notes"></p>
                        </div>
                    </div>

                    <div class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6 py-4 rounded-b-[20px]">
                        <div class="flex flex-wrap items-center justify-end gap-3">
                            <button
                                x-show="selectedReport?.status === 'pending'"
                                @click="openReject(selectedReport.id); closeReportDetails()"
                                class="px-4 py-2 border border-rose-300 dark:border-rose-700 text-rose-700 dark:text-rose-300 rounded-lg text-sm font-medium hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-colors"
                            >
                                Reject
                            </button>
                            <button
                                x-show="selectedReport?.status === 'pending'"
                                @click="openApprove(selectedReport.id); closeReportDetails()"
                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors"
                            >
                                Approve Report
                            </button>
                            <button @click="closeReportDetails()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <div x-show="showApprove" x-cloak x-transition.opacity.duration.200ms class="fixed inset-0 z-[70] overflow-y-auto" style="display:none;">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-0" @click="closeApprove()"></div>
            <div class="flex min-h-full items-center justify-center p-4 relative z-10">
                <div x-show="showApprove"
                     x-transition:enter="transform transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transform transition ease-in duration-180"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-md bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700">
                    <form method="POST" :action="selectedId ? '{{ url('/tech-head/reports') }}/' + selectedId + '/approve' : '#'" class="p-6 space-y-4">
                        @csrf
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Approve Report</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300">This will lock the job as completed.</p>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="closeApprove()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium">Approve</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div x-show="showReject" x-cloak x-transition.opacity.duration.200ms class="fixed inset-0 z-[70] overflow-y-auto" style="display:none;">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-0" @click="closeReject()"></div>
            <div class="flex min-h-full items-center justify-center p-4 relative z-10">
                <div x-show="showReject"
                     x-transition:enter="transform transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transform transition ease-in duration-180"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-md bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700">
                    <form method="POST" :action="selectedId ? '{{ url('/tech-head/reports') }}/' + selectedId + '/reject' : '#'" class="p-6 space-y-4">
                        @csrf
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Reject Report</h3>
                        <textarea name="review_notes" placeholder="Reason" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white"></textarea>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="closeReject()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                            <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-medium">Reject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        
    </div>
@endsection
