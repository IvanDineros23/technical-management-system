<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'released_by',
        'released_at',
        'released_to',
        'delivery_method',
        'notes',
    ];

    protected $casts = [
        'released_at' => 'datetime',
    ];

    /**
     * Relationship: Release belongs to Certificate
     */
    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }

    /**
     * Relationship: Released by User
     */
    public function releasedBy()
    {
        return $this->belongsTo(User::class, 'released_by');
    }
}
