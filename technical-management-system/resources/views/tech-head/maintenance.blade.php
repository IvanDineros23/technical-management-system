@extends('layouts.dashboard')

@section('title', 'Maintenance')

@section('page-title', 'Maintenance')
@section('page-subtitle', 'Equipment under maintenance or calibration')

@section('sidebar-nav')
    @include('tech-head.partials.sidebar')
@endsection

@section('content')
    <div x-data="{ showAdd:false, showDetails:false, selectedItem:null }" class="space-y-6">
        
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $maintenanceTasks->total() }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">In Maintenance</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $maintenanceTasks->where('calibration_required', true)->count() }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Needs Calibration</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $recentRecords }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">This Month</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $overdueCount }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Overdue</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end">
            <button @click="showAdd=true" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Maintenance Record
                </span>
            </button>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Maintenance Queue</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Equipment requiring maintenance or calibration</p>
                </div>
            </div>
            
            @if($maintenanceTasks->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($maintenanceTasks as $item)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-700/30 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->name ?? 'Equipment' }}</p>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200">
                                {{ ucfirst($item->status ?? 'maintenance') }}
                            </span>
                        </div>
                        
                        <div class="space-y-1 mb-3">
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                <span class="font-semibold">Code:</span> {{ $item->equipment_code ?? $item->id }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                <span class="font-semibold">Category:</span> {{ $item->category ?? 'N/A' }}
                            </p>
                            @if($item->calibration_required)
                            <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                                🔍 Calibration Required
                            </p>
                            @endif
                            @if($item->next_maintenance)
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                <span class="font-semibold">Next Due:</span> {{ optional($item->next_maintenance)->format('M d, Y') }}
                            </p>
                            @endif
                        </div>
                        
                        <div class="border-t border-gray-200 dark:border-gray-600 pt-2 mt-2">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white mb-1">Recent Maintenance History</p>
                            @if($item->maintenanceRecords && $item->maintenanceRecords->count() > 0)
                                <div class="space-y-1">
                                    @foreach($item->maintenanceRecords->take(2) as $record)
                                        <div class="text-xs text-gray-700 dark:text-gray-300">
                                            <span class="font-medium">{{ optional($record->performed_at)->format('M d, Y') }}</span>
                                            <span class="text-gray-500">•</span>
                                            <span>{{ ucfirst($record->maintenance_type ?? 'general') }}</span>
                                        </div>
                                    @endforeach
                                    @if($item->maintenanceRecords->count() > 2)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 italic">+{{ $item->maintenanceRecords->count() - 2 }} more records</p>
                                    @endif
                                </div>
                            @else
                                <p class="text-xs text-gray-500 dark:text-gray-400 italic">No maintenance history</p>
                            @endif
                        </div>
                        
                        <button @click="selectedItem={{ json_encode($item) }}; showDetails=true;" 
                                class="mt-3 w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition-colors">
                            View Full Details
                        </button>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6">
                {{ $maintenanceTasks->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No equipment in maintenance</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All equipment is currently operational.</p>
            </div>
            @endif
        </div>
        
        <!-- Equipment Details Modal -->
        <div 
            x-show="showDetails" 
            x-cloak
            @keydown.escape.window="showDetails=false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showDetails=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-3xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700 max-h-[85vh] overflow-y-auto"
                >
                <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 rounded-t-[20px]">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Equipment Details</h3>
                        <button @click="showDetails=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="p-6 space-y-4" x-show="selectedItem">
                    <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Basic Information</h4>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Name:</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedItem?.name || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Code:</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedItem?.equipment_code || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Category:</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedItem?.category || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Status:</p>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedItem?.status || 'N/A'"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6 py-4 rounded-b-[20px]">
                    <button @click="showDetails=false" class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Close</button>
                </div>
                </div>
            </div>
        </div>
        <!-- Add Maintenance Modal -->
        <div 
            x-show="showAdd" 
            x-cloak
            @keydown.escape.window="showAdd=false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showAdd=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                <form method="POST" action="{{ route('tech-head.maintenance.store') }}" class="p-6 space-y-4">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Add Maintenance Record</h3>
                    <input name="equipment_id" placeholder="Equipment ID" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                    <select name="maintenance_type" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                        <option value="preventive">Preventive</option>
                        <option value="corrective">Corrective</option>
                        <option value="calibration">Calibration</option>
                        <option value="repair">Repair</option>
                    </select>
                    <input type="datetime-local" name="performed_at" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                    <textarea name="description" placeholder="Description" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white"></textarea>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showAdd=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Add</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
@endsection
