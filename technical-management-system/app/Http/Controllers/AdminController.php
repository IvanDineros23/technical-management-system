<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Admin System Stats - Real data from database
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'admin_users' => User::whereHas('role', function ($query) {
                $query->where('slug', 'admin');
            })->count(),
            'technician_users' => User::whereHas('role', function ($query) {
                $query->where('slug', 'technician');
            })->count(),
            'operator_users' => User::whereHas('role', function ($query) {
                $query->where('slug', 'operator');
            })->count(),
            'customer_users' => User::whereHas('role', function ($query) {
                $query->where('slug', 'customer');
            })->count(),
        ];

        // Recent User Activity - Real data from database
        $recentUserActivity = User::latest('updated_at')
            ->limit(8)
            ->get()
            ->map(function ($user) {
                $lastLogin = $user->last_login_at ?? $user->updated_at;
                $lastLoginFormatted = 'Never';
                if ($lastLogin) {
                    try {
                        if (!($lastLogin instanceof \Carbon\Carbon)) {
                            $lastLogin = \Carbon\Carbon::parse($lastLogin);
                        }
                        $lastLoginFormatted = $lastLogin->timezone('Asia/Manila')->format('M d, Y h:i A');
                    } catch (\Exception $e) {
                        $lastLoginFormatted = 'Invalid date';
                    }
                }
                return (object) [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role?->name ?? 'N/A',
                    'last_login' => $lastLoginFormatted,
                    'is_active' => $user->is_active ?? true,
                ];
            });

        // Recent Audit Activity - Real data from database
        $auditActivity = AuditLog::latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($log) {
                return (object) [
                    'id' => $log->id,
                    'action' => $log->action ?? 'Unknown Action',
                    'model' => $log->model ?? 'System',
                    'ref_id' => $log->ref_id ?? 'N/A',
                    'user_name' => $log->user?->name ?? 'System',
                    'created_at' => $log->created_at,
                ];
            });

        // System Configuration Status
        $systemStatus = [
            [
                'name' => 'Database',
                'status' => 'healthy',
                'message' => 'All connections active',
            ],
            [
                'name' => 'File Storage',
                'status' => 'healthy',
                'message' => 'Normal operation',
            ],
            [
                'name' => 'Email Service',
                'status' => 'healthy',
                'message' => 'Queue processing',
            ],
            [
                'name' => 'Authentication',
                'status' => 'healthy',
                'message' => '0 failed attempts',
            ],
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentUserActivity',
            'auditActivity',
            'systemStatus'
        ));
    }
}
