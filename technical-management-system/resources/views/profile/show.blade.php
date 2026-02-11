@extends('layouts.dashboard')

@section('title', 'My Profile')

@section('page-title', 'My Profile')
@section('page-subtitle', 'View and manage your account information')

@section('head')
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script>
        function profilePage() {
            return {
                showEditModal: false,
                showPasswordModal: false,
                showCustomerModal: false,
                toast: {
                    show: false,
                    message: '',
                    type: 'success' // 'success' or 'error'
                },
                showToast(message, type = 'success') {
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.show = true;
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 4000);
                },
                openEditModal() {
                    this.showEditModal = true;
                    document.body.style.overflow = 'hidden';
                },
                closeEditModal() {
                    this.showEditModal = false;
                    setTimeout(() => {
                        document.body.style.overflow = 'auto';
                    }, 300);
                },
                openPasswordModal() {
                    this.showPasswordModal = true;
                    document.body.style.overflow = 'hidden';
                },
                closePasswordModal() {
                    this.showPasswordModal = false;
                    setTimeout(() => {
                        document.body.style.overflow = 'auto';
                    }, 300);
                },
                openCustomerModal() {
                    this.showCustomerModal = true;
                    document.body.style.overflow = 'hidden';
                },
                closeCustomerModal() {
                    this.showCustomerModal = false;
                    setTimeout(() => {
                        document.body.style.overflow = 'auto';
                    }, 300);
                },
                handleEscKey(event) {
                    if (event.key === 'Escape') {
                        this.closeEditModal();
                        this.closePasswordModal();
                        this.closeCustomerModal();
                    }
                },
                async submitEditForm(event) {
                    event.preventDefault();
                    const form = event.target;
                    const formData = new FormData(form);
                    
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        if (response.ok) {
                            setTimeout(() => {
                                this.showToast('Profile updated successfully!', 'success');
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            }, 600);
                        } else {
                            setTimeout(() => {
                                this.showToast('Error updating profile. Please try again.', 'error');
                            }, 600);
                        }
                    } catch (error) {
                        setTimeout(() => {
                            this.showToast('An error occurred. Please try again.', 'error');
                        }, 600);
                    }
                },
                async submitPasswordForm(event) {
                    event.preventDefault();
                    const form = event.target;
                    const formData = new FormData(form);
                    
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        if (response.ok) {
                            setTimeout(() => {
                                this.showToast('Password changed successfully!', 'success');
                                form.reset();
                                setTimeout(() => {
                                    this.closePasswordModal();
                                }, 1500);
                            }, 600);
                        } else {
                            setTimeout(() => {
                                const message = response.statusText || 'Error changing password. Please try again.';
                                this.showToast(message, 'error');
                            }, 600);
                        }
                    } catch (error) {
                        setTimeout(() => {
                            this.showToast('An error occurred. Please try again.', 'error');
                        }, 600);
                    }
                },
                async submitCustomerForm(event) {
                    event.preventDefault();
                    const form = event.target;
                    const formData = new FormData(form);
                    
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        if (response.ok) {
                            setTimeout(() => {
                                this.showToast('Customer details updated successfully!', 'success');
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            }, 600);
                        } else {
                            setTimeout(() => {
                                this.showToast('Error updating customer details. Please try again.', 'error');
                            }, 600);
                        }
                    } catch (error) {
                        setTimeout(() => {
                            this.showToast('An error occurred. Please try again.', 'error');
                        }, 600);
                    }
                }
            }
        }

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
@endsection

@section('sidebar-nav')
    <a href="{{ route('dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Back to Dashboard
    </a>
@endsection

@section('content')
@php
    $isCustomer = ($user->role?->slug ?? '') === 'customer';
    $customerProfile = $user->customer;
    $missingCustomerFields = [];
    if ($isCustomer) {
        $requiredCustomerFields = [
            'email' => 'Email',
            'phone' => 'Phone',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'Province/State',
            'postal_code' => 'Postal Code',
            'contact_person' => 'Contact Person',
            'tax_id' => 'Tax ID',
        ];

        if (!$customerProfile) {
            $missingCustomerFields = array_values($requiredCustomerFields);
        } else {
            foreach ($requiredCustomerFields as $field => $label) {
                if (empty($customerProfile->{$field})) {
                    $missingCustomerFields[] = $label;
                }
            }
        }
    }
