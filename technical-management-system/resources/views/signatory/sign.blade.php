@extends('layouts.dashboard')

@section('title', 'Sign Certificate')
@section('page-title', 'Digital Signature')
@section('page-subtitle', 'Sign and finalize the calibration certificate')

@section('sidebar-nav')
    @include('signatory.partials.sidebar')
@endsection

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $calibration->assignment->jobOrder->job_order_number }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Sign & Finalize Certificate
                </p>
            </div>
            <a href="{{ route('signatory.for-review') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                ← Back
            </a>
        </div>

        <!-- Approval Confirmation -->
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-[20px] p-6">
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-emerald-900 dark:text-emerald-200">✓ Approved</h3>
                    <p class="text-sm text-emerald-800 dark:text-emerald-300 mt-1">
                        This calibration has been approved and is ready for digital signature.
                    </p>
                </div>
            </div>
        </div>

        <!-- Calibration Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">📋 Calibration Summary</h3>
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Customer</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $calibration->assignment->jobOrder->customer->name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Technician</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $calibration->performedBy->name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Calibration Date</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $calibration->calibration_date->format('M d, Y') }}
                        </p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Measurement Points</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $calibration->measurementPoints->count() }} points
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Result</p>
                        @if($calibration->pass_fail === 'pass')
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">✓ Pass</span>
                        @elseif($calibration->pass_fail === 'fail')
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200">✗ Fail</span>
                        @else
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">~ Conditional</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Signature Form -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-6">🔐 Digital Signature</h3>
            
            <form method="POST" action="{{ route('signatory.sign', $calibration) }}" class="space-y-6">
                @csrf

                <!-- Signatory Info -->
                <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Signatory</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ auth()->user()->title ?? 'Signatory' }}</p>
                </div>

                <!-- Certificate Number -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Certificate Number *</label>
                    <input type="text" name="certificate_number" required placeholder="Will be auto-generated or enter custom"
                        value="{{ \App\Models\Certificate::generateCertificateNumber() }}"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: CERT-YYYY-NNNN</p>
                </div>

                <!-- Signature Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Signature Password *</label>
                    <input type="password" name="signature_password" required placeholder="Enter your signature password"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Required to authorize digital signature</p>
                </div>

                <!-- Agreement -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <label class="flex items-start gap-3">
                        <input type="checkbox" required class="mt-1 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-xs text-gray-700 dark:text-gray-300">
                            I certify that I have reviewed this calibration and its measurement data. 
                            I hereby approve and digitally sign this certificate as evidence of authentication and authorization.
                        </span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('signatory.for-review') }}" class="flex-1 px-4 py-3 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors">
                        🔐 Sign & Finalize
                    </button>
                </div>
            </form>

            <!-- Security Notice -->
            <div class="mt-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                <p class="text-xs text-amber-800 dark:text-amber-300">
                    <strong>⚠️ Security Notice:</strong> Your digital signature is legally binding. 
                    Ensure all calibration data is accurate before signing. Once signed, the certificate is locked and ready for release.
                </p>
            </div>
        </div>
    </div>
@endsection
