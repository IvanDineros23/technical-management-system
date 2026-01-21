<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Assignment extends Model
{
    protected $fillable = [
        'job_order_id',
        'assigned_to',
        'assigned_by',
        'scheduled_date',
        'scheduled_time',
        'priority',
        'status',
        'location',
        'notes',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function report(): HasOne
    {
        return $this->hasOne(Report::class);
    }

    public function jobOrderItems(): HasManyThrough
    {
        return $this->hasManyThrough(JobOrderItem::class, JobOrder::class);
    }

    public function calibrations(): HasMany
    {
        return $this->hasMany(Calibration::class);
    }
}
