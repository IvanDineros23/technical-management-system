@extends('layouts.dashboard')

@section('title', 'Certificate Preview')
@section('page-title', 'Certificate Preview')
@section('page-subtitle', $certificate->certificate_number)

@section('sidebar-nav')
    @include('signatory.partials.sidebar')
@endsection

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $certificate->certificate_number }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Status: Ready for Release
                </p>
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    📥 Download PDF
                </button>
                <a href="{{ route('signatory.certificates') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium transition-colors">
                    Back
                </a>
            </div>
        </div>

        <!-- Certificate Document (Print Preview) -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-12" style="background: white;">
            <!-- Certificate Header -->
            <div class="text-center pb-8 border-b-2 border-gray-300">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">CERTIFICATE OF CALIBRATION</h1>
                <p class="text-sm text-gray-600">{{ $certificate->certificate_number }}</p>
            </div>

            <!-- Certificate Body -->
            <div class="mt-8 space-y-6 text-gray-900 text-sm">
                <!-- Calibration Info -->
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <p class="font-semibold text-gray-600 mb-1">CALIBRATION PERFORMED FOR:</p>
                        <p class="font-bold">{{ $certificate->jobOrder->customer->name }}</p>
                        <p class="text-xs text-gray-600 mt-2">{{ $certificate->jobOrder->job_order_number }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-600 mb-1">CALIBRATION DATE:</p>
                        <p class="font-bold">{{ optional($certificate->calibration->calibration_date)->setTimezone('Asia/Manila')->format('F d, Y') }}</p>
                        <p class="text-xs text-gray-600 mt-2">Certificate Date: {{ optional($certificate->signed_at)->setTimezone('Asia/Manila')->format('F d, Y') }}</p>
                    </div>
                </div>

                <!-- Technician & Reference -->
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <p class="font-semibold text-gray-600 mb-1">CALIBRATED BY:</p>
                        <p class="font-bold">{{ $certificate->calibration->performedBy->name }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-600 mb-1">PROCEDURE REFERENCE:</p>
                        <p class="font-bold">{{ $certificate->calibration->procedure_reference ?? 'Not Specified' }}</p>
                    </div>
                </div>

                <!-- Measurement Summary -->
                <div class="mt-8">
                    <p class="font-semibold text-gray-600 mb-3">MEASUREMENT RESULTS:</p>
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-400">
                                <th class="text-left py-2 px-2 font-bold">Point</th>
                                <th class="text-left py-2 px-2 font-bold">Reference</th>
                                <th class="text-left py-2 px-2 font-bold">UUT Reading</th>
                                <th class="text-left py-2 px-2 font-bold">Error</th>
                                <th class="text-left py-2 px-2 font-bold">Uncertainty</th>
                                <th class="text-left py-2 px-2 font-bold">Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificate->calibration->measurementPoints as $point)
                                <tr class="border-b border-gray-200">
                                    <td class="py-2 px-2">{{ $point->point_number }}</td>
                                    <td class="py-2 px-2">{{ number_format($point->reference_value, 4) }}</td>
                                    <td class="py-2 px-2">{{ number_format($point->uut_reading, 4) }}</td>
                                    <td class="py-2 px-2">{{ number_format($point->error, 4) }}</td>
                                    <td class="py-2 px-2">{{ $point->uncertainty ? number_format($point->uncertainty, 4) : 'N/A' }}</td>
                                    <td class="py-2 px-2 font-bold">{{ strtoupper($point->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Overall Result -->
                <div class="mt-8 p-4 bg-gray-50 rounded border border-gray-300">
                    <p class="font-semibold text-gray-600 mb-2">OVERALL RESULT:</p>
                    @if($certificate->calibration->pass_fail === 'pass')
                        <p class="text-lg font-bold text-emerald-700">✓ PASS - Equipment meets specifications</p>
                    @elseif($certificate->calibration->pass_fail === 'fail')
                        <p class="text-lg font-bold text-rose-700">✗ FAIL - Equipment does not meet specifications</p>
                    @else
                        <p class="text-lg font-bold text-amber-700">~ CONDITIONAL - Equipment meets specifications with limitations</p>
                    @endif
                </div>

                <!-- Signature Section -->
                <div class="mt-12 pt-8 border-t-2 border-gray-300 grid grid-cols-2 gap-12">
                    <div>
                        <p class="font-semibold text-gray-600 mb-8">Technician</p>
                        <div class="border-b-2 border-gray-400" style="height: 40px;"></div>
                        <p class="text-xs text-gray-600 mt-2">{{ $certificate->calibration->performedBy->name }}</p>
                        <p class="text-xs text-gray-600">{{ optional($certificate->calibration->calibration_date)->setTimezone('Asia/Manila')->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-600 mb-8">Signatory</p>
                        <div class="border-b-2 border-gray-400" style="height: 40px;"></div>
                        <p class="text-xs text-gray-600 mt-2">{{ $certificate->signedBy->name }}</p>
                        <p class="text-xs text-gray-600">Digital Signature - {{ optional($certificate->signed_at)->setTimezone('Asia/Manila')->format('F d, Y') }}</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-12 pt-6 border-t border-gray-300 text-center text-xs text-gray-600">
                    <p>This certificate is valid and ready for customer delivery.</p>
                    <p class="mt-2">Certificate Number: {{ $certificate->certificate_number }}</p>
                </div>
            </div>
        </div>

        <!-- Certificate Info -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">ℹ️ Certificate Information</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Certificate Number</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $certificate->certificate_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Signed By</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $certificate->signedBy->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Signed Date</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ optional($certificate->signed_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">
                        ✓ Ready for Release
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection
