<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Models\AuditLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest('created_at');

        // Filter by department
        if ($request->filled('department')) {
            $query->whereHas('user', function ($userQuery) use ($request) {
                $userQuery->where('department', $request->department);
            });
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', strtoupper($request->action));
        }

        // Search logs
        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('description', 'like', '%' . $search . '%')
                    ->orWhere('action', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('department', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auditLogs = $query->paginate(12);
        $departments = User::whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        return view('admin.audit-logs', compact('auditLogs', 'departments'));
    }

    public function export(Request $request)
    {
        $query = AuditLog::with('user')->latest('created_at');

        // Apply same filters as index
        if ($request->filled('department')) {
            $query->whereHas('user', function ($userQuery) use ($request) {
                $userQuery->where('department', $request->department);
            });
        }

        if ($request->filled('action')) {
            $query->where('action', strtoupper($request->action));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('description', 'like', '%' . $search . '%')
                    ->orWhere('action', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('department', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get all filtered logs (no pagination for export)
        $auditLogs = $query->get();

        // Log the export action for security tracking
        $filters = [];
        if ($request->filled('department')) $filters['department'] = $request->department;
        if ($request->filled('action')) $filters['action'] = $request->action;
        if ($request->filled('search')) $filters['search'] = $request->search;
        if ($request->filled('date_from')) $filters['date_from'] = $request->date_from;
        if ($request->filled('date_to')) $filters['date_to'] = $request->date_to;
        
        AuditLogHelper::log(
            action: 'EXPORT',
            modelType: 'AuditLog',
            modelId: null,
            description: 'Admin exported audit logs (' . $auditLogs->count() . ' records)',
            newValues: ['filters' => $filters, 'record_count' => $auditLogs->count()],
            changedFields: ['export']
        );

        // Generate PDF
        $pdf = Pdf::loadView('admin.audit-logs-export', compact('auditLogs'))
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

        $filename = 'audit-logs-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
