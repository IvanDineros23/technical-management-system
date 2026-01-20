<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentMaintenance extends Model
{
    protected $table = 'equipment_maintenance';

    protected $fillable = [
        'equipment_id',
        'maintenance_type',
        'performed_by',
        'performed_at',
        'description',
        'parts_replaced',
        'cost',
        'downtime_hours',
        'next_maintenance_date',
        'status',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'next_maintenance_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
