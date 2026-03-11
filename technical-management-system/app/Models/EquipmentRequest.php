<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\JobOrder;

class EquipmentRequest extends Model
{
    protected $fillable = [
        'equipment_id',
        'equipment_name',
        'job_order_id',
        'requested_by',
        'approved_by',
        'purpose',
        'status',
        'admin_notes',
        'approved_at',
        'returned_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
