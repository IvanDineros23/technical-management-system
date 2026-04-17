@extends('layouts.dashboard')

@section('title', 'Edit Service Request')
@section('page-title', 'Edit Service Request')
@section('page-subtitle', 'Update your pending request details')

@section('sidebar-nav')
    <a href="{{ route('customer.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('customer.requests') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Requests
    </a>

    <a href="{{ route('customer.certificates') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
        </svg>
        Certificates
    </a>

    <a href="{{ route('certificate-verification.verify') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12.75L11.25 15 15 9.75M12 3l7.5 4.5v5.25c0 4.5-3.09 8.69-7.5 9.75-4.41-1.06-7.5-5.25-7.5-9.75V7.5L12 3z"/>
        </svg>
        Verify Certificate
    </a>
@endsection

@section('content')
    @php
        $initialEquipmentRows = old('items', $jobOrder->items->map(function ($item) {
            return [
                'qty' => (int) ($item->quantity ?? 1),
                'equipment_name' => (string) ($item->equipment_type ?? ''),
                'model' => (string) ($item->model ?? ''),
                'serial_no' => (string) ($item->serial_number ?? ''),
                'capacity' => (string) ($item->range ?? ''),
            ];
        })->values()->all());

        if (empty($initialEquipmentRows)) {
            $initialEquipmentRows = [
                ['qty' => 1, 'equipment_name' => '', 'model' => '', 'serial_no' => '', 'capacity' => ''],
                ['qty' => 1, 'equipment_name' => '', 'model' => '', 'serial_no' => '', 'capacity' => ''],
            ];
        }
    @endphp

    <div class="max-w-5xl mx-auto" x-data="{ equipmentRows: @js($initialEquipmentRows), addRow() { if (this.equipmentRows.length < 8) this.equipmentRows.push({ qty: 1, equipment_name: '', model: '', serial_no: '', capacity: '' }); }, removeRow(index) { if (this.equipmentRows.length > 2) this.equipmentRows.splice(index, 1); } }">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $jobOrder->job_order_number }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Update your request and regenerate the PDF details.</p>
            </div>
            <a href="{{ route('customer.requests') }}" class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">
                Back to Requests
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.requests.update', $jobOrder) }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm space-y-5">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Service Type *</label>
                    <select name="service_type" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                        @php($serviceType = old('service_type', $jobOrder->service_type))
                        <option value="Calibration" {{ $serviceType === 'Calibration' ? 'selected' : '' }}>Calibration</option>
                        <option value="Repair" {{ $serviceType === 'Repair' ? 'selected' : '' }}>Repair</option>
                        <option value="Maintenance" {{ $serviceType === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="Installation" {{ $serviceType === 'Installation' ? 'selected' : '' }}>Installation</option>
                        <option value="Consultation" {{ $serviceType === 'Consultation' ? 'selected' : '' }}>Consultation</option>
                        <option value="Other" {{ $serviceType === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Priority *</label>
                    @php($priority = old('priority', $jobOrder->priority ?? 'normal'))
                    <select name="priority" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                        <option value="normal" {{ $priority === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ $priority === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ $priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Service Description *</label>
                <textarea name="service_description" rows="4" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">{{ old('service_description', $jobOrder->service_description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Service Address *</label>
                <input type="text" name="service_address" required value="{{ old('service_address', $jobOrder->service_address) }}"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">City</label>
                    <input type="text" name="city" value="{{ old('city', $jobOrder->city) }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Province</label>
                    <input type="text" name="province" value="{{ old('province', $jobOrder->province) }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Postal Code</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $jobOrder->postal_code) }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Expected Completion Date</label>
                <input type="date" name="expected_completion_date" value="{{ old('expected_completion_date', optional($jobOrder->expected_completion_date)->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Special Instructions</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">{{ old('notes', $jobOrder->notes) }}</textarea>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Request Equipment Details (PDF Rows 1-8)</h4>
                    <button type="button" @click="addRow()" :disabled="equipmentRows.length >= 8" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Add Row
                    </button>
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
                                            <button type="button" @click="removeRow(index)" x-show="equipmentRows.length > 2" class="inline-flex items-center px-2 py-1.5 text-xs font-semibold rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">
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

            <div class="pt-2 flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
                <a href="{{ route('customer.requests') }}" class="px-6 py-2.5 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-100 text-sm font-semibold hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection