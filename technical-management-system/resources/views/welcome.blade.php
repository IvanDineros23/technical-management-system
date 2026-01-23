<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gemarc Enterprises Inc | Technical Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/0fae4580-eff0-4ee7-98e2-8ab80dd542cf-removebg-preview.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- HERO -->
    <section class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 via-blue-500 to-blue-400 overflow-hidden">
        <!-- Decorative Background Elements -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-white rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 text-center text-white max-w-4xl px-6 py-12">
            <!-- Logo & Company Name -->
            <div class="flex items-center justify-center gap-4 mb-8 animate-fade-in">
                <img src="{{ asset('assets/0fae4580-eff0-4ee7-98e2-8ab80dd542cf-removebg-preview.png') }}" 
                     alt="Gemarc Logo" 
                     class="w-16 h-16 md:w-20 md:h-20 object-contain drop-shadow-lg">
                <div class="text-left">
                    <h2 class="text-xl md:text-2xl font-bold tracking-tight">Gemarc Enterprises Inc</h2>
                    <p class="text-sm md:text-base text-blue-100 font-light">Technical Management System</p>
                </div>
            </div>

            <!-- Main Heading -->
            <div class="mb-12">
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold mb-6 leading-tight drop-shadow-lg">
                    Streamline Your<br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-blue-100">
                        Technical Operations
                    </span>
                </h1>
                <p class="text-lg md:text-xl lg:text-2xl mb-4 text-blue-50 max-w-2xl mx-auto font-light leading-relaxed">
                    A unified platform for managing job orders, technical operations,
                    maintenance, approvals, and reporting.
                </p>
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-col items-center gap-4 mb-16">
                <div class="flex justify-center gap-4 flex-wrap">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" 
                           class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-blue-600 bg-white rounded-xl shadow-2xl hover:shadow-blue-200/50 hover:scale-105 transition-all duration-300">
                            <span class="relative z-10 flex items-center gap-2">
                                Login
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </span>
                        </a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" 
                           class="group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white border-2 border-white rounded-xl hover:bg-white hover:text-blue-600 transition-all duration-300 shadow-lg hover:shadow-2xl hover:scale-105">
                            <span class="flex items-center gap-2">
                                Register
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </span>
                        </a>
                    @endif
                </div>
                <a href="{{ route('verification.verify') }}" 
                   class="group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-blue-600 bg-white/90 border-2 border-white rounded-xl hover:bg-white hover:text-blue-700 transition-all duration-300 shadow-lg hover:shadow-2xl hover:scale-105">
                    <span class="flex items-center gap-2">
                        Verify Certificate
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>
            </div>
    </section>
</body>
</html>
