<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'code',
        'name',
        'business_name',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'email',
        'contact_person',
        'industry_type',
        'tax_id',
        'credit_terms',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate customer code if not provided
        static::creating(function ($model) {
            if (!$model->code) {
                $prefix = 'CUST';
                $lastCustomer = self::latest('id')->first();
                $nextNumber = ($lastCustomer ? (int)substr($lastCustomer->code, 4) : 0) + 1;
                $model->code = $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function jobOrders()
    {
        return $this->hasMany(JobOrder::class);
    }

    public function contacts()
    {
        return $this->hasMany(CustomerContact::class);
    }

    public function equipment()
    {
        return $this->hasMany(CustomerEquipment::class);
    }
}
