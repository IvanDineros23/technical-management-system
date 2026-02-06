<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Helpers\AuditLogHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Log the password change
        AuditLogHelper::log(
            action: 'UPDATE',
            modelType: 'Password',
            modelId: $user->id,
            description: 'Changed user password',
            oldValues: null,
            newValues: [
                'password_changed' => true,
                'changed_at' => now()->toDateTimeString(),
            ],
            changedFields: ['password']
        );

        return Redirect::route('profile.show')->with('status', 'password-updated');
    }
}

