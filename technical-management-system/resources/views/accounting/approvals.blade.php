@extends('layouts.dashboard')

@section('title', 'Approvals')
@section('page-title', 'Approvals')
@section('page-subtitle', 'Review and process accounting queue (new requests and tech-head approved reports)')

@section('sidebar-nav')
    @include('accounting.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header Stats --}}
    <div class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl px-5 py-3">
            <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-amber-700 dark:text-amber-400 font-medium">Pending Accounting Queue</p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-300">{{ $totalPending }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
        <form method="GET" action="{{ route('accounting.approvals') }}" class="flex flex-wrap gap-3 items-end">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Search</label>
                <input type="text" name="q" value="{{ $search }}"
                       placeholder="JO number, customer, service..."
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Priority --}}
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Priority</label>
                <select name="priority"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Priorities</option>
                    <option value="normal"  {{ $priority === 'normal'  ? 'selected' : '' }}>Normal</option>
                    <option value="high"    {{ $priority === 'high'    ? 'selected' : '' }}>High</option>
                    <option value="urgent"  {{ $priority === 'urgent'  ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>

            {{-- Date From --}}
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Date To --}}
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Filter
                </button>
                <a href="{{ route('accounting.approvals') }}"
                   class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-semibold rounded-lg transition-colors">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Flash messages --}}
    @if(session('status'))
        <div class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl px-4 py-3 text-emerald-700 dark:text-emerald-300 text-sm">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="flex items-start gap-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl px-4 py-3 text-rose-700 dark:text-rose-300 text-sm">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"/>
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Job Orders for Accounting Review</h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $jobOrders->total() }} record(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">JO Number</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Service</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Priority</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Queue</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Submitted By</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Date Submitted</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($jobOrders as $jobOrder)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                        {{-- JO Number --}}
                        <td class="px-5 py-4">
                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $jobOrder->job_order_number }}</span>
                        </td>

                        {{-- Customer --}}
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $jobOrder->customer->name ?? 'N/A' }}</p>
                            @if($jobOrder->customer?->email)
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $jobOrder->customer->email }}</p>
                            @endif
                        </td>

                        {{-- Service --}}
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $jobOrder->service_type ?? 'N/A' }}</p>
                            @if($jobOrder->service_address)
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[180px]">{{ $jobOrder->service_address }}</p>
                            @endif
                        </td>

                        {{-- Priority --}}
                        <td class="px-5 py-4">
                            @php
                                $priorityColors = [
                                    'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'high'   => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                    'normal' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                ];
                                $pColor = $priorityColors[$jobOrder->priority] ?? $priorityColors['normal'];
                                $isCertificationQueue = $jobOrder->status === 'pending_certification';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $pColor }}">
                                {{ ucfirst($jobOrder->priority ?? 'normal') }}
                            </span>
                        </td>

                        {{-- Queue Type --}}
                        <td class="px-5 py-4">
                            @if($isCertificationQueue)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                    Certification Review
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                    Initial Approval
                                </span>
                            @endif
                        </td>

                        {{-- Submitted By --}}
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $jobOrder->creator->name ?? 'N/A' }}</p>
                            @if($jobOrder->creator?->role?->name)
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $jobOrder->creator->role->name }}</p>
                            @endif
                        </td>

                        {{-- Date Submitted --}}
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $jobOrder->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $jobOrder->created_at->diffForHumans() }}</p>
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($isCertificationQueue)
                                    <a href="{{ route('accounting.certifications', ['q' => $jobOrder->job_order_number]) }}"
                                       class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0A9 9 0 113 12a9 9 0 0118 0z"/>
                                        </svg>
                                        Review Queue
                                    </a>

                                    <form method="POST" action="{{ route('accounting.certifications.approve', $jobOrder) }}">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Approve for Signing
                                        </button>
                                    </form>
                                @else
                                    {{-- View Customer Request Form --}}
                                    <a href="{{ route('accounting.job-orders.customer-request-form', $jobOrder) }}"
                                       target="_blank"
                                       title="View customer request form"
                                       class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>

                                    {{-- Approve --}}
                                    <form method="POST" action="{{ route('accounting.job-orders.approve', $jobOrder) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Approve
                                        </button>
                                    </form>

                                    {{-- Return --}}
                                    <form method="POST" action="{{ route('accounting.job-orders.reject', $jobOrder) }}"
                                          onsubmit="return confirm('Return JO {{ $jobOrder->job_order_number }} to marketing for revision?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-sm font-semibold text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                            Return
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">No pending approvals</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500">All job orders have been processed.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($jobOrders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $jobOrders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
