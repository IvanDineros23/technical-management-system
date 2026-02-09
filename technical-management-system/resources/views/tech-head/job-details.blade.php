@extends('layouts.dashboard')

@section('title', 'Job Details')

@section('page-title', 'Job Order Details')
@section('page-subtitle', 'View job order information and progress')

@section('sidebar-nav')
    @include('tech-head.partials.sidebar')
@endsection

@section('content')
    <div x-data="{ showEdit: false }" x-cloak>
        <div class="mb-6">
            <a href="{{ route('tech-head.work-orders') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm mb-2 inline-block">
                ← Back to Work Orders
            </a>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $job->job_order_number }}</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Job Order Details & Tracking</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Job Information -->
                <div class="bg-white dark:bg-gray-800 rounded-[16px] shadow-md border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Job Information</h3>
                        <button @click="showEdit = true" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                            Edit
                        </button>
                    </div>
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
                                {{ $job->status === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
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
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $job->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Description</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $job->service_description ?? 'No description provided' }}</p>
                    </div>
                </div>

                <!-- Task Checklist (Read-only) -->
                <div class="bg-white dark:bg-gray-800 rounded-[16px] shadow-md border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Task Checklist</h3>
                    @if($checklistItems && count($checklistItems) > 0)
                        <div class="space-y-2">
                            @foreach($checklistItems as $item)
                                <div class="flex items-center gap-3 p-3 rounded-lg {{ $item['is_completed'] ? 'bg-green-50 dark:bg-green-900/20' : 'bg-gray-50 dark:bg-gray-700/40' }}">
                                    <input type="checkbox" {{ $item['is_completed'] ? 'checked' : '' }} disabled
                                           class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-not-allowed">
                                    <span class="{{ $item['is_completed'] ? 'line-through text-gray-400' : 'text-gray-900 dark:text-white' }} text-sm">
                                        {{ $item['description'] }}
                                    </span>
                                    @if($item['is_completed'])
                                        <span class="ml-auto text-xs text-gray-500 dark:text-gray-400">
                                            ✓ {{ $item['completed_by'] ?? 'Completed' }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No checklist items yet.</p>
                    @endif
                </div>

                <!-- Attachments -->
                <div class="bg-white dark:bg-gray-800 rounded-[16px] shadow-md border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Attachments</h3>
                    @if($job->attachments->count() > 0)
                    <div class="space-y-2">
                        @foreach($job->attachments as $attachment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/40 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
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
                <!-- Assignment Info -->
                @if($assignment)
                <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Assignment</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Assigned To</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $assignment->assignedTo->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Field Team -->
                <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Field Team</h3>
                    @if(count($crewMembers) > 0)
                        <div class="space-y-2">
                            @foreach($crewMembers as $member)
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700/40 rounded-lg">
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $member['name'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No crew members assigned yet.</p>
                    @endif
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

        <!-- Edit Job Order Modal -->
        <div 
            x-show="showEdit" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto" 
            @keydown.escape.window="showEdit = false"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showEdit = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto"
                >
                    <div class="sticky top-0 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between z-10">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Job Order</h3>
                        <button @click="showEdit = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('tech-head.work-orders.update', $job->id) }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Customer</label>
                                <select name="customer_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $job->customer_id == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Priority</label>
                                <select name="priority" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="low" {{ $job->priority == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="normal" {{ $job->priority == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ $job->priority == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ $job->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Status</label>
                                <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="pending" {{ $job->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="assigned" {{ $job->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                    <option value="in_progress" {{ $job->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $job->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $job->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Required Date</label>
                                <input type="date" name="required_date" value="{{ $job->required_date ? $job->required_date->format('Y-m-d') : '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Service Type</label>
                            <input type="text" name="service_type" value="{{ $job->service_type }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Service Description</label>
                            <textarea name="service_description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $job->service_description }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Service Address</label>
                            <input type="text" name="service_address" value="{{ $job->service_address }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">City</label>
                                <input type="text" name="city" value="{{ $job->city }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Province</label>
                                <input type="text" name="province" value="{{ $job->province }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Postal Code</label>
                                <input type="text" name="postal_code" value="{{ $job->postal_code }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Notes</label>
                            <textarea name="notes" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $job->notes }}</textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" @click="showEdit = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
