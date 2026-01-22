<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Gemarc Enterprises Inc</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/0fae4580-eff0-4ee7-98e2-8ab80dd542cf-removebg-preview.png') }}">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        .toast-enter {
            animation: slideIn 0.3s ease-out;
        }
        .toast-exit {
            animation: slideOut 0.3s ease-in;
        }
    </style>
    @yield('head')
</head>
<body class="h-full bg-gray-100 dark:bg-gray-900 overflow-hidden transition-colors" x-data="{ 
    toasts: [],
    addToast(message, type = 'success') {
        const id = Date.now();
        this.toasts.push({ id, message, type });
        setTimeout(() => this.removeToast(id), 5000);
    },
    removeToast(id) {
        const index = this.toasts.findIndex(t => t.id === id);
        if (index > -1) {
            this.toasts.splice(index, 1);
        }
    }
}" @toast.window="addToast($event.detail.message, $event.detail.type)">
    
    <!-- Toast Container -->
    <div class="fixed top-4 right-4 z-[9999] space-y-3 max-w-sm pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="toast-enter pointer-events-auto"
                 x-show="true"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform translate-x-full"
                 :class="{
                     'bg-emerald-500 dark:bg-emerald-600': toast.type === 'success',
                     'bg-rose-500 dark:bg-rose-600': toast.type === 'error',
                     'bg-amber-500 dark:bg-amber-600': toast.type === 'warning',
                     'bg-blue-500 dark:bg-blue-600': toast.type === 'info'
                 }"
                 class="rounded-lg shadow-lg p-4 flex items-center gap-3 min-w-[320px]">
                <div class="flex-shrink-0">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </template>
                </div>
                <div class="flex-1 mr-2">
                    <p class="text-sm font-semibold text-white leading-tight" x-text="toast.message"></p>
                </div>
                <button @click="removeToast(toast.id)" class="flex-shrink-0 text-white/80 hover:text-white transition-colors ml-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>
    
    <div class="flex h-full">
        <!-- Sidebar -->
        <aside class="w-64 flex-shrink-0 bg-gradient-to-b from-blue-100 to-blue-50 dark:from-gray-800 dark:to-gray-900 rounded-r-3xl shadow-lg flex flex-col overflow-hidden border-r border-blue-200 dark:border-gray-700">
            <!-- Logo -->
            <div class="p-5 flex-shrink-0">
                <div class="flex flex-col items-center gap-3">
                    <img src="{{ asset('assets/0fae4580-eff0-4ee7-98e2-8ab80dd542cf-removebg-preview.png') }}" alt="Gemarc Logo" class="w-20 h-20 object-contain">
                    <h1 class="text-lg font-bold text-gray-800 dark:text-white text-center">Gemarc - TMS</h1>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto px-4 space-y-1 min-h-0">
                @yield('sidebar-nav')
            </nav>

            <!-- Theme Toggle -->
            <div class="px-4 py-3 flex-shrink-0">
                <div class="flex gap-2">
                    <button id="theme-light-btn" onclick="toggleLightMode()" class="flex-1 flex items-center justify-center gap-1 px-2 py-2 bg-blue-500 text-white rounded-xl text-xs font-medium hover:bg-blue-600 transition-colors dark:bg-white dark:text-gray-600 dark:hover:bg-gray-100">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Light
                    </button>
                    <button id="theme-dark-btn" onclick="toggleDarkMode()" class="flex-1 flex items-center justify-center gap-1 px-2 py-2 bg-white text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-100 transition-colors dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        Dark
                    </button>
                </div>
            </div>

            <!-- User Info -->
            <div class="px-4 py-3 border-t border-blue-200 dark:border-gray-700 flex-shrink-0">
                <a href="{{ route('profile.show') }}" class="flex items-center gap-2 mb-2 p-2 rounded-lg hover:bg-blue-200 dark:hover:bg-gray-700 transition-colors cursor-pointer group">
                    <div class="w-8 h-8 rounded-full bg-blue-500 dark:bg-blue-600 flex items-center justify-center text-xs font-semibold text-white group-hover:ring-2 group-hover:ring-blue-300 dark:group-hover:ring-blue-400 transition-all">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-800 dark:text-white truncate group-hover:text-blue-700 dark:group-hover:text-blue-300">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate group-hover:text-gray-700 dark:group-hover:text-gray-300">{{ auth()->user()->role->name ?? 'User' }}</p>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-2 py-1.5 text-xs text-gray-600 dark:text-gray-300 rounded-lg hover:bg-blue-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-0 overflow-hidden bg-gray-50 dark:bg-gray-800">            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto px-8 py-6 min-h-0">
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Session Flash Messages to Toast -->
    @if(session('status') || session('error') || session('warning') || session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('status'))
                window.dispatchEvent(new CustomEvent('toast', { 
                    detail: { message: '{{ session('status') }}', type: 'success' } 
                }));
            @endif
            @if(session('error'))
                window.dispatchEvent(new CustomEvent('toast', { 
                    detail: { message: '{{ session('error') }}', type: 'error' } 
                }));
            @endif
            @if(session('warning'))
                window.dispatchEvent(new CustomEvent('toast', { 
                    detail: { message: '{{ session('warning') }}', type: 'warning' } 
                }));
            @endif
            @if(session('info'))
                window.dispatchEvent(new CustomEvent('toast', { 
                    detail: { message: '{{ session('info') }}', type: 'info' } 
                }));
            @endif
        });
    </script>
    @endif
</body>
</html>