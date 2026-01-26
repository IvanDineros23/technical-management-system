@extends('layouts.dashboard')

@section('title', 'Section Title')
@section('page-title', 'Section Title')
@section('page-subtitle', 'Manage section resources')

@section('sidebar-nav')
    <!-- Include the same sidebar navigation -->
    @include('admin.sidebar-nav')
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Section Title</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Brief description of this section</p>
        </div>
        <a href="#" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
            + New Item
        </a>
    </div>

    <!-- Placeholder Content Card -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-lg font-semibold text-gray-900 dark:text-white">Section Under Development</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">This section is being built and will be available soon</p>
        </div>
    </div>
</div>
@endsection
