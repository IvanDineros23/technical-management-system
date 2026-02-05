@extends('layouts.dashboard')

@section('title', 'Certificates For Release')

@section('sidebar-nav')
    <a href="{{ route('accounting.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.dashboard') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('accounting.payments') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.payments') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Payment Verification
    </a>

    <a href="{{ route('accounting.certificates.for-release') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.certificates.for-release') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        For Release
    </a>

    <a href="{{ route('accounting.certificates.released') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.certificates.released') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        Released History
    </a>

    <a href="{{ route('accounting.reports') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.reports') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Reports
    </a>

    <a href="{{ route('accounting.timelines') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.timelines', 'accounting.timeline') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Timeline
    </a>
@endsection

@section('content')
<div x-data="{
    showReleaseModal: false,
    showHoldModal: false,
    showViewModal: false,
    selectedCertificate: null,
    viewCertificate: null,
    releasedTo: '',
    deliveryMethod: 'pickup',
    releaseNotes: '',
    holdReason: '',
    init() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.showReleaseModal = false;
                this.showHoldModal = false;
                this.showViewModal = false;
            }
        });
    },
    openReleaseModal(certificate) {
        this.selectedCertificate = certificate;
        this.releasedTo = '';
        this.deliveryMethod = 'pickup';
        this.releaseNotes = '';
        this.showReleaseModal = true;
    },
    openHoldModal(certificate) {
        this.selectedCertificate = certificate;
        this.holdReason = '';
        this.showHoldModal = true;
    },
    openViewModal(certificate) {
        this.viewCertificate = certificate;
        this.showViewModal = true;
    }
}">
    <!-- Filters -->
    <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
        <form method="GET" action="{{ route('accounting.certificates.for-release') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Certificate # or Customer"
                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Status</label>
                <select name="payment_status" 
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="verified" {{ request('payment_status') === 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="unverified" {{ request('payment_status') === 'unverified' ? 'selected' : '' }}>Unverified</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm">
                    Apply Filters
                </button>
                <a href="{{ route('accounting.certificates.for-release') }}" 
                   class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-white font-medium rounded-md shadow-sm">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Certificates Table -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Certificate #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Job Order #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Generated
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Payment Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($certificates as $certificate)
                            @php
                                $isPaymentVerified = $certificate->jobOrder->payment && $certificate->jobOrder->payment->status === 'verified';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $certificate->certificate_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $certificate->jobOrder->job_order_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $certificate->jobOrder->customer->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $certificate->generated_at->setTimezone('Asia/Manila')->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($isPaymentVerified)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Unverified
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    @if($isPaymentVerified)
                                        <button @click="openReleaseModal({{ json_encode([
                                            'id' => $certificate->id,
                                            'certificate_number' => $certificate->certificate_number,
                                            'job_order_number' => $certificate->jobOrder->job_order_number,
                                            'customer' => $certificate->jobOrder->customer->name
                                        ]) }})"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Release
                                        </button>
                                        <button @click="openHoldModal({{ json_encode([
                                            'id' => $certificate->id,
                                            'certificate_number' => $certificate->certificate_number
                                        ]) }})"
                                                class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                            <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            Hold
                                        </button>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-600 text-xs italic">
                                            Payment verification required
                                        </span>
                                    @endif
                                    
                                    <button @click="openViewModal({{ json_encode([
                                        'id' => $certificate->id,
                                        'certificate_number' => $certificate->certificate_number,
                                        'job_order_number' => $certificate->jobOrder->job_order_number,
                                        'customer' => $certificate->jobOrder->customer->name,
                                        'customer_address' => $certificate->jobOrder->customer->address ?? 'N/A',
                                        'generated_at' => $certificate->generated_at->setTimezone('Asia/Manila')->format('F d, Y'),
                                        'signed_at' => $certificate->signed_at ? \Carbon\Carbon::parse($certificate->signed_at)->setTimezone('Asia/Manila')->format('F d, Y') : null,
                                        'signed_by' => $certificate->signedBy?->name,
                                        'issued_by' => $certificate->issuedBy?->name ?? 'System',
                                        'valid_until' => $certificate->valid_until ? \Carbon\Carbon::parse($certificate->valid_until)->setTimezone('Asia/Manila')->format('F d, Y') : null,
                                        'status' => $certificate->status,
                                        'payment_status' => $isPaymentVerified ? 'verified' : 'unverified',
                                        'payment_amount' => $certificate->jobOrder->payment?->amount,
                                        'payment_method' => $certificate->jobOrder->payment?->payment_method,
                                        'payment_reference' => $certificate->jobOrder->payment?->reference_number
                                    ]) }})"
                                            class="inline-flex items-center px-3 py-1 border border-blue-300 dark:border-blue-600 text-xs font-medium rounded-md text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800">
                                        <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No certificates pending release
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $certificates->links() }}
            </div>
        </div>

    <!-- View Certificate Modal -->
    <div x-show="showViewModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showViewModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 aria-hidden="true"
                 @click="showViewModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showViewModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-white" x-text="viewCertificate?.certificate_number"></h3>
                            <p class="text-blue-100 text-sm mt-1">Certificate of Calibration</p>
                        </div>
                        <button @click="showViewModal = false" class="text-white hover:text-blue-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Body -->
                <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                    <!-- Certificate Preview Card -->
                    <div class="bg-white border-2 border-gray-200 rounded-lg p-6 mb-6" style="background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);">
                        <!-- Certificate Header -->
                        <div class="text-center pb-4 border-b-2 border-gray-300 mb-4">
                            <h2 class="text-2xl font-bold text-gray-900">CERTIFICATE OF CALIBRATION</h2>
                            <p class="text-sm text-gray-600 mt-1" x-text="viewCertificate?.certificate_number"></p>
                        </div>

                        <!-- Certificate Info Grid -->
                        <div class="grid grid-cols-2 gap-6 text-sm">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Customer</p>
                                <p class="font-bold text-gray-900" x-text="viewCertificate?.customer"></p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Job Order</p>
                                <p class="font-bold text-gray-900" x-text="viewCertificate?.job_order_number"></p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Generated Date</p>
                                <p class="text-gray-900" x-text="viewCertificate?.generated_at"></p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Valid Until</p>
                                <p class="text-gray-900" x-text="viewCertificate?.valid_until || 'N/A'"></p>
                            </div>
                        </div>

                        <!-- Signature Section -->
                        <div class="mt-6 pt-4 border-t border-gray-200 grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Issued By</p>
                                <p class="text-gray-900" x-text="viewCertificate?.issued_by || 'System'"></p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Signed By</p>
                                <p class="text-gray-900" x-text="viewCertificate?.signed_by || 'Not Signed'"></p>
                                <template x-if="viewCertificate?.signed_at">
                                    <p class="text-xs text-gray-500" x-text="'Signed on: ' + viewCertificate?.signed_at"></p>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Payment Information
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                                <template x-if="viewCertificate?.payment_status === 'verified'">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        ✓ Verified
                                    </span>
                                </template>
                                <template x-if="viewCertificate?.payment_status !== 'verified'">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        ⏳ Unverified
                                    </span>
                                </template>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Amount</p>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    <template x-if="viewCertificate?.payment_amount">
                                        <span x-text="'₱' + Number(viewCertificate?.payment_amount).toLocaleString('en-PH', {minimumFractionDigits: 2})"></span>
                                    </template>
                                    <template x-if="!viewCertificate?.payment_amount">
                                        <span class="text-gray-400">N/A</span>
                                    </template>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Method</p>
                                <p class="font-semibold text-gray-900 dark:text-white capitalize" x-text="viewCertificate?.payment_method?.replace('_', ' ') || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Reference</p>
                                <p class="font-semibold text-gray-900 dark:text-white" x-text="viewCertificate?.payment_reference || 'N/A'"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end gap-3">
                    <button @click="showViewModal = false" 
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium rounded-lg transition-colors">
                        Close
                    </button>
                    <template x-if="viewCertificate?.payment_status === 'verified'">
                        <button @click="showViewModal = false; openReleaseModal(viewCertificate)" 
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                            Release Certificate
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Release Certificate Modal -->
    <div x-show="showReleaseModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showReleaseModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 aria-hidden="true"
                 @click="showReleaseModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showReleaseModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="`{{ url('/accounting/certificates') }}/${selectedCertificate?.id}/release`" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Release Certificate
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-md p-3">
                                        <p class="text-sm text-blue-800 dark:text-blue-200">
                                            <strong>Certificate:</strong> <span x-text="selectedCertificate?.certificate_number"></span>
                                        </p>
                                        <p class="text-sm text-blue-800 dark:text-blue-200">
                                            <strong>Customer:</strong> <span x-text="selectedCertificate?.customer"></span>
                                        </p>
                                    </div>

                                    <div>
                                        <label for="released_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Released To <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="released_to" 
                                               id="released_to" 
                                               x-model="releasedTo"
                                               placeholder="Name of recipient"
                                               required
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                                    </div>

                                    <div>
                                        <label for="delivery_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Delivery Method <span class="text-red-500">*</span>
                                        </label>
                                        <select name="delivery_method" 
                                                id="delivery_method" 
                                                x-model="deliveryMethod"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                                            <option value="pickup">Pickup</option>
                                            <option value="courier">Courier</option>
                                            <option value="email">Email</option>
                                            <option value="hand_delivery">Hand Delivery</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="release_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Notes (Optional)
                                        </label>
                                        <textarea name="release_notes" 
                                                  id="release_notes" 
                                                  x-model="releaseNotes"
                                                  rows="3"
                                                  placeholder="Additional notes about the release..."
                                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Release Certificate
                        </button>
                        <button type="button" 
                                @click="showReleaseModal = false" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hold Certificate Modal -->
    <div x-show="showHoldModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showHoldModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 aria-hidden="true"
                 @click="showHoldModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showHoldModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="`{{ url('/accounting/certificates') }}/${selectedCertificate?.id}/hold`" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="hold">
                    
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Hold Certificate
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Certificate: <strong class="text-gray-900 dark:text-white" x-text="selectedCertificate?.certificate_number"></strong>
                                    </p>

                                    <div>
                                        <label for="hold_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Reason for Hold <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="hold_reason" 
                                                  id="hold_reason" 
                                                  x-model="holdReason"
                                                  rows="3"
                                                  required
                                                  placeholder="Explain why this certificate is being held..."
                                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Place on Hold
                        </button>
                        <button type="button" 
                                @click="showHoldModal = false" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
