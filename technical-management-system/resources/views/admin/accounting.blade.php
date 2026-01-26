@extends('layouts.dashboard')

@section('title', 'Accounting & Billing')

@section('page-title', 'Accounting & Billing')

@section('page-subtitle', 'Manage invoices, payments and financial records')

@section('sidebar-nav')
    @include('admin.sidebar-nav')
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Accounting & Billing</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Financial management and billing records</p>
        </div>
        <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Invoice
        </button>
    </div>

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">₱285,400</p>
            <p class="text-xs text-green-600 dark:text-green-400 mt-1">↑ 12% this month</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Pending Invoices</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">₱45,200</p>
            <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">8 invoices outstanding</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Paid This Month</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">₱152,000</p>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">12 payments received</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Overdue Payments</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">₱28,500</p>
            <p class="text-xs text-red-600 dark:text-red-400 mt-1">3 invoices overdue</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <div class="flex gap-8">
            <button class="px-4 py-3 font-medium text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400">Invoices</button>
            <button class="px-4 py-3 font-medium text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white">Payments</button>
            <button class="px-4 py-3 font-medium text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white">Reports</button>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Invoice #</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Customer</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Amount</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Due Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">INV-2024-001</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">ABC Corporation</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">₱12,500</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">Jan 5, 2024</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">Jan 20, 2024</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Paid</span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400 mr-3">View</button>
                            <button class="text-gray-600 hover:text-gray-900 dark:hover:text-white">Download</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
