@extends('layouts.dashboard')

@section('title', 'Request Details')
@section('page-title', 'Request Details')
@section('page-subtitle', 'Track progress and history')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-6">

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                {{ $jobOrder->job_order_number }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $jobOrder->service_type ?? 'N/A' }} • Requested:
                {{ optional($jobOrder->request_date)->format('M d, Y') ?? 'N/A' }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            @php
                $statusStyles = [
                    'pending' => 'bg-amber-100 text-amber-700',
                    'approved' => 'bg-emerald-100 text-emerald-700',
                    'in_progress' => 'bg-blue-100 text-blue-700',
                    'completed' => 'bg-emerald-100 text-emerald-700',
                    'cancelled' => 'bg-rose-100 text-rose-700',
                ];
                $statusClass = $statusStyles[$jobOrder->status] ?? 'bg-slate-100 text-slate-700';

                $priority = $jobOrder->priority ?? 'normal';
                $priorityStyles = [
                    'normal' => 'bg-slate-100 text-slate-700',
                    'high' => 'bg-amber-100 text-amber-700',
                    'urgent' => 'bg-rose-100 text-rose-700',
                ];
                $priorityClass = $priorityStyles[$priority] ?? 'bg-slate-100 text-slate-700';
            @endphp

            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $priorityClass }}">
                Priority: {{ ucfirst($priority) }}
            </span>

            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                {{ ucfirst(str_replace('_', ' ', $jobOrder->status)) }}
            </span>

            @if($jobOrder->pdf_filename)
                <a href="{{ asset('storage/generated/' . $jobOrder->pdf_filename) }}"
                   download
                   class="inline-flex items-center justify-center h-9 px-4 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                    Download PDF
                </a>
            @endif
        </div>
    </div>

    {{-- Timeline --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Status Timeline</h3>

        @forelse($jobOrder->statusLogs as $log)
            <div class="flex gap-3 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                <div class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600 flex-shrink-0"></div>
                <div class="flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">
                            {{ ucfirst(str_replace('_',' ', $log->status)) }}
                            @if($log->previous_status)
                                <span class="text-xs font-normal text-gray-500">
                                    (from {{ ucfirst(str_replace('_',' ', $log->previous_status)) }})
                                </span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ optional($log->changed_at)->format('M d, Y h:i A') ?? optional($log->created_at)->format('M d, Y h:i A') }}
                        </p>
                    </div>

                    @if($log->remarks)
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 whitespace-pre-line">
                            {{ $log->remarks }}
                        </p>
                    @endif

                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Changed by: {{ optional($log->changer)->name ?? ('User #' . ($log->changed_by ?? 'N/A')) }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500">No status history yet. (Wala pang logs sa job_order_statuses)</p>
        @endforelse
    </div>

    {{-- Attachments --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-5 space-y-4">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-2">Attachments</h3>

        <form method="POST" action="{{ route('customer.requests.attachments.store', $jobOrder) }}" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-2 mb-4">
            @csrf
            <input type="file" name="file" required class="block w-full text-sm">
            <input type="text" name="description" placeholder="Description (optional)" class="block w-full text-sm">
            <button class="h-10 px-4 rounded-lg bg-blue-600 text-white font-semibold">Upload</button>
        </form>

        @forelse($jobOrder->attachments as $att)
            <div class="flex items-center gap-3 mb-2">
                <a class="text-blue-600 hover:underline text-sm"
                   href="{{ asset('storage/' . $att->file_path) }}" target="_blank">
                   {{ $att->file_name }}
                </a>
                <span class="text-xs text-gray-500">{{ $att->file_type }} • {{ number_format($att->file_size / 1024, 1) }} KB</span>
                @if($att->description)
                    <span class="text-xs text-gray-700">{{ $att->description }}</span>
                @endif
                <span class="text-xs text-gray-400">Uploaded: {{ optional($att->uploaded_at)->format('M d, Y h:i A') }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500">No attachments yet.</p>
        @endforelse
    </div>

    {{-- Details --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-2">Service Description</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">
                {{ $jobOrder->service_description ?? 'N/A' }}
            </p>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-2">Service Address</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">
                {{ $jobOrder->service_address ?? 'N/A' }}
                @php
                    $loc = collect([$jobOrder->city, $jobOrder->province, $jobOrder->postal_code])->filter()->implode(', ');
                @endphp
                @if($loc)
                    <span class="block mt-2 text-xs text-gray-500">{{ $loc }}</span>
                @endif
            </p>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-5 md:col-span-2">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-2">Special Instructions</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">
                {{ $jobOrder->notes ?? 'None' }}
            </p>
        </div>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('customer.requests') }}"
           class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-100 font-semibold hover:bg-gray-300 dark:hover:bg-gray-500">
            Back to Requests
        </a>
    </div>
</div>
@endsection
