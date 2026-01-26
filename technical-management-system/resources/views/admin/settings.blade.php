@extends('layouts.dashboard')

@section('title', 'Settings')

@section('page-title', 'System Settings')

@section('page-subtitle', 'Configure system-wide settings and preferences')

@section('sidebar-nav')
    @include('admin.sidebar-nav')
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">System Settings</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage configuration and system preferences</p>
    </div>

    <!-- Settings Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <div class="flex overflow-x-auto">
                <button class="px-6 py-4 font-medium text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400 whitespace-nowrap">General</button>
                <button class="px-6 py-4 font-medium text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white whitespace-nowrap">Numbering</button>
                <button class="px-6 py-4 font-medium text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white whitespace-nowrap">Services</button>
                <button class="px-6 py-4 font-medium text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white whitespace-nowrap">Validity</button>
                <button class="px-6 py-4 font-medium text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white whitespace-nowrap">Email</button>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="p-6 space-y-6">
            <!-- General Settings -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name</label>
                    <input type="text" value="Technical Management System" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                    <input type="email" value="contact@tms.com" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                    <input type="tel" value="(555) 123-4567" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                    <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="3">123 Technical Avenue, City, State, ZIP</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
                    <select class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="Asia/Manila" selected>Asia/Manila (UTC+8)</option>
                        <option value="Asia/Bangkok">Asia/Bangkok (UTC+7)</option>
                        <option value="Asia/Kolkata">Asia/Kolkata (UTC+5:30)</option>
                    </select>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Save Changes</button>
                <button class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Service Types -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Service Types</h3>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">Add Service</button>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Calibration</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Equipment calibration service</p>
                </div>
                <button class="text-gray-600 hover:text-gray-900 dark:hover:text-white">Edit</button>
            </div>
            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Repair</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Equipment repair service</p>
                </div>
                <button class="text-gray-600 hover:text-gray-900 dark:hover:text-white">Edit</button>
            </div>
        </div>
    </div>

    <!-- Acceptance Criteria -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Acceptance Criteria</h3>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">Add Criteria</button>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Accuracy ≤ 0.1%</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Applied to all measurement devices</p>
                </div>
                <button class="text-gray-600 hover:text-gray-900 dark:hover:text-white">Edit</button>
            </div>
        </div>
    </div>
</div>
@endsection