@endphp

<div class="max-w-5xl mx-auto" x-data="profilePage()" @keydown.window="handleEscKey($event)">
    <!-- Toast Notification -->
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         style="display: none"
         class="fixed top-4 right-4 z-[9999] max-w-sm">
        <div :class="[
            'px-4 py-3 rounded-lg shadow-lg border flex items-center gap-3',
            toast.type === 'success' 
                ? 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-700 text-green-800 dark:text-green-200'
                : 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-700 text-red-800 dark:text-red-200'
        ]">
            <svg x-show="toast.type === 'success'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <svg x-show="toast.type === 'error'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span class="text-sm font-medium" x-text="toast.message"></span>
        </div>
    </div>

    <!-- Profile Header Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg mb-6 overflow-hidden border border-gray-200 dark:border-gray-700">
        <!-- Decorative Header -->
        <div class="h-24 bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-700 dark:to-blue-800"></div>
        
        <!-- Profile Info -->
        <div class="px-8 py-6 -mt-12 relative z-10">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between md:gap-6">
                <div class="flex flex-col md:flex-row md:items-end md:gap-6 flex-1">
                    <!-- Avatar -->
                    <div class="flex-shrink-0 mb-4 md:mb-0">
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 dark:from-blue-600 dark:to-blue-800 flex items-center justify-center text-4xl font-bold text-white shadow-lg border-4 border-white dark:border-gray-800">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    </div>
                    
                    <!-- Name and Role -->
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ $user->name }}
                        </h1>
                        <p class="text-lg text-blue-600 dark:text-blue-400 font-medium mb-2">
                            {{ $user->role->name ?? 'User' }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Member since {{ $user->created_at->format('F Y') }}
                        </p>
                    </div>
                </div>

                <!-- Edit Profile Button -->
                <button @click="openEditModal()"
                        class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors whitespace-nowrap mt-4 md:mt-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </button>
            </div>
        </div>
    </div>

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Left Column: Personal Info and Other Details -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- PERSONAL INFO SECTION -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Personal Information
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Email -->
                    <div class="flex items-start justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Email Address
                                </p>
                                <p class="text-sm text-gray-900 dark:text-white break-all">
                                    {{ $user->email }}
                                </p>
                            </div>
                        </div>
                        @if($user->email_verified_at)
                            <span class="px-2.5 py-0.5 rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-semibold flex-shrink-0">
                                ✓ Verified
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs font-semibold flex-shrink-0">
                                ⚠ Unverified
                            </span>
                        @endif
                    </div>

                    <!-- Department -->
                    @if(!$isCustomer)
                    <div class="flex items-start justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.5m0 0H9m3.5 0H9"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Department
                                </p>
                                <p class="text-sm text-gray-900 dark:text-white font-medium">
                                    {{ $user->department ?? 'Not assigned' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Role -->
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    User Role
                                </p>
                                <p class="text-sm text-gray-900 dark:text-white font-medium">
                                    {{ $user->role->name ?? 'Not assigned' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHANGE PASSWORD SECTION -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-amber-50 to-orange-100 dark:from-amber-900/30 dark:to-orange-800/30 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Security
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Manage your password and account security settings.
                    </p>
                    <button @click="openPasswordModal()"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Change Password
                    </button>
                </div>
            </div>

            @if($isCustomer)
                <!-- CUSTOMER PROFILE SECTION -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-100 dark:from-indigo-900/30 dark:to-blue-800/30 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 00-10 0v2a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2v-8a2 2 0 00-2-2zm-5 8a2 2 0 110-4 2 2 0 010 4z"/>
                            </svg>
                            Customer Profile
                        </h2>
                        @if($customerProfile)
                        <button @click="openCustomerModal()" 
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Edit Details
                        </button>
                        @endif
                    </div>
                    <div class="p-6">
                        @if(!$customerProfile)
                            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900">
                                <p class="text-sm font-semibold">No customer profile linked yet.</p>
                                <p class="text-xs mt-1">Please contact marketing to complete your customer record.</p>
                            </div>
                        @else
                            @if(count($missingCustomerFields))
                                <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900">
                                    <p class="text-sm font-semibold">Customer profile is incomplete.</p>
                                    <p class="text-xs mt-1">Missing: {{ implode(', ', $missingCustomerFields) }}</p>
                                    <p class="text-xs mt-2">Click the <strong>Edit Details</strong> button above to update your information.</p>
                                </div>
                            @endif
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Company Name</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $customerProfile->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Business Name</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $customerProfile->business_name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact Person</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $customerProfile->contact_person ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Industry</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $customerProfile->industry_type ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $customerProfile->phone ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $customerProfile->email ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Address</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $customerProfile->address ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">City</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $customerProfile->city ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Province/State</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $customerProfile->state ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Postal Code</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $customerProfile->postal_code ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Country</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $customerProfile->country ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tax ID</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $customerProfile->tax_id ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- WORK SUMMARY SECTION -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-100 dark:from-green-900/30 dark:to-emerald-800/30 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Work Summary
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($assignmentStats && $assignmentStats->total > 0)
                            <div class="grid grid-cols-3 gap-4">
                                <!-- Total Assignments -->
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-center border border-blue-200 dark:border-blue-800">
                                    <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">
                                        Total Assignments
                                    </p>
                                    <p class="text-3xl font-bold text-blue-700 dark:text-blue-300">
                                        {{ $assignmentStats->total ?? 0 }}
                                    </p>
                                </div>

                                <!-- Completed -->
                                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-center border border-green-200 dark:border-green-800">
                                    <p class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider mb-1">
                                        Completed
                                    </p>
                                    <p class="text-3xl font-bold text-green-700 dark:text-green-300">
                                        {{ $assignmentStats->completed ?? 0 }}
                                    </p>
                                </div>

                                <!-- Active/In Progress -->
                                <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4 text-center border border-amber-200 dark:border-amber-800">
                                    <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1">
                                        Active
                                    </p>
                                    <p class="text-3xl font-bold text-amber-700 dark:text-amber-300">
                                        {{ $assignmentStats->active ?? 0 }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-gray-600 dark:text-gray-400">No assignments yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Activity and Last Login -->
        <div class="space-y-6">
            
            <!-- LAST LOGIN SECTION -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-rose-50 to-pink-100 dark:from-rose-900/30 dark:to-pink-800/30 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Last Login
                    </h2>
                </div>
                <div class="p-6">
                    @if($lastLogin)
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">
                                    Last Activity
                                </p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::createFromTimestamp($lastLogin->last_activity)->setTimezone('Asia/Manila')->format('M d, Y • h:i A') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    ({{ \Carbon\Carbon::createFromTimestamp($lastLogin->last_activity)->diffForHumans() }})
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-gray-600 dark:text-gray-400">No login data available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ACTIVITY SUMMARY SECTION -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-cyan-50 to-blue-100 dark:from-cyan-900/30 dark:to-blue-800/30 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Recent Activity
                    </h2>
                </div>
                <div class="p-6">
                    @if($recentActivity->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentActivity as $activity)
                                <div class="pb-3 border-b border-gray-200 dark:border-gray-700 last:pb-0 last:border-b-0">
                                    <div class="flex items-start gap-3">
                                        <div class="w-2 h-2 rounded-full bg-blue-500 dark:bg-blue-400 mt-2 flex-shrink-0"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                <span class="capitalize font-semibold">{{ $activity->action }}</span>
                                                <span class="text-gray-600 dark:text-gray-400">on</span>
                                                <span class="text-gray-700 dark:text-gray-300">{{ $activity->model_type }}</span>
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm text-gray-600 dark:text-gray-400">No activity recorded</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Member Since Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Member Since
                        </p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $user->created_at->format('M d, Y') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            ({{ $user->created_at->diffForHumans() }})
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT PROFILE MODAL -->
    <div x-show="showEditModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak
         @click.self="closeEditModal()"
         class="fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
        <div x-show="showEditModal"
             x-transition:enter="transition ease-out duration-300 delay-75"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             @click.stop
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full border border-gray-200 dark:border-gray-700 overflow-hidden">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between rounded-t-2xl">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Profile</h3>
                <button @click="closeEditModal()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <form method="POST" action="{{ route('profile.update') }}" class="p-6 space-y-4" @submit="submitEditForm">
                @csrf
                @method('PATCH')

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ $user->name }}" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Account Email</label>
                    <input type="email" id="email" name="email" value="{{ $user->email }}" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="closeEditModal()"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-900 dark:text-white font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CHANGE PASSWORD MODAL -->
    <div x-show="showPasswordModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak
         @click.self="closePasswordModal()"
         class="fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
        <div x-show="showPasswordModal"
             x-transition:enter="transition ease-out duration-300 delay-75"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             @click.stop
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full border border-gray-200 dark:border-gray-700 overflow-hidden">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-amber-50 to-orange-100 dark:from-amber-900/30 dark:to-orange-800/30 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between rounded-t-2xl">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Change Password</h3>
                <button @click="closePasswordModal()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <form method="POST" action="{{ route('password.update') }}" class="p-6 space-y-4" @submit="submitPasswordForm">
                @csrf
                @method('PUT')

                <!-- Current Password Field -->
                <div>
                    <label for="current_password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password" required
                               class="w-full px-4 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-colors">
                        <button type="button" onclick="togglePassword('current_password', 'eyeCurrent')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                            <svg id="eyeCurrent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- New Password Field -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-colors">
                        <button type="button" onclick="togglePassword('password', 'eyePassword')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                            <svg id="eyePassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-4 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-colors">
                        <button type="button" onclick="togglePassword('password_confirmation', 'eyeConfirm')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                            <svg id="eyeConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="closePasswordModal()"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-900 dark:text-white font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($isCustomer && $customerProfile)
    <!-- EDIT CUSTOMER DETAILS MODAL -->
    <div x-show="showCustomerModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak
         @click.self="closeCustomerModal()"
         class="fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
        <div x-show="showCustomerModal"
             x-transition:enter="transition ease-out duration-300 delay-75"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             @click.stop
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-3xl w-full border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-indigo-50 to-blue-100 dark:from-indigo-900/30 dark:to-blue-800/30 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between rounded-t-2xl flex-shrink-0">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Customer Details</h3>
                <button @click="closeCustomerModal()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <form method="POST" action="{{ route('profile.update') }}" class="p-6 space-y-4 overflow-y-auto flex-1" @submit="submitCustomerForm">
                @csrf
                @method('PATCH')

                <p class="text-sm text-gray-500 dark:text-gray-400">Update your business and contact information below.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="cust_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Company Name</label>
                        <input type="text" id="cust_name" name="customer_name" value="{{ $customerProfile->name ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                        <label for="cust_business_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Business Name</label>
                        <input type="text" id="cust_business_name" name="customer_business_name" value="{{ $customerProfile->business_name ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                        <label for="cust_contact_person" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Contact Person</label>
                        <input type="text" id="cust_contact_person" name="customer_contact_person" value="{{ $customerProfile->contact_person ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                        <label for="cust_industry_type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Industry</label>
                        <input type="text" id="cust_industry_type" name="customer_industry_type" value="{{ $customerProfile->industry_type ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                        <label for="cust_phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                        <input type="text" id="cust_phone" name="customer_phone" value="{{ $customerProfile->phone ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                        <label for="cust_email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Customer Email</label>
                        <input type="email" id="cust_email" name="customer_email" value="{{ $customerProfile->email ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label for="cust_address" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Address</label>
                        <input type="text" id="cust_address" name="customer_address" value="{{ $customerProfile->address ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                        <label for="cust_city" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">City</label>
                        <input type="text" id="cust_city" name="customer_city" value="{{ $customerProfile->city ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                        <label for="cust_state" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Province/State</label>
                        <input type="text" id="cust_state" name="customer_state" value="{{ $customerProfile->state ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                        <label for="cust_postal_code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Postal Code</label>
                        <input type="text" id="cust_postal_code" name="customer_postal_code" value="{{ $customerProfile->postal_code ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                        <label for="cust_country" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Country</label>
                        <input type="text" id="cust_country" name="customer_country" value="{{ $customerProfile->country ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                        <label for="cust_tax_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tax ID</label>
                        <input type="text" id="cust_tax_id" name="customer_tax_id" value="{{ $customerProfile->tax_id ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="closeCustomerModal()"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-900 dark:text-white font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                        Save Details
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
