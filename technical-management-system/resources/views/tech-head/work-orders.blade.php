@extends('layouts.dashboard')

@section('title', 'Work Orders')

@section('page-title', 'Work Orders')
@section('page-subtitle', 'Manage and monitor all work orders')

@section('sidebar-nav')
    @include('tech-head.partials.sidebar')
@endsection

@section('content')
    <div x-data="{ 
        showCreate: false, 
        showEdit: false, 
        showAssign: false, 
        showStatus: false,
        showDetails: false,
        showApproval: false,
        showTimeline: false,
        selectedId: null,
        selectedOrder: null,
        signatureData: '',
        init() {
            this.$watch('showCreate', value => this.handleModalState(value));
            this.$watch('showEdit', value => this.handleModalState(value));
            this.$watch('showAssign', value => this.handleModalState(value));
            this.$watch('showStatus', value => this.handleModalState(value));
            this.$watch('showDetails', value => this.handleModalState(value));
            this.$watch('showApproval', value => this.handleModalState(value));
            this.$watch('showTimeline', value => this.handleModalState(value));
        },
        handleModalState(isOpen) {
            if (isOpen) {
                document.body.style.overflow = 'hidden';
                this.setupEscapeKey();
            } else {
                document.body.style.overflow = 'auto';
            }
        },
        setupEscapeKey() {
            const handler = (e) => {
                if (e.key === 'Escape') {
                    this.closeAllModals();
                    document.removeEventListener('keydown', handler);
                }
            };
            document.addEventListener('keydown', handler);
        },
        closeAllModals() {
            this.showCreate = false;
            this.showEdit = false;
            this.showAssign = false;
            this.showStatus = false;
            this.showDetails = false;
            this.showApproval = false;
            this.showTimeline = false;
            this.signatureData = '';
        },
        openDetails(order) {
            this.selectedOrder = order;
            this.showDetails = true;
        },
        openEdit(order) {
            this.selectedOrder = { ...order };
            this.selectedId = order.id;
            this.showEdit = true;
        },
        openAssign(id) {
            this.selectedId = id;
            this.showAssign = true;
        },
        openStatus(id) {
            this.selectedId = id;
            this.showStatus = true;
        },
        openApproval(order) {
            this.selectedOrder = order;
            this.selectedId = order.id;
            this.showApproval = true;
        },
        openTimeline(order) {
            this.selectedOrder = order;
            this.showTimeline = true;
        },
        normalizeStatus(status) {
            return status === 'pending_approval' ? 'for_accounting_approval' : status;
        },
        hasPendingTechHeadReview(order) {
            if (!order || !order.assignments || order.assignments.length === 0) {
                return false;
            }

            return order.assignments.some(assignment => assignment && assignment.report_status === 'pending');
        },
        statusLabel(status, order = null) {
            const normalizedStatus = this.normalizeStatus(status);
            const hasAssignments = Boolean(order && order.assignments && order.assignments.length > 0);
            const hasPendingTechHeadReview = this.hasPendingTechHeadReview(order)
                || Boolean(order && order.has_pending_tech_head_review);

            if (normalizedStatus === 'pending') {
                if (hasAssignments && hasPendingTechHeadReview) {
                    return 'Pending Tech Head Review';
                }

                return 'Waiting for Assignment';
            }

            if (normalizedStatus === 'approved') {
                return 'Ready for Assignment';
            }

            if (normalizedStatus === 'for_accounting_approval') {
                return 'For Accounting Approval';
            }

            if (!normalizedStatus) {
                return 'N/A';
            }

            return normalizedStatus.replace(/_/g, ' ').replace(/\b\w/g, character => character.toUpperCase());
        },
        assignmentTimelineLabel(order) {
            if (!order || !order.assignments || order.assignments.length === 0) {
                return 'Pending assignment';
            }

            return order.assignments
                .map(assignment => assignment.technician)
                .filter(Boolean)
                .join(', ');
        },
        calibrationTimelineLabel(order) {
            const normalizedStatus = this.normalizeStatus(order ? order.status : null);
            const hasAssignments = Boolean(order && order.assignments && order.assignments.length > 0);

            if (!hasAssignments) {
                return 'Not yet started';
            }

            if (['in_progress', 'for_accounting_approval', 'approved', 'completed'].includes(normalizedStatus)) {
                return this.statusLabel(normalizedStatus, order);
            }

            return 'Not yet started';
        },
        reportTimelineLabel(order) {
            const normalizedStatus = this.normalizeStatus(order ? order.status : null);
            const hasAssignments = Boolean(order && order.assignments && order.assignments.length > 0);

            if (!hasAssignments) {
                return 'Awaiting report';
            }

            if (['for_accounting_approval', 'approved', 'completed'].includes(normalizedStatus)) {
                return 'Submitted for accounting approval';
            }

            if (normalizedStatus === 'in_progress') {
                return 'Work in progress';
            }

            return 'Awaiting report';
        },
        approvalTimelineLabel(order) {
            const normalizedStatus = this.normalizeStatus(order ? order.status : null);
            const hasAssignments = Boolean(order && order.assignments && order.assignments.length > 0);

            if (!hasAssignments) {
                return 'Pending approval';
            }

            if (normalizedStatus === 'for_accounting_approval') {
                return 'Awaiting accounting approval';
            }

            if (['approved', 'completed'].includes(normalizedStatus)) {
                return 'Approved & signed';
            }

            return 'Pending approval';
        },
        certificateTimelineLabel(order) {
            if (order && Number(order.certificates_count || 0) > 0) {
                return 'Certificate generated';
            }

            return 'Not yet generated';
        },
        timelineStepState(step, order) {
            const normalizedStatus = this.normalizeStatus(order ? order.status : null);
            const hasAssignments = Boolean(order && order.assignments && order.assignments.length > 0);
            const hasCertificate = Boolean(order && Number(order.certificates_count || 0) > 0);

            if (step === 'created') {
                return 'completed';
            }

            if (step === 'assigned') {
                if (!hasAssignments && ['pending', 'approved'].includes(normalizedStatus)) {
                    return 'current';
                }

                if (!hasAssignments) {
                    return 'pending';
                }

                return normalizedStatus === 'assigned' ? 'current' : 'completed';
            }

            if (step === 'calibration') {
                if (normalizedStatus === 'in_progress') {
                    return 'current';
                }

                return hasAssignments && ['for_accounting_approval', 'approved', 'completed'].includes(normalizedStatus)
                    ? 'completed'
                    : 'pending';
            }

            if (step === 'report') {
                return hasAssignments && ['for_accounting_approval', 'approved', 'completed'].includes(normalizedStatus)
                    ? 'completed'
                    : 'pending';
            }

            if (step === 'approval') {
                if (hasAssignments && normalizedStatus === 'for_accounting_approval') {
                    return 'current';
                }

                return hasAssignments && ['approved', 'completed'].includes(normalizedStatus)
                    ? 'completed'
                    : 'pending';
            }

            if (step === 'certificate_generated') {
                if (hasCertificate) {
                    return normalizedStatus === 'completed' ? 'completed' : 'current';
                }

                return hasAssignments && normalizedStatus === 'approved' ? 'current' : 'pending';
            }

            if (step === 'certificate_released') {
                return normalizedStatus === 'completed' && hasCertificate ? 'current' : 'pending';
            }

            return 'pending';
        },
        timelineStepBadge(step, order) {
            const state = this.timelineStepState(step, order);

            if (state === 'completed') {
                return 'Done';
            }

            if (state === 'current') {
                return 'Current';
            }

            return 'Next';
        },
        currentTimelineStage(order) {
            const normalizedStatus = this.normalizeStatus(order ? order.status : null);
            const hasAssignments = Boolean(order && order.assignments && order.assignments.length > 0);
            const hasCertificate = Boolean(order && Number(order.certificates_count || 0) > 0);

            if (normalizedStatus === 'completed' && hasCertificate) {
                return 'Certificate Ready for Release';
            }

            if (hasCertificate) {
                return 'Certificate Generated';
            }

            if (normalizedStatus === 'approved' && !hasAssignments) {
                return 'Ready for Assignment';
            }

            if (normalizedStatus === 'approved') {
                return 'Approved and Signed';
            }

            if (normalizedStatus === 'for_accounting_approval') {
                return 'Awaiting Approval';
            }

            if (normalizedStatus === 'in_progress') {
                return 'Calibration in Progress';
            }

            if (normalizedStatus === 'assigned' || hasAssignments) {
                return 'Assigned to Technician';
            }

            if (normalizedStatus === 'cancelled') {
                return 'Job Order Cancelled';
            }

            return 'Waiting for Assignment';
        },
        timelineProgress(order) {
            const steps = ['created', 'assigned', 'calibration', 'report', 'approval', 'certificate_generated'];
            const completedSteps = steps.filter(step => this.timelineStepState(step, order) === 'completed').length;
            const currentStep = steps.some(step => this.timelineStepState(step, order) === 'current') ? 1 : 0;

            return Math.round(((completedSteps + currentStep) / steps.length) * 100);
        },
        initSignaturePad() {
            const canvas = document.getElementById('signatureCanvas');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;

            canvas.addEventListener('mousedown', (e) => {
                isDrawing = true;
                [lastX, lastY] = [e.offsetX, e.offsetY];
            });

            canvas.addEventListener('mousemove', (e) => {
                if (!isDrawing) return;
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(e.offsetX, e.offsetY);
                ctx.strokeStyle = '#000';
                ctx.lineWidth = 2;
                ctx.stroke();
                [lastX, lastY] = [e.offsetX, e.offsetY];
            });

            canvas.addEventListener('mouseup', () => {
                isDrawing = false;
                this.signatureData = canvas.toDataURL();
            });

            canvas.addEventListener('mouseleave', () => isDrawing = false);
        },
        clearSignature() {
            const canvas = document.getElementById('signatureCanvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                this.signatureData = '';
            }
        }
    }" class="space-y-6">
        <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
            <!-- Search Bar -->
            <form method="GET" action="{{ route('tech-head.work-orders') }}" class="flex-1 max-w-2xl">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search ?? '' }}"
                        placeholder="Search by WO number, customer, service type..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                    <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>
            
            <button 
                @click="showCreate=true" 
                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg text-sm font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 flex items-center gap-2 whitespace-nowrap"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Create Work Order</span>
            </button>
        </div>
        
        @if($search)
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <span>Search results for: <strong class="text-gray-900 dark:text-white">"{{ $search }}"</strong></span>
                <a href="{{ route('tech-head.work-orders') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Clear</a>
            </div>
        @endif
        
        <!-- Filter Buttons -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('tech-head.work-orders') }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request('status') && !request('priority') ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    All
                </a>
                
                <span class="text-gray-400 self-center">|</span>
                
                <a href="{{ route('tech-head.work-orders', ['status' => 'pending'] + request()->except('status')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'pending' ? 'bg-amber-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Waiting for Assignment
                </a>

                <a href="{{ route('tech-head.work-orders', ['status' => 'approved'] + request()->except('status')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'approved' ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Approved
                </a>
                
                <a href="{{ route('tech-head.work-orders', ['status' => 'in_progress'] + request()->except('status')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'in_progress' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    In Progress
                </a>
                
                <a href="{{ route('tech-head.work-orders', ['status' => 'completed'] + request()->except('status')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'completed' ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Completed
                </a>
                
                <a href="{{ route('tech-head.work-orders', ['status' => 'cancelled'] + request()->except('status')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'cancelled' ? 'bg-rose-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Cancelled
                </a>
                
                     <a href="{{ route('tech-head.work-orders', ['status' => 'for_accounting_approval'] + request()->except('status')) }}" 
                         class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ in_array(request('status'), ['pending_approval', 'for_accounting_approval'], true) ? 'bg-purple-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Pending Approval
                </a>
                
                <span class="text-gray-400 self-center">|</span>
                
                <a href="{{ route('tech-head.work-orders', ['priority' => 'urgent'] + request()->except('priority')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('priority') === 'urgent' ? 'bg-red-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Urgent
                </a>
                
                <a href="{{ route('tech-head.work-orders', ['priority' => 'high'] + request()->except('priority')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('priority') === 'high' ? 'bg-orange-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    High Priority
                </a>
                
                <a href="{{ route('tech-head.work-orders', ['priority' => 'normal'] + request()->except('priority')) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('priority') === 'normal' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Normal
                </a>
                
            </div>
            
            @if(request('status') || request('priority'))
                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Active filters:</span>
                        @if(request('status'))
                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded text-xs font-medium">
                                Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                            </span>
                        @endif
                        @if(request('priority'))
                            <span class="px-2 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200 rounded text-xs font-medium">
                                Priority: {{ ucfirst(request('priority')) }}
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('tech-head.work-orders', request()->except(['status', 'priority'])) }}" 
                       class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        Clear all filters
                    </a>
                </div>
            @endif
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Work Order List</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">WO Number</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Customer</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Assigned To</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Service Type</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Status</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Date</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($workOrders as $order)
                            @php
                                $hasPendingTechHeadReview = $order->assignments->contains(function ($assignment) {
                                    return optional($assignment->report)->status === 'pending';
                                });

                                $orderPayload = [
                                    'id' => $order->id,
                                    'job_order_number' => $order->job_order_number,
                                    'wo_number' => $order->job_order_number,
                                    'customer' => $order->customer->name ?? 'N/A',
                                    'service_type' => $order->service_type ?? 'N/A',
                                    'service_description' => $order->service_description ?? '',
                                    'priority' => $order->priority ?? 'normal',
                                    'status' => $order->status,
                                    'required_date' => $order->required_date ? $order->required_date->setTimezone('Asia/Manila')->format('M d, Y') : null,
                                    'service_address' => $order->service_address ?? '',
                                    'city' => $order->city ?? '',
                                    'notes' => $order->notes ?? '',
                                    'created_at' => $order->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A'),
                                    'certificates_count' => (int) $order->certificates_count,
                                    'has_pending_tech_head_review' => $hasPendingTechHeadReview,
                                    'attachments' => $order->attachments->map(function ($attachment) {
                                        return [
                                            'id' => $attachment->id,
                                            'file_name' => $attachment->file_name,
                                            'file_path' => $attachment->file_path,
                                            'created_at' => optional($attachment->created_at)->toDateTimeString(),
                                        ];
                                    })->values(),
                                    'checklist' => $order->checklistItems->map(function ($item) {
                                        return [
                                            'id' => $item->id,
                                            'description' => $item->description,
                                            'is_completed' => (bool) $item->is_completed,
                                            'completed_at' => optional($item->completed_at)->toDateTimeString(),
                                            'created_by' => optional($item->creator)->name,
                                            'completed_by' => optional($item->completer)->name,
                                        ];
                                    })->values(),
                                    'assignments' => $order->assignments->map(function ($assignment) {
                                        return [
                                            'id' => $assignment->id,
                                            'technician' => optional($assignment->assignedTo)->name ?? 'N/A',
                                            'status' => $assignment->status,
                                            'report_status' => optional($assignment->report)->status,
                                            'started_at' => $assignment->started_at ? $assignment->started_at->setTimezone('Asia/Manila')->toDateTimeString() : null,
                                            'completed_at' => $assignment->completed_at ? $assignment->completed_at->setTimezone('Asia/Manila')->toDateTimeString() : null,
                                        ];
                                    })->values(),
                                ];

                                $editPayload = [
                                    'id' => $order->id,
                                    'wo_number' => $order->job_order_number,
                                    'customer' => $order->customer->name ?? 'N/A',
                                    'service_type' => $order->service_type ?? 'N/A',
                                    'service_description' => $order->service_description ?? '',
                                    'priority' => $order->priority ?? 'normal',
                                    'status' => $order->status,
                                    'required_date' => $order->required_date ? $order->required_date->setTimezone('Asia/Manila')->format('Y-m-d') : null,
                                    'service_address' => $order->service_address ?? '',
                                    'city' => $order->city ?? '',
                                    'notes' => $order->notes ?? '',
                                    'created_at' => $order->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A'),
                                ];
                            @endphp
                            <tr 
                                @click="openDetails({{ json_encode($orderPayload) }})"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                            >
                                <td class="py-3 text-center">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $order->job_order_number }}</p>
                                </td>
                                <td class="py-3 text-center">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order->customer->name ?? 'N/A' }}</p>
                                </td>
                                <td class="py-3 text-center">
                                    @if($order->assignments && $order->assignments->count() > 0)
                                        @php
                                            $assignedNames = $order->assignments->pluck('assignedTo.name')->filter()->unique()->take(2);
                                        @endphp
                                        <div class="flex flex-col items-center gap-1">
                                            @foreach($assignedNames as $name)
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200">
                                                    {{ $name }}
                                                </span>
                                            @endforeach
                                            @if($order->assignments->count() > 2)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">+{{ $order->assignments->count() - 2 }} more</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            Unassigned
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order->service_type ?? 'N/A' }}</p>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $order->status === 'pending' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200' : '' }}
                                        {{ $order->status === 'assigned' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' : '' }}
                                        {{ $order->status === 'for_accounting_approval' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-200' : '' }}
                                        {{ $order->status === 'approved' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : '' }}
                                        {{ $order->status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' : '' }}
                                        {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : '' }}
                                        {{ $order->status === 'cancelled' ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200' : '' }}">
                                        {{ $order->status === 'pending' ? ($hasPendingTechHeadReview ? 'Pending Tech Head Review' : 'Waiting for Assignment') : ($order->status === 'approved' ? 'Ready for Assignment' : ($order->status === 'for_accounting_approval' ? 'For Accounting Approval' : ucfirst(str_replace('_', ' ', $order->status)))) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order->required_date ? $order->required_date->setTimezone('Asia/Manila')->format('M d, Y') : $order->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}</p>
                                </td>
                                <td class="py-3 text-center" @click.stop>
                                    <div class="flex gap-2 justify-center flex-wrap">
                                        @if($order->certificates_count > 0)
                                            <a href="{{ route('tech-head.certificates', ['status' => 'generated']) }}" class="px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800/50 rounded-md text-xs font-semibold transition-all duration-150 hover:shadow-sm" title="Certificate Generated">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                                    </svg>
                                                    Certificate
                                                </span>
                                            </a>
                                        @endif
                                        
                                        <button @click="openTimeline({{ json_encode($orderPayload) }})" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md text-xs font-semibold transition-all duration-150 hover:shadow-sm" title="View Timeline">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Timeline
                                            </span>
                                        </button>
                                        
                                        @if($order->status === 'for_accounting_approval' || $order->status === 'completed')
                                            <button @click="openApproval({{ json_encode($orderPayload) }})" class="px-3 py-1.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-800/50 rounded-md text-xs font-semibold transition-all duration-150 hover:shadow-sm animate-pulse" title="Approve & Sign">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Approve
                                                </span>
                                            </button>
                                        @endif
                                        
                                        <button @click="openEdit({{ json_encode($editPayload) }})" class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800/50 rounded-md text-xs font-semibold transition-all duration-150 hover:shadow-sm">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit
                                            </span>
                                        </button>
                                        <button @click="openAssign({{ $order->id }})" class="px-3 py-1.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800/50 rounded-md text-xs font-semibold transition-all duration-150 hover:shadow-sm">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                </svg>
                                                Assign
                                            </span>
                                        </button>
                                        <button @click="openStatus({{ $order->id }})" class="px-3 py-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 hover:bg-amber-200 dark:hover:bg-amber-800/50 rounded-md text-xs font-semibold transition-all duration-150 hover:shadow-sm">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Status
                                            </span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No work orders found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $workOrders->links() }}
            </div>
        </div>

        <!-- Work Order Details Modal -->
        <div 
            x-show="showDetails" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showDetails=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Work Order Details</h3>
                            <button @click="showDetails=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Work Order Number</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedOrder?.wo_number"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Customer</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedOrder?.customer"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Service Type</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedOrder?.service_type"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Priority</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-200" x-text="selectedOrder?.priority"></span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200" x-text="selectedOrder ? statusLabel(selectedOrder.status, selectedOrder) : 'N/A'"></span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Required Date</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedOrder?.required_date || '—'"></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Service Description</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selectedOrder?.service_description || 'No description'"></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Service Address</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="(selectedOrder?.service_address || '') + (selectedOrder?.city ? ', ' + selectedOrder?.city : '') || 'No address'"></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notes</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selectedOrder?.notes || 'No notes'"></p>
                            </div>
                            <div class="col-span-2" x-show="selectedOrder?.attachments?.length">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Attachments</p>
                                <div class="space-y-2">
                                    <template x-for="attachment in selectedOrder.attachments" :key="attachment.id">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/40 rounded-lg">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="attachment.file_name"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="attachment.created_at ? new Date(attachment.created_at).toLocaleString('en-US') : ''"></p>
                                            </div>
                                            <a class="text-blue-600 dark:text-blue-400 hover:underline text-sm"
                                               :href="'{{ rtrim(Storage::url(''), '/') }}/' + attachment.file_path"
                                               target="_blank">
                                                View
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Assigned Technicians</p>
                                <div x-show="selectedOrder?.assignments?.length > 0" class="space-y-2">
                                    <template x-for="assignment in selectedOrder.assignments" :key="assignment.id">
                                        <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="assignment.technician"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'Status: ' + assignment.status"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-show="assignment.started_at" x-text="'Started: ' + new Date(assignment.started_at).toLocaleString('en-US')"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-show="assignment.completed_at" x-text="'Completed: ' + new Date(assignment.completed_at).toLocaleString('en-US')"></p>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200" x-text="assignment.status"></span>
                                        </div>
                                    </template>
                                </div>
                                <p x-show="!selectedOrder?.assignments?.length" class="text-sm text-gray-500 dark:text-gray-400 p-3 bg-gray-50 dark:bg-gray-700/40 rounded-lg">
                                    No technicians assigned yet. Click "Assign" to assign this job.
                                </p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Task Checklist</p>
                                <div class="space-y-2" x-show="selectedOrder?.checklist?.length">
                                    <template x-for="item in selectedOrder.checklist" :key="item.id">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/40 rounded-lg">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="item.description"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="item.is_completed ? 'Completed' : 'Pending'"></p>
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 text-right">
                                                <div x-text="item.completed_by ? 'By: ' + item.completed_by : ''"></div>
                                                <div x-text="item.completed_at ? new Date(item.completed_at).toLocaleString('en-US') : ''"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-show="!selectedOrder?.checklist?.length">No checklist items yet.</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Created At</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selectedOrder?.created_at"></p>
                            </div>
                        </div>
                        
                        <div class="flex justify-between gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a :href="`{{ route('tech-head.job-details', ['id' => '__ID__']) }}`.replace('__ID__', selectedOrder.id)"
                               class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Full Details
                            </a>
                            <div class="flex gap-3">
                                <button @click="showDetails=false; selectedId=selectedOrder.id; showEdit=true;" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Edit</button>
                                <button @click="showDetails=false; selectedId=selectedOrder.id; showAssign=true;" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Assign</button>
                                <button @click="showDetails=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Work Order Modal -->
        <div 
            x-show="showCreate" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto" 
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showCreate=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Create New Work Order</h3>
                            <button @click="showCreate=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <form action="{{ route('tech-head.work-orders.store') }}" method="POST" class="space-y-4">
                            @csrf
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Customer</label>
                                    <input type="text" x-model="selectedOrder.customer" disabled class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white text-sm cursor-default">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Priority</label>
                                    <select name="priority" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="normal">Normal</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Service Type</label>
                                    <input type="text" name="service_type" placeholder="e.g., Maintenance, Repair" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Required Date</label>
                                    <input type="date" name="required_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Service Description</label>
                                    <textarea name="service_description" rows="3" placeholder="Describe the service needed..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Service Address</label>
                                    <input type="text" name="service_address" placeholder="Full address" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Notes</label>
                                    <textarea name="notes" rows="2" placeholder="Additional notes..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                            </div>
                            
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="showCreate=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Create Work Order</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Work Order Modal -->
        <div 
            x-show="showEdit" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showEdit=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Work Order</h3>
                            <button @click="showEdit=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <form :action="'/tech-head/work-orders/' + selectedId" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Customer</label>
                                    <input type="text" x-model="selectedOrder.customer" disabled class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Priority</label>
                                    <select name="priority" x-model="selectedOrder.priority" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="normal">Normal</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Service Type</label>
                                    <input type="text" name="service_type" x-model="selectedOrder.service_type" placeholder="e.g., Maintenance, Repair" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Required Date</label>
                                    <input type="date" name="required_date" x-model="selectedOrder.required_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Service Description</label>
                                    <textarea name="service_description" x-model="selectedOrder.service_description" rows="3" placeholder="Describe the service needed..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Service Address</label>
                                    <input type="text" name="service_address" x-model="selectedOrder.service_address" placeholder="Full address" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Notes</label>
                                    <textarea name="notes" x-model="selectedOrder.notes" rows="2" placeholder="Additional notes..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                            </div>
                            
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="showEdit=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Update Work Order</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Work Order Modal -->
        <div 
            x-show="showAssign" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto" 
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showAssign=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Assign Technician</h3>
                            <button @click="showAssign=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <form :action="'/tech-head/work-orders/' + selectedId + '/assign'" method="POST" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Select Technician</label>
                                <select name="assigned_to" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Choose a technician...</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="showAssign=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">Assign</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Status Modal -->
        <div 
            x-show="showStatus" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto" 
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showStatus=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Update Status</h3>
                            <button @click="showStatus=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <form :action="'/tech-head/work-orders/' + selectedId + '/status'" method="POST" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Work Order Status</label>
                                <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="pending">Waiting for Assignment</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="showStatus=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700 transition-colors">Update Status</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval & Signature Modal -->
        <div 
            x-show="showApproval" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showApproval=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-3xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Approve Work Order</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="'WO: ' + (selectedOrder?.job_order_number || 'N/A')"></p>
                            </div>
                            <button @click="showApproval=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Work Order Summary -->
                        <div class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Customer</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedOrder?.customer?.name || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Service Type</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedOrder?.service_type || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedOrder?.status === 'pending' ? 'Waiting for Assignment' : (selectedOrder?.status === 'approved' ? 'Ready for Assignment' : (selectedOrder?.status || 'N/A'))"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Priority</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedOrder?.priority || 'N/A'"></p>
                            </div>
                        </div>

                        <form :action="'/tech-head/work-orders/' + selectedId + '/approve'" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            
                            <!-- Signature Pad -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Digital Signature</label>
                                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800">
                                    <canvas 
                                        id="signatureCanvas" 
                                        width="600" 
                                        height="200"
                                        class="w-full border border-gray-200 dark:border-gray-600 rounded cursor-crosshair bg-white"
                                        @mouseenter="initSignaturePad()"
                                    ></canvas>
                                    <div class="flex justify-between items-center mt-2">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Sign above using your mouse or touchpad</p>
                                        <button 
                                            type="button" 
                                            @click="clearSignature()" 
                                            class="text-xs text-blue-600 dark:text-blue-400 hover:underline"
                                        >
                                            Clear
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="signature" x-model="signatureData">
                            </div>

                            <!-- Comments -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Approval Comments</label>
                                <textarea 
                                    name="comments" 
                                    rows="3"
                                    placeholder="Add any comments or notes..."
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                ></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-between gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button 
                                    type="button" 
                                    @click="showApproval=false" 
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
                                >
                                    Cancel
                                </button>
                                <div class="flex gap-3">
                                    <button 
                                        type="submit" 
                                        name="action" 
                                        value="reject"
                                        class="px-6 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors"
                                    >
                                        Reject
                                    </button>
                                    <button 
                                        type="submit" 
                                        name="action" 
                                        value="approve"
                                        class="px-6 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors"
                                    >
                                        Approve & Sign
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Modal -->
        <div 
            x-show="showTimeline" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showTimeline=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Job Order Timeline</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="'WO: ' + (selectedOrder?.job_order_number || 'N/A')"></p>
                            </div>
                            <button @click="showTimeline=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
                            <div class="md:col-span-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 dark:border-blue-900/50 dark:bg-blue-900/20">
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-300">Current Stage</p>
                                <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white" x-text="currentTimelineStage(selectedOrder)"></p>
                                <p class="mt-1 text-sm text-blue-700 dark:text-blue-300" x-text="selectedOrder ? statusLabel(selectedOrder.status, selectedOrder) : 'N/A'"></p>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-700/50">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Progress</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="selectedOrder ? timelineProgress(selectedOrder) + '%' : '0%'"></p>
                                </div>
                                <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-600">
                                    <div class="h-full rounded-full bg-gradient-to-r from-blue-600 via-indigo-500 to-emerald-500 transition-all duration-300"
                                         :style="'width: ' + (selectedOrder ? timelineProgress(selectedOrder) : 0) + '%'">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="space-y-4">
                            <!-- Created -->
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                         :class="timelineStepState('created', selectedOrder) === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-blue-100 dark:bg-blue-900/30'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             :class="timelineStepState('created', selectedOrder) === 'completed' ? 'text-emerald-600 dark:text-emerald-400' : 'text-blue-600 dark:text-blue-400'">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </div>
                                    <div class="w-0.5 h-full mt-2" :class="timelineStepState('created', selectedOrder) === 'completed' ? 'bg-emerald-300 dark:bg-emerald-700' : 'bg-gray-200 dark:bg-gray-700'"></div>
                                </div>
                                <div class="flex-1 pb-8 rounded-xl px-3 py-2 transition-colors"
                                     :class="timelineStepState('created', selectedOrder) === 'completed' ? 'bg-emerald-50 dark:bg-emerald-900/10' : ''">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-semibold text-gray-900 dark:text-white">Work Order Created</p>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                              :class="timelineStepState('created', selectedOrder) === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'"
                                              x-text="timelineStepBadge('created', selectedOrder)"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="selectedOrder?.created_at || 'N/A'"></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Initial work order submitted</p>
                                </div>
                            </div>

                            <!-- Assigned -->
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                         :class="timelineStepState('assigned', selectedOrder) === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30' : (timelineStepState('assigned', selectedOrder) === 'current' ? 'bg-indigo-100 dark:bg-indigo-900/30 ring-2 ring-indigo-300 dark:ring-indigo-700' : 'bg-gray-100 dark:bg-gray-700/70')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             :class="timelineStepState('assigned', selectedOrder) === 'completed' ? 'text-emerald-600 dark:text-emerald-400' : (timelineStepState('assigned', selectedOrder) === 'current' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500')">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div class="w-0.5 h-full mt-2" :class="timelineStepState('assigned', selectedOrder) !== 'pending' ? 'bg-indigo-300 dark:bg-indigo-700' : 'bg-gray-200 dark:bg-gray-700'"></div>
                                </div>
                                <div class="flex-1 pb-8 rounded-xl px-3 py-2 transition-colors"
                                     :class="timelineStepState('assigned', selectedOrder) === 'current' ? 'bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-200 dark:border-indigo-800/60' : (timelineStepState('assigned', selectedOrder) === 'completed' ? 'bg-emerald-50 dark:bg-emerald-900/10' : '')">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-semibold text-gray-900 dark:text-white">Technician Assigned</p>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                              :class="timelineStepState('assigned', selectedOrder) === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : (timelineStepState('assigned', selectedOrder) === 'current' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300')"
                                              x-text="timelineStepBadge('assigned', selectedOrder)"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="assignmentTimelineLabel(selectedOrder)"></p>
                                </div>
                            </div>

                            <!-- Calibration Started -->
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                         :class="timelineStepState('calibration', selectedOrder) === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30' : (timelineStepState('calibration', selectedOrder) === 'current' ? 'bg-amber-100 dark:bg-amber-900/30 ring-2 ring-amber-300 dark:ring-amber-700' : 'bg-gray-100 dark:bg-gray-700/70')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             :class="timelineStepState('calibration', selectedOrder) === 'completed' ? 'text-emerald-600 dark:text-emerald-400' : (timelineStepState('calibration', selectedOrder) === 'current' ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400 dark:text-gray-500')">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                    <div class="w-0.5 h-full mt-2" :class="timelineStepState('calibration', selectedOrder) !== 'pending' ? 'bg-amber-300 dark:bg-amber-700' : 'bg-gray-200 dark:bg-gray-700'"></div>
                                </div>
                                <div class="flex-1 pb-8 rounded-xl px-3 py-2 transition-colors"
                                     :class="timelineStepState('calibration', selectedOrder) === 'current' ? 'bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/60' : (timelineStepState('calibration', selectedOrder) === 'completed' ? 'bg-emerald-50 dark:bg-emerald-900/10' : '')">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-semibold text-gray-900 dark:text-white">Calibration Started</p>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                              :class="timelineStepState('calibration', selectedOrder) === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : (timelineStepState('calibration', selectedOrder) === 'current' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300')"
                                              x-text="timelineStepBadge('calibration', selectedOrder)"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="calibrationTimelineLabel(selectedOrder)"></p>
                                </div>
                            </div>

                            <!-- Report Uploaded -->
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                         :class="timelineStepState('report', selectedOrder) === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30' : (timelineStepState('report', selectedOrder) === 'current' ? 'bg-purple-100 dark:bg-purple-900/30 ring-2 ring-purple-300 dark:ring-purple-700' : 'bg-gray-100 dark:bg-gray-700/70')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             :class="timelineStepState('report', selectedOrder) === 'completed' ? 'text-emerald-600 dark:text-emerald-400' : (timelineStepState('report', selectedOrder) === 'current' ? 'text-purple-600 dark:text-purple-400' : 'text-gray-400 dark:text-gray-500')">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <div class="w-0.5 h-full mt-2" :class="timelineStepState('report', selectedOrder) !== 'pending' ? 'bg-purple-300 dark:bg-purple-700' : 'bg-gray-200 dark:bg-gray-700'"></div>
                                </div>
                                <div class="flex-1 pb-8 rounded-xl px-3 py-2 transition-colors"
                                     :class="timelineStepState('report', selectedOrder) === 'current' ? 'bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-800/60' : (timelineStepState('report', selectedOrder) === 'completed' ? 'bg-emerald-50 dark:bg-emerald-900/10' : '')">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-semibold text-gray-900 dark:text-white">Report Uploaded</p>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                              :class="timelineStepState('report', selectedOrder) === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : (timelineStepState('report', selectedOrder) === 'current' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300')"
                                              x-text="timelineStepBadge('report', selectedOrder)"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="reportTimelineLabel(selectedOrder)"></p>
                                </div>
                            </div>

                            <!-- Approved -->
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                         :class="timelineStepState('approval', selectedOrder) === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30' : (timelineStepState('approval', selectedOrder) === 'current' ? 'bg-green-100 dark:bg-green-900/30 ring-2 ring-green-300 dark:ring-green-700' : 'bg-gray-100 dark:bg-gray-700/70')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             :class="timelineStepState('approval', selectedOrder) === 'completed' ? 'text-emerald-600 dark:text-emerald-400' : (timelineStepState('approval', selectedOrder) === 'current' ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500')">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="w-0.5 h-full mt-2" :class="timelineStepState('approval', selectedOrder) !== 'pending' ? 'bg-green-300 dark:bg-green-700' : 'bg-gray-200 dark:bg-gray-700'"></div>
                                </div>
                                <div class="flex-1 pb-8 rounded-xl px-3 py-2 transition-colors"
                                     :class="timelineStepState('approval', selectedOrder) === 'current' ? 'bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-800/60' : (timelineStepState('approval', selectedOrder) === 'completed' ? 'bg-emerald-50 dark:bg-emerald-900/10' : '')">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-semibold text-gray-900 dark:text-white">Approved & Signed</p>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                              :class="timelineStepState('approval', selectedOrder) === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : (timelineStepState('approval', selectedOrder) === 'current' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300')"
                                              x-text="timelineStepBadge('approval', selectedOrder)"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="approvalTimelineLabel(selectedOrder)"></p>
                                </div>
                            </div>

                            <!-- Certificate Generated -->
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                         :class="timelineStepState('certificate_generated', selectedOrder) === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30' : (timelineStepState('certificate_generated', selectedOrder) === 'current' ? 'bg-cyan-100 dark:bg-cyan-900/30 ring-2 ring-cyan-300 dark:ring-cyan-700' : 'bg-gray-100 dark:bg-gray-700/70')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             :class="timelineStepState('certificate_generated', selectedOrder) === 'completed' ? 'text-emerald-600 dark:text-emerald-400' : (timelineStepState('certificate_generated', selectedOrder) === 'current' ? 'text-cyan-600 dark:text-cyan-400' : 'text-gray-400 dark:text-gray-500')">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="w-0.5 h-full mt-2" :class="timelineStepState('certificate_generated', selectedOrder) !== 'pending' ? 'bg-cyan-300 dark:bg-cyan-700' : 'bg-gray-200 dark:bg-gray-700'"></div>
                                </div>
                                <div class="flex-1 pb-8 rounded-xl px-3 py-2 transition-colors"
                                     :class="timelineStepState('certificate_generated', selectedOrder) === 'current' ? 'bg-cyan-50 dark:bg-cyan-900/10 border border-cyan-200 dark:border-cyan-800/60' : (timelineStepState('certificate_generated', selectedOrder) === 'completed' ? 'bg-emerald-50 dark:bg-emerald-900/10' : '')">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-semibold text-gray-900 dark:text-white">Certificate Generated</p>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                              :class="timelineStepState('certificate_generated', selectedOrder) === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : (timelineStepState('certificate_generated', selectedOrder) === 'current' ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300')"
                                              x-text="timelineStepBadge('certificate_generated', selectedOrder)"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="certificateTimelineLabel(selectedOrder)"></p>
                                </div>
                            </div>

                            <!-- Certificate Released -->
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                         :class="timelineStepState('certificate_released', selectedOrder) === 'current' ? 'bg-emerald-100 dark:bg-emerald-900/30 ring-2 ring-emerald-300 dark:ring-emerald-700' : 'bg-gray-100 dark:bg-gray-700/70'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             :class="timelineStepState('certificate_released', selectedOrder) === 'current' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500'">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 rounded-xl px-3 py-2 transition-colors"
                                     :class="timelineStepState('certificate_released', selectedOrder) === 'current' ? 'bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800/60' : ''">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-semibold text-gray-900 dark:text-white">Certificate Released</p>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                              :class="timelineStepState('certificate_released', selectedOrder) === 'current' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'"
                                              x-text="timelineStepBadge('certificate_released', selectedOrder)"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="timelineStepState('certificate_released', selectedOrder) === 'current' ? 'Ready for final release or turnover' : 'Not yet released'"></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700 mt-6">
                            <button @click="showTimeline=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
