@extends('layouts.dashboard')

@section('title', 'Certificates')

@section('page-title', 'Calibration Certificates')
@section('page-subtitle', 'View certificates for your assigned jobs')

@section('sidebar-nav')
    <a href="{{ route('technician.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('technician.assignments') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        My Assignments
    </a>

    <a href="{{ route('technician.work-orders') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Work Orders
    </a>

    <a href="{{ route('technician.maintenance') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Maintenance Tasks
    </a>

    <a href="{{ route('technician.equipment') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
        </svg>
        Equipment
    </a>

    <a href="{{ route('technician.inventory') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        Inventory
    </a>

    <a href="{{ route('technician.reports') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Reports
    </a>

    <a href="{{ route('technician.certificates') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
        </svg>
        Certificates
    </a>

    <a href="{{ route('technician.calendar') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Calendar
    </a>

    <a href="{{ route('technician.timeline') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Timeline
    </a>
@endsection

@section('content')
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg">
            <p class="font-semibold">Please fix the following errors:</p>
            <ul class="list-disc list-inside mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{ showCreate: false, init() { this.$watch('showCreate', value => { document.body.style.overflow = value ? 'hidden' : 'auto'; }); }, closeOnEsc(event) { if (event.key === 'Escape') { this.showCreate = false; } } }" @keydown.window="closeOnEsc($event)" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-2xl p-5 border border-blue-200 dark:border-blue-700/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide">Total</p>
                        <p class="text-3xl font-bold text-blue-900 dark:text-blue-100 mt-1">{{ $certificates->count() }}</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Certificates</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-500 dark:bg-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-2xl p-5 border border-green-200 dark:border-green-700/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wide">Generated</p>
                        <p class="text-3xl font-bold text-green-900 dark:text-green-100 mt-1">{{ $certificates->where('status', 'generated')->count() }}</p>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">Ready to Release</p>
                    </div>
                    <div class="w-12 h-12 bg-green-500 dark:bg-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-2xl p-5 border border-purple-200 dark:border-purple-700/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wide">Released</p>
                        <p class="text-3xl font-bold text-purple-900 dark:text-purple-100 mt-1">{{ $certificates->where('status', 'released')->count() }}</p>
                        <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">To Customers</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-500 dark:bg-purple-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-2xl p-5 border border-amber-200 dark:border-amber-700/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wide">Pending</p>
                        <p class="text-3xl font-bold text-amber-900 dark:text-amber-100 mt-1">{{ $certificates->where('status', 'pending')->count() }}</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Generation</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-500 dark:bg-amber-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('technician.certificates') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        All Certificates
                    </a>

                    <a href="{{ route('technician.certificates', ['status' => 'generated']) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'generated' ? 'bg-green-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Generated
                    </a>

                    <a href="{{ route('technician.certificates', ['status' => 'released']) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'released' ? 'bg-purple-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Released
                    </a>

                    <a href="{{ route('technician.certificates', ['status' => 'pending']) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'pending' ? 'bg-amber-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Pending
                    </a>
                </div>

                <button
                    @click="showCreate=true"
                    class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg text-sm font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 flex items-center gap-2 whitespace-nowrap"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Certificate
                </button>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Certificate List</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Total: {{ $certificates->count() }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Certificate No.</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Work Order</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Customer</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Generated Date</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Status</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($certificates as $cert)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="py-3 text-center">
                                    <p class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $cert->certificate_number }}</p>
                                </td>
                                <td class="py-3 text-center">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $cert->jobOrder->job_order_number ?? 'N/A' }}</p>
                                </td>
                                <td class="py-3 text-center">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $cert->jobOrder->customer->name ?? 'N/A' }}</p>
                                </td>
                                <td class="py-3 text-center">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        @if($cert->generated_at)
                                            {{ $cert->generated_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}
                                        @else
                                            Pending
                                        @endif
                                    </p>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $cert->status === 'generated' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' : '' }}
                                        {{ $cert->status === 'released' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-200' : '' }}
                                        {{ $cert->status === 'pending' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200' : '' }}">
                                        {{ ucfirst($cert->status) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center" @click.stop>
                                    <div class="flex gap-2 justify-center flex-wrap">
                                        <a href="{{ route('technician.certificates.download', $cert) }}"
                                           class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800/50 rounded-md text-xs font-semibold transition-all duration-150">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
                                                </svg>
                                                Download
                                            </span>
                                        </a>

                                        @if($cert->status === 'pending')
                                            <form action="{{ route('technician.certificates.generate', $cert->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 hover:bg-amber-200 dark:hover:bg-amber-800/50 rounded-md text-xs font-semibold transition-all duration-150">
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                        </svg>
                                                        Generate Now
                                                    </span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No certificates found for your assignments.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div
            x-show="showCreate"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showCreate=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Create New Certificate</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Generate a calibration certificate manually</p>
                            </div>
                            <button @click="showCreate=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('technician.certificates.store') }}" method="POST" class="space-y-4" x-data="{ hasWorkOrder: false }">
                            @csrf

                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Work Order (Optional)
                                        <span class="text-xs font-normal text-gray-500">- Leave blank for standalone certificate</span>
                                    </label>
                                    <select
                                        name="job_order_id"
                                        @change="hasWorkOrder = $event.target.value !== ''"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="">-- Manual Entry (No Work Order) --</option>
                                        @foreach($jobOrders as $order)
                                            <option value="{{ $order->id }}">{{ $order->job_order_number }} - {{ $order->customer->name ?? 'N/A' }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-2" x-show="!hasWorkOrder">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Customer Name *</label>
                                    <input
                                        type="text"
                                        name="customer_name"
                                        :required="!hasWorkOrder"
                                        placeholder="Enter customer name"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>

                                <div class="col-span-2" x-show="!hasWorkOrder">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Equipment/Item Description *</label>
                                    <input
                                        type="text"
                                        name="equipment_description"
                                        :required="!hasWorkOrder"
                                        placeholder="e.g., Digital Multimeter Model XYZ-123"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>

                                <div class="col-span-2" x-show="!hasWorkOrder">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Service/Calibration Type *</label>
                                    <select
                                        name="service_type"
                                        :required="!hasWorkOrder"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="">Select service type</option>
                                        <option value="electrical_calibration">Electrical Calibration</option>
                                        <option value="dimensional_calibration">Dimensional Calibration</option>
                                        <option value="temperature_calibration">Temperature Calibration</option>
                                        <option value="pressure_calibration">Pressure Calibration</option>
                                        <option value="mechanical_calibration">Mechanical Calibration</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Issue Date</label>
                                    <input
                                        type="date"
                                        name="issue_date"
                                        value="{{ now()->setTimezone('Asia/Manila')->format('Y-m-d') }}"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Valid Until</label>
                                    <input
                                        type="date"
                                        name="valid_until"
                                        value="{{ now()->setTimezone('Asia/Manila')->addYear()->format('Y-m-d') }}"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                    <select
                                        name="status"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="pending">Pending</option>
                                        <option value="generated" selected>Generated</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Template</label>
                                    <select
                                        name="template_used"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="default">Default Template</option>
                                        <option value="iso17025">ISO 17025 Template</option>
                                        <option value="custom">Custom Template</option>
                                    </select>
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                                    <textarea
                                        name="notes"
                                        rows="3"
                                        placeholder="Add any notes or special remarks..."
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="showCreate=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Cancel</button>
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Create Certificate
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
