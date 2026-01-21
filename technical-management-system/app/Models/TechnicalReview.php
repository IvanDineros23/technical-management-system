<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicalReview extends Model
{
    protected $fillable = [
        'calibration_id',
        'reviewer_id',
        'review_date',
        'review_time',
        'result',
        'findings',
        'recommendations',
        'data_reviewed',
        'calculations_verified',
        'standards_checked',
        'status',
    ];

    protected $casts = [
        'review_date' => 'date',
        'data_reviewed' => 'boolean',
        'calculations_verified' => 'boolean',
        'standards_checked' => 'boolean',
    ];

    // Relationships
    public function calibration(): BelongsTo
    {
        return $this->belongsTo(Calibration::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('result', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('result', 'rejected');
    }

    public function scopeConditional($query)
    {
        return $query->where('result', 'conditional');
    }

    // Methods
    public function isApproved(): bool
    {
        return $this->result === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->result === 'rejected';
    }

    public function isConditional(): bool
    {
        return $this->result === 'conditional';
    }

    public function allChecksCompleted(): bool
    {
        return $this->data_reviewed && $this->calculations_verified && $this->standards_checked;
    }
}
