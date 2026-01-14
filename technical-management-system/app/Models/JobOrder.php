<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'job_order_number',
        'customer_id',
        'service_type',
        'service_description',
        'expected_start_date',
        'expected_completion_date',
        'service_address',
        'city',
        'province',
        'postal_code',
        'requested_by',
        'request_date',
        'required_date',
        'priority',
        'status',
        'total_items',
        'total_amount',
        'discount',
        'tax_amount',
        'grand_total',
        'notes',
        'special_instructions',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'request_date' => 'date',
        'required_date' => 'date',
        'expected_start_date' => 'date',
        'expected_completion_date' => 'date',
        'approved_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(JobOrderItem::class);
    }

    public function statuses()
    {
        return $this->hasMany(JobOrderStatus::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
