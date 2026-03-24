<a href="{{ route('accounting.dashboard') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.dashboard') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    Dashboard
</a>

<a href="{{ route('accounting.approvals') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.approvals') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
    </svg>
    <span class="flex-1">Approvals</span>
    @php $pendingCount = \App\Models\JobOrder::whereIn('status', ['for_accounting_approval', 'pending_certification'])->count(); @endphp
    @if($pendingCount > 0)
        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold rounded-full
            {{ request()->routeIs('accounting.approvals') ? 'bg-white text-blue-600' : 'bg-amber-500 text-white' }}">
            {{ $pendingCount }}
        </span>
    @endif
</a>

<a href="{{ route('accounting.certifications') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.certifications*') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span class="flex-1">Certifications</span>
    @php $certPendingCount = \App\Models\JobOrder::where('status', 'pending_certification')->count(); @endphp
    @if($certPendingCount > 0)
        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold rounded-full
            {{ request()->routeIs('accounting.certifications*') ? 'bg-white text-blue-600' : 'bg-blue-500 text-white' }}">
            {{ $certPendingCount }}
        </span>
    @endif
</a>

<a href="{{ route('accounting.timeline') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounting.timeline') ? 'bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"/>
    </svg>
    Timeline
</a>
