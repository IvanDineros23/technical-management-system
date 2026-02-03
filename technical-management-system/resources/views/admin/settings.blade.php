@extends('layouts.dashboard')

@section('title', 'Settings')

@section('page-title', 'System Settings')

@section('page-subtitle', 'Configure system-wide settings and preferences')

@section('sidebar-nav')
    @include('admin.sidebar-nav')
@endsection

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'general' }">
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
                <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white'" class="px-6 py-4 font-medium whitespace-nowrap">General</button>
                <button @click="activeTab = 'backup'" :class="activeTab === 'backup' ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white'" class="px-6 py-4 font-medium whitespace-nowrap">Backup & Restore</button>
                <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white'" class="px-6 py-4 font-medium whitespace-nowrap">Security</button>
                <button @click="activeTab = 'maintenance'" :class="activeTab === 'maintenance' ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white'" class="px-6 py-4 font-medium whitespace-nowrap">Maintenance</button>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- General Settings Tab -->
            <div x-show="activeTab === 'general'" class="space-y-6">
                <form action="{{ route('admin.settings.general.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name</label>
                        <input type="text" name="company_name" value="Gemarc Enterprises Inc" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">System Email</label>
                        <input type="email" name="system_email" value="system@gemarcph.com" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email used for system notifications and alerts</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Phone</label>
                        <input type="tel" name="contact_phone" value="+63 (02) 8123-4567" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Business Address</label>
                        <textarea name="business_address" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="3">123 Calibration Street, Metro Manila, Philippines</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
                        <select name="timezone" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="Asia/Manila" selected>Asia/Manila (UTC+8)</option>
                            <option value="Asia/Bangkok">Asia/Bangkok (UTC+7)</option>
                            <option value="Asia/Singapore">Asia/Singapore (UTC+8)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Format</label>
                        <select name="date_format" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="M d, Y">Feb 03, 2026 (M d, Y)</option>
                            <option value="d/m/Y">03/02/2026 (d/m/Y)</option>
                            <option value="Y-m-d">2026-02-03 (Y-m-d)</option>
                        </select>
                    </div>
                    <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Save Changes</button>
                        <button type="button" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Backup & Restore Tab -->
            <div x-show="activeTab === 'backup'" class="space-y-6">
                <!-- Database Backup -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Database Backup</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Create a backup of your database including all data</p>
                        </div>
                        <svg class="w-12 h-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                        </svg>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Last Backup</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Feb 02, 2026 at 11:45 PM</p>
                            </div>
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs rounded-full">Success</span>
                        </div>
                        <div class="flex gap-3">
                            <form action="{{ route('admin.settings.backup.create') }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Create Backup Now
                                </button>
                            </form>
                            <button class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                Schedule
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Backup History -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Backup History</h3>
                    <div class="space-y-3">
                        @forelse($backups as $backup)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $backup['filename'] }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $backup['size'] }} • {{ $backup['date'] }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.settings.backup.download', $backup['filename']) }}" class="px-3 py-1 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">Download</a>
                                <form action="{{ route('admin.settings.backup.delete', $backup['filename']) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this backup?')" class="px-3 py-1 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">Delete</button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-gray-500 dark:text-gray-400 py-8">No backups found. Create your first backup to get started.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Restore Database -->
                <div class="border border-yellow-200 dark:border-yellow-700 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6">
                    <div class="flex items-start gap-4">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-200 mb-2">Restore Database</h3>
                            <p class="text-sm text-yellow-800 dark:text-yellow-300 mb-4">⚠️ Warning: Restoring will replace all current data with the backup data. This action cannot be undone.</p>
                            <form action="{{ route('admin.settings.backup.restore') }}" method="POST" enctype="multipart/form-data" class="space-y-3" onsubmit="return confirm('Are you ABSOLUTELY SURE you want to restore the database? This will replace ALL current data!')">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-yellow-900 dark:text-yellow-200 mb-2">Select Backup File</label>
                                    <input type="file" name="backup_file" accept=".sql,.zip" required class="w-full px-4 py-2 border border-yellow-300 dark:border-yellow-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-500">
                                </div>
                                <button type="submit" class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Restore Database
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Tab -->
            <div x-show="activeTab === 'security'" class="space-y-6">
                <form action="{{ route('admin.settings.security.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Session Settings</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Session Timeout (minutes)</label>
                                <input type="number" name="session_timeout" value="120" min="5" max="1440" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Users will be automatically logged out after this period of inactivity</p>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Force Password Change</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Require users to change password every 90 days</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="force_password_change" value="1" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Two-Factor Authentication</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Enable 2FA for all admin accounts</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="two_factor_auth" value="1" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Active Sessions</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">Current Session</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">127.0.0.1 • Chrome • Active now</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs rounded-full">Active</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Save Security Settings</button>
                    </div>
                </form>
                <form action="{{ route('admin.settings.sessions.terminate') }}" method="POST" onsubmit="return confirm('Are you sure you want to terminate all other sessions?')">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">Terminate All Sessions</button>
                </form>
            </div>

            <!-- Maintenance Tab -->
            <div x-show="activeTab === 'maintenance'" class="space-y-6">
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Maintenance</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Clear Cache</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Remove all cached data to improve performance</p>
                            </div>
                            <form action="{{ route('admin.settings.cache.clear') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Clear</button>
                            </form>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Optimize Database</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Analyze and optimize database tables</p>
                            </div>
                            <form action="{{ route('admin.settings.database.optimize') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Optimize</button>
                            </form>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Clear Logs</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Delete old system logs (older than 30 days)</p>
                            </div>
                            <form action="{{ route('admin.settings.logs.clear') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete old logs?')">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Clear</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Laravel Version</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">11.x</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">PHP Version</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ phpversion() }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Database</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">MySQL</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Storage Used</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">125 MB</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
