@extends('layouts.dashboard')

@section('title', 'Analytics')

@section('page-title', 'Analytics')
@section('page-subtitle', 'Operational KPIs and team performance')

@section('sidebar-nav')
    @include('tech-head.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-6">
        <!-- KPI cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-4 flex flex-col justify-between h-28">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Job Orders</p>
                </div>
                <div>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['totalJobOrders'] ?? 0 }}</p>
                </div>
            </div>
            <div class="bg-emerald-50 dark:bg-emerald-900/30 rounded-[20px] shadow-md border border-emerald-200 dark:border-emerald-800 p-4 flex flex-col justify-between h-28">
                <div>
                    <p class="text-sm text-emerald-900 dark:text-emerald-200">Completed Jobs</p>
                </div>
                <div>
                    <p class="text-2xl sm:text-3xl font-bold text-emerald-900 dark:text-emerald-100">{{ $stats['completedJobs'] ?? 0 }}</p>
                </div>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/30 rounded-[20px] shadow-md border border-blue-200 dark:border-blue-800 p-4 flex flex-col justify-between h-28">
                <div>
                    <p class="text-sm text-blue-900 dark:text-blue-200">Avg Completion Time (hrs)</p>
                </div>
                <div>
                    <p class="text-2xl sm:text-3xl font-bold text-blue-900 dark:text-blue-100">{{ number_format($stats['avgCompletionTime'] ?? 0, 1) }}</p>
                </div>
            </div>
            <div class="bg-amber-50 dark:bg-amber-900/30 rounded-[20px] shadow-md border border-amber-200 dark:border-amber-800 p-4 flex flex-col justify-between h-28">
                <div>
                    <p class="text-sm text-amber-900 dark:text-amber-200">Team Size</p>
                </div>
                <div>
                    <p class="text-2xl sm:text-3xl font-bold text-amber-900 dark:text-amber-100">{{ ($stats['technicianPerformance'] ?? collect())->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Technician performance table / mobile cards -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Technician Performance</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Totals vs completed</p>
            </div>

            <!-- Mobile cards -->
            <div class="space-y-3 md:hidden">
                @foreach($stats['technicianPerformance'] ?? [] as $tech)
                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/20 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $tech->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total: <span class="font-medium text-gray-700 dark:text-gray-200">{{ $tech->total_jobs ?? 0 }}</span></p>
                        </div>
                        <div class="text-right">
                            <div class="inline-flex items-center gap-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">{{ $tech->completed_jobs ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Table for md+ -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr class="text-left">
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Technician</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Total Jobs</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Completed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($stats['technicianPerformance'] ?? [] as $tech)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="py-3">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $tech->name }}</p>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-200">{{ $tech->total_jobs ?? 0 }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">{{ $tech->completed_jobs ?? 0 }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
