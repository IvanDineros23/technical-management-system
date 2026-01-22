<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_order_id',
        'payment_code',
        'amount_paid',
        'paid_at',
        'status',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Relationship: Payment belongs to JobOrder
     */
    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class);
    }

    /**
     * Relationship: Payment was verified by a User
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
