@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'System administration, user management, and compliance monitoring')

@section('sidebar-nav')
    @include('admin.sidebar-nav')
@endsection

@section('content')
<div class="space-y-6">
    <!-- Current Date & Time Display -->
    <div class="text-right mb-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            <span id="current-datetime"></span>
        </p>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Total Users -->
        <a href="{{ route('admin.users.index') }}" class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M7 20H2v-2a3 3 0 015.856-1.487M12 14a4 4 0 100-8 4 4 0 000 8z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_users'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Users</p>
                </div>
            </div>
        </a>

        <!-- Active Users -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_users'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Active Users</p>
                </div>
            </div>
        </div>

        <!-- Inactive Users -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['inactive_users'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Inactive Users</p>
                </div>
            </div>
        </div>
    </div>

    <!-- User Role Distribution -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['technician_users'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Technicians</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['operator_users'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Operators</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['customer_users'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Customers</p>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.roles.index') }}" class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition-all cursor-pointer">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">Roles</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Manage →</p>
                </div>
            </div>
        </a>
    </div>

    <!-- System Status -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">🏥 System Status</h3>
        </div>

        <div class="space-y-3">
            @foreach($systemStatus as $service)
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $service['name'] }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $service['message'] }}</p>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $service['status'] === 'healthy' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200' }}">
                    {{ ucfirst($service['status']) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent User Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">👥 Recent User Activity</h3>
            <a href="{{ route('admin.users.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-medium">View all →</a>
        </div>

        @if($recentUserActivity->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr class="text-left text-xs">
                            <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">User</th>
                            <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Email</th>
                            <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Role</th>
                            <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                            <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Last Login</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($recentUserActivity as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="py-3 font-semibold text-gray-900 dark:text-white">{{ $user->name }}</td>
                            <td class="py-3 text-gray-700 dark:text-gray-300 text-sm">{{ $user->email }}</td>
                            <td class="py-3 text-gray-700 dark:text-gray-300 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-3 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $user->last_login }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-sm text-gray-500 dark:text-gray-400">No user activity</p>
            </div>
        @endif
    </div>

    <!-- Audit Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">📋 Audit Logs</h3>
            <a href="{{ route('admin.audit-logs.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-medium">View all →</a>
        </div>

        @if($auditActivity->count() > 0)
            <div class="space-y-4">
                @foreach($auditActivity as $audit)
                <div class="pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0 last:pb-0">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full 
                            @if($audit->action === 'CREATE') bg-green-600 dark:bg-green-400
                            @elseif($audit->action === 'UPDATE') bg-blue-600 dark:bg-blue-400
                            @elseif($audit->action === 'DELETE') bg-red-600 dark:bg-red-400
                            @else bg-gray-600 dark:bg-gray-400
                            @endif mt-1.5 flex-shrink-0"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                {{ $audit->description ?? ($audit->user_name . ' has ' . strtolower($audit->action)) }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $audit->model }} • {{ $audit->ref_id }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-500 dark:text-gray-500">{{ $audit->created_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-500">{{ $audit->created_at?->diffForHumans() ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-sm text-gray-500 dark:text-gray-400">No audit activity</p>
            </div>
        @endif
    </div>

    <!-- Quick Admin Actions -->
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/30 rounded-[20px] border border-blue-200 dark:border-blue-800 p-6">
        <h3 class="text-sm font-bold text-blue-900 dark:text-blue-200 mb-3">⚡ Quick Admin Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                👥 Manage Users →
            </a>
            <a href="{{ route('admin.roles.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                🔐 Manage Roles →
            </a>
            <a href="{{ route('admin.settings.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                ⚙️ Settings →
            </a>
            <a href="{{ route('admin.audit-logs.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                📊 Audit Logs →
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Display current date and time in Asia/Manila timezone
    function updateDateTime() {
        const now = new Date();
        const formatter = new Intl.DateTimeFormat('en-US', {
            timeZone: 'Asia/Manila',
            year: 'numeric',
            month: 'long',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });
        
        const datetimeStr = formatter.format(now);
        document.getElementById('current-datetime').textContent = datetimeStr;
    }
    
    // Update on page load and every second
    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>
@endsection
