<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeasurementPoint extends Model
{
    protected $fillable = [
        'calibration_id',
        'point_number',
        'reference_value',
        'uut_reading',
        'error',
        'uncertainty',
        'acceptance_criteria',
        'status',
        'readings_ascending',
        'readings_descending',
    ];

    protected $casts = [
        'readings_ascending' => 'array',
        'readings_descending' => 'array',
        'reference_value' => 'float',
        'uut_reading' => 'float',
        'error' => 'float',
        'uncertainty' => 'float',
    ];

    // Relationships
    public function calibration(): BelongsTo
    {
        return $this->belongsTo(Calibration::class);
    }

    // Scopes
    public function scopePass($query)
    {
        return $query->where('status', 'pass');
    }

    public function scopeFail($query)
    {
        return $query->where('status', 'fail');
    }

    // Methods
    public function calculateError(): float
    {
        return $this->uut_reading - $this->reference_value;
    }

    public function isWithinTolerance(): bool
    {
        return $this->status === 'pass';
    }
}
