@extends('layouts.dashboard')

@section('title', 'Generate Customer Request Form')
@section('page-title', 'Generate Customer Request Form')
@section('page-subtitle', 'Pending customer requests without generated form')

@section('head')
    <script>
        function customerRequestFormsPage(initialEquipmentRows) {
            return {
                showManualFormModal: false,
                equipmentRows: [],
                init() {
                    const baseRows = Array.isArray(initialEquipmentRows) && initialEquipmentRows.length
                        ? initialEquipmentRows
                        : [{ qty: 1, equipment_name: '', model: '', serial_no: '', capacity: '' }, { qty: 1, equipment_name: '', model: '', serial_no: '', capacity: '' }];

                    this.equipmentRows = baseRows.map((row) => ({
                        qty: Number(row?.qty ?? 1) || 1,
                        equipment_name: row?.equipment_name ?? '',
                        model: row?.model ?? '',
                        serial_no: row?.serial_no ?? '',
                        capacity: row?.capacity ?? '',
                    }));

                    while (this.equipmentRows.length < 2) {
                        this.equipmentRows.push({ qty: 1, equipment_name: '', model: '', serial_no: '', capacity: '' });
                    }
                },
                openManualFormModal() {
                    this.showManualFormModal = true;
                    document.body.style.overflow = 'hidden';
                },
                closeManualFormModal() {
                    this.showManualFormModal = false;
                    document.body.style.overflow = 'auto';
                },
                addEquipmentRow() {
                    if (this.equipmentRows.length >= 8) {
                        return;
                    }

                    this.equipmentRows.push({ qty: 1, equipment_name: '', model: '', serial_no: '', capacity: '' });
                },
                removeEquipmentRow(index) {
                    if (this.equipmentRows.length <= 2) {
                        return;
                    }

                    this.equipmentRows.splice(index, 1);
                }
            };
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
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
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

    <a href="{{ route('marketing.customer-request-forms') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Request Forms
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
    @php
        $initialEquipmentRows = old('items', [
            ['qty' => 1, 'equipment_name' => '', 'model' => '', 'serial_no' => '', 'capacity' => ''],
            ['qty' => 1, 'equipment_name' => '', 'model' => '', 'serial_no' => '', 'capacity' => ''],
        ]);
    @endphp
    <div class="space-y-6" x-data="customerRequestFormsPage(@js($initialEquipmentRows))" @keydown.escape.window="if (showManualFormModal) closeManualFormModal()">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Customer Request Forms</h2>
               
            </div>
            <button type="button"
                    @click="openManualFormModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Manual Customer Request Form
            </button>
        </div>

        <div>
            <div class="mb-3">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Pending Requests (Not Yet Generated)</h3>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">JO Number</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Requested By</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Date Created</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($pendingRequests as $jobOrder)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $jobOrder->job_order_number }}</div>
                                    <div class="text-xs text-orange-600 dark:text-orange-300">Pending</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $jobOrder->customer->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $jobOrder->customer->email ?? 'No email' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $jobOrder->requested_by ?? $jobOrder->creator->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $jobOrder->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('marketing.job-orders.generate-customer-request-form', $jobOrder) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-2 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                                Generate Form
                                            </button>
                                        </form>
                                        <a href="{{ route('marketing.job-orders.edit', $jobOrder) }}"
                                           class="inline-flex items-center px-3 py-2 text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                            Edit JO
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No pending requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pendingRequests->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                    {{ $pendingRequests->links() }}
                </div>
            @endif
            </div>
        </div>

        <div>
            <div class="mb-3 mt-2">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Generated Customer Request Forms</h3>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">JO Number</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Requested By</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Generated/Updated</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($generatedRequests as $jobOrder)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $jobOrder->job_order_number }}</div>
                                        <div class="text-xs text-emerald-600 dark:text-emerald-300">Generated</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $jobOrder->customer->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $jobOrder->customer->email ?? 'No email' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $jobOrder->requested_by ?? $jobOrder->creator->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ optional($jobOrder->updated_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('marketing.job-orders.customer-request-form', $jobOrder) }}" target="_blank"
                                               class="inline-flex items-center px-3 py-2 text-xs font-semibold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                                                View Form
                                            </a>

                                            <form method="POST" action="{{ route('marketing.job-orders.submit-accounting', $jobOrder) }}" class="inline"
                                                  onsubmit="return confirm('Convert this generated customer request form to Job Order and submit to accounting?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-2 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                                    Convert to JO
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        No generated customer request forms yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($generatedRequests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                        {{ $generatedRequests->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="showManualFormModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div x-show="showManualFormModal"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="closeManualFormModal()"
                     class="fixed inset-0 bg-gray-900/60"></div>

                <div x-show="showManualFormModal"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="relative w-full max-w-6xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Manual Customer Request Form</h3>
                        <button type="button" @click="closeManualFormModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form id="manual-request-form" method="POST" action="{{ route('marketing.customer-request-forms.manual-store') }}" class="p-6 space-y-6 max-h-[78vh] overflow-y-auto">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Customer / Company Name *</label>
                                <input type="text" name="company_name" required value="{{ old('company_name') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm" placeholder="Type customer or company name">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Date *</label>
                                <input type="date" name="request_date" required value="{{ old('request_date', now()->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Company TIN</label>
                            <input type="text" name="company_tin" value="{{ old('company_tin') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Address *</label>
                            <input type="text" name="address_1" required value="{{ old('address_1') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Contact Person *</label>
                                <input type="text" name="contact_person" required value="{{ old('contact_person') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email Address *</label>
                                <input type="email" name="email_address" required value="{{ old('email_address') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Contact Number *</label>
                                <input type="text" name="contact_number" required value="{{ old('contact_number') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Calibration Site Address *</label>
                            <input type="text" name="calibration_site_address_1" required value="{{ old('calibration_site_address_1') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Request Type *</label>
                            <input type="text" name="others" required value="{{ old('others') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Remarks</label>
                            <textarea name="remarks" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">{{ old('remarks') }}</textarea>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Equipment Rows (PDF Rows 1-8)</h4>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Default: 2 rows</span>
                                    <button type="button" @click="addEquipmentRow()" :disabled="equipmentRows.length >= 8" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                        Add Row
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                                <table class="w-full min-w-[900px]">
                                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">#</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Qty</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Equipment Name</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Model</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Serial No</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Capacity</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <template x-for="(row, index) in equipmentRows" :key="index">
                                            <tr>
                                                <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300" x-text="index + 1"></td>
                                                <td class="px-3 py-2">
                                                    <input type="number" min="1" x-bind:name="`items[${index}][qty]`" x-model="row.qty" class="w-20 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="text" x-bind:name="`items[${index}][equipment_name]`" x-model="row.equipment_name" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="text" x-bind:name="`items[${index}][model]`" x-model="row.model" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="text" x-bind:name="`items[${index}][serial_no]`" x-model="row.serial_no" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <div class="flex items-center gap-2">
                                                        <input type="text" x-bind:name="`items[${index}][capacity]`" x-model="row.capacity" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm">
                                                        <button type="button" @click="removeEquipmentRow(index)" x-show="equipmentRows.length > 2" class="inline-flex items-center px-2 py-1.5 text-xs font-semibold rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="pt-2 flex items-center justify-end gap-3">
                            <button type="button" @click="closeManualFormModal()" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                Save & Generate PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
