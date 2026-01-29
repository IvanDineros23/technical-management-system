<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'changed_fields',
        'ip_address',
        'user_agent',
        'session_id',
        'url',
        'method',
        'description',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'changed_fields' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}