@extends('layouts.dashboard')

@section('title', 'Accounting Dashboard')

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
<!-- Stats (Unified style like Marketing) -->
<div class="mb-8">
    <div class="flex gap-4 overflow-x-auto pb-1">
        <!-- Pending for Release -->
        <div class="flex-1 min-w-[220px] bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-900/20 rounded-[20px] shadow-md p-6 border border-blue-200 dark:border-blue-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold mb-3">Pending for Release</p>
                    <h3 class="text-4xl font-bold text-blue-900 dark:text-blue-100 mb-3">{{ $stats['pending_for_release'] }}</h3>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-auto">Awaiting customer pickup/delivery</p>
                </div>
            </div>

        <!-- Unpaid Jobs -->
        <div class="flex-1 min-w-[220px] bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/30 dark:to-amber-900/20 rounded-[20px] shadow-md p-6 border border-amber-200 dark:border-amber-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-amber-600 dark:text-amber-400 font-semibold mb-3">Unpaid Jobs</p>
                    <h3 class="text-4xl font-bold text-amber-900 dark:text-amber-100 mb-3">{{ $stats['unpaid_jobs'] }}</h3>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-auto">Awaiting payment verification</p>
                </div>
            </div>

        <!-- Released Today -->
        <div class="flex-1 min-w-[220px] bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/30 dark:to-emerald-900/20 rounded-[20px] shadow-md p-6 border border-emerald-200 dark:border-emerald-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mb-3">Released Today</p>
                    <h3 class="text-4xl font-bold text-emerald-900 dark:text-emerald-100 mb-3">{{ $stats['released_today'] }}</h3>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-auto">Certificates handed over</p>
                </div>
            </div>

        <!-- Released This Week -->
        <div class="flex-1 min-w-[220px] bg-gradient-to-br from-violet-50 to-violet-100 dark:from-violet-900/30 dark:to-violet-900/20 rounded-[20px] shadow-md p-6 border border-violet-200 dark:border-violet-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs font-semibold mb-3 text-violet-600 dark:text-violet-300">Released This Week</p>
                    <h3 class="text-4xl font-bold text-slate-900 dark:text-white mb-3">{{ $stats['released_this_week'] }}</h3>
                    <p class="text-xs mt-auto text-violet-600 dark:text-violet-300">Week-to-date</p>
                </div>
        </div>
    </div>
</div>

<!-- Main Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Pending Releases -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md dark:shadow-gray-900/50 border border-gray-200 dark:border-gray-700">
                <div class="p-4 border-b border-slate-100 dark:border-gray-700">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Pending Certificate Releases</h3>
                </div>
                <div class="p-4 space-y-2">
                    @forelse($pendingReleases as $certificate)
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-gray-700 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg flex items-center justify-center">
                                    <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">CERT</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $certificate->certificate_number }}</p>
                                    <p class="text-sm text-slate-500 dark:text-gray-400">{{ $certificate->jobOrder->customer->name }}</p>
                                </div>
                            </div>
                            @php $isVerified = $certificate->jobOrder->payment && $certificate->jobOrder->payment->status === 'verified'; @endphp
                            @if($isVerified)
                                <span class="px-3 py-1 text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full">Verified</span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-full">Pending</span>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <p>No pending certificate releases</p>
                        </div>
                    @endforelse

                    <a href="{{ route('accounting.certificates.for-release') }}" class="mt-4 w-full block text-center py-2.5 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold rounded-xl hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                        View All →
                    </a>
                </div>
            </div>

    <!-- Pending Payments -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md dark:shadow-gray-900/50 border border-gray-200 dark:border-gray-700">
                <div class="p-4 border-b border-slate-100 dark:border-gray-700">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Pending Payment Verification</h3>
                </div>
                <div class="p-4 space-y-2">
                    @forelse($pendingPayments as $jobOrder)
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-gray-700 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/50 rounded-lg flex items-center justify-center">
                                    <span class="text-sm font-bold text-amber-600 dark:text-amber-400">JO</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $jobOrder->job_order_number }}</p>
                                    <p class="text-sm text-slate-500 dark:text-gray-400">{{ $jobOrder->customer->name }}</p>
                                </div>
                            </div>
                            @if(!$jobOrder->payment)
                                <span class="px-3 py-1 text-xs font-semibold bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 rounded-full">Unpaid</span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-full">{{ ucfirst($jobOrder->payment->status) }}</span>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <p>No pending payments</p>
                        </div>
                    @endforelse

                    <a href="{{ route('accounting.payments') }}" class="mt-4 w-full block text-center py-2.5 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold rounded-xl hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                        View All →
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md dark:shadow-gray-900/50 border border-gray-200 dark:border-gray-700 mt-8">
            <div class="p-4 border-b border-slate-100 dark:border-gray-700">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Quick Actions</h3>
            </div>
    <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <a href="{{ route('accounting.payments') }}" class="flex items-center justify-center gap-2 p-3 bg-slate-50 dark:bg-gray-700 text-slate-700 dark:text-gray-200 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-colors font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>Verify Payments</span>
        </a>
        <a href="{{ route('accounting.certificates.for-release') }}" class="flex items-center justify-center gap-2 p-3 bg-slate-50 dark:bg-gray-700 text-slate-700 dark:text-gray-200 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-colors font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Release Certificates</span>
        </a>
        <a href="{{ route('accounting.certificates.released') }}" class="flex items-center justify-center gap-2 p-3 bg-slate-50 dark:bg-gray-700 text-slate-700 dark:text-gray-200 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-colors font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span>View History</span>
        </a>
        <a href="{{ route('accounting.reports') }}" class="flex items-center justify-center gap-2 p-3 bg-slate-50 dark:bg-gray-700 text-slate-700 dark:text-gray-200 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-colors font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Reports</span>
        </a>
    </div>
</div>
@endsection
