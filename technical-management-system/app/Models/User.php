<?php

namespace App\Models;

use App\Notifications\BrandedVerifyEmailNotification;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'customer_id',
        'department',
        'availability',
        'skills',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'skills' => 'array',
        ];
    }

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the customer profile associated with the user.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->role && in_array($this->role->slug, $roleSlugs);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->role || !$this->role->permissions) {
            return false;
        }

        $permissions = is_array($this->role->permissions) 
            ? $this->role->permissions 
            : json_decode($this->role->permissions, true);

        return in_array($permission, $permissions ?? []);
    }

    /**
     * Get assignments where user is the assignee (technician).
     */
    public function assignmentsAsAssignee()
    {
        return $this->hasMany(Assignment::class, 'assigned_to');
    }

    /**
     * Required customer profile fields before allowing service requests.
     *
     * @return array<string, string>
     */
    public function requiredCustomerProfileFields(): array
    {
        return [
            'email' => 'Email',
            'phone' => 'Phone',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'Province/State',
            'postal_code' => 'Postal Code',
            'contact_person' => 'Contact Person',
            'tax_id' => 'Tax ID',
        ];
    }

    /**
     * Return missing required customer profile fields for this user.
     *
     * @return array<int, string>
     */
    public function missingCustomerProfileFields(): array
    {
        if (!$this->hasRole('customer')) {
            return [];
        }

        $requiredFields = $this->requiredCustomerProfileFields();
        $customerProfile = $this->customer;

        if (!$customerProfile) {
            return array_values($requiredFields);
        }

        $missingFields = [];
        foreach ($requiredFields as $field => $label) {
            if (blank($customerProfile->{$field})) {
                $missingFields[] = $label;
            }
        }

        return $missingFields;
    }

    /**
     * Check if a customer profile has all required details.
     */
    public function hasCompleteCustomerProfile(): bool
    {
        return count($this->missingCustomerProfileFields()) === 0;
    }

    /**
     * Send branded email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new BrandedVerifyEmailNotification());
    }
}
