@extends('layouts.dashboard')

@section('title', 'Create New Job Order')

@section('head')
    <script>
        function jobOrderForm() {
            return {
                isSubmitting: false,
                customers: @json($customers),
                selectedCustomer: null,
                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },
                selectCustomer() {
                    const customerId = document.querySelector('select[name="customer_id"]').value;
                    if (customerId) {
                        this.selectedCustomer = this.customers.find(c => c.id == customerId);
                    } else {
                        this.selectedCustomer = null;
                    }
                },
                showToast(message, type = 'success') {
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.show = true;
                    
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 3000);
                },
                submitForm(event) {
                    event.preventDefault();
                    this.isSubmitting = true;
                    
                    const formData = new FormData(event.target);
                    const data = Object.fromEntries(formData.entries());
                    
                    // Validate customer selection
                    if (!data.customer_id) {
                        this.showToast('Please select a customer', 'error');
                        this.isSubmitting = false;
                        return;
                    }
                    
                    fetch('{{ route('marketing.job-orders.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.showToast('Job Order created successfully!', 'success');
                            setTimeout(() => {
                                window.location.href = '{{ route('marketing.job-orders') }}';
                            }, 1500);
                        } else {
                            this.showToast(data.message || 'Error creating job order', 'error');
                            this.isSubmitting = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.showToast('Network error. Please try again.', 'error');
                        this.isSubmitting = false;
                    });
                }
            }
        }
    </script>
@endsection

@section('sidebar-nav')
    <a href="{{ route('marketing.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('marketing.create-job-order') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4v16m8-8H4"/>
        </svg>
        Create New JO
    </a>

    <a href="{{ route('marketing.customers') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Customers
    </a>

    <a href="{{ route('marketing.job-orders') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Job Orders
    </a>

    <a href="{{ route('marketing.reports') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Reports
    </a>
      <a href="{{ route('marketing.timeline') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Timeline
    </a>
@endsection

@section('content')
    <div x-data="jobOrderForm()">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Job Order</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Fill in the details to create a new job order</p>
        </div>

        <form @submit.prevent="submitForm($event)">
        <!-- Customer Selection -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Customer Selection</h3>
                <a href="{{ route('marketing.customers') }}" 
                   class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Customer
                </a>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Customer *</label>
                    <select name="customer_id" 
                            required 
                            @change="selectCustomer()"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Choose a customer...</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Customer Details Display -->
                <div x-show="selectedCustomer" 
                     x-transition
                     class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Customer Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Email:</span>
                            <span class="ml-2 text-gray-900 dark:text-white" x-text="selectedCustomer?.email || 'N/A'"></span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                            <span class="ml-2 text-gray-900 dark:text-white" x-text="selectedCustomer?.phone || 'N/A'"></span>
                        </div>
                        <div class="md:col-span-2">
                            <span class="text-gray-600 dark:text-gray-400">Address:</span>
                            <span class="ml-2 text-gray-900 dark:text-white" x-text="selectedCustomer?.address || 'N/A'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Details -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Service Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Type *</label>
                    <select name="service_type" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option>Select service type</option>
                        <option>Generator Maintenance</option>
                        <option>Equipment Repair</option>
                        <option>Installation Service</option>
                        <option>Preventive Maintenance</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority Level *</label>
                    <select name="priority_level" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option>Normal</option>
                        <option>High</option>
                        <option>Urgent</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Person for this Job</label>
                    <input type="text" 
                           name="contact_person"
                           placeholder="Leave blank to use customer name"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Description *</label>
                    <textarea name="service_description" required rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Describe the service requirements..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Start Date</label>
                    <input name="expected_start_date" type="date" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Completion Date</label>
                    <input name="expected_completion_date" type="date" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Location Details -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Service Location</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Address *</label>
                    <input name="service_address" required type="text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Street address">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                        <input type="text" name="city" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="City">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Province</label>
                        <input type="text" name="province" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Province">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Postal Code</label>
                        <input type="text" name="postal_code" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0000">
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-3 justify-end">
            <a href="{{ route('marketing.dashboard') }}" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-md shadow-blue-200 dark:shadow-blue-900/50" :disabled="isSubmitting">
                <span x-show="!isSubmitting">Create Job Order</span>
                <span x-show="isSubmitting">Creating...</span>
            </button>
        </div>
    </form>

    <!-- Toast Notification -->
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-4 right-4 z-50 max-w-sm"
         style="display: none;">
        <div :class="{
            'bg-green-500': toast.type === 'success',
            'bg-red-500': toast.type === 'error'
        }" class="text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3">
            <svg x-show="toast.type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <svg x-show="toast.type === 'error'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span x-text="toast.message"></span>
        </div>
    </div>
@endsection
