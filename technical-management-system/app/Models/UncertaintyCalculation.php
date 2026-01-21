<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UncertaintyCalculation extends Model
{
    protected $fillable = [
        'calibration_id',
        'measurement_point_id',
        'standard_uncertainty',
        'coverage_factor',
        'expanded_uncertainty',
        'confidence_level',
        'method',
        'notes',
    ];

    protected $casts = [
        'standard_uncertainty' => 'float',
        'coverage_factor' => 'float',
        'expanded_uncertainty' => 'float',
    ];

    // Relationships
    public function calibration(): BelongsTo
    {
        return $this->belongsTo(Calibration::class);
    }

    public function measurementPoint(): BelongsTo
    {
        return $this->belongsTo(MeasurementPoint::class);
    }

    // Methods
    public function calculateExpandedUncertainty(): float
    {
        return $this->standard_uncertainty * $this->coverage_factor;
    }
}
