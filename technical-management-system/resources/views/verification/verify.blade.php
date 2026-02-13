<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification | Gemarc Enterprises Inc</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/0fae4580-eff0-4ee7-98e2-8ab80dd542cf-removebg-preview.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- VERIFICATION SECTION -->
    <section class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 via-blue-500 to-blue-400 overflow-hidden">
        <!-- Decorative Background Elements -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-white rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 w-full max-w-2xl px-6 py-12">
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
                <div class="text-center mb-8">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Certificate Verification</h1>
                    <p class="text-gray-600">Enter the certificate number to verify authenticity</p>
                </div>

                <!-- Search Form -->
                <form method="GET" action="{{ route('certificate-verification.verify') }}" class="space-y-4">
                    <div>
                        <label for="certificate_number" class="block text-sm font-semibold text-gray-700 mb-2">Certificate Number</label>
                        <div class="flex gap-3">
                            <input 
                                type="text" 
                                name="certificate_number" 
                                id="certificate_number"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all" 
                                placeholder="e.g., CERT-2026-00001" 
                                value="{{ request('certificate_number') }}"
                            />
                            <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl hover:scale-105">
                                Verify
                            </button>
                        </div>
                    </div>
                </form>

                @if($searchPerformed)
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        @if($error)
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                                <p class="font-medium">{{ $error }}</p>
                            </div>
                        @elseif($certificate)
                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-xl">
                                        <span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Certificate Number</span>
                                        <div class="text-gray-900 font-medium">{{ $certificate->certificate_number }}</div>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-xl">
                                        <span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status</span>
                                        <div>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $certificate->signed_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $certificate->signed_at ? 'Signed & Verified' : 'Issued (Pending)' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-xl">
                                        <span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Customer</span>
                                        <div class="text-gray-900 font-medium">{{ optional($certificate->jobOrder)->customer->name ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <a href="{{ route('certificate-verification.show', $certificate->certificate_number) }}" 
                                   class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-semibold transition-colors">
                                    View full certificate details
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Back to Home -->
            <div class="text-center mt-6">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-white hover:text-blue-100 font-medium transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Dashboard
                    </a>
                @else
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-white hover:text-blue-100 font-medium transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Home
                    </a>
                @endauth
            </div>
        </div>
    </section>
</body>
</html>