<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Helpers\AuditLogHelper;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $user->load('customer');
        
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
        $validated = $request->validated();
        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $userData = Arr::only($validated, ['name', 'email']);
        $user->fill($userData);

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
                newValues: $userData,
                changedFields: $changedFields
            );
        }

        $isCustomer = ($user->role?->slug ?? '') === 'customer';
        $customerData = [
            'name' => $validated['customer_name'] ?? null,
            'business_name' => $validated['customer_business_name'] ?? null,
            'email' => $validated['customer_email'] ?? null,
            'phone' => $validated['customer_phone'] ?? null,
            'address' => $validated['customer_address'] ?? null,
            'city' => $validated['customer_city'] ?? null,
            'state' => $validated['customer_state'] ?? null,
            'postal_code' => $validated['customer_postal_code'] ?? null,
            'country' => $validated['customer_country'] ?? null,
            'contact_person' => $validated['customer_contact_person'] ?? null,
            'industry_type' => $validated['customer_industry_type'] ?? null,
            'tax_id' => $validated['customer_tax_id'] ?? null,
            'credit_terms' => $validated['customer_credit_terms'] ?? null,
            'notes' => $validated['customer_notes'] ?? null,
        ];

        if ($isCustomer) {
            $hasCustomerInput = count(array_filter($customerData, function ($value) {
                return $value !== null && $value !== '';
            })) > 0;

            if ($hasCustomerInput) {
                $customer = $user->customer;
                $customerData['name'] = $customerData['name'] ?: $user->name;
                $customerData['email'] = $customerData['email'] ?: $user->email;
                $customerData['country'] = $customerData['country'] ?: 'Philippines';

                if ($customer) {
                    $oldCustomerValues = $customer->only(array_keys($customerData));
                    $customer->fill($customerData);
                    $customer->save();

                    $customerChanged = [];
                    foreach ($customerData as $field => $value) {
                        if ($oldCustomerValues[$field] != $value) {
                            $customerChanged[] = $field;
                        }
                    }

                    if (!empty($customerChanged)) {
                        AuditLogHelper::log(
                            action: 'UPDATE',
                            modelType: 'Customer',
                            modelId: $customer->id,
                            description: "Customer updated their profile details",
                            oldValues: $oldCustomerValues,
                            newValues: $customerData,
                            changedFields: $customerChanged
                        );
                    }
                } else {
                    $customer = Customer::create(array_merge($customerData, [
                        'created_by' => $user->id,
                        'is_active' => true,
                    ]));
                    $user->customer_id = $customer->id;
                    $user->save();

                    AuditLogHelper::log(
                        action: 'CREATE',
                        modelType: 'Customer',
                        modelId: $customer->id,
                        description: "Customer created their profile details",
                        newValues: $customerData,
                        changedFields: array_keys($customerData)
                    );
                }
            }
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
