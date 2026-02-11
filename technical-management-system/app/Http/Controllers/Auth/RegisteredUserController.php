<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\AuditLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $customerRoleId = Role::where('slug', 'customer')->value('id');
        if (!$customerRoleId) {
            $customerRoleId = DB::table('roles')->insertGetId([
                'name' => 'Customer',
                'slug' => 'customer',
                'description' => 'View own job requests and certificates',
                'permissions' => json_encode([
                    'view_job_orders',
                    'view_certificates',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $defaultRoleId = $customerRoleId
            ?? Role::where('slug', 'tech_personnel')->value('id')
            ?? Role::orderBy('id')->value('id');

        $customer = Customer::where('email', $request->email)->first();
        if (!$customer) {
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'contact_person' => $request->name,
                'is_active' => true,
                'created_by' => null,
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $defaultRoleId,
            'customer_id' => $customer?->id,
            'is_active' => false,
        ]);

        event(new Registered($user));

        // Log account registration in audit trail
        AuditLogHelper::log(
            action: 'REGISTER',
            modelType: 'User',
            modelId: $user->id,
            description: "New account registered: {$user->name} ({$user->email}) - Pending approval",
            newValues: [
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $defaultRoleId,
                'customer_id' => $customer?->id,
                'is_active' => false,
            ],
            changedFields: ['name', 'email', 'password', 'role_id', 'customer_id', 'is_active']
        );

        return redirect()->route('login')
            ->with('status', 'Registration successful. Your account is pending admin approval.');
    }
}
