<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Admin System Stats
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

        // Recent User Activity (mock data for admin focus)
        $recentUserActivity = User::latest('updated_at')
            ->limit(8)
            ->get()
            ->map(function ($user) {
                return (object) [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role?->name ?? 'N/A',
                    'last_login' => $user->last_login_at ?? $user->updated_at,
                    'is_active' => $user->is_active ?? true,
                ];
            });

        // Recent Audit Activity (mock data - would be from audit_logs table)
        $auditActivity = collect([
            (object) [
                'id' => 1,
                'action' => 'User Created',
                'model' => 'User',
                'ref_id' => 'USR-2025-042',
                'user_name' => 'Admin System',
                'created_at' => now()->subHours(2),
            ],
            (object) [
                'id' => 2,
                'action' => 'Role Updated',
                'model' => 'Role',
                'ref_id' => 'TECHNICIAN',
                'user_name' => 'John Admin',
                'created_at' => now()->subHours(4),
            ],
            (object) [
                'id' => 3,
                'action' => 'Settings Changed',
                'model' => 'Settings',
                'ref_id' => 'SYSTEM_CONFIG',
                'user_name' => 'Admin Master',
                'created_at' => now()->subHours(6),
            ],
            (object) [
                'id' => 4,
                'action' => 'User Deactivated',
                'model' => 'User',
                'ref_id' => 'USR-2025-041',
                'user_name' => 'Admin System',
                'created_at' => now()->subHours(8),
            ],
            (object) [
                'id' => 5,
                'action' => 'Permission Added',
                'model' => 'Permission',
                'ref_id' => 'EDIT_EQUIPMENT',
                'user_name' => 'John Admin',
                'created_at' => now()->subHours(10),
            ],
        ]);

        // System Configuration Status (mock data)
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
