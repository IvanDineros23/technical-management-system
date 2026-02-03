@extends('layouts.dashboard')

@section('title', 'Accounting & Billing')

@section('page-title', 'Accounting & Billing')

@section('page-subtitle', 'Manage invoices, payments and financial records')

@section('sidebar-nav')
    @if(auth()->user()->role->slug === 'admin')
        @include('admin.sidebar-nav')
    @else
        @include('accounting.partials.sidebar')
    @endif
@endsection

@section('content')
<div class="space-y-6" x-data="{ 
    showAdd: false, 
    showView: false, 
    showDelete: false, 
    selectedInvoice: null, 
    formData: { 
        customer_id: '', 
        subtotal: 0, 
        issue_date: '', 
        due_date: '', 
        description: '' 
    },
    viewInvoice(invoice) {
        this.selectedInvoice = invoice;
        this.showView = true;
    },
    confirmDelete(invoice) {
        this.selectedInvoice = invoice;
        this.showDelete = true;
    },
    deleteInvoice() {
        document.getElementById('delete-form-' + this.selectedInvoice.id).submit();
    }
}">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Accounting & Billing</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Financial management and billing records</p>
        </div>
        <button @click="showAdd=true" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
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
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">₱{{ number_format($stats['totalRevenue'] ?? 0, 2) }}</p>
            <p class="text-xs text-green-600 dark:text-green-400 mt-1">All time</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Pending Invoices</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">₱{{ number_format($stats['pendingAmount'] ?? 0, 2) }}</p>
            <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">{{ $stats['pendingCount'] ?? 0 }} invoices outstanding</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Paid This Month</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">₱{{ number_format($stats['paidThisMonth'] ?? 0, 2) }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $stats['paidCount'] ?? 0 }} payments received</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Overdue Payments</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">₱{{ number_format($stats['overdueAmount'] ?? 0, 2) }}</p>
            <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $stats['overdueCount'] ?? 0 }} invoices overdue</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" action="{{ auth()->user()->role->slug === 'admin' ? route('admin.accounting.index') : route('accounting.invoices') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice # or Customer..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Filter</button>
            </div>
        </form>
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
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $invoice->customer->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">₱{{ number_format($invoice->total, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $invoice->issue_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $invoice->due_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            @if($invoice->payment_status === 'paid')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Paid</span>
                            @elseif($invoice->payment_status === 'overdue')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">Overdue</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button @click="viewInvoice({
                                ...@js($invoice->toArray()),
                                customer: @js($invoice->customer),
                                issue_date_formatted: '{{ $invoice->issue_date->format('M d, Y') }}',
                                due_date_formatted: '{{ $invoice->due_date->format('M d, Y') }}'
                            })" class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400 mr-3">View</button>
                            @if($invoice->payment_status !== 'paid')
                            <form method="POST" action="{{ auth()->user()->role->slug === 'admin' ? route('admin.invoices.pay', $invoice) : route('accounting.invoices.pay', $invoice) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-600 hover:text-green-900 dark:hover:text-green-400 mr-3">Mark Paid</button>
                            </form>
                            @endif
                            <button @click="confirmDelete(@js($invoice))" class="text-red-600 hover:text-red-900 dark:hover:text-red-400">Delete</button>
                            <form id="delete-form-{{ $invoice->id }}" method="POST" action="{{ auth()->user()->role->slug === 'admin' ? route('admin.invoices.destroy', $invoice) : route('accounting.invoices.destroy', $invoice) }}" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No invoices found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- Add Invoice Modal -->
    <div x-show="showAdd" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" @keydown.escape.window="showAdd=false">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAdd" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showAdd=false" class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showAdd" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ auth()->user()->role->slug === 'admin' ? route('admin.invoices.store') : route('accounting.invoices.store') }}" class="p-6 space-y-4">
                    @csrf
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Create New Invoice</h3>
                        <button type="button" @click="showAdd=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">✕</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer *</label>
                            <select name="customer_id" x-model="formData.customer_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                                <option value="">Select Customer</option>
                                @foreach(\App\Models\Customer::orderBy('name')->get() as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subtotal (₱) *</label>
                            <input type="number" step="0.01" name="subtotal" min="0" x-model="formData.subtotal" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Issue Date *</label>
                            <input type="date" name="issue_date" x-model="formData.issue_date" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date *</label>
                            <input type="date" name="due_date" x-model="formData.due_date" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea name="description" rows="3" x-model="formData.description" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showAdd=false" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Create Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Invoice Modal -->
    <div x-show="showView" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" @keydown.escape.window="showView=false">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showView" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showView=false" class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showView" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Invoice Details</h3>
                        <button type="button" @click="showView=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-2xl leading-none">✕</button>
                    </div>
                    <template x-if="selectedInvoice">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Invoice Number</p>
                                    <p class="text-base font-semibold text-gray-900 dark:text-white mt-1" x-text="selectedInvoice.invoice_number"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                                    <div class="mt-1">
                                        <span x-show="selectedInvoice.payment_status === 'paid'" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Paid</span>
                                        <span x-show="selectedInvoice.payment_status === 'overdue'" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">Overdue</span>
                                        <span x-show="selectedInvoice.payment_status === 'pending'" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Pending</span>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer</p>
                                        <p class="text-base text-gray-900 dark:text-white mt-1" x-text="selectedInvoice.customer ? selectedInvoice.customer.name : 'N/A'"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Issue Date</p>
                                        <p class="text-base text-gray-900 dark:text-white mt-1" x-text="selectedInvoice.issue_date_formatted"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Due Date</p>
                                        <p class="text-base text-gray-900 dark:text-white mt-1" x-text="selectedInvoice.due_date_formatted"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Terms</p>
                                        <p class="text-base text-gray-900 dark:text-white mt-1" x-text="selectedInvoice.payment_terms || 'N/A'"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal</span>
                                        <span class="text-sm text-gray-900 dark:text-white" x-text="'₱' + parseFloat(selectedInvoice.subtotal).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Tax (<span x-text="selectedInvoice.tax_rate"></span>%)</span>
                                        <span class="text-sm text-gray-900 dark:text-white" x-text="'₱' + parseFloat(selectedInvoice.tax_amount).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </div>
                                    <div class="flex justify-between" x-show="selectedInvoice.discount > 0">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Discount</span>
                                        <span class="text-sm text-red-600 dark:text-red-400" x-text="'-₱' + parseFloat(selectedInvoice.discount).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                        <span class="text-base font-bold text-gray-900 dark:text-white">Total</span>
                                        <span class="text-base font-bold text-gray-900 dark:text-white" x-text="'₱' + parseFloat(selectedInvoice.total).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Amount Paid</span>
                                        <span class="text-sm text-green-600 dark:text-green-400" x-text="'₱' + parseFloat(selectedInvoice.amount_paid).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Balance</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="'₱' + parseFloat(selectedInvoice.balance).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4" x-show="selectedInvoice.notes">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</p>
                                <p class="text-sm text-gray-900 dark:text-white" x-text="selectedInvoice.notes"></p>
                            </div>
                        </div>
                    </template>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showView=false" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDelete" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" @keydown.escape.window="showDelete=false">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDelete" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showDelete=false" class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showDelete" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 dark:bg-red-900/30 rounded-full">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white text-center mb-2">Delete Invoice</h3>
                    <template x-if="selectedInvoice">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-4">
                                Are you sure you want to delete invoice <span class="font-semibold text-gray-900 dark:text-white" x-text="selectedInvoice.invoice_number"></span>? This action cannot be undone.
                            </p>
                        </div>
                    </template>
                    <div class="flex justify-center gap-3 mt-6">
                        <button type="button" @click="showDelete=false" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                        <button type="button" @click="deleteInvoice()" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">Delete Invoice</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
