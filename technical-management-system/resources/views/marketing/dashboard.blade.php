@extends('layouts.dashboard')

@section('title', 'Marketing Dashboard')

@section('page-title', 'Marketing Dashboard')
@section('page-subtitle', 'Manage job orders and customer requests')

@section('sidebar-nav')
    <a href="{{ route('marketing.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="#"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Job Orders
    </a>

    <a href="#"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4v16m8-8H4"/>
        </svg>
        Create New JO
    </a>

    <a href="#"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Customers
    </a>

    <a href="#"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Reports
    </a>
@endsection

@section('content')

    <!-- ===================== STATS (HORIZONTAL, EQUAL) ===================== -->
    <div class="mb-8">
        <div class="flex gap-4 overflow-x-auto pb-1">

            <!-- Total JO -->
            <div class="flex-1 min-w-[220px] bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-900/20
                        rounded-[20px] shadow-md p-6 border border-blue-200 dark:border-blue-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold mb-3">Total Job Orders</p>
                    <h3 class="text-4xl font-bold text-blue-900 dark:text-blue-100 mb-3">24</h3>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-auto">+12% from last month</p>
                </div>
            </div>

            <!-- Pending -->
            <div class="flex-1 min-w-[220px] bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/30 dark:to-orange-900/20
                        rounded-[20px] shadow-md p-6 border border-orange-200 dark:border-orange-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-orange-600 dark:text-orange-400 font-semibold mb-3">Pending Approval</p>
                    <h3 class="text-4xl font-bold text-orange-900 dark:text-orange-100 mb-3">8</h3>
                    <p class="text-xs text-orange-600 dark:text-orange-400 mt-auto">Awaiting review</p>
                </div>
            </div>

            <!-- In Progress -->
            <div class="flex-1 min-w-[220px] bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/30 dark:to-cyan-900/20
                        rounded-[20px] shadow-md p-6 border border-cyan-200 dark:border-cyan-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-cyan-600 dark:text-cyan-400 font-semibold mb-3">In Progress</p>
                    <h3 class="text-4xl font-bold text-cyan-900 dark:text-cyan-100 mb-3">12</h3>
                    <p class="text-xs text-cyan-600 dark:text-cyan-400 mt-auto">Being processed</p>
                </div>
            </div>

            <!-- Completed -->
            <div class="flex-1 min-w-[220px] bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-900/20
                        rounded-[20px] shadow-md p-6 border border-green-200 dark:border-green-800">
                <div class="flex flex-col h-full">
                    <p class="text-xs text-green-600 dark:text-green-400 font-semibold mb-3">Completed</p>
                    <h3 class="text-4xl font-bold text-green-900 dark:text-green-100 mb-3">4</h3>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-auto">This month</p>
                </div>
            </div>

        </div>
    </div>

    <!-- ===================== MAIN GRID ===================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- Recent Job Orders -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-[20px] shadow-md dark:shadow-gray-900/50 border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-slate-100 dark:border-gray-700">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Recent Job Orders</h3>
            </div>

            <div class="p-4 space-y-2">
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-gray-700 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-colors cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">JO</span>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">JO-2026-001</p>
                            <p class="text-sm text-slate-500 dark:text-gray-400">ABC Corporation - Generator Maintenance</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded-full">Pending</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-gray-700 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-colors cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">JO</span>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">JO-2026-002</p>
                            <p class="text-sm text-slate-500 dark:text-gray-400">XYZ Industries - Equipment Repair</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full">In Progress</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-gray-700 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-colors cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">JO</span>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">JO-2026-003</p>
                            <p class="text-sm text-slate-500 dark:text-gray-400">DEF Company - Installation Service</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full">Completed</span>
                </div>

                <button class="mt-4 w-full py-2.5 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold rounded-xl hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                    View All Job Orders →
                </button>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md dark:shadow-gray-900/50 border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-slate-100 dark:border-gray-700">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Quick Actions</h3>
            </div>

            <div class="p-4 space-y-2">
                <button class="w-full flex items-center gap-3 p-3 bg-blue-600 dark:bg-blue-700 text-white rounded-xl hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors shadow-md shadow-blue-200 dark:shadow-blue-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="font-semibold">Create Job Order</span>
                </button>

                <button class="w-full flex items-center gap-3 p-3 bg-slate-50 dark:bg-gray-700 text-slate-700 dark:text-gray-200 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="font-semibold">Add Customer</span>
                </button>

                 <button class="w-full flex items-center gap-3 p-3 bg-slate-50 text-slate-700 rounded-xl hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="font-semibold">View Reports</span>
                </button>
            </div>
        </div>
@endsection
