@extends('layouts.dashboard')

@section('title', 'Create New Job Order')

@section('head')
<script>
    function jobOrderForm() {
        return {
            isSubmitting: false,
            customers: @json($customers),
            selectedCustomer: null,

            // services checkbox (matches PDF)
            services: {
                inspection: false,
                repair: false,
                installation: false,
                demonstration: false,
                calibration: false,
            },

            // dynamic items table (matches PDF)
            items: [
                { item_no: 1, qty: 1, equipment_name: '', model: '', capacity: '', serial_no: '' }
            ],

            toast: { show: false, message: '', type: 'success' },

            selectCustomer() {
                const customerId = document.querySelector('select[name="customer_id"]').value;
                this.selectedCustomer = customerId
                    ? this.customers.find(c => c.id == customerId)
                    : null;

                // OPTIONAL: auto-fill some fields based on customer (if you want)
                // if (this.selectedCustomer) {
                //   document.querySelector('input[name="company_name"]').value = this.selectedCustomer.name ?? '';
                //   document.querySelector('input[name="contact_no"]').value = this.selectedCustomer.phone ?? '';
                //   document.querySelector('input[name="address"]').value = this.selectedCustomer.address ?? '';
                //   document.querySelector('input[name="contact_person"]').value = this.selectedCustomer.contact_person ?? '';
                // }
            },

            addItem() {
                const nextNo = this.items.length ? (parseInt(this.items[this.items.length - 1].item_no) + 1) : 1;
                this.items.push({ item_no: nextNo, qty: 1, equipment_name: '', model: '', capacity: '', serial_no: '' });
            },

            removeItem(index) {
                if (this.items.length === 1) return;
                this.items.splice(index, 1);
                // re-number item_no for cleanliness
                this.items = this.items.map((it, idx) => ({ ...it, item_no: idx + 1 }));
            },

            showToast(message, type = 'success') {
                this.toast.message = message;
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => this.toast.show = false, 3000);
            },

            submitForm(event) {
                event.preventDefault();
                this.isSubmitting = true;

                const form = event.target;
                const formData = new FormData(form);

                // services[] payload
                const selectedServices = Object.entries(this.services)
                    .filter(([_, v]) => v)
                    .map(([k]) => k);

                if (!formData.get('customer_id')) {
                    this.showToast('Please select a customer', 'error');
                    this.isSubmitting = false;
                    return;
                }

                if (selectedServices.length === 0) {
                    this.showToast('Please select at least one service (Inspection/Repair/Installation/Demo/Calibration)', 'error');
                    this.isSubmitting = false;
                    return;
                }

                // items validation (at least 1 equipment_name)
                const hasAnyItem = this.items.some(i => (i.equipment_name || '').trim() !== '');
                if (!hasAnyItem) {
                    this.showToast('Please add at least one item in the equipment table.', 'error');
                    this.isSubmitting = false;
                    return;
                }

                // build JSON payload
                const data = Object.fromEntries(formData.entries());
                data.services = selectedServices;
                data.items = this.items;
                // auto set created_by (marketing user)
                data.created_by = @json(auth()->user()->id);

                fetch('{{ route('marketing.job-orders.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(async (response) => {
                    const json = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(json.message || 'Request failed');
                    }
                    return json;
                })
                .then(data => {
                    if (data.success) {
                        this.showToast(data.message || 'Job Order created successfully!', data.pdf_generated === false ? 'error' : 'success');
                        setTimeout(() => window.location.href = '{{ route('marketing.job-orders') }}', 1200);
                    } else {
                        this.showToast(data.message || 'Error creating job order', 'error');
                        this.isSubmitting = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.showToast(error.message || 'Network error. Please try again.', 'error');
                    this.isSubmitting = false;
                });
            }
        }
    }
</script>
@endsection

