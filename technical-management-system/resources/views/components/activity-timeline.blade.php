@props([
    'role' => 'marketing',
    'stats' => [],
    'timelineEntries' => collect(),
    'filters' => [],
])

@php
    $subtitle = $role === 'technician'
        ? 'Real-time updates on assigned work and tasks'
        : 'Real-time updates on job orders';

    $emptySubtext = $role === 'technician'
        ? 'Timeline entries will appear here as work is assigned and updated.'
        : 'Timeline entries will appear here as job orders are created.';

    $cards = $role === 'technician'
        ? [
            ['label' => "Today's Tasks", 'value' => $stats['today_tasks'] ?? 0, 'color' => 'blue'],
            ['label' => 'Pending', 'value' => $stats['pending'] ?? 0, 'color' => 'yellow'],
            ['label' => 'In Progress', 'value' => $stats['in_progress'] ?? 0, 'color' => 'blue'],
            ['label' => 'Completed Today', 'value' => $stats['completed_today'] ?? 0, 'color' => 'green'],
        ]
        : [
            ['label' => 'Total Jobs', 'value' => $stats['total_jobs'] ?? 0, 'color' => 'blue'],
            ['label' => 'Pending', 'value' => $stats['pending'] ?? 0, 'color' => 'yellow'],
            ['label' => 'In Progress', 'value' => $stats['in_progress'] ?? 0, 'color' => 'blue'],
            ['label' => 'Completed', 'value' => $stats['completed'] ?? 0, 'color' => 'green'],
        ];

    $colorMap = [
        'blue' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-600 dark:text-blue-400'],
        'yellow' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'text' => 'text-yellow-600 dark:text-yellow-400'],
        'green' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-600 dark:text-green-400'],
        'orange' => ['bg' => 'bg-orange-100 dark:bg-orange-900/30', 'text' => 'text-orange-600 dark:text-orange-400'],
    ];

    $statusDots = [
        'pending' => 'bg-orange-500',
        'in_progress' => 'bg-blue-500',
        'completed' => 'bg-green-500',
    ];

    $statusBadges = [
        'pending' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-200',
        'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200',
        'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-200',
    ];

    $entries = collect($timelineEntries ?? []);
    $currentStatus = $filters['status'] ?? null;
    $currentSearch = $filters['search'] ?? '';
    $filterOptions = [
        ['key' => null, 'label' => 'All'],
        ['key' => 'pending', 'label' => 'Pending'],
        ['key' => 'in_progress', 'label' => 'In Progress'],
        ['key' => 'completed', 'label' => 'Completed'],
    ];

    $buildUrl = function (?string $status) use ($currentSearch) {
        $query = array_filter([
            'status' => $status,
            'search' => $currentSearch,
        ], function ($value) {
            return $value !== null && $value !== '';
        });

        $queryString = http_build_query($query);
        return url()->current() . ($queryString ? '?' . $queryString : '');
    };
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @foreach($cards as $card)
            @php
                $colors = $colorMap[$card['color']] ?? $colorMap['blue'];
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6 min-h-[148px] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $card['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $card['value'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $colors['bg'] }}">
                        <svg class="w-6 h-6 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Activity Timeline</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
            </div>
            <div class="flex items-center gap-2">
                @foreach($filterOptions as $filter)
                    @php
                        $isActive = $currentStatus === $filter['key'] || ($filter['key'] === null && !$currentStatus);
                        $url = $buildUrl($filter['key']);
                    @endphp
                    <a href="{{ $url }}"
                       class="px-3 py-2 text-sm rounded-lg border transition
                        {{ $isActive ? 'border-blue-500 bg-blue-50 text-blue-700 dark:border-blue-400 dark:bg-blue-900/30 dark:text-blue-100' : 'border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        {{ $filter['label'] }}
                    </a>
                @endforeach
                <form method="GET" action="{{ url()->current() }}" class="relative">
                    <input type="hidden" name="status" value="{{ $currentStatus }}" />
                    <input type="text" name="search" value="{{ $currentSearch }}" placeholder="Search activity"
                           class="pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1016.65 16.65z" />
                    </svg>
                </form>
            </div>
        </div>

        <div class="space-y-3 max-h-[420px] overflow-y-auto pr-1">
            @forelse($entries as $entry)
                @php
                    $status = $entry['status'] ?? 'pending';
                    $dotColor = $statusDots[$status] ?? 'bg-gray-400';
                    $badgeClass = $statusBadges[$status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200';
                    $title = $entry['title'] ?? 'Untitled activity';
                    $description = $entry['description'] ?? null;
                    $reference = $entry['reference']
                        ?? ($entry['job_order_number'] ?? null)
                        ?? ($entry['metadata']['reference'] ?? null);
                    if (!$reference && isset($entry['id'])) {
                        $reference = ($role === 'technician' ? 'Task-' : 'JO-') . str_pad($entry['id'], 4, '0', STR_PAD_LEFT);
                    }
                    $customer = $entry['customer'] ?? null;
                    $equipment = $entry['equipment'] ?? ($entry['metadata']['equipment'] ?? null);
                    $assigned = $entry['assigned_to'] ?? ($entry['assigned'] ?? null);
                    $timestamp = $entry['date'] ?? ($entry['timestamp'] ?? null);
                    $timeLabel = $timestamp instanceof \Carbon\Carbon ? $timestamp->diffForHumans() : ($timestamp ?? '');
                    $link = $entry['url'] ?? '#';
                @endphp

                <a href="{{ $link }}" class="flex items-start gap-4 p-4 border border-gray-100 dark:border-gray-700 rounded-2xl hover:shadow-md transition bg-gray-50/60 dark:bg-gray-900/40">
                    <span class="mt-1 w-3 h-3 rounded-full {{ $dotColor }}"></span>
                    <div class="flex-1 min-w-0 space-y-2">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $title }}</p>
                                @if($description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $description }}</p>
                                @endif
                                <div class="flex flex-wrap gap-2 mt-2 text-xs text-gray-600 dark:text-gray-300">
                                    @if($reference)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            Ref: {{ $reference }}
                                        </span>
                                    @endif
                                    @if($customer)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $customer }}
                                        </span>
                                    @endif
                                    @if($equipment)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                            </svg>
                                            {{ $equipment }}
                                        </span>
                                    @endif
                                    @if($assigned)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Assigned: {{ $assigned }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                @if($timeLabel)
                                    <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $timeLabel }}</span>
                                @endif
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $badgeClass }} capitalize">{{ str_replace('_', ' ', $status) }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="flex flex-col items-center justify-center text-center py-12 bg-gray-50/60 dark:bg-gray-900/40 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h4 class="mt-4 text-base font-semibold text-gray-900 dark:text-white">No activity yet</h4>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $emptySubtext }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
