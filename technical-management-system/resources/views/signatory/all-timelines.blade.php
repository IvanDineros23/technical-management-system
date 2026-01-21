@extends('layouts.dashboard')
@section('title', 'Timeline')
@section('page-title', 'Job Order Timelines')
@section('page-subtitle', 'View the progress and history of all job orders')

@section('sidebar-nav')
    @include('signatory.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Job Order # or Customer..."
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" id="status"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Job Orders List -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Job Orders</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $jobOrders->total() }} job orders found</p>
        </div>

        @if($jobOrders->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-600 dark:text-gray-400 text-lg">No job orders found</p>
                <p class="text-gray-500 dark:text-gray-500 text-sm mt-2">Try adjusting your filters</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Job Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Calibrations</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($jobOrders as $jobOrder)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $jobOrder->job_order_number }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $jobOrder->service_type }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $jobOrder->customer->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200',
                                            'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200',
                                            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200',
                                            'on_hold' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-200',
                                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200',
                                        ];
                                        $statusColor = $statusColors[$jobOrder->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/50 dark:text-gray-200';
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $jobOrder->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $jobOrder->calibrations->count() }} items</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $jobOrder->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $jobOrder->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button onclick="openTimelineModal({{ $jobOrder->id }}, '{{ $jobOrder->job_order_number }}')"
                                       class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View Timeline
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($jobOrders->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $jobOrders->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Timeline Modal -->
<div id="timelineModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Job Order Timeline</h2>
                <p id="modalJobOrderNumber" class="text-sm text-gray-600 dark:text-gray-400 mt-1"></p>
            </div>
            <button onclick="closeTimelineModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div id="timelineContent" class="px-6 py-6 overflow-y-auto max-h-[calc(90vh-100px)]">
            <!-- Loading spinner -->
            <div id="timelineLoading" class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>

            <!-- Timeline content will be inserted here -->
            <div id="timelineData" class="hidden"></div>
        </div>
    </div>
</div>

<script>
function openTimelineModal(jobOrderId, jobOrderNumber) {
    const modal = document.getElementById('timelineModal');
    const modalJobNumber = document.getElementById('modalJobOrderNumber');
    const loading = document.getElementById('timelineLoading');
    const timelineData = document.getElementById('timelineData');
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Set job order number
    modalJobNumber.textContent = `Job Order: ${jobOrderNumber}`;
    
    // Show loading, hide data
    loading.classList.remove('hidden');
    timelineData.classList.add('hidden');
    timelineData.innerHTML = '';
    
    // Fetch timeline data
    fetch(`/signatory/timeline/${jobOrderId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        loading.classList.add('hidden');
        timelineData.classList.remove('hidden');
        
        if (data.timeline && data.timeline.length > 0) {
            let html = '<div class="space-y-4">';
            
            data.timeline.forEach((event, index) => {
                const isLast = index === data.timeline.length - 1;
                const date = new Date(event.created_at).toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                html += `
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                            ${!isLast ? '<div class="w-0.5 h-full bg-gray-300 dark:bg-gray-600 mt-1"></div>' : ''}
                        </div>
                        <div class="flex-1 pb-6">
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">${event.event}</h4>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">${date}</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">${event.description || ''}</p>
                                ${event.created_by ? `<p class="text-xs text-gray-500 dark:text-gray-500 mt-2">By: ${event.created_by.name}</p>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            timelineData.innerHTML = html;
        } else {
            timelineData.innerHTML = `
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-600 dark:text-gray-400">No timeline events found</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error fetching timeline:', error);
        loading.classList.add('hidden');
        timelineData.classList.remove('hidden');
        timelineData.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-600 dark:text-red-400">Error loading timeline</p>
            </div>
        `;
    });
}

function closeTimelineModal() {
    const modal = document.getElementById('timelineModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTimelineModal();
    }
});

// Close modal when clicking outside
document.getElementById('timelineModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTimelineModal();
    }
});
</script>
@endsection
