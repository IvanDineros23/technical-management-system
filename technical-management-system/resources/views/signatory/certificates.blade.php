@extends('layouts.dashboard')

@section('title', 'Certificates for Approval')
@section('page-title', 'Certificates for Approval')
@section('page-subtitle', 'Review and approve certificates for release')

@section('sidebar-nav')
    @include('signatory.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">🔍 Filters</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Certificates Table -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    Certificates for Approval
                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ $certificates->total() }} total)</span>
                </h3>
            </div>

            @if($certificates->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 dark:border-gray-700">
                            <tr class="text-center text-xs">
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400 text-center">Certificate #</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400 text-center">WO Number</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400 text-center">Customer</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400 text-center">Signed By</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400 text-center">Signed Date</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400 text-center">Status</th>
                                <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($certificates as $certificate)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="py-3 text-sm font-semibold text-gray-900 dark:text-white text-center">
                                        {{ $certificate->certificate_number }}
                                    </td>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300 text-center">
                                        {{ $certificate->jobOrder?->job_order_number ?? $certificate->calibration?->assignment?->jobOrder?->job_order_number ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300 text-center">
                                        {{ $certificate->jobOrder?->customer?->name ?? $certificate->calibration?->assignment?->jobOrder?->customer?->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300 text-center">
                                        {{ $certificate->signedBy?->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300 text-center">
                                        {{ $certificate->signed_at ? $certificate->signed_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') : 'Not Signed' }}
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($certificate->signed_by)
                                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">
                                                ✓ Approved
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">
                                                ⏳ Pending Approval
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($certificate->signed_by)
                                            <a href="{{ route('signatory.certificate.preview', $certificate) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-medium">
                                                Preview →
                                            </a>
                                        @else
                                            <div class="flex gap-2 justify-center">
                                                <a href="{{ route('signatory.certificate.preview', $certificate) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-medium">
                                                    Review
                                                </a>
                                                <button onclick="approveConfirm('{{ $certificate->id }}', '{{ $certificate->certificate_number }}')" class="text-emerald-600 dark:text-emerald-400 hover:underline text-xs font-medium">
                                                    ✓ Approve
                                                </button>
                                                <button onclick="rejectConfirm('{{ $certificate->id }}', '{{ $certificate->certificate_number }}')" class="text-rose-600 dark:text-rose-400 hover:underline text-xs font-medium">
                                                    ✗ Reject
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $certificates->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No certificates for approval</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All certificates have been reviewed</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function approveConfirm(certId, certNumber) {
            if (confirm(`Approve certificate ${certNumber}?\n\nThis will sign and release the certificate for customer delivery.`)) {
                fetch(`/signatory/certificates/${certId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to approve certificate');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving the certificate');
                });
            }
        }

        function rejectConfirm(certId, certNumber) {
            const reason = prompt(`Reject certificate ${certNumber}?\n\nPlease provide a reason for rejection:`);
            if (reason !== null && reason.trim() !== '') {
                fetch(`/signatory/certificates/${certId}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ reason: reason })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to reject certificate');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while rejecting the certificate');
                });
            }
        }
    </script>
@endsection
