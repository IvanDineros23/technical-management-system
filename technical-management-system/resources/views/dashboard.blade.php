<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-lg border border-gray-200 p-4">
                            <p class="text-xs text-gray-500">Job Orders</p>
                            <p class="text-2xl font-semibold">{{ $stats['jobOrders'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-4">
                            <p class="text-xs text-gray-500">Customers</p>
                            <p class="text-2xl font-semibold">{{ $stats['customers'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-4">
                            <p class="text-xs text-gray-500">Users</p>
                            <p class="text-2xl font-semibold">{{ $stats['users'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
