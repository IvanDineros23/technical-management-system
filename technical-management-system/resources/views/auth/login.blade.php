<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gemarc Enterprises Inc | Technical Management System</title>
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

                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Welcome
                </h1>
                <p class="mt-1 text-sm text-slate-500">Please login here</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
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
                        autocomplete="username"
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-11 text-sm text-slate-900 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('password', 'eyePassword')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none"
                        >
                            <svg id="eyePassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        >
                        <span class="text-slate-600">Remember Me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                            Forgot Password?
                        </a>
                    @endif
                </div>

                <button
                    type="submit"
                    class="mt-4 w-full rounded-xl bg-blue-600 py-3 text-sm font-semibold text-white shadow-md shadow-blue-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-1"
                >
                    Login
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId, eyeId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            
            if (input.type === 'password') {
                input.type = 'text';
                eye.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                `;
            } else {
                input.type = 'password';
                eye.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                `;
            }
        }
    </script>
</body>
</html>
