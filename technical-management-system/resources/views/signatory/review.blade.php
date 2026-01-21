@extends('layouts.dashboard')

@section('title', 'Review Calibration')
@section('page-title', 'Calibration Review')
@section('page-subtitle', 'Review and approve calibration data for signing')

@section('sidebar-nav')
    @include('signatory.partials.sidebar')
@endsection

@section('content')
    <div x-data="{ showRevisionModal: false, showApproveModal: false }" class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $calibration->assignment->jobOrder->job_order_number }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Calibration ID: {{ $calibration->calibration_number }}
                </p>
            </div>
            <a href="{{ route('signatory.for-review') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                ← Back to list
            </a>
        </div>

        <!-- Status Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Customer</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $calibration->assignment->jobOrder->customer->name }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Technician</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $calibration->performedBy->name }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Calibration Date</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $calibration->calibration_date->format('M d, Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Result</p>
                    @if($calibration->pass_fail === 'pass')
                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">✓ Pass</span>
                    @elseif($calibration->pass_fail === 'fail')
                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200">✗ Fail</span>
                    @else
                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">~ Conditional</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Calibration Details -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Calibration Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="pb-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Location</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $calibration->location ?? 'Not specified' }}</p>
                    </div>
                    <div class="pb-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Start Time</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $calibration->start_time ?? 'Not specified' }}</p>
                    </div>
                    <div class="pb-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">End Time</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $calibration->end_time ?? 'Not specified' }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="pb-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Procedure Reference</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $calibration->procedure_reference ?? 'Not specified' }}</p>
                    </div>
                    <div class="pb-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Technician Remarks</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $calibration->remarks ?? 'No remarks' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Measurement Points Table -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">📊 Measurement Points</h3>
            
            @if($calibration->measurementPoints->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-200 dark:border-gray-700">
                            <tr class="text-left text-xs">
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Point</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Reference Value</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">UUT Reading</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Error</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Uncertainty</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Acceptance</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Result</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($calibration->measurementPoints as $point)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-3 font-semibold">{{ $point->point_number }}</td>
                                    <td class="py-3">{{ number_format($point->reference_value, 4) }}</td>
                                    <td class="py-3">{{ number_format($point->uut_reading, 4) }}</td>
                                    <td class="py-3">{{ number_format($point->error, 4) }}</td>
                                    <td class="py-3">{{ $point->uncertainty ? number_format($point->uncertainty, 4) : 'N/A' }}</td>
                                    <td class="py-3">{{ $point->acceptance_criteria ?? 'N/A' }}</td>
                                    <td class="py-3">
                                        @if($point->status === 'pass')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">✓ Pass</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200">✗ Fail</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No measurement points recorded</p>
            @endif
        </div>

        <!-- Technical Review History -->
        @if($timeline && $timeline->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">📅 Timeline</h3>
                <div class="space-y-2">
                    @foreach($timeline as $event)
                        <div class="flex gap-3 pb-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
                            <div class="w-2 h-2 rounded-full bg-blue-600 mt-2 flex-shrink-0"></div>
                            <div>
                                <p class="text-xs font-semibold text-gray-900 dark:text-white">
                                    {{ ucwords(str_replace('_', ' ', $event->event)) }}
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $event->description }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    {{ $event->created_at->format('M d, Y H:i') }} by {{ $event->createdBy->name ?? 'System' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex gap-3">
            <button @click="showApproveModal = true" 
                    class="flex-1 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors">
                ✓ Approve for Signing
            </button>
            <button @click="showRevisionModal = true"
                    class="flex-1 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-lg transition-colors">
                ↺ Request Revision
            </button>
        </div>

        <!-- Approve Modal -->
        <div x-show="showApproveModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" style="display:none;">
            <div class="absolute inset-0 bg-gray-900/60" @click="showApproveModal = false"></div>
            <div class="relative max-w-md mx-auto bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('signatory.approve', $calibration) }}" class="p-6 space-y-4">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Approve for Signing</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Approve this calibration and prepare it for digital signature?
                    </p>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Optional Remarks</label>
                        <textarea name="remarks" rows="3" placeholder="Add any findings or notes..."
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="showApproveModal = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium">Approve</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Revision Request Modal -->
        <div x-show="showRevisionModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" style="display:none;">
            <div class="absolute inset-0 bg-gray-900/60" @click="showRevisionModal = false"></div>
            <div class="relative max-w-md mx-auto bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('signatory.request-revision', $calibration) }}" class="p-6 space-y-4">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Request Revision</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Return this calibration to the tech head for revision. Explain what needs to be corrected.
                    </p>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Revision Remarks *</label>
                        <textarea name="revision_remarks" rows="4" required placeholder="Describe what needs to be revised..."
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="showRevisionModal = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium">Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
