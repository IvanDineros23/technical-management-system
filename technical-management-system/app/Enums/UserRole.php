<?php

namespace App\Enums;

enum UserRole: string
{
    case MARKETING = 'marketing';
    case TEC_PERSONNEL = 'tec_personnel';
    case SIGNATORY_REVIEWER = 'signatory_reviewer';
    case SIGNATORY_APPROVER = 'signatory_approver';
    case ACCOUNTING = 'accounting';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match($this) {
            self::MARKETING => 'Marketing',
            self::TEC_PERSONNEL => 'TEC Personnel',
            self::SIGNATORY_REVIEWER => 'Signatory (Reviewer)',
            self::SIGNATORY_APPROVER => 'Signatory (Approver)',
            self::ACCOUNTING => 'Accounting',
            self::ADMIN => 'Administrator',
        };
    }

    public function permissions(): array
    {
        return match($this) {
            self::MARKETING => [
                'job_orders' => ['create', 'read', 'update'],
                'customers' => ['create', 'read', 'update', 'delete'],
                'reports' => ['read'],
            ],
            self::TEC_PERSONNEL => [
                'calibrations' => ['create', 'read', 'update'],
                'assignments' => ['read'],
                'standards' => ['read'],
                'equipment' => ['read'],
            ],
            self::SIGNATORY_REVIEWER => [
                'approvals' => ['read', 'review'],
                'calibrations' => ['read'],
                'certificates' => ['read'],
            ],
            self::SIGNATORY_APPROVER => [
                'approvals' => ['read', 'approve'],
                'calibrations' => ['read'],
                'certificates' => ['read', 'approve'],
            ],
            self::ACCOUNTING => [
                'releases' => ['create', 'read', 'update'],
                'invoices' => ['create', 'read', 'update'],
                'reports' => ['read'],
            ],
            self::ADMIN => [
                '*' => ['*'],
            ],
        };
    }
}
