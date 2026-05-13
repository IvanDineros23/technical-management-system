<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="{
        toasts: [],
        addToast(message, type = 'success') {
            const id = Date.now() + Math.random();
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
        <div class="fixed left-3 right-3 bottom-3 sm:left-auto sm:right-6 sm:bottom-6 z-[9999] flex flex-col gap-2 sm:gap-3 pointer-events-none" style="padding-bottom: max(0px, env(safe-area-inset-bottom));">
            <template x-for="toast in toasts" :key="toast.id">
                <div class="pointer-events-auto w-full max-w-[calc(100vw-1.5rem)] sm:max-w-none sm:w-[400px] rounded-lg shadow-xl flex items-start sm:items-center px-3 sm:px-5 py-3 sm:py-4"
                     x-show="true"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform translate-y-4"
                     :class="{
                         'bg-emerald-500': toast.type === 'success',
                         'bg-red-500': toast.type === 'error',
                         'bg-orange-400': toast.type === 'warning',
                         'bg-fuchsia-600': toast.type === 'info'
                     }">
                    <div class="flex-shrink-0 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/20 flex items-center justify-center mr-3 sm:mr-4 mt-0.5 sm:mt-0">
                        <template x-if="toast.type === 'success'">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'error'">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'warning'">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'info'">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                    </div>
                    <span class="flex-1 min-w-0 text-white font-semibold text-sm sm:text-base leading-snug break-words" x-text="toast.message"></span>
                    <button @click="removeToast(toast.id)" class="flex-shrink-0 ml-2 sm:ml-4 p-1 -m-1 text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @if(session('status') || session('error') || session('warning') || session('info'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    @if(session('status'))
                        @php
                            $statusToastMessage = match (session('status')) {
                                'verification-link-sent' => 'The verification link has been sent to your email address.',
                                'profile-updated' => 'Profile updated successfully.',
                                'password-updated' => 'Password updated successfully.',
                                default => session('status'),
                            };
                        @endphp
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: @js($statusToastMessage), type: 'success' }
                        }));
                    @endif
                    @if(session('error'))
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: @js(session('error')), type: 'error' }
                        }));
                    @endif
                    @if(session('warning'))
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: @js(session('warning')), type: 'warning' }
                        }));
                    @endif
                    @if(session('info'))
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: @js(session('info')), type: 'info' }
                        }));
                    @endif
                });
            </script>
        @endif
    </body>
</html>