@section('sidebar-nav')
    {{-- keep your sidebar as-is --}}
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
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Job Order</h2>
    </div>

    <form @submit.prevent="submitForm($event)" class="space-y-6">

        {{-- HEADER FIELDS: Date + JO No --}}
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date *</label>
                    <input name="date" type="date" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">J.O. No. *</label>
                    <input name="jo_no" type="text" required placeholder="e.g., JO-2026-0001"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>

        {{-- CUSTOMER SELECTION (kept, but aligned to template fields) --}}
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Company / Customer</h3>
                <a href="{{ route('marketing.customers') }}"
                   class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Customer
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Customer *</label>
                    <select name="customer_id" required @change="selectCustomer()"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Choose a customer...</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name *</label>
                    <input name="company_name" required type="text" placeholder="Auto-fill or manual"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company TIN</label>
                    <input name="company_tin" type="text" placeholder="TIN"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address *</label>
                    <input name="address" required type="text" placeholder="Company address"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact No.</label>
                    <input name="contact_no" type="text" placeholder="09xx..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Person</label>
                    <input name="contact_person" type="text" placeholder="Person in charge"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Customer Details Preview --}}
                <div x-show="selectedCustomer" x-transition
                     class="md:col-span-2 mt-2 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Customer Details (Preview)</h4>
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

        {{-- CALIBRATION SITE ADDRESS + SERVICE TYPES (checkboxes like PDF) --}}
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Service Details (Template)</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Calibration Site Address *</label>
                    <input name="calibration_site_address" required type="text" placeholder="Where the service will be performed"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Type *</label>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        <label class="flex items-center gap-2 text-sm text-gray-800 dark:text-gray-200">
                            <input type="checkbox" x-model="services.inspection" class="rounded border-gray-300 dark:border-gray-600">
                            Inspection
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-800 dark:text-gray-200">
                            <input type="checkbox" x-model="services.repair" class="rounded border-gray-300 dark:border-gray-600">
                            Repair
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-800 dark:text-gray-200">
                            <input type="checkbox" x-model="services.installation" class="rounded border-gray-300 dark:border-gray-600">
                            Installation
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-800 dark:text-gray-200">
                            <input type="checkbox" x-model="services.demonstration" class="rounded border-gray-300 dark:border-gray-600">
                            Demonstration
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-800 dark:text-gray-200">
                            <input type="checkbox" x-model="services.calibration" class="rounded border-gray-300 dark:border-gray-600">
                            Calibration
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        (Matches the checkbox row in the GEI-MAR-F-1 template)
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Client P.O. Ctrl No.</label>
                        <input name="client_po_ctrl_no" type="text" placeholder="Optional"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Invoice Number</label>
                        <input name="service_invoice_number" type="text" placeholder="Optional"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Others</label>
                        <input name="others" type="text" placeholder="Optional"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remarks</label>
                    <textarea name="remarks" rows="3" placeholder="REMARKS..."
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
            </div>
        </div>

        {{-- ITEMS TABLE (matches PDF columns) --}}
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Items / Equipment</h3>
                <button type="button" @click="addItem()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    + Add Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-gray-700 dark:text-gray-300">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 pr-2">Item No</th>
                            <th class="text-left py-2 pr-2">Qty</th>
                            <th class="text-left py-2 pr-2">Equipment Name</th>
                            <th class="text-left py-2 pr-2">Model</th>
                            <th class="text-left py-2 pr-2">Capacity</th>
                            <th class="text-left py-2 pr-2">Serial No</th>
                            <th class="text-left py-2 pr-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="border-b border-gray-100 dark:border-gray-700/60">
                                <td class="py-2 pr-2 w-20">
                                    <input type="number" min="1" x-model="item.item_no"
                                           class="w-20 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </td>
                                <td class="py-2 pr-2 w-24">
                                    <input type="number" min="1" x-model="item.qty"
                                           class="w-24 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </td>
                                <td class="py-2 pr-2 min-w-[220px]">
                                    <input type="text" x-model="item.equipment_name" placeholder="Equipment Name"
                                           class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </td>
                                <td class="py-2 pr-2 min-w-[160px]">
                                    <input type="text" x-model="item.model" placeholder="Model"
                                           class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </td>
                                <td class="py-2 pr-2 min-w-[160px]">
                                    <input type="text" x-model="item.capacity" placeholder="Capacity"
                                           class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </td>
                                <td class="py-2 pr-2 min-w-[160px]">
                                    <input type="text" x-model="item.serial_no" placeholder="Serial No"
                                           class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </td>
                                <td class="py-2 pr-2 w-16">
                                    <button type="button" @click="removeItem(index)"
                                            class="px-3 py-1 rounded bg-red-600 text-white text-xs hover:bg-red-700"
                                            :disabled="items.length === 1">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TERMS + RECEIVED BY + PREPARED/APPROVED BY (template signatures block) --}}
        {{-- TERMS (no signatories inputs) --}}
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Terms</h3>

                {{-- display only, not editable --}}
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    Created by:
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ auth()->user()->name }}
                    </span>
                </div>
            </div>

            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Terms</label>
            <textarea name="terms" rows="4"
                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Terms..."></textarea>
        </div>

        {{-- ACTIONS --}}
        <div class="flex gap-3 justify-end">
            <a href="{{ route('marketing.dashboard') }}"
               class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-md shadow-blue-200 dark:shadow-blue-900/50"
                    :disabled="isSubmitting">
                <span x-show="!isSubmitting">Create Job Order</span>
                <span x-show="isSubmitting">Creating...</span>
            </button>
        </div>

    </form>

    {{-- TOAST --}}
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
        }"
        class="text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3">
            <svg x-show="toast.type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <svg x-show="toast.type === 'error'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span x-text="toast.message"></span>
        </div>
    </div>

</div>
@endsection
