@extends('layouts.dashboard')

@section('title', 'Job Details')

@section('page-title', 'Job Order Details')
@section('page-subtitle', 'View and manage job order information')

@section('head')
    <script>
        function jobDetailsPage() {
            return {
                checklist: @json($checklistItems),
                newTaskText: '',
                checklistCreateUrl: '{{ route('technician.job.checklist.store', $job->id) }}',
                checklistUpdateUrl: '{{ route('technician.checklist.update', ['item' => '__ITEM__']) }}',
                checklistDeleteUrl: '{{ route('technician.checklist.delete', ['item' => '__ITEM__']) }}',
                crewMembers: @json($crewMembers),
                availableTechnicians: @json($technicians),
                crewCreateUrl: '{{ route('technician.job.crew-members.store', $job->id) }}',
                crewDeleteUrl: '{{ route('technician.crew-members.delete', ['member' => '__MEMBER__']) }}',
                currentUserId: {{ auth()->id() }},
                newCrewName: '',
                showRemoveModal: false,
                taskToRemove: null,
                timeStarted: null,
                timeFinished: null,
                remarks: '',
                async addTask() {
                    const text = this.newTaskText.trim();
                    if (!text) return;
                    const response = await fetch(this.checklistCreateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ description: text })
                    });

                    if (!response.ok) {
                        alert('Unable to add checklist item.');
                        return;
                    }

                    const data = await response.json();
                    this.checklist.push(data.item);
                    this.newTaskText = '';
                },
                confirmRemove(task) {
                    this.taskToRemove = task;
                    this.showRemoveModal = true;
                },
                async removeTask() {
                    if (!this.taskToRemove) return;
                    const task = this.taskToRemove;
                    const url = this.checklistDeleteUrl.replace('__ITEM__', task.id);
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (!response.ok) {
                        alert('Unable to remove checklist item.');
                        return;
                    }

                    this.checklist = this.checklist.filter(item => item.id !== task.id);
                    this.showRemoveModal = false;
                    this.taskToRemove = null;
                },
                closeRemoveModal() {
                    this.showRemoveModal = false;
                    this.taskToRemove = null;
                },
                crewMemberByUser(userId) {
                    return this.crewMembers.find(member => member.user_id === userId) || null;
                },
                isCrewSelected(userId) {
                    return !!this.crewMemberByUser(userId);
                },
                async toggleCrew(technician) {
                    const existing = this.crewMemberByUser(technician.id);
                    if (existing) {
                        await this.removeCrew(existing);
                        return;
                    }

                    const response = await fetch(this.crewCreateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ user_id: technician.id })
                    });

                    if (!response.ok) {
                        alert('Unable to add crew member.');
                        return;
                    }

                    const data = await response.json();
                    this.crewMembers.push(data.item);
                },
                async addCrewName() {
                    const name = this.newCrewName.trim();
                    if (!name) return;

                    const response = await fetch(this.crewCreateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ name })
                    });

                    if (!response.ok) {
                        alert('Unable to add crew member.');
                        return;
                    }

                    const data = await response.json();
                    this.crewMembers.push(data.item);
                    this.newCrewName = '';
                },
                async removeCrew(member) {
                    // Prevent assigned technician from removing themselves
                    if (member.user_id === this.currentUserId) {
                        alert('You cannot remove yourself. You are assigned to this job.');
                        return;
                    }
                    
                    const url = this.crewDeleteUrl.replace('__MEMBER__', member.id);
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (!response.ok) {
                        alert('Unable to remove crew member.');
                        return;
                    }

                    this.crewMembers = this.crewMembers.filter(item => item.id !== member.id);
                },
                startTimer() {
                    this.timeStarted = new Date().toLocaleString();
                    alert('Timer started!');
                },
                stopTimer() {
                    this.timeFinished = new Date().toLocaleString();
                    alert('Timer stopped!');
                },
                async toggleTask(task) {
                    const original = task.is_completed;
                    task.is_completed = !original;
                    const url = this.checklistUpdateUrl.replace('__ITEM__', task.id);

                    const response = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ is_completed: task.is_completed })
                    });

                    if (!response.ok) {
                        task.is_completed = original;
                        alert('Unable to update checklist item.');
                        return;
                    }

                    const data = await response.json();
                    task.is_completed = data.item.is_completed;
                    task.completed_at = data.item.completed_at;
                    task.completed_by = data.item.completed_by;
                }
            }
        }
    </script>
@endsection

