<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Helpers\AuditLogHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        
        // Get user's assignments summary
        $assignmentStats = \App\Models\Assignment::where('assigned_to', $user->id)
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed, SUM(CASE WHEN status IN ("assigned", "in_progress") THEN 1 ELSE 0 END) as active')
            ->first();
        
        // Get recent activity logs
        $recentActivity = \App\Models\AuditLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get last login from sessions table
        $lastLogin = \DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->first();
        
        return view('profile.show', [
            'user' => $user,
            'assignmentStats' => $assignmentStats,
            'recentActivity' => $recentActivity,
            'lastLogin' => $lastLogin,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $user->fill($request->validated());

        $changedFields = [];
        if ($user->isDirty('name')) $changedFields[] = 'name';
        if ($user->isDirty('email')) {
            $changedFields[] = 'email';
            $user->email_verified_at = null;
        }

        $user->save();

        if (!empty($changedFields)) {
            AuditLogHelper::log(
                action: 'UPDATE',
                modelType: 'Profile',
                modelId: $user->id,
                description: "Updated profile information",
                oldValues: $oldValues,
                newValues: $request->validated(),
                changedFields: $changedFields
            );
        }

        return Redirect::route('profile.show')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
