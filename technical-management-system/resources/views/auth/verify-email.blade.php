<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Gemarc Enterprises Inc | Technical Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/0fae4580-eff0-4ee7-98e2-8ab80dd542cf-removebg-preview.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f5f9ff] flex items-center justify-center">
    <div class="w-full max-w-7xl mx-auto flex bg-white rounded-[32px] shadow-2xl overflow-hidden">
        <div class="hidden md:flex w-1/2 bg-[#f5f9ff] overflow-hidden">
            <img src="{{ asset('assets/technicalbg.png') }}" alt="Technical background" class="w-full h-full object-cover">
        </div>

        <div class="w-full md:w-1/2 px-10 py-12 md:px-16 md:py-20 flex flex-col justify-center">
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-6">
                    <img src="{{ asset('assets/0fae4580-eff0-4ee7-98e2-8ab80dd542cf-removebg-preview.png') }}" alt="Gemarc Enterprises Inc" class="w-14 h-14 rounded-2xl object-cover">
                    <span class="text-xl font-semibold text-slate-900">Gemarc Enterprises Inc</span>
                </div>

                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">Verify Your Email</h1>
                <p class="mt-2 text-sm text-slate-500">
                    Thanks for signing up. Before getting started, please verify your email address using the link we sent to your inbox.
                </p>
            </div>

            @if (session('status') === 'verification-link-sent')
                <div class="mb-6 p-3 text-sm text-green-600 bg-green-50 rounded-xl border border-green-200">
                    A new verification link has been sent to your email address.
                </div>
            @endif

            <div class="space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-blue-600 py-3 text-sm font-semibold text-white shadow-md shadow-blue-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-1"
                    >
                        Resend Verification Email
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-1"
                    >
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
