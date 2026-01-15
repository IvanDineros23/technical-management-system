@extends('layouts.dashboard')
@section('title', 'Timeline')
@section('page-title', 'Activity Timeline')
@section('page-subtitle', 'Chronological activity log')
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

    <a href="{{ route('technician.calendar') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Calendar
    </a>

    <a href="{{ route('technician.timeline') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Timeline
    </a>

@endsection
@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Activity Timeline</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Track your job activities and progress</p>
            </div>
            <div class="flex gap-3">
                <select class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option>All Activities</option>
                    <option>Job Assigned</option>
                    <option>Work Started</option>
                    <option>Work Completed</option>
                    <option>Report Submitted</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option>Last 7 Days</option>
                    <option>Last 30 Days</option>
                    <option>Last 3 Months</option>
                    <option>All Time</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="space-y-8">
            <!-- Timeline Item 1 - Today -->
            <div class="relative pl-8 pb-8 border-l-2 border-blue-500 dark:border-blue-400">
                <div class="absolute -left-3 top-0 w-6 h-6 bg-blue-500 dark:bg-blue-400 rounded-full border-4 border-white dark:border-gray-800"></div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-semibold rounded">Today</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">10:30 AM</span>
                </div>
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Work Completed - JO-2026-015</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">HVAC Filter Replacement at 3rd Floor Office completed successfully</p>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Duration: 1.5 hours</span>
                    <span class="text-green-600 dark:text-green-400 font-semibold">• Completed</span>
                </div>
            </div>

            <!-- Timeline Item 2 -->
            <div class="relative pl-8 pb-8 border-l-2 border-gray-300 dark:border-gray-600">
                <div class="absolute -left-3 top-0 w-6 h-6 bg-green-500 dark:bg-green-400 rounded-full border-4 border-white dark:border-gray-800"></div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Jan 15, 2026 - 8:45 AM</span>
                </div>
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Work Started - JO-2026-015</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Started HVAC maintenance work at 3rd Floor Office</p>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-full">HVAC Maintenance</span>
                </div>
            </div>

            <!-- Timeline Item 3 -->
            <div class="relative pl-8 pb-8 border-l-2 border-gray-300 dark:border-gray-600">
                <div class="absolute -left-3 top-0 w-6 h-6 bg-purple-500 dark:bg-purple-400 rounded-full border-4 border-white dark:border-gray-800"></div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Jan 14, 2026 - 3:20 PM</span>
                </div>
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Report Submitted - JO-2026-012</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Generator routine inspection report submitted with photos and signature</p>
                <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-semibold">
                    View Report →
                </button>
            </div>

            <!-- Timeline Item 4 -->
            <div class="relative pl-8 pb-8 border-l-2 border-gray-300 dark:border-gray-600">
                <div class="absolute -left-3 top-0 w-6 h-6 bg-yellow-500 dark:bg-yellow-400 rounded-full border-4 border-white dark:border-gray-800"></div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Jan 14, 2026 - 9:15 AM</span>
                </div>
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Equipment Checked Out</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Checked out: Power Drill (DRL-008), Digital Multimeter (MTR-015)</p>
            </div>

            <!-- Timeline Item 5 -->
            <div class="relative pl-8 pb-8 border-l-2 border-gray-300 dark:border-gray-600">
                <div class="absolute -left-3 top-0 w-6 h-6 bg-blue-500 dark:bg-blue-400 rounded-full border-4 border-white dark:border-gray-800"></div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Jan 13, 2026 - 2:00 PM</span>
                </div>
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Job Assigned - JO-2026-015</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">New job assigned: HVAC Filter Replacement at 3rd Floor Office</p>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs rounded-full">Priority: Normal</span>
                </div>
            </div>

            <!-- Timeline Item 6 -->
            <div class="relative pl-8 pb-8 border-l-2 border-gray-300 dark:border-gray-600">
                <div class="absolute -left-3 top-0 w-6 h-6 bg-green-500 dark:bg-green-400 rounded-full border-4 border-white dark:border-gray-800"></div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Jan 13, 2026 - 11:45 AM</span>
                </div>
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Work Completed - JO-2026-011</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Elevator safety inspection completed at Tower A</p>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Duration: 3 hours</span>
                    <span class="text-green-600 dark:text-green-400 font-semibold">• Completed</span>
                </div>
            </div>

            <!-- Timeline Item 7 -->
            <div class="relative pl-8">
                <div class="absolute -left-3 top-0 w-6 h-6 bg-gray-400 dark:bg-gray-500 rounded-full border-4 border-white dark:border-gray-800"></div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Jan 12, 2026 - 4:30 PM</span>
                </div>
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Inventory Request Approved</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">HVAC Filter (50 pcs) request approved and available for pickup</p>
            </div>
        </div>
    </div>
</div>
@endsection
