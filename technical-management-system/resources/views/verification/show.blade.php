<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate {{ $certificate->certificate_number }} | Gemarc Enterprises Inc</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/0fae4580-eff0-4ee7-98e2-8ab80dd542cf-removebg-preview.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- CERTIFICATE DETAILS SECTION -->
    <section class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 via-blue-500 to-blue-400 overflow-hidden py-12">
        <!-- Decorative Background Elements -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-white rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 w-full max-w-5xl px-6">
            <!-- Logo & Company Name -->
            <div class="flex items-center justify-center gap-4 mb-8 animate-fade-in">
                <img src="{{ asset('assets/0fae4580-eff0-4ee7-98e2-8ab80dd542cf-removebg-preview.png') }}" 
                     alt="Gemarc Logo" 
                     class="w-16 h-16 md:w-20 md:h-20 object-contain drop-shadow-lg">
                <div class="text-left text-white">
                    <h2 class="text-xl md:text-2xl font-bold tracking-tight">Gemarc Enterprises Inc</h2>
                    <p class="text-sm md:text-base text-blue-100 font-light">Technical Management System</p>
                </div>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-10">
                <div class="mb-8">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Certificate {{ $certificate->certificate_number }}</h1>
                    <p class="text-gray-600">Verification details and calibration summary</p>
                </div>

                <!-- Certificate Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl border border-gray-200">
                        <span class="block text-xs font-semibold text-gray-500 uppercase mb-2">Status</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $certificate->signed_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $certificate->signed_at ? 'Signed & Verified' : 'Issued (Pending Signature)' }}
                        </span>
                    </div>
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl border border-gray-200">
                        <span class="block text-xs font-semibold text-gray-500 uppercase mb-2">Customer</span>
                        <div class="text-gray-900 font-medium">{{ optional($certificate->jobOrder)->customer->name ?? 'N/A' }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl border border-gray-200">
                        <span class="block text-xs font-semibold text-gray-500 uppercase mb-2">Job Order</span>
                        <div class="text-gray-900 font-medium">{{ optional($certificate->jobOrder)->job_order_number ?? 'N/A' }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl border border-gray-200">
                        <span class="block text-xs font-semibold text-gray-500 uppercase mb-2">Signed By</span>
                        <div class="text-gray-900 font-medium">{{ optional($certificate->signedBy)->name ?? 'N/A' }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl border border-gray-200">
                        <span class="block text-xs font-semibold text-gray-500 uppercase mb-2">Signed Date</span>
                        <div class="text-gray-900 font-medium">{{ $certificate->signed_at ? $certificate->signed_at->format('M d, Y H:i') : 'Not Signed' }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl border border-gray-200">
                        <span class="block text-xs font-semibold text-gray-500 uppercase mb-2">Certificate Number</span>
                        <div class="text-gray-900 font-medium">{{ $certificate->certificate_number }}</div>
                    </div>
                </div>

                @if($certificate->calibration && $certificate->calibration->measurementPoints->count() > 0)
                <!-- Calibration Points Table -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Calibration Points</h2>
                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Point #</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reference</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">UUT</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Error</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Uncertainty</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Result</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($certificate->calibration->measurementPoints as $p)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $p->point_number }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ number_format($p->reference_value, 4) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ number_format($p->uut_reading, 4) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ number_format($p->error, 4) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $p->uncertainty ? number_format($p->uncertainty, 4) : 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ strtolower($p->status) == 'pass' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ strtoupper($p->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- QR Code & Verification URL -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Verification</h2>
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                        <img class="w-32 h-32 rounded-lg shadow-md border-2 border-white" 
                             src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('verification.show', $certificate->certificate_number)) }}" 
                             alt="QR Code"/>
                        <div class="flex-1">
                            <span class="block text-xs font-semibold text-gray-600 uppercase mb-2">Verify URL</span>
                            <a href="{{ route('verification.show', $certificate->certificate_number) }}" 
                               class="text-blue-600 hover:text-blue-700 font-medium break-all transition-colors">
                                {{ route('verification.show', $certificate->certificate_number) }}
                            </a>
                            <p class="text-sm text-gray-600 mt-2">Scan the QR code or visit the URL above to verify this certificate.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back to Verification -->
            <div class="text-center mt-6">
                <a href="{{ route('verification.verify') }}" class="inline-flex items-center gap-2 text-white hover:text-blue-100 font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Verification
                </a>
            </div>
        </div>
    </section>
</body>
</html>