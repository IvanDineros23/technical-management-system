@props([
    'timelines' => collect(),
    'emptyTitle' => 'No activities',
    'emptyText' => 'Timeline entries will appear here.',
])

@php
    $entries = collect($timelines ?? []);

    $extractJobNumber = function ($entry) {
        $jobOrder = data_get($entry, 'job_order');
        $jobOrderNumber = data_get($jobOrder, 'job_order_number');

        if (is_string($jobOrderNumber) && trim($jobOrderNumber) !== '') {
            return strtoupper(trim($jobOrderNumber));
        }

        $title = (string) data_get($entry, 'title', '');
        if (preg_match('/\bJO-[A-Za-z0-9-]+\b/i', $title, $matches)) {
            return strtoupper($matches[0]);
        }

        $description = (string) data_get($entry, 'description', '');
        if (preg_match('/\bJO-[A-Za-z0-9-]+\b/i', $description, $matches)) {
            return strtoupper($matches[0]);
        }

        return null;
    };

    $grouped = $entries
        ->map(function ($entry) use ($extractJobNumber) {
            $jobNumber = $extractJobNumber($entry);
            $groupKey = $jobNumber ? 'jo:' . $jobNumber : 'misc';

            return [
                'group_key' => $groupKey,
                'job_number' => $jobNumber,
                'entry' => $entry,
            ];
        })
        ->groupBy('group_key')
        ->map(function ($items, $groupKey) {
            $jobNumber = $items->first()['job_number'] ?? null;
            $entryItems = $items->pluck('entry')->values();

            $latestDate = $entryItems
                ->map(function ($entry) {
                    return data_get($entry, 'date');
                })
                ->filter()
                ->max();

            $status = (string) data_get($entryItems->first(), 'status', 'pending');

            return [
                'key' => $groupKey,
                'title' => $jobNumber ? $jobNumber : 'Other Activities',
                'job_number' => $jobNumber,
                'latest_date' => $latestDate,
                'status' => $status,
                'entries' => $entryItems,
            ];
        })
        ->sortByDesc('latest_date')
        ->values();

    $groupKeys = $grouped->pluck('key')->values();
    $totalActivities = $entries->count();
@endphp

@if($grouped->isEmpty())
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $emptyTitle }}</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $emptyText }}</p>
    </div>
@else
    <div
        x-data="{
            openGroups: @js($groupKeys),
            allGroupKeys: @js($groupKeys),
            isOpen(key) {
                return this.openGroups.includes(key);
            },
            toggleGroup(key) {
                if (this.isOpen(key)) {
                    this.openGroups = this.openGroups.filter(groupKey => groupKey !== key);
                    return;
                }

                this.openGroups = [...this.openGroups, key];
            },
            expandAll() {
                this.openGroups = [...this.allGroupKeys];
            },
            collapseAll() {
                this.openGroups = [];
            }
        }"
        class="space-y-3"
    >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-1">
            <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">
                {{ $grouped->count() }} {{ Str::plural('job group', $grouped->count()) }}
                | {{ $totalActivities }} {{ Str::plural('activity', $totalActivities) }}
            </p>
            <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden w-full sm:w-auto">
                <button
                    type="button"
                    @click="expandAll"
                    class="flex-1 sm:flex-none px-2 sm:px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    Expand all
                </button>
                <button
                    type="button"
                    @click="collapseAll"
                    class="flex-1 sm:flex-none px-2 sm:px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    Collapse all
                </button>
            </div>
        </div>

        @foreach($grouped as $group)
            @php
                $groupStatus = $group['status'] ?? 'pending';
                $statusClass = match($groupStatus) {
                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
                    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
                    'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                    'cancelled' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
                    default => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-200',
                };
            @endphp

            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
                <button
                    type="button"
                    @click="toggleGroup('{{ $group['key'] }}')"
                    class="w-full flex items-center justify-between px-2 sm:px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                >
                    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <div class="text-left min-w-0">
                            <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ $group['title'] }}
                            </p>
                            <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">
                                {{ $group['entries']->count() }} {{ Str::plural('activity', $group['entries']->count()) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 text-[11px] sm:text-xs font-medium rounded-full {{ $statusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $groupStatus)) }}
                        </span>
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform"
                            :class="isOpen('{{ $group['key'] }}') ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>

                <div x-show="isOpen('{{ $group['key'] }}')"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="border-t border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/30">
                    <div class="space-y-2 p-3">
                        @foreach($group['entries'] as $entry)
                            @php
                                $entryStatus = (string) data_get($entry, 'status', 'pending');
                                $entryStatusClass = match($entryStatus) {
                                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
                                    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
                                    'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                                    'cancelled' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
                                    default => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-200',
                                };
                                $metaName = data_get($entry, 'metadata.user_name');
                                $metaRole = data_get($entry, 'metadata.user_role');
                                $isSystemGenerated = (bool) data_get($entry, 'metadata.is_system_generated', false);
                                $metaAction = data_get($entry, 'metadata.action');
                                $metaModelType = data_get($entry, 'metadata.model_type');
                                $entryDate = data_get($entry, 'date');
                                if ($entryDate instanceof \Carbon\Carbon) {
                                    $dateLabel = $entryDate->copy()->setTimezone('Asia/Manila')->format('M d, Y h:i A');
                                } elseif (is_string($entryDate) && trim($entryDate) !== '') {
                                    try {
                                        $dateLabel = \Carbon\Carbon::parse($entryDate)->setTimezone('Asia/Manila')->format('M d, Y h:i A');
                                    } catch (\Throwable $e) {
                                        $dateLabel = $entryDate;
                                    }
                                } else {
                                    $dateLabel = '';
                                }
                            @endphp

                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 sm:p-3">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ data_get($entry, 'title', 'Activity') }}
                                        </p>
                                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ data_get($entry, 'description', '') }}
                                        </p>
                                    </div>
                                    <span class="mt-2 sm:mt-0 px-2 py-1 text-[11px] sm:text-xs font-medium rounded-full whitespace-nowrap {{ $entryStatusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $entryStatus)) }}
                                    </span>
                                </div>

                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs sm:text-xs text-gray-500 dark:text-gray-400">
                                    @if($metaName)
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7"/>
                                            </svg>
                                            {{ $isSystemGenerated ? 'System' : $metaName }}
                                        </span>
                                    @endif
                                    @if($metaRole)
                                        <span>{{ $isSystemGenerated ? 'Automated' : $metaRole }}</span>
                                    @endif
                                    @if($metaModelType)
                                        <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                            {{ $metaModelType }}
                                        </span>
                                    @endif
                                    @if($metaAction)
                                        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200 text-[10px]">
                                            {{ strtoupper($metaAction) }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"/>
                                        </svg>
                                        {{ $dateLabel }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
