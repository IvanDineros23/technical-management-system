<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Gemarc Enterprises Inc | Technical Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/0fae4580-eff0-4ee7-98e2-8ab80dd542cf-removebg-preview.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f5f9ff] flex items-center justify-center">
    <div class="w-full max-w-7xl mx-auto flex bg-white rounded-[32px] shadow-2xl overflow-hidden">
        <!-- Left panel -->
        <div class="hidden md:flex w-1/2 bg-[#f5f9ff] overflow-hidden">
            <img src="{{ asset('assets/technicalbg.png') }}" alt="Technical background" class="w-full h-full object-cover">
        </div>

        <!-- Right panel -->
        <div class="w-full md:w-1/2 px-10 py-12 md:px-16 md:py-20 flex flex-col justify-center">
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-6">
                    <img src="{{ asset('assets/0fae4580-eff0-4ee7-98e2-8ab80dd542cf-removebg-preview.png') }}" alt="Gemarc Enterprises Inc" class="w-14 h-14 rounded-2xl object-cover">
                    <span class="text-xl font-semibold text-slate-900">Gemarc Enterprises Inc</span>
                </div>

                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900 flex items-center gap-2">
                    Forgot Password
                    <span class="text-2xl">🔑</span>
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
                </p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 p-3 text-sm text-green-600 bg-green-50 rounded-xl border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs pt-2">
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                        Back to Login
                    </a>
                </div>

                <button
                    type="submit"
                    class="mt-4 w-full rounded-xl bg-blue-600 py-3 text-sm font-semibold text-white shadow-md shadow-blue-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-1"
                >
                    Email Password Reset Link
                </button>
            </form>
        </div>
    </div>
</body>
</html>
