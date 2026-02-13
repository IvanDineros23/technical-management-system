@extends('layouts.dashboard')

@section('title', 'Customer Dashboard')

@section('page-title', 'Customer Dashboard')
@section('page-subtitle', 'Track your requests and certificates')

@section('sidebar-nav')
    <a href="{{ route('customer.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('customer.requests') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Requests
    </a>

    <a href="{{ route('customer.certificates') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
        </svg>
        Certificates
    </a>

    <a href="{{ route('certificate-verification.verify') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Verify Certificate
    </a>
@endsection

@section('content')
    @if(!$customer)
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
            <p class="text-sm font-semibold">No customer profile linked to this account yet.</p>
            <p class="text-xs mt-1">Please contact the administrator to link your customer record.</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase">Total Requests</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['total_requests'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-semibold text-amber-500 uppercase">Pending Requests</p>
            <p class="text-3xl font-bold text-amber-600 mt-2">{{ $stats['pending_requests'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-semibold text-blue-500 uppercase">Pending Certificates</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['pending_certificates'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-semibold text-emerald-500 uppercase">Released Certificates</p>
            <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $stats['released_certificates'] }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-6">
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Quick Certificate Verification</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enter a certificate number to validate.</p>
        <form method="GET" action="{{ route('certificate-verification.verify') }}" class="mt-4 flex flex-col sm:flex-row gap-3">
            <input
                type="text"
                name="certificate_number"
                placeholder="e.g., CERT-2026-0001"
                class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100"
            >
            <button
                type="submit"
                class="px-6 py-3 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700"
            >
                Validate
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Pending Requests</h3>
                <a href="{{ route('customer.requests', ['status' => 'pending']) }}" class="text-xs font-semibold text-blue-600">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="pb-2 text-xs font-semibold text-gray-500 text-left">Job Order</th>
                            <th class="pb-2 text-xs font-semibold text-gray-500 text-left">Requested</th>
                            <th class="pb-2 text-xs font-semibold text-gray-500 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($pendingRequests as $order)
                            <tr>
                                <td class="py-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $order->job_order_number }}</td>
                                <td class="py-2 text-sm text-gray-600 dark:text-gray-300">
                                    {{ optional($order->request_date)->format('M d, Y') ?? 'N/A' }}
                                </td>
                                <td class="py-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">{{ ucfirst($order->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-sm text-gray-500">No pending requests.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Pending Certificates</h3>
                <a href="{{ route('customer.certificates', ['status' => 'pending']) }}" class="text-xs font-semibold text-blue-600">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="pb-2 text-xs font-semibold text-gray-500 text-left">Certificate</th>
                            <th class="pb-2 text-xs font-semibold text-gray-500 text-left">Job Order</th>
                            <th class="pb-2 text-xs font-semibold text-gray-500 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($pendingCertificates as $cert)
                            <tr>
                                <td class="py-2 text-sm font-semibold text-blue-600">{{ $cert->certificate_number }}</td>
                                <td class="py-2 text-sm text-gray-600 dark:text-gray-300">{{ $cert->jobOrder?->job_order_number ?? 'N/A' }}</td>
                                <td class="py-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                        {{ $cert->status ? ucfirst($cert->status) : 'Pending' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-sm text-gray-500">No pending certificates.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
