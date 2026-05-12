@extends('layouts.dashboard')
@section('title', 'Equipment')
@section('page-title', 'My Equipment & Tools')
@section('page-subtitle', 'Tools and equipment overview')

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
        Job Orders
    </a>

    <a href="{{ route('technician.equipment') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
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
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
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
<div class="space-y-6" x-data="{ showRequest: false, showMyRequests: false }">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 md:gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-3 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
                <div>
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Checked Out</p>
                    <h3 class="text-2xl sm:text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1 sm:mt-2">{{ $equipmentStats['in_use'] ?? 0 }}</h3>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 sm:w-6 h-5 sm:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-3 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
                <div>
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Available</p>
                    <h3 class="text-2xl sm:text-3xl font-bold text-green-600 dark:text-green-400 mt-1 sm:mt-2">{{ $equipmentStats['available'] ?? 0 }}</h3>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 sm:w-6 h-5 sm:h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-3 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
                <div>
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">In Use (Others)</p>
                    <h3 class="text-2xl sm:text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-1 sm:mt-2">{{ ($equipmentStats['total'] ?? 0) - ($equipmentStats['available'] ?? 0) - ($equipmentStats['maintenance'] ?? 0) }}</h3>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 sm:w-6 h-5 sm:h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-3 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
                <div>
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Under Repair</p>
                    <h3 class="text-2xl sm:text-3xl font-bold text-red-600 dark:text-red-400 mt-1 sm:mt-2">{{ $equipmentStats['maintenance'] ?? 0 }}</h3>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 sm:w-6 h-5 sm:h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Table -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-6">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Equipment</h3>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Browse and request available equipment</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <form method="GET" action="{{ route('technician.equipment') }}" class="flex items-center gap-2 flex-1 sm:flex-none">
                    <div class="relative flex-1 sm:flex-none">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3 sm:w-4 h-3 sm:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search..." class="w-full sm:w-52 pl-8 sm:pl-9 pr-3 sm:pr-4 py-2 text-xs sm:text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                    @if(!empty($search))
                    <a href="{{ route('technician.equipment') }}" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 whitespace-nowrap">Clear</a>
                    @endif
                </form>
                @if(isset($myRequests) && $myRequests->count() > 0)
                <button @click="showMyRequests=true" class="flex items-center gap-1 sm:gap-2 px-2 sm:px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-xs sm:text-sm font-medium transition-colors whitespace-nowrap">
                    <svg class="w-3 sm:w-4 h-3 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="hidden sm:inline">My Requests</span>
                    <span class="inline-flex items-center justify-center w-4 h-4 text-xs font-bold bg-indigo-600 text-white rounded-full">{{ $myRequests->count() }}</span>
                </button>
                @endif
                <button @click="showRequest=true" class="flex items-center gap-1 sm:gap-2 px-2 sm:px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors whitespace-nowrap">
                    <svg class="w-3 sm:w-4 h-3 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="hidden sm:inline">Request Equipment</span>
                    <span class="sm:hidden">Request</span>
                </button>
            </div>
        </div>

        @if($equipment->count() > 0)
        <!-- Mobile Cards View -->
        <div class="sm:hidden space-y-3 flex-1">
            @foreach($equipment as $item)
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-3 hover:border-blue-300 dark:hover:border-blue-500 transition-colors">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->equipment_code ?? 'N/A' }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full flex-shrink-0
                        {{ ($item->status ?? '') === 'available' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : '' }}
                        {{ ($item->status ?? '') === 'in_use' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' : '' }}
                        {{ ($item->status ?? '') === 'maintenance' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200' : '' }}
                        {{ ($item->status ?? '') === 'retired' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-200' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $item->status ?? 'unknown')) }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-400">
                    <div>
                        <p class="text-gray-500 dark:text-gray-500 font-semibold">Category</p>
                        <p>{{ $item->category ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-500 font-semibold">Location</p>
                        <p>{{ $item->location ?? '—' }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-gray-200 dark:border-gray-700">
                    <tr class="text-left">
                        <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Name</th>
                        <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Code</th>
                        <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Status</th>
                        <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Category</th>
                        <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Location</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($equipment as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="py-3">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->name ?? 'N/A' }}</p>
                        </td>
                        <td class="py-3 text-center"><p class="text-sm text-gray-700 dark:text-gray-300">{{ $item->equipment_code ?? 'N/A' }}</p></td>
                        <td class="py-3 text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ ($item->status ?? '') === 'available' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : '' }}
                                {{ ($item->status ?? '') === 'in_use' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' : '' }}
                                {{ ($item->status ?? '') === 'maintenance' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200' : '' }}
                                {{ ($item->status ?? '') === 'retired' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-200' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $item->status ?? 'unknown')) }}
                            </span>
                        </td>
                        <td class="py-3 text-center"><p class="text-sm text-gray-700 dark:text-gray-300">{{ $item->category ?? '—' }}</p></td>
                        <td class="py-3 text-center"><p class="text-sm text-gray-700 dark:text-gray-300">{{ $item->location ?? '—' }}</p></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $equipment->links() }}
        </div>
        @else
        <div class="text-center py-10">
            <svg class="w-10 sm:w-12 h-10 sm:h-12 text-gray-400 mx-auto mb-2 sm:mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ !empty($search) ? 'No equipment found for "' . e($search) . '"' : 'No equipment available' }}</p>
        </div>
        @endif
    </div>

    <!-- My Equipment Requests Modal -->
    <div
        x-show="showMyRequests"
        x-cloak
        @keydown.escape.window="showMyRequests=false"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto p-4"
    >
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showMyRequests=false"></div>
        <div class="flex min-h-full items-center justify-center p-0 sm:p-4">
            <div
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-t-[20px] sm:rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
            >
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4 sm:mb-5">
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">My Equipment Requests</h3>
                            <p class="text-xs sm:text-xs text-gray-500 dark:text-gray-400 mt-1">Track the status of your equipment requests</p>
                        </div>
                        <button type="button" @click="showMyRequests=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-gray-200 dark:border-gray-700">
                                <tr class="text-left">
                                    <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Equipment</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Purpose</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Job Order</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Status</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Date Requested</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($myRequests ?? [] as $req)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-3 text-xs sm:text-sm font-medium text-gray-900 dark:text-white">{{ $req->equipment_name }}</td>
                                    <td class="py-3 text-xs sm:text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $req->purpose }}</td>
                                    <td class="py-3 text-center">
                                        @if($req->jobOrder)
                                        <span class="text-xs font-medium text-blue-700 dark:text-blue-300">{{ $req->jobOrder->job_order_number }}</span>
                                        @else
                                        <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($req->returned_at)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">Returned</span>
                                        @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $req->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200' : '' }}
                                            {{ $req->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' : '' }}
                                            {{ $req->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200' : '' }}">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center whitespace-nowrap">{{ $req->created_at->format('M d, Y') }}</td>
                                    <td class="py-3 text-xs sm:text-sm text-gray-500 dark:text-gray-400 italic">{{ $req->admin_notes ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Request Equipment Modal -->
    <div
        x-show="showRequest"
        x-cloak
        @keydown.escape.window="showRequest=false"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto p-4"
    >
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showRequest=false"></div>
        <div class="flex min-h-full items-center justify-center p-0 sm:p-4">
            <div
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-t-[20px] sm:rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
            >
                <form method="POST" action="{{ route('technician.equipment.request') }}" class="p-4 sm:p-6 space-y-3 sm:space-y-4">
                    @csrf
                    <div class="flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Request Equipment</h3>
                        <button type="button" @click="showRequest=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div x-data="{ selectMode: 'existing', eqName: '', eqId: '' }">
                        <div class="flex gap-2 sm:gap-3 mb-3 sm:mb-4">
                            <button type="button" @click="selectMode='existing'; eqName=''; eqId=''" :class="selectMode==='existing' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="flex-1 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors">Select from Inventory</button>
                            <button type="button" @click="selectMode='new'; eqName=''; eqId=''" :class="selectMode==='new' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="flex-1 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors">Enter Equipment Name</button>
                        </div>

                        <div x-show="selectMode==='existing'">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Equipment *</label>
                            <select @change="eqId=$event.target.value; eqName=$event.target.selectedOptions[0].dataset.name" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-xs sm:text-sm text-gray-900 dark:text-white">
                                <option value="" data-name="">-- Select Equipment --</option>
                                @foreach($allEquipment ?? [] as $eq)
                                <option value="{{ $eq->id }}" data-name="{{ $eq->name }}">{{ $eq->name }} ({{ $eq->equipment_code }}) - {{ ucfirst(str_replace('_', ' ', $eq->status)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="selectMode==='new'">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Equipment Name *</label>
                            <input type="text" x-model="eqName" placeholder="e.g., Oscilloscope Model XYZ" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-xs sm:text-sm text-gray-900 dark:text-white" />
                        </div>

                        {{-- Single hidden inputs always submitted with correct values --}}
                        <input type="hidden" name="equipment_id" :value="eqId">
                        <input type="hidden" name="equipment_name" :value="eqName">
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Purpose / Reason *</label>
                        <textarea name="purpose" rows="3" required placeholder="Explain why you need this equipment..." class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-xs sm:text-sm text-gray-900 dark:text-white resize-none"></textarea>
                    </div>

                    @if(isset($myActiveJobOrders) && $myActiveJobOrders->count() > 0)
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Link to Job Order <span class="text-gray-400 font-normal">(optional)</span></label>
                        <select name="job_order_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-xs sm:text-sm text-gray-900 dark:text-white">
                            <option value="">-- None --</option>
                            @foreach($myActiveJobOrders as $asgn)
                            <option value="{{ $asgn->jobOrder->id }}">{{ $asgn->jobOrder->job_order_number }} — {{ $asgn->jobOrder->service_description ?? $asgn->jobOrder->service_type }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Equipment will be automatically returned to available when the job is completed.</p>
                    </div>
                    @endif

                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-2">
                        <button type="button" @click="showRequest=false" class="px-3 sm:px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-xs sm:text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                        <button type="submit" class="px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs sm:text-sm font-medium hover:bg-indigo-700 transition-colors">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
