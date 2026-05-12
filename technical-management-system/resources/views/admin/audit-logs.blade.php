@extends('layouts.dashboard')

@section('title', 'Audit Logs')

@section('page-title', 'Audit Logs')

@section('page-subtitle', 'System activity and user action history')

@section('sidebar-nav')
    @include('admin.sidebar-nav')
@endsection

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Logs</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Track all system activities and user actions</p>
        </div>
        <a href="{{ route('admin.audit-logs.export', request()->all()) }}" class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export Logs
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
        <form id="auditLogFiltersForm" method="GET" action="{{ route('admin.audit-logs.index') }}" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Search</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-gray-400 dark:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input id="auditLogSearchInput" type="text" name="search" value="{{ request('search') }}" placeholder="Search user, action, or details..." class="w-full h-12 pl-12 pr-4 border border-gray-300 dark:border-gray-600 rounded-2xl bg-gray-50 dark:bg-gray-700/80 dark:text-white placeholder:text-gray-500 dark:placeholder:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 sm:gap-4 items-end">
                <div class="lg:col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Department</label>
                    <div>
                        <select name="department" class="w-full h-12 px-4 border border-gray-300 dark:border-gray-600 rounded-2xl bg-gray-50 dark:bg-gray-700/80 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Action Type</label>
                    <select name="action" class="w-full h-12 px-4 border border-gray-300 dark:border-gray-600 rounded-2xl bg-gray-50 dark:bg-gray-700/80 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm">
                        <option value="">All Actions</option>
                        <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                        <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                        <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                        <option value="calibrate" {{ request('action') == 'calibrate' ? 'selected' : '' }}>Calibrate</option>
                    </select>
                </div>

                <div class="lg:col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Date Range</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full h-12 px-4 border border-gray-300 dark:border-gray-600 rounded-2xl bg-gray-50 dark:bg-gray-700/80 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm">
                </div>

                <div class="lg:col-span-3 flex flex-col sm:flex-row items-center justify-end gap-2 whitespace-nowrap">
                    <a href="{{ route('admin.audit-logs.index') }}" class="h-12 w-full sm:w-auto inline-flex items-center justify-center px-5 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 transition-colors text-center font-medium">
                        Reset
                    </a>
                    <button type="submit" class="h-12 w-full sm:w-auto inline-flex items-center justify-center px-5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium">Filter</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Audit Log List -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 overflow-hidden p-3 sm:p-4 md:p-0">
        <div class="space-y-3 md:hidden">
            @forelse($auditLogs as $log)
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-3 sm:p-4">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $log->user?->name ?? 'System' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}</p>
                        </div>
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
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        @php
                            $actor = $log->user?->name ?? 'System';
                            $action = strtoupper((string) $log->action);
                            $model = (string) ($log->model_type ?? 'Record');
                            $oldValues = is_array($log->old_values) ? $log->old_values : [];
                            $newValues = is_array($log->new_values) ? $log->new_values : [];
                            $changedFields = is_array($log->changed_fields) ? $log->changed_fields : [];
                            $targetUser = ($model === 'User' && !empty($log->model_id)) ? \App\Models\User::find($log->model_id) : null;
                            $department = $newValues['department'] ?? $oldValues['department'] ?? ($targetUser?->department ?? null);
                            $departmentLabel = $department ?: 'Unassigned Department';

                            $summary = $log->description ?: "{$actor} performed {$action}";

                            if ($model === 'User' && $action === 'UPDATE') {
                                if (in_array('name', $changedFields, true) && isset($newValues['name'])) {
                                    $summary = "{$actor} changed name to {$newValues['name']} for this user (Department: {$departmentLabel}).";
                                } elseif (in_array('email', $changedFields, true) && isset($newValues['email'])) {
                                    $summary = "{$actor} changed email to {$newValues['email']} for this user (Department: {$departmentLabel}).";
                                } elseif (in_array('role_id', $changedFields, true) && isset($newValues['role_id'])) {
                                    $summary = "{$actor} changed role for this user (Department: {$departmentLabel}).";
                                } elseif (in_array('is_active', $changedFields, true)) {
                                    $isActive = (bool) ($newValues['is_active'] ?? false);
                                    $summary = $isActive
                                        ? "{$actor} activated this user account (Department: {$departmentLabel})."
                                        : "{$actor} deactivated this user account (Department: {$departmentLabel}).";
                                } else {
                                    $summary = "{$actor} updated this user account (Department: {$departmentLabel}).";
                                }
                            }

                            if ($model === 'User' && $action === 'CREATE') {
                                $summary = "{$actor} created a user account (Department: {$departmentLabel}).";
                            }

                            if ($model === 'User' && $action === 'DELETE') {
                                $summary = "{$actor} deleted a user account (Department: {$departmentLabel}).";
                            }
                        @endphp
                        {{ $summary }}
                    </div>
                </div>
            @empty
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    No audit logs found
                </div>
            @endforelse
        </div>
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full table-fixed">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <th class="w-1/5 px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-white">Timestamp</th>
                        <th class="w-1/6 px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-white">User</th>
                        <th class="w-1/6 px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-white">Action</th>
                        <th class="w-1/2 px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-white">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-center align-middle text-sm text-gray-600 dark:text-gray-400">
                            {{ $log->created_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-center align-middle text-sm font-medium text-gray-900 dark:text-white">
                            {{ $log->user?->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
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
                        <td class="px-6 py-4 text-center align-middle text-sm text-gray-600 dark:text-gray-400">
                            @php
                                $actor = $log->user?->name ?? 'System';
                                $action = strtoupper((string) $log->action);
                                $model = (string) ($log->model_type ?? 'Record');
                                $oldValues = is_array($log->old_values) ? $log->old_values : [];
                                $newValues = is_array($log->new_values) ? $log->new_values : [];
                                $changedFields = is_array($log->changed_fields) ? $log->changed_fields : [];
                                $targetUser = ($model === 'User' && !empty($log->model_id)) ? \App\Models\User::find($log->model_id) : null;
                                $department = $newValues['department'] ?? $oldValues['department'] ?? ($targetUser?->department ?? null);
                                $departmentLabel = $department ?: 'Unassigned Department';

                                $summary = $log->description ?: "{$actor} performed {$action}";

                                if ($model === 'User' && $action === 'UPDATE') {
                                    if (in_array('name', $changedFields, true) && isset($newValues['name'])) {
                                        $summary = "{$actor} changed name to {$newValues['name']} for this user (Department: {$departmentLabel}).";
                                    } elseif (in_array('email', $changedFields, true) && isset($newValues['email'])) {
                                        $summary = "{$actor} changed email to {$newValues['email']} for this user (Department: {$departmentLabel}).";
                                    } elseif (in_array('role_id', $changedFields, true) && isset($newValues['role_id'])) {
                                        $summary = "{$actor} changed role for this user (Department: {$departmentLabel}).";
                                    } elseif (in_array('is_active', $changedFields, true)) {
                                        $isActive = (bool) ($newValues['is_active'] ?? false);
                                        $summary = $isActive
                                            ? "{$actor} activated this user account (Department: {$departmentLabel})."
                                            : "{$actor} deactivated this user account (Department: {$departmentLabel}).";
                                    } else {
                                        $summary = "{$actor} updated this user account (Department: {$departmentLabel}).";
                                    }
                                }

                                if ($model === 'User' && $action === 'CREATE') {
                                    $summary = "{$actor} created a user account (Department: {$departmentLabel}).";
                                }

                                if ($model === 'User' && $action === 'DELETE') {
                                    $summary = "{$actor} deleted a user account (Department: {$departmentLabel}).";
                                }
                            @endphp
                            {{ $summary }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            No audit logs found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-0 md:px-6 py-3 sm:py-4 border-t border-gray-200 dark:border-gray-700 text-xs sm:text-sm">
            {{ $auditLogs->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('auditLogFiltersForm');
        const searchInput = document.getElementById('auditLogSearchInput');

        if (!filterForm || !searchInput) {
            return;
        }

        let searchDebounce;

        searchInput.addEventListener('input', function () {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(function () {
                filterForm.submit();
            }, 350);
        });
    });
</script>
@endsection
