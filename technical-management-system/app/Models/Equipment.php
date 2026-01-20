<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'equipment_code',
        'name',
        'category',
        'manufacturer',
        'model',
        'serial_number',
        'asset_number',
        'purchase_date',
        'purchase_cost',
        'location',
        'responsible_person',
        'status',
        'specifications',
        'calibration_required',
        'last_maintenance',
        'next_maintenance',
        'notes',
    ];

    protected $casts = [
        'specifications' => 'array',
        'calibration_required' => 'boolean',
        'purchase_date' => 'date',
        'last_maintenance' => 'date',
        'next_maintenance' => 'date',
        'purchase_cost' => 'decimal:2',
    ];

    public function maintenanceRecords()
    {
        return $this->hasMany(EquipmentMaintenance::class);
    }

    public function responsiblePerson()
    {
        return $this->belongsTo(User::class, 'responsible_person');
    }
}
