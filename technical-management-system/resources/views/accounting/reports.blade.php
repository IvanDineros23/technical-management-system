@extends('layouts.dashboard')

@section('title', 'Reports')

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
<!-- Report Filters -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Generate Reports</h2>
    <form action="{{ route('accounting.reports') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Report Type</label>
            <select name="report_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="all" {{ ($reportType ?? 'all') === 'all' ? 'selected' : '' }}>All Reports</option>
                <option value="payments" {{ ($reportType ?? '') === 'payments' ? 'selected' : '' }}>Payment Summary</option>
                <option value="releases" {{ ($reportType ?? '') === 'releases' ? 'selected' : '' }}>Released Certificates</option>
                <option value="pending" {{ ($reportType ?? '') === 'pending' ? 'selected' : '' }}>Pending Items</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                Generate Report
            </button>
        </div>
    </form>
</div>

<!-- Reports Tables (lean layout) -->
<div class="space-y-6">
    <!-- Released Certificates Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Released Certificates</h3>
            <form action="{{ route('accounting.reports.export') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="report_type" value="released_certificates">
                <input type="hidden" name="start_date" value="{{ $startDate ?? '' }}">
                <input type="hidden" name="end_date" value="{{ $endDate ?? '' }}">
                <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-600 hover:bg-green-700 text-white">Export CSV</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100 dark:bg-gray-700/70 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                    <tr>
                        <th class="px-4 py-3">Certificate #</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Released At</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800">
                    @forelse($releasedCertificates as $cert)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $cert->certificate_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ optional($cert->jobOrder->customer)->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ optional($cert->released_at)->setTimezone('Asia/Manila')->format('Y-m-d') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No released certificate reports available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payment Summary Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Summary</h3>
            <form action="{{ route('accounting.reports.export') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="report_type" value="payment_summary">
                <input type="hidden" name="start_date" value="{{ $startDate ?? '' }}">
                <input type="hidden" name="end_date" value="{{ $endDate ?? '' }}">
                <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-600 hover:bg-green-700 text-white">Export CSV</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100 dark:bg-gray-700/70 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                    <tr>
                        <th class="px-4 py-3">Job Order #</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Verified At</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800">
                    @forelse($payments as $payment)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $payment->jobOrder->job_order_number ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ optional($payment->jobOrder->customer)->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">₱{{ number_format($payment->amount_paid, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ optional($payment->verified_at)->setTimezone('Asia/Manila')->format('Y-m-d') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No payment reports available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Monthly Summary Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Summary</h3>
            <form action="{{ route('accounting.reports.export') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="report_type" value="monthly_summary">
                <input type="hidden" name="start_date" value="{{ $startDate ?? '' }}">
                <input type="hidden" name="end_date" value="{{ $endDate ?? '' }}">
                <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-600 hover:bg-green-700 text-white">Export CSV</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100 dark:bg-gray-700/70 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">
                    <tr>
                        <th class="px-4 py-3">Period</th>
                        <th class="px-4 py-3">Total Payments</th>
                        <th class="px-4 py-3">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800">
                    @forelse($monthlySummary as $row)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row->period }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $row->total_payments }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">₱{{ number_format($row->total_amount ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No monthly summary available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection