@extends('layouts.dashboard')
@section('title', 'Timeline')
@section('page-title', 'Job Order Timeline')
@section('page-subtitle', 'Track job order progress from creation to completion')

@section('sidebar-nav')
    <a href="{{ route('marketing.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('marketing.create-job-order') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4v16m8-8H4"/>
        </svg>
        Create New JO
    </a>

    <a href="{{ route('marketing.customers') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Customers
    </a>

    <a href="{{ route('marketing.job-orders') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Job Orders
    </a>

    <a href="{{ route('marketing.reports') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Reports
    </a>

    <a href="{{ route('marketing.timeline') }}"
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
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Job Order Timeline</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Track all job orders from creation to certificate release</p>
            </div>
            <div class="flex gap-3">
                <input type="text" placeholder="Search JO Number..." 
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <select class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option>All Stages</option>
                    <option>Created</option>
                    <option>Assigned</option>
                    <option>In Progress</option>
                    <option>Completed</option>
                    <option>Approved</option>
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

    <!-- Active Job Orders Timeline -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Active Job Orders</h3>
        
        <!-- JO Timeline 1 - In Progress -->
        <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white">JO-2026-015</h4>
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-semibold rounded-full">In Progress</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-1">HVAC Filter Replacement - 3rd Floor Office</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">Customer: Acme Corp | Technician: Juan Dela Cruz</p>
                </div>
                <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-semibold text-sm">
                    View Details →
                </button>
            </div>
            
            <!-- Timeline Steps -->
            <div class="relative pl-8">
                <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                
                <!-- Step 1 - Completed -->
                <div class="relative mb-6">
                    <div class="absolute -left-[19px] top-1 w-5 h-5 bg-green-500 rounded-full border-4 border-white dark:border-gray-800"></div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">JO Created</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Jan 12, 2026 2:00 PM - by Maria Santos</p>
                        </div>
                    </div>
                </div>

                <!-- Step 2 - Completed -->
                <div class="relative mb-6">
                    <div class="absolute -left-[19px] top-1 w-5 h-5 bg-green-500 rounded-full border-4 border-white dark:border-gray-800"></div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">Assigned & Scheduled</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Jan 13, 2026 9:00 AM - Assigned to Juan Dela Cruz</p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 - Current -->
                <div class="relative mb-6">
                    <div class="absolute -left-[19px] top-1 w-5 h-5 bg-blue-500 rounded-full border-4 border-white dark:border-gray-800 animate-pulse"></div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">Calibration Execution</p>
                            <p class="text-sm text-blue-600 dark:text-blue-400">In Progress - Started Jan 15, 2026 8:45 AM</p>
                        </div>
                    </div>
                </div>

                <!-- Step 4 - Pending -->
                <div class="relative mb-6">
                    <div class="absolute -left-[19px] top-1 w-5 h-5 bg-gray-300 dark:bg-gray-600 rounded-full border-4 border-white dark:border-gray-800"></div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-500 dark:text-gray-400">Report Upload</p>
                            <p class="text-sm text-gray-400">Pending</p>
                        </div>
                    </div>
                </div>

                <!-- Step 5 - Pending -->
                <div class="relative mb-6">
                    <div class="absolute -left-[19px] top-1 w-5 h-5 bg-gray-300 dark:bg-gray-600 rounded-full border-4 border-white dark:border-gray-800"></div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-500 dark:text-gray-400">Approval & Signature</p>
                            <p class="text-sm text-gray-400">Pending</p>
                        </div>
                    </div>
                </div>

                <!-- Step 6 - Pending -->
                <div class="relative mb-6">
                    <div class="absolute -left-[19px] top-1 w-5 h-5 bg-gray-300 dark:bg-gray-600 rounded-full border-4 border-white dark:border-gray-800"></div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-500 dark:text-gray-400">Certificate Generation</p>
                            <p class="text-sm text-gray-400">Pending</p>
                        </div>
                    </div>
                </div>

                <!-- Step 7 - Pending -->
                <div class="relative">
                    <div class="absolute -left-[19px] top-1 w-5 h-5 bg-gray-300 dark:bg-gray-600 rounded-full border-4 border-white dark:border-gray-800"></div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-500 dark:text-gray-400">Certificate Release</p>
                            <p class="text-sm text-gray-400">Pending</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JO Timeline 2 - Completed -->
        <div class="mb-8">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white">JO-2026-012</h4>
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-semibold rounded-full">Completed</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-1">Generator Routine Inspection - Main Building</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">Customer: Global Industries | Technician: Juan Dela Cruz</p>
                </div>
                <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-semibold text-sm">
                    View Certificate →
                </button>
            </div>
            
            <!-- Timeline Steps - All Completed -->
            <div class="relative pl-8">
                <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-green-200 dark:bg-green-900"></div>
                
                <div class="space-y-4 text-sm">
                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Created: Jan 10, 2026</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Assigned: Jan 11, 2026</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Executed: Jan 13, 2026</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Report Submitted: Jan 14, 2026</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Approved: Jan 14, 2026</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Certificate Generated: Jan 14, 2026</span>
                    </div>
                    <div class="flex items-center gap-3 text-green-600 dark:text-green-400 font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Certificate Released: Jan 15, 2026</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
