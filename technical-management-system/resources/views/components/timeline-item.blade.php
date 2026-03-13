@props(['timeline'])

@php
    // Color mapping for different action types
    $actionColorMap = [
        'create' => 'border-green-500 bg-green-50 dark:bg-green-900/20',
        'update' => 'border-orange-500 bg-orange-50 dark:bg-orange-900/20',
        'delete' => 'border-red-500 bg-red-50 dark:bg-red-900/20',
        'completed' => 'border-green-500 bg-green-50 dark:bg-green-900/20',
        'in_progress' => 'border-blue-500 bg-blue-50 dark:bg-blue-900/20',
        'pending' => 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20',
        'approved' => 'border-purple-500 bg-purple-50 dark:bg-purple-900/20',
        'payment' => 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20',
        'default' => 'border-gray-500 bg-gray-50 dark:bg-gray-900/20',
    ];
    
    $statusColors = [
        'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 border-yellow-500',
        'in_progress' => 'bg-blue-100 dark:bg-blue-900/30 border-blue-500',
        'completed' => 'bg-green-100 dark:bg-green-900/30 border-green-500',
        'cancelled' => 'bg-red-100 dark:bg-red-900/30 border-red-500',
        'pending_signature' => 'bg-purple-100 dark:bg-purple-900/30 border-purple-500',
        'billable' => 'bg-emerald-100 dark:bg-emerald-900/30 border-emerald-500',
        'unpaid' => 'bg-orange-100 dark:bg-orange-900/30 border-orange-500',
    ];
    
    $priorityColors = [
        'low' => 'text-gray-600 dark:text-gray-400',
        'normal' => 'text-blue-600 dark:text-blue-400',
        'high' => 'text-orange-600 dark:text-orange-400',
        'urgent' => 'text-red-600 dark:text-red-400',
    ];
    
    $typeIcons = [
        'job_order' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
        'work_assignment' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
        'oversight' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>',
        'approval' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'financial' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'system' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
    ];
    
    // Check if timeline item has a job order associated
    $hasJobOrder = isset($timeline['job_order']) && $timeline['job_order'];
    $jobOrderId = $hasJobOrder ? $timeline['job_order']->id : null;
    
    $actionColor = $actionColorMap[$timeline['action_color'] ?? 'default'] ?? $actionColorMap['default'];
    $priorityColor = $priorityColors[$timeline['priority']] ?? 'text-gray-600';
    $icon = $typeIcons[$timeline['type']] ?? $typeIcons['system'];
@endphp

<div class="flex gap-4 {{ $actionColor }} border-l-4 p-4 rounded-r-lg hover:shadow-md transition-shadow {{ $hasJobOrder ? 'cursor-pointer hover:bg-opacity-75' : '' }}"
    @if($hasJobOrder)
        onclick="window.dispatchEvent(new CustomEvent('open-audit-modal', { detail: { jobOrderId: {{ (int) $jobOrderId }} } }))"
    @endif>
    <!-- Icon -->
    <div class="flex-shrink-0">
        <div class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-300 shadow">
            {!! $icon !!}
        </div>
    </div>
    
    <!-- Content -->
    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
            <div class="flex-1">
                <h4 class="font-semibold text-gray-900 dark:text-white text-sm {{ $hasJobOrder ? 'hover:underline' : '' }}">
                    {{ $timeline['title'] }}
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $timeline['description'] }}
                </p>
            </div>
            
            <!-- Status Badge -->
            <span class="px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap
                {{ $timeline['status'] === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : '' }}
                {{ $timeline['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' : '' }}
                {{ $timeline['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' : '' }}
                {{ $timeline['status'] === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' : '' }}
                {{ !in_array($timeline['status'], ['completed', 'pending', 'in_progress', 'cancelled']) ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300' : '' }}">
                {{ ucfirst(str_replace('_', ' ', $timeline['status'])) }}
            </span>
        </div>
        
        <!-- Metadata -->
        <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
            <!-- User/Employee Info -->
            @if(isset($timeline['metadata']['user_name']))
                <span class="flex items-center gap-1 font-medium text-gray-700 dark:text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ $timeline['metadata']['user_name'] }}
                </span>
                @if(isset($timeline['metadata']['user_role']))
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $timeline['metadata']['user_role'] }}
                    </span>
                @endif
                @if(isset($timeline['metadata']['user_dept']) && $timeline['metadata']['user_dept'])
                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded">
                        {{ $timeline['metadata']['user_dept'] }}
                    </span>
                @endif
            @elseif(isset($timeline['customer']))
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ $timeline['customer'] }}
                </span>
            @endif
            
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $timeline['date']->diffForHumans() }}
            </span>
            
            @if($timeline['priority'] !== 'normal')
                <span class="flex items-center gap-1 {{ $priorityColor }} font-medium">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ ucfirst($timeline['priority']) }}
                </span>
            @endif
            
            @if(isset($timeline['metadata']['grand_total']))
                <span class="flex items-center gap-1 font-medium text-green-600 dark:text-green-400">
                    ₱{{ number_format($timeline['metadata']['grand_total'], 2) }}
                </span>
            @endif
        </div>
    </div>
</div>
