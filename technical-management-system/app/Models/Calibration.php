<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Calibration extends Model
{
    protected $fillable = [
        'calibration_number',
        'job_order_item_id',
        'assignment_id',
        'performed_by',
        'calibration_date',
        'start_time',
        'end_time',
        'location',
        'procedure_reference',
        'standards_used',
        'environmental_conditions',
        'status',
        'pass_fail',
        'remarks',
    ];

    protected $casts = [
        'standards_used' => 'array',
        'environmental_conditions' => 'array',
        'calibration_date' => 'date',
    ];

    // Relationships
    public function jobOrderItem(): BelongsTo
    {
        return $this->belongsTo(JobOrderItem::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function measurementPoints(): HasMany
    {
        return $this->hasMany(MeasurementPoint::class);
    }

    public function technicalReview(): BelongsTo
    {
        return $this->belongsTo(TechnicalReview::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeSubmittedForReview($query)
    {
        return $query->where('status', 'submitted_for_review');
    }

    // Methods
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSubmittedForReview(): bool
    {
        return $this->status === 'submitted_for_review';
    }
}
