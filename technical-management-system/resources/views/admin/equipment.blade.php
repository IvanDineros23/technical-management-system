@extends('layouts.dashboard')

@section('title', 'Equipment')

@section('page-title', 'Equipment')

@section('page-subtitle', 'Manage equipment and calibration registry')

@section('sidebar-nav')
    @include('admin.sidebar-nav')
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Equipment</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Equipment registry and calibration tracking</p>
        </div>
        <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Register Equipment
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Equipment</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">156</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Due for Calibration</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">12</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Overdue</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">3</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" placeholder="Search by name or serial..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                <select class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Types</option>
                    <option value="meter">Meter</option>
                    <option value="scale">Scale</option>
                    <option value="microscope">Microscope</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="calibration">In Calibration</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Equipment Table -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Equipment Name</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Serial Number</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Type</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Last Calibrated</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">Digital Meter A</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">DM-2024-001</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">Meter</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">Dec 15, 2023</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Active</span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400 mr-3">View</button>
                            <button class="text-orange-600 hover:text-orange-900 dark:hover:text-orange-400">Calibrate</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
