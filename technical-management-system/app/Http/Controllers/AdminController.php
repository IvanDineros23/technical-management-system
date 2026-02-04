<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\JobOrder;
use App\Models\Role;
use App\Helpers\AuditLogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
            'tech_personnel_users' => User::whereHas('role', function ($query) {
                $query->where('slug', 'tech_personnel');
            })->count(),
            'tech_head_users' => User::whereHas('role', function ($query) {
                $query->where('slug', 'tech_head');
            })->count(),
            'marketing_users' => User::whereHas('role', function ($query) {
                $query->where('slug', 'marketing');
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
                    'model' => $log->model_type ?? 'System',
                    'ref_id' => $log->model_id ?? 'N/A',
                    'user_name' => $log->user?->name ?? 'System',
                    'description' => $log->description,
                    'created_at' => $log->created_at,
                ];
            });

        // System Configuration Status (DB-backed health indicators)
        $databaseOk = true;
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $databaseOk = false;
        }

        $activeUsersCount = User::where('is_active', true)->count();
        $totalUsersCount = User::count();
        $recentAuditCount = AuditLog::where('created_at', '>=', now()->subDay())->count();

        $systemStatus = [
            [
                'name' => 'Database',
                'status' => $databaseOk ? 'healthy' : 'unhealthy',
                'message' => $databaseOk ? ('Connected • ' . DB::connection()->getDatabaseName()) : 'Connection failed',
            ],
            [
                'name' => 'User Activity',
                'status' => $activeUsersCount > 0 ? 'healthy' : 'unhealthy',
                'message' => "Active: {$activeUsersCount} / {$totalUsersCount}",
            ],
            [
                'name' => 'Audit Trail',
                'status' => $recentAuditCount > 0 ? 'healthy' : 'unhealthy',
                'message' => $recentAuditCount . ' events (24h)',
            ],
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentUserActivity',
            'auditActivity',
            'systemStatus'
        ));
    }

    public function users(Request $request)
    {
        $search = $request->string('search')->toString();
        $role = $request->string('role')->toString();
        $status = $request->string('status')->toString();

        $query = User::with('role')->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $query->where('role_id', $role);
        }

        if ($status !== '') {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->paginate(15)->appends([
            'search' => $search,
            'role' => $role,
            'status' => $status,
        ]);

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        $roles = Role::orderBy('name')->get();

        return view('admin.users', compact('users', 'stats', 'roles', 'search', 'role', 'status'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'is_active' => $validated['status'] === 'active',
            'password' => Hash::make($validated['password']),
        ]);

        // Log the user creation
        AuditLogHelper::log(
            action: 'CREATE',
            modelType: 'User',
            modelId: $user->id,
            description: "Admin created new user: {$validated['name']} ({$validated['email']})",
            newValues: [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role_id' => $validated['role_id'],
                'is_active' => $validated['status'] === 'active',
            ],
            changedFields: ['name', 'email', 'role_id', 'is_active', 'password']
        );

        return redirect()->route('admin.users.index')
            ->with('status', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Capture old values for audit log
        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'is_active' => $user->is_active,
        ];

        $newValues = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'is_active' => $validated['status'] === 'active',
        ];

        $changedFields = [];
        if ($user->name !== $validated['name']) $changedFields[] = 'name';
        if ($user->email !== $validated['email']) $changedFields[] = 'email';
        if ($user->role_id !== $validated['role_id']) $changedFields[] = 'role_id';
        if ($user->is_active !== ($validated['status'] === 'active')) $changedFields[] = 'is_active';

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'is_active' => $validated['status'] === 'active',
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
            $changedFields[] = 'password';
        }

        // Log the user update
        AuditLogHelper::log(
            action: 'UPDATE',
            modelType: 'User',
            modelId: $user->id,
            description: "Admin updated user: {$validated['name']} ({$validated['email']})",
            oldValues: $oldValues,
            newValues: $newValues,
            changedFields: $changedFields
        );

        return redirect()->route('admin.users.index')
            ->with('status', 'User updated successfully.');
    }

    public function deactivateUser(User $user)
    {
        $user->update(['is_active' => false]);

        // Log the user deactivation
        AuditLogHelper::log(
            action: 'DEACTIVATE',
            modelType: 'User',
            modelId: $user->id,
            description: "Admin deactivated user: {$user->name} ({$user->email})",
            oldValues: ['is_active' => true],
            newValues: ['is_active' => false],
            changedFields: ['is_active']
        );

        return redirect()->route('admin.users.index')
            ->with('status', 'User deactivated successfully.');
    }

    public function deleteUser(User $user)
    {
        $userName = $user->name;
        $userEmail = $user->email;
        $userId = $user->id;

        $user->delete();

        // Log the user deletion
        AuditLogHelper::log(
            action: 'DELETE',
            modelType: 'User',
            modelId: $userId,
            description: "Admin deleted user: {$userName} ({$userEmail})",
            oldValues: [
                'name' => $userName,
                'email' => $userEmail,
                'is_active' => true,
            ],
            changedFields: ['deleted']
        );

        return redirect()->route('admin.users.index')
            ->with('status', 'User deleted successfully.');
    }
}
