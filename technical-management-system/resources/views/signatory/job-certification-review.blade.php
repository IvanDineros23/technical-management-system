@extends('layouts.dashboard')

@section('title', 'Review Job Certificate')
@section('page-title', 'Job Certificate Review')
@section('page-subtitle', 'Review job completion details and sign certificate')

@section('sidebar-nav')
    @include('signatory.partials.sidebar')
@endsection

@section('content')
<div class="space-y-4 sm:space-y-6">

    {{-- Job Information --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        {{-- Main Details --}}
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            {{-- Job Order Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-6">Job Order Details</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Job Order Number</p>
                        <p class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mt-1 break-words">{{ $jobOrder->job_order_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Customer</p>
                        <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mt-1 break-words">{{ $jobOrder->customer->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Service Type</p>
                        <p class="text-gray-700 dark:text-gray-300 mt-1 break-words">{{ $jobOrder->service_type ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Priority</p>
                        <p class="text-gray-700 dark:text-gray-300 mt-1">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium
                                @if($jobOrder->priority === 'urgent') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200
                                @elseif($jobOrder->priority === 'high') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-200
                                @else bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200 @endif">
                                {{ ucfirst($jobOrder->priority) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Technician Report Details --}}
            @if($report)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-6">Technician Report</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Submitted By</p>
                            <p class="text-gray-700 dark:text-gray-300 mt-1 break-words">{{ optional($report->submittedBy)->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Submitted Date</p>
                            <p class="text-gray-700 dark:text-gray-300 mt-1 break-words">
                                {{ $report->created_at?->setTimezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Work Summary</p>
                            <p class="text-gray-700 dark:text-gray-300 mt-1 whitespace-pre-wrap">{{ $report->work_summary ?? 'No summary provided' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Parts Used</p>
                            <p class="text-gray-700 dark:text-gray-300 mt-1 whitespace-pre-wrap">{{ $report->parts_used ?? 'None' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Remarks</p>
                            <p class="text-gray-700 dark:text-gray-300 mt-1 whitespace-pre-wrap">{{ $report->remarks ?? 'No remarks' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Signing Form Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-4 sm:p-6 lg:sticky lg:top-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 sm:mb-6">Sign Certificate</h2>
                
                <form action="{{ route('signatory.job-certifications.sign', $jobOrder->id) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    {{-- Signature Date --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">
                            Signature Date
                        </label>
                        <input type="date" name="signature_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">
                            Additional Notes (Optional)
                        </label>
                        <textarea name="signature_notes" maxlength="500" rows="4"
                                  placeholder="Add any additional notes about this certification..."
                                  class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"></textarea>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max 500 characters</p>
                    </div>

                    {{-- Confirmation --}}
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4 mt-6">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="confirm" required
                                   class="mt-1 w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500">
                            <label for="confirm" class="text-sm text-emerald-700 dark:text-emerald-300 break-words">
                                I confirm that I have reviewed all job completion details and authorize signing this certificate. This will mark the job as COMPLETED and make the certificate available to the customer.
                            </label>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('signatory.job-certifications') }}" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium text-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors">
                            ✓ Sign & Complete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
