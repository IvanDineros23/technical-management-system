<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'certificate_number',
        'job_order_id',
        'job_order_item_id',
        'calibration_id',
        'issue_date',
        'expiry_date',
        'valid_until',
        'qr_code',
        'qr_code_path',
        'pdf_path',
        'pdf_hash',
        'template_used',
        'status',
        'version',
        'revision_number',
        'is_current',
        'issued_by',
        'reviewed_by',
        'approved_by',
        'signed_by',
        'signed_at',
        'supersedes_certificate_id',
        'notes',
        'generated_at',
        'released_at',
        'released_to',
        'released_by',
        'delivery_method',
        'release_notes',
        'data',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'valid_until' => 'date',
        'is_current' => 'boolean',
        'generated_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    // Relationships
    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function jobOrderItem()
    {
        return $this->belongsTo(JobOrderItem::class);
    }

    public function calibration()
    {
        return $this->belongsTo(Calibration::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function releasedBy()
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    // Helper method to generate certificate number
    public static function generateCertificateNumber()
    {
        $year = date('Y');
        $lastCert = self::where('certificate_number', 'like', "CERT-{$year}-%")
            ->orderBy('certificate_number', 'desc')
            ->first();
        
        if ($lastCert) {
            $lastNumber = (int) substr($lastCert->certificate_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "CERT-{$year}-{$newNumber}";
    }
}
