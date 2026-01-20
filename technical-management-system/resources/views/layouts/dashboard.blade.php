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
    <div class="fixed top-4 right-4 z-[9999] space-y-2" style="pointer-events: none;">
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
                 class="min-w-[300px] max-w-md rounded-lg shadow-lg p-4 flex items-start gap-3">
                <div class="flex-shrink-0">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-white" x-text="toast.message"></p>
                </div>
                <button @click="removeToast(toast.id)" class="flex-shrink-0 text-white hover:text-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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