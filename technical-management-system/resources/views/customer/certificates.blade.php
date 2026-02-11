@extends('layouts.dashboard')

@section('title', 'My Certificates')

@section('page-title', 'My Certificates')
@section('page-subtitle', 'View and validate your certificates')

@section('sidebar-nav')
    <a href="{{ route('customer.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
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
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
        </svg>
        Certificates
    </a>

    <a href="{{ route('verification.verify') }}"
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

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Certificates</h3>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('customer.certificates') }}"
                   class="px-4 py-2 rounded-lg text-xs font-semibold {{ $status === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                    All
                </a>
                <a href="{{ route('customer.certificates', ['status' => 'pending']) }}"
                   class="px-4 py-2 rounded-lg text-xs font-semibold {{ $status === 'pending' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Pending
                </a>
                <a href="{{ route('customer.certificates', ['status' => 'released']) }}"
                   class="px-4 py-2 rounded-lg text-xs font-semibold {{ $status === 'released' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Released
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="pb-3 text-xs font-semibold text-gray-500 text-left">Certificate</th>
                        <th class="pb-3 text-xs font-semibold text-gray-500 text-left">Job Order</th>
                        <th class="pb-3 text-xs font-semibold text-gray-500 text-left">Generated</th>
                        <th class="pb-3 text-xs font-semibold text-gray-500 text-left">Status</th>
                        <th class="pb-3 text-xs font-semibold text-gray-500 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($certificates as $cert)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="py-3 text-sm font-semibold text-blue-600">{{ $cert->certificate_number }}</td>
                            <td class="py-3 text-sm text-gray-600 dark:text-gray-300">{{ $cert->jobOrder?->job_order_number ?? 'N/A' }}</td>
                            <td class="py-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ $cert->generated_at ? $cert->generated_at->setTimezone('Asia/Manila')->format('M d, Y') : 'Pending' }}
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-700">
                                    {{ $cert->released_at ? 'Released' : 'Pending' }}
                                </span>
                            </td>
                            <td class="py-3">
                                <a
                                    href="{{ route('verification.show', $cert->certificate_number) }}"
                                    class="text-xs font-semibold text-blue-600 hover:text-blue-700"
                                >
                                    Validate
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-sm text-gray-500">No certificates found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($certificates, 'links'))
            <div class="mt-4">{{ $certificates->links() }}</div>
        @endif
    </div>
@endsection