@section('sidebar-nav')
    <a href="{{ route('technician.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('technician.assignments') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        My Assignments
    </a>

    <a href="{{ route('technician.work-orders') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Work Orders
    </a>

    <a href="{{ route('technician.maintenance') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Maintenance Tasks
    </a>

    <a href="{{ route('technician.equipment') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
        </svg>
        Equipment
    </a>

    <a href="{{ route('technician.inventory') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        Inventory
    </a>

    <a href="{{ route('technician.reports') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Reports
    </a>

    <a href="{{ route('technician.certificates') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
        </svg>
        Certificates
    </a>

    <a href="{{ route('technician.calendar') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Calendar
    </a>

    <a href="{{ route('technician.timeline') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Timeline
    </a>
@endsection

@section('content')
    <div x-data="jobDetailsPage()">
        <div class="mb-6">
            <a href="{{ route('technician.assignments') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm mb-2 inline-block">
                ← Back to Assignments
            </a>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $job->job_order_number }}</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Job Order Details & Tracking</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Job Information -->
                <div class="bg-white dark:bg-gray-800 rounded-[16px] shadow-md border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Job Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Customer</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $job->customer->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $job->status === 'assigned' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $job->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $job->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ $job->status === 'pending' ? 'Waiting for Assignment' : ucfirst(str_replace('_', ' ', $job->status)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Priority</p>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                {{ ucfirst($job->priority ?? 'Normal') }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Date Created</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $job->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Description</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $job->service_description ?? 'No description provided' }}</p>
                    </div>
                </div>

                <!-- Task Checklist -->
                <div class="bg-white dark:bg-gray-800 rounded-[16px] shadow-md border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Task Checklist</h3>
                    <div class="flex flex-col sm:flex-row gap-3 mb-4">
                        <input type="text" x-model="newTaskText" @keydown.enter.prevent="addTask()"
                               placeholder="Add a checklist item..."
                               class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <button type="button" @click="addTask()"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                            Add Item
                        </button>
                    </div>
                    <div class="space-y-3" x-show="checklist.length > 0">
                        <template x-for="task in checklist" :key="task.id">
                            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <input type="checkbox" :checked="task.is_completed" @change="toggleTask(task)"
                                       class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span :class="task.is_completed ? 'line-through text-gray-400' : 'text-gray-900 dark:text-white'" class="text-sm" x-text="task.description"></span>
                                <button type="button" @click="confirmRemove(task)"
                                        class="ml-auto text-xs text-red-600 hover:text-red-700">Remove</button>
                            </div>
                        </template>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-show="checklist.length === 0">No checklist items yet.</p>
                </div>

                <!-- Remove Checklist Item Modal -->
                <div 
                    x-show="showRemoveModal" 
                    x-cloak
                    @keydown.escape.window="closeRemoveModal()"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 overflow-y-auto"
                >
                    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeRemoveModal()"></div>
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
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Remove Checklist Item</h3>
                                    <button @click="closeRemoveModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Are you sure you want to remove this item?</p>
                                    <div class="p-3 bg-gray-50 dark:bg-gray-700/40 rounded-lg">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="taskToRemove?.description"></p>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <button type="button" @click="closeRemoveModal()"
                                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                        Cancel
                                    </button>
                                    <button type="button" @click="removeTask()"
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes & Report -->
                <div class="bg-white dark:bg-gray-800 rounded-[16px] shadow-md border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Notes & Report</h3>
                        @if($assignment?->report)
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                {{ $assignment->report->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $assignment->report->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $assignment->report->status === 'rejected' ? 'bg-rose-100 text-rose-700' : '' }}">
                                {{ ucfirst($assignment->report->status) }}
                            </span>
                        @endif
                    </div>
                    <form id="jobReportForm" action="{{ route('technician.job.submit-report', $job->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Work Summary *</label>
                            <textarea name="work_summary" required placeholder="Describe the work completed and key findings..."
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"
                                rows="4">{{ old('work_summary', $assignment?->report?->work_summary) }}</textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This will be reviewed by the tech head.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Parts Used (Optional)</label>
                            <textarea name="parts_used" placeholder="List any parts used..."
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"
                                rows="3">{{ old('parts_used', $assignment?->report?->parts_used) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Additional Remarks (Optional)</label>
                            <textarea name="remarks" placeholder="Any extra notes or observations..."
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"
                                rows="3">{{ old('remarks', $assignment?->report?->remarks) }}</textarea>
                        </div>
                    </form>
                </div>

                <!-- Attachments -->
                <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Attachments</h3>
                    
                    <!-- Upload Form -->
                    <form action="{{ route('technician.job.attachments.upload', $job->id) }}" method="POST" enctype="multipart/form-data" class="mb-6">
                        @csrf
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Upload photos or Excel raw data (max 10MB per file)</p>
                            <input type="file" name="files[]" multiple accept="image/*,.xls,.xlsx" 
                                   class="hidden" id="fileInput" onchange="this.form.querySelector('.file-name').textContent = Array.from(this.files).map(f => f.name).join(', ')">
                            <button type="button" onclick="document.getElementById('fileInput').click()"
                                    class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                                Choose Files
                            </button>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 file-name"></p>
                            <button type="submit" class="mt-3 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                                Upload Files
                            </button>
                        </div>
                    </form>

                    <!-- Existing Attachments -->
                    @if($job->attachments && $job->attachments->count() > 0)
                    <div class="space-y-2">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Uploaded Files</h4>
                        @foreach($job->attachments as $attachment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $attachment->file_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($attachment->file_size / 1024, 2) }} KB • {{ $attachment->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" 
                               class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                                Download
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No attachments uploaded yet.</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        @if($job->status === 'assigned')
                        <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            Start Work
                        </button>
                        @endif

                        @if($assignment)
                            @if($assignment->report && $assignment->report->status === 'approved')
                                <button type="button" disabled class="w-full px-4 py-2 bg-green-200 text-green-900 rounded-lg font-medium cursor-not-allowed">
                                    Report Approved
                                </button>
                            @else
                                <button type="submit" form="jobReportForm" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                                    Submit for Review
                                </button>
                            @endif
                        @else
                            <div class="text-xs text-gray-500 dark:text-gray-400 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                No active assignment found for this job.
                            </div>
                        @endif

                        <a href="{{ route('technician.inventory') }}" class="block w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-center">
                            Request Materials
                        </a>
                    </div>
                </div>

                <!-- Field Team -->
                <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Field Team</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Select technicians or add manual names for credit.</p>

                    <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                        <template x-for="tech in availableTechnicians" :key="tech.id">
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="checkbox"
                                       class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       :checked="isCrewSelected(tech.id)"
                                       @change="toggleCrew(tech)">
                                <span x-text="tech.name"></span>
                            </label>
                        </template>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-show="availableTechnicians.length === 0">No technicians found.</p>
                    </div>

                    <div class="pt-3 mt-3 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Add manual name</p>
                        <div class="flex gap-2">
                            <input type="text" x-model="newCrewName" @keydown.enter.prevent="addCrewName()"
                                   placeholder="Enter name..."
                                   class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <button type="button" @click="addCrewName()"
                                    class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                                Add
                            </button>
                        </div>
                    </div>

                    <div class="pt-3 mt-3 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Selected team</p>
                        <div class="space-y-2" x-show="crewMembers.length > 0">
                            <template x-for="member in crewMembers" :key="member.id">
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700/40 rounded-lg">
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        <span x-text="member.name"></span>
                                        <span x-show="member.user_id === currentUserId" class="text-xs text-blue-600 dark:text-blue-400 ml-1">(You)</span>
                                    </p>
                                    <button type="button" @click="removeCrew(member)"
                                            x-show="member.user_id !== currentUserId"
                                            class="text-xs text-red-600 hover:text-red-700">Remove</button>
                                </div>
                            </template>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-show="crewMembers.length === 0">No crew selected yet.</p>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Instructions</h3>
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                        <p>• Follow standard safety procedures</p>
                        <p>• Document all findings</p>
                        <p>• Take photos of work completed</p>
                        <p>• Report any issues immediately</p>
                    </div>
                </div>

                <!-- Equipment -->
                <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Equipment</h3>
                    @if($job->items->count() > 0)
                        <div class="space-y-3">
                            @foreach($job->items as $item)
                                <div class="p-3 bg-gray-50 dark:bg-gray-700/40 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $item->equipment_type }}
                                        </p>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">#{{ $item->item_number }}</span>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
                                        @if($item->manufacturer || $item->model)
                                            <p>{{ trim(($item->manufacturer ?? '') . ' ' . ($item->model ?? '')) }}</p>
                                        @endif
                                        @if($item->serial_number)
                                            <p>SN: {{ $item->serial_number }}</p>
                                        @endif
                                        @if($item->id_number)
                                            <p>ID: {{ $item->id_number }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No equipment listed for this job.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
