@extends('layouts.dashboard')

@section('title', 'Audit Logs')

@section('page-title', 'Audit Logs')

@section('page-subtitle', 'System activity and user action history')

@section('sidebar-nav')
    @include('admin.sidebar-nav')
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Logs</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Track all system activities and user actions</p>
        </div>
        <a href="{{ route('admin.audit-logs.export', request()->all()) }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export Logs
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User</label>
                <select name="user_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Users</option>
                    @foreach(\App\Models\User::orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Action Type</label>
                <select name="action" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Actions</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="calibrate" {{ request('action') == 'calibrate' ? 'selected' : '' }}>Calibrate</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Module</label>
                <select name="model_type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Modules</option>
                    <option value="User" {{ request('model_type') == 'User' ? 'selected' : '' }}>User</option>
                    <option value="Profile" {{ request('model_type') == 'Profile' ? 'selected' : '' }}>Profile</option>
                    <option value="Invoice" {{ request('model_type') == 'Invoice' ? 'selected' : '' }}>Invoice</option>
                    <option value="Inventory" {{ request('model_type') == 'Inventory' ? 'selected' : '' }}>Inventory</option>
                    <option value="InventoryRequest" {{ request('model_type') == 'InventoryRequest' ? 'selected' : '' }}>Inventory Request</option>
                    <option value="Equipment" {{ request('model_type') == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                    <option value="JobOrder" {{ request('model_type') == 'JobOrder' ? 'selected' : '' }}>Job Order</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Filter</button>
            </div>
        </form>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Timestamp</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">User</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Action</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Module</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $log->created_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            {{ $log->user?->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $action = strtoupper($log->action);
                                $actionClasses = [
                                    'CREATE' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                                    'UPDATE' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
                                    'DELETE' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                                    'LOGIN' => 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200',
                                    'LOGOUT' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
                                    'CALIBRATE' => 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200',
                                    'DEACTIVATE' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
                                    'SUBMIT' => 'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200',
                                    'VIEW' => 'bg-cyan-100 dark:bg-cyan-900 text-cyan-800 dark:text-cyan-200',
                                ];
                                $classes = $actionClasses[$action] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $classes }}">
                                {{ ucfirst(strtolower($log->action)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $log->model_type ?? 'System' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $log->description ?? 'N/A' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            No audit logs found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $auditLogs->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
