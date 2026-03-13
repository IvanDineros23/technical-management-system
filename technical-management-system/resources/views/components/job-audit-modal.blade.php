<!-- Job Audit Trail Modal -->
<div x-data="globalJobAuditTrailModal()" x-show="isOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
    @open-audit-modal.window="handleOpenModal($event)"
    @keydown.escape.window="closeModal()">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 transition-opacity" @click="closeModal()"></div>
    
    <!-- Modal Container -->
    <div class="flex items-start justify-center min-h-screen pt-4 px-4 sm:pt-6 sm:px-0">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
            @click.stop>
            
            <!-- Modal Header -->
            <div class="sticky top-0 bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 flex items-center justify-between border-b border-blue-600">
                <div>
                    <h2 class="text-2xl font-bold">Complete Job Journey</h2>
                    <p class="text-blue-100 text-sm mt-1" x-text="'Job Order ' + (jobOrder && jobOrder.job_order_number ? jobOrder.job_order_number : '')"></p>
                </div>
                <button @click="closeModal()" class="text-white hover:text-blue-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 space-y-6">
                <!-- Loading State -->
                <div x-show="loading" class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                </div>

                <!-- Job Header Info -->
                <template x-if="!loading && jobOrder">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Job Number -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Job Order Number</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="jobOrder.job_order_number"></p>
                        </div>
                        
                        <!-- Customer -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Customer</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white" x-text="stats.customer || 'N/A'"></p>
                        </div>
                        
                        <!-- Status -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Current Status</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-block w-3 h-3 rounded-full"
                                    :class="statusColor()"></span>
                                <span class="font-semibold capitalize text-gray-900 dark:text-white" x-text="((jobOrder && jobOrder.status ? jobOrder.status : '')).replace(/_/g, ' ')"></span>
                            </div>
                        </div>
                        
                        <!-- Total Activities -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Total Activities</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="activities.length"></p>
                        </div>
                    </div>
                </template>

                <!-- Department Legend -->
                <template x-if="!loading && activities.length > 0">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center gap-2 text-xs">
                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                            <span class="text-gray-600 dark:text-gray-400">Marketing</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <span class="text-gray-600 dark:text-gray-400">Technician</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                            <span class="text-gray-600 dark:text-gray-400">Signatory</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span class="text-gray-600 dark:text-gray-400">Accounting</span>
                        </div>
                    </div>
                </template>

                <!-- Timeline Activities -->
                <template x-if="!loading && activities.length > 0">
                    <div class="space-y-3">
                        <template x-for="activity in activities" :key="activity.id">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm" x-text="activity.title"></h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1" x-text="activity.description"></p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium rounded whitespace-nowrap"
                                        :class="getStatusClass(activity.status)"
                                        x-text="(activity.status || '').replace(/_/g, ' ')">
                                    </span>
                                </div>

                                <!-- Metadata -->
                                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                    <!-- Department Badge -->
                                    <template x-if="activity.metadata && activity.metadata.user_dept">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded"
                                            :class="getDeptClass(activity.metadata.user_dept)">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v4h8v-4zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>
                                            <span x-text="activity.metadata.user_dept"></span>
                                        </span>
                                    </template>

                                    <!-- User Name -->
                                    <template x-if="activity.metadata && activity.metadata.user_name">
                                        <span class="inline-flex items-center gap-1 text-gray-700 dark:text-gray-300 font-medium">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span x-text="activity.metadata.user_name"></span>
                                        </span>
                                    </template>

                                    <!-- Timestamp -->
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span x-text="formatDate(activity.date)"></span>
                                    </span>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Empty State -->
                <template x-if="!loading && activities.length === 0">
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No Activities Found</h3>
                        <p class="text-gray-600 dark:text-gray-400">This job order doesn't have any recorded activities yet.</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function globalJobAuditTrailModal() {
    return {
        isOpen: false,
        loading: false,
        jobOrder: null,
        activities: [],
        stats: {},

        handleOpenModal(event) {
            const jobOrderId = event.detail.jobOrderId;
            this.openModal(jobOrderId);
        },

        openModal(jobOrderId) {
            this.isOpen = true;
            this.loading = true;
            this.activities = [];
            
            // Fetch audit trail data
            fetch(`/api/job-orders/${jobOrderId}/audit-trail`)
                .then(response => response.json())
                .then(data => {
                    this.jobOrder = data.jobOrder;
                    this.activities = data.activities;
                    this.stats = data.stats;
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Error fetching audit trail:', error);
                    this.loading = false;
                    alert('Error loading audit trail. Please try again.');
                });
        },

        closeModal() {
            this.isOpen = false;
            this.jobOrder = null;
            this.activities = [];
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        statusColor() {
            if (!this.jobOrder) return 'bg-gray-300';
            const colors = {
                'completed': 'bg-green-300',
                'in_progress': 'bg-blue-300',
                'pending': 'bg-yellow-300',
                'cancelled': 'bg-red-300'
            };
            return colors[this.jobOrder.status] || 'bg-gray-300';
        },

        getStatusClass(status) {
            const classes = {
                'completed': 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
                'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300'
            };
            return classes[status] || 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300';
        },

        getDeptClass(dept) {
            const classes = {
                'Marketing': 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                'Technician': 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                'Signatory': 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
                'Accounting': 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300'
            };
            return classes[dept] || 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
        }
    }
}
</script>
