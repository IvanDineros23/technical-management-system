@extends('layouts.dashboard')

@section('title', 'Job Details')

@section('page-title', 'Job Order Details')
@section('page-subtitle', 'View and manage job order information')

@section('head')
    <script>
        function jobDetailsPage() {
            return {
                checklist: [
                    { id: 1, task: 'Inspect equipment', completed: true },
                    { id: 2, task: 'Replace faulty parts', completed: false },
                    { id: 3, task: 'Test functionality', completed: false },
                    { id: 4, task: 'Clean and document', completed: false }
                ],
                timeStarted: null,
                timeFinished: null,
                remarks: '',
                startTimer() {
                    this.timeStarted = new Date().toLocaleString();
                    alert('Timer started!');
                },
                stopTimer() {
                    this.timeFinished = new Date().toLocaleString();
                    alert('Timer stopped!');
                },
                toggleTask(taskId) {
                    const task = this.checklist.find(t => t.id === taskId);
                    if (task) task.completed = !task.completed;
                },
                submitReport() {
                    window.location.href = '/technician/reports?job_id={{ $job->id }}';
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
                                {{ ucfirst(str_replace('_', ' ', $job->status)) }}
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
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $job->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Description</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $job->description ?? 'No description provided' }}</p>
                    </div>
                </div>

                <!-- Task Checklist -->
                <div class="bg-white dark:bg-gray-800 rounded-[16px] shadow-md border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Task Checklist</h3>
                    <div class="space-y-3">
                        <template x-for="task in checklist" :key="task.id">
                            <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors">
                                <input type="checkbox" :checked="task.completed" @change="toggleTask(task.id)"
                                       class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span :class="task.completed ? 'line-through text-gray-400' : 'text-gray-900 dark:text-white'" class="text-sm" x-text="task.task"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-white dark:bg-gray-800 rounded-[16px] shadow-md border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Notes</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Work Notes</label>
                            <textarea x-model="remarks" placeholder="Record any observations, issues encountered, or special notes about the work performed..."
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"
                                rows="4"></textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Document findings, issues, or special observations</p>
                        </div>
                    </div>
                </div>

                <!-- Calibration Data Entry -->
                <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Calibration Data Entry</h3>
                    
                    <form action="{{ route('technician.calibration.store-points', optional($job->assignments->first()?->calibrations()->latest()->first())->id ?? 0) }}" 
                          method="POST" 
                          class="space-y-6">
                        @csrf

                        <!-- Calibration Details -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Calibration Date *</label>
                                    <input type="date" name="calibration_date" required 
                                        value="{{ old('calibration_date', now()->format('Y-m-d')) }}"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Location</label>
                                    <input type="text" name="location" placeholder="e.g., Lab A, Field, Customer Site"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Start Time</label>
                                    <input type="time" name="start_time"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">End Time</label>
                                    <input type="time" name="end_time"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Procedure Reference</label>
                                    <input type="text" name="procedure_reference" placeholder="e.g., ISO/IEC 17025:2017"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Measurement Points -->
                        <div x-data="{ points: [{ id: 1 }] }" class="space-y-4">
                            <div class="flex justify-between items-center">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Measurement Points *</label>
                                <button type="button" 
                                        @click="points.push({ id: points.length + 1 })"
                                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">
                                    + Add Point
                                </button>
                            </div>

                            <template x-for="(point, index) in points" :key="point.id">
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Point #</label>
                                            <input type="number" 
                                                   :name="`measurement_points[${index}][point_number]`"
                                                   placeholder="1"
                                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Reference Value</label>
                                            <input type="number" step="0.0001" 
                                                   :name="`measurement_points[${index}][reference_value]`"
                                                   placeholder="0.0000"
                                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">UUT Reading</label>
                                            <input type="number" step="0.0001" 
                                                   :name="`measurement_points[${index}][uut_reading]`"
                                                   placeholder="0.0000"
                                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Uncertainty</label>
                                            <input type="number" step="0.0001" 
                                                   :name="`measurement_points[${index}][uncertainty]`"
                                                   placeholder="0.0000"
                                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                                        </div>
                                        <div class="flex items-end">
                                            <button type="button" 
                                                    @click="points.splice(index, 1)"
                                                    x-show="points.length > 1"
                                                    class="w-full px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Acceptance Criteria</label>
                                        <input type="text" 
                                               :name="`measurement_points[${index}][acceptance_criteria]`"
                                               placeholder="e.g., ±0.5%"
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" 
                                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                                Save & Submit for Review
                            </button>
                            <button type="reset" 
                                    class="px-6 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-semibold rounded-lg transition">
                                Reset
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Attachments -->
                <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Attachments</h3>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Upload photos or documents</p>
                        <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                            Choose Files
                        </button>
                    </div>
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
                        @elseif($job->status === 'in_progress')
                        <button @click="submitReport()" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                            Complete & Submit Report
                        </button>
                        <button class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition-colors">
                            Pause Work
                        </button>
                        @endif
                        <a href="{{ route('technician.inventory') }}" class="block w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-center">
                            Request Materials
                        </a>
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

                <!-- Equipment Info -->
                <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Equipment</h3>
                    <div class="space-y-2">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Generator Unit</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SN: GEN-2024-001</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
