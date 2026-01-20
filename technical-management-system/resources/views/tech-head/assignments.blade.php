@extends('layouts.dashboard')

@section('title', 'Assignments')

@section('page-title', 'Assignments')
@section('page-subtitle', 'All work assignments across technicians')

@section('sidebar-nav')
    @include('tech-head.partials.sidebar')
@endsection

@section('content')
    <div x-data="{ 
        showCreate: false, 
        showReassign: false, 
        showUnassign: false, 
        showSchedule: false, 
        showDetails: false,
        selectedId: null,
        selectedAssignment: null,
        init() {
            this.$watch('showCreate', value => this.handleModalState(value));
            this.$watch('showReassign', value => this.handleModalState(value));
            this.$watch('showUnassign', value => this.handleModalState(value));
            this.$watch('showSchedule', value => this.handleModalState(value));
            this.$watch('showDetails', value => this.handleModalState(value));
        },
        handleModalState(isOpen) {
            if (isOpen) {
                document.body.style.overflow = 'hidden';
                this.setupEscapeKey();
            } else {
                document.body.style.overflow = 'auto';
            }
        },
        setupEscapeKey() {
            const handler = (e) => {
                if (e.key === 'Escape') {
                    this.closeAllModals();
                    document.removeEventListener('keydown', handler);
                }
            };
            document.addEventListener('keydown', handler);
        },
        closeAllModals() {
            this.showCreate = false;
            this.showReassign = false;
            this.showUnassign = false;
            this.showSchedule = false;
            this.showDetails = false;
        },
        openDetails(assignment) {
            this.selectedAssignment = assignment;
            this.showDetails = true;
        }
    }" class="space-y-6">
        <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
            <!-- Search Bar -->
            <form method="GET" action="{{ route('tech-head.assignments') }}" class="flex-1 max-w-2xl">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search ?? '' }}"
                        placeholder="Search by WO number, customer, or technician..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                    <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>
            
            <button 
                @click="showCreate=true" 
                class="px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm hover:shadow-md"
            >
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Assignment
                </span>
            </button>
        </div>
        
        @if($search)
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <span>Search results for: <strong class="text-gray-900 dark:text-white">"{{ $search }}"</strong></span>
                <a href="{{ route('tech-head.assignments') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Clear</a>
            </div>
        @endif
        
        <!-- Filter Buttons -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('tech-head.assignments') }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request('status') && !request('priority') ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    All
                </a>
                
                <span class="text-gray-400 self-center">|</span>
                
                <a href="{{ route('tech-head.assignments', ['status' => 'assigned'] + request()->except('status')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'assigned' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Assigned
                </a>
                
                <a href="{{ route('tech-head.assignments', ['status' => 'in_progress'] + request()->except('status')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'in_progress' ? 'bg-amber-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    In Progress
                </a>
                
                <a href="{{ route('tech-head.assignments', ['status' => 'completed'] + request()->except('status')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'completed' ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Completed
                </a>
                
                <a href="{{ route('tech-head.assignments', ['status' => 'cancelled'] + request()->except('status')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'cancelled' ? 'bg-rose-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Cancelled
                </a>
                
                <span class="text-gray-400 self-center">|</span>
                
                <a href="{{ route('tech-head.assignments', ['priority' => 'urgent'] + request()->except('priority')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('priority') === 'urgent' ? 'bg-red-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Urgent
                </a>
                
                <a href="{{ route('tech-head.assignments', ['priority' => 'high'] + request()->except('priority')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('priority') === 'high' ? 'bg-orange-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    High Priority
                </a>
                
                <a href="{{ route('tech-head.assignments', ['priority' => 'normal'] + request()->except('priority')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('priority') === 'normal' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Normal
                </a>
            </div>
            
            @if(request('status') || request('priority'))
                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Active filters:</span>
                        @if(request('status'))
                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded text-xs font-medium">
                                Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                            </span>
                        @endif
                        @if(request('priority'))
                            <span class="px-2 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200 rounded text-xs font-medium">
                                Priority: {{ ucfirst(request('priority')) }}
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('tech-head.assignments', request()->except(['status', 'priority'])) }}" 
                       class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        Clear all filters
                    </a>
                </div>
            @endif
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Assignment List</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Latest first</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">WO Number</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Customer</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Technician</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Status</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Scheduled Date</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Time</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($assignments as $assignment)
                            <tr 
                                @click="openDetails({{ json_encode([
                                    'id' => $assignment->id,
                                    'wo_number' => $assignment->jobOrder->job_order_number ?? 'N/A',
                                    'customer' => $assignment->jobOrder->customer->name ?? 'N/A',
                                    'technician' => $assignment->assignedTo->name ?? 'Unassigned',
                                    'status' => $assignment->status,
                                    'scheduled_date' => optional($assignment->scheduled_date)->format('M d, Y'),
                                    'scheduled_time' => optional($assignment->scheduled_time)->format('h:i A'),
                                    'priority' => $assignment->priority ?? 'normal',
                                    'notes' => $assignment->notes ?? '',
                                    'created_at' => $assignment->created_at->format('M d, Y h:i A')
                                ]) }})"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                            >
                                <td class="py-3 text-center">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $assignment->jobOrder->job_order_number ?? 'N/A' }}</p>
                                </td>
                                <td class="py-3 text-center">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $assignment->jobOrder->customer->name ?? 'N/A' }}</p>
                                </td>
                                <td class="py-3 text-center">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $assignment->assignedTo->name ?? 'Unassigned' }}</p>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $assignment->status === 'assigned' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' : '' }}
                                        {{ $assignment->status === 'in_progress' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200' : '' }}
                                        {{ $assignment->status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : '' }}
                                        {{ $assignment->status === 'cancelled' ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $assignment->status ?? 'pending')) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ optional($assignment->scheduled_date)->format('M d, Y') ?? '—' }}</p>
                                </td>
                                <td class="py-3 text-center">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ optional($assignment->scheduled_time)->format('h:i A') ?? '—' }}</p>
                                </td>
                                <td class="py-3 text-center" @click.stop>
                                    <div class="flex gap-3 justify-center">
                                        <button @click="selectedId={{ $assignment->id }}; showReassign=true;" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-medium">Reassign</button>
                                        <button @click="selectedId={{ $assignment->id }}; showUnassign=true;" class="text-rose-600 dark:text-rose-400 hover:underline text-xs font-medium">Unassign</button>
                                        <button @click="selectedId={{ $assignment->id }}; showSchedule=true;" class="text-amber-600 dark:text-amber-400 hover:underline text-xs font-medium">Schedule</button>
                                    </div>
                                </td>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $assignments->links() }}
            </div>
        </div>

        <!-- Assignment Details Modal -->
        <div 
            x-show="showDetails" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto" 
            style="display:none;"
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
                    class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Assignment Details</h3>
                            <button @click="showDetails=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Work Order Number</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedAssignment?.wo_number"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Customer</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedAssignment?.customer"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Technician</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedAssignment?.technician"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200" x-text="selectedAssignment?.status"></span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Scheduled Date</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedAssignment?.scheduled_date || '—'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Scheduled Time</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedAssignment?.scheduled_time || '—'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Priority</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white capitalize" x-text="selectedAssignment?.priority"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Created At</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedAssignment?.created_at"></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notes</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selectedAssignment?.notes || 'No notes'"></p>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button @click="showDetails=false; selectedId=selectedAssignment.id; showReassign=true;" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Reassign</button>
                            <button @click="showDetails=false; selectedId=selectedAssignment.id; showSchedule=true;" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700">Schedule</button>
                            <button @click="showDetails=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Assignment Modal -->
        <div 
            x-show="showCreate" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto" 
            style="display:none;"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showCreate=false"></div>
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
                <form method="POST" action="{{ route('tech-head.assignments.store') }}" class="p-6 space-y-4">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Create Assignment</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Job Order ID</label>
                            <input name="job_order_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Technician User ID</label>
                            <input name="assigned_to" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Scheduled Date</label>
                            <input type="date" name="scheduled_date" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Scheduled Time</label>
                            <input type="time" name="scheduled_time" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showCreate=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Create</button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        <!-- Reassign Modal -->
        <div 
            x-show="showReassign" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto" 
            style="display:none;"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showReassign=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                <form method="POST" :action="selectedId ? '{{ url('/tech-head/assignments') }}/' + selectedId + '/reassign' : '#'" class="p-6 space-y-4">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Reassign Technician</h3>
                    <input name="assigned_to" placeholder="Technician User ID" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showReassign=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium">Reassign</button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        <!-- Unassign Modal -->
        <div 
            x-show="showUnassign" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto" 
            style="display:none;"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showUnassign=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                <form method="POST" :action="selectedId ? '{{ url('/tech-head/assignments') }}/' + selectedId + '/unassign' : '#'" class="p-6 space-y-4">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Unassign</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">Remove technician from assignment.</p>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showUnassign=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-medium">Unassign</button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        <!-- Schedule Modal -->
        <div 
            x-show="showSchedule" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto" 
            style="display:none;"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showSchedule=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                <form method="POST" :action="selectedId ? '{{ url('/tech-head/assignments') }}/' + selectedId + '/schedule' : '#'" class="p-6 space-y-4">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Schedule Assignment</h3>
                    <input type="date" name="scheduled_date" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                    <input type="time" name="scheduled_time" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showSchedule=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                        <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium">Save</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
@endsection
