# Implementation Guide - Technical Management System

## Quick Start Guide

This guide provides step-by-step instructions for implementing the calibration management system.

---

## Phase 1: Environment Setup

### 1.1 Prerequisites
- PHP >= 8.1
- Composer
- XAMPP (MySQL only)
- Node.js & NPM (for Tailwind CSS)
- Git

### 1.2 Laravel Installation

```bash
# Install Laravel via Composer
composer create-project laravel/laravel technical-management-system

# Navigate to project directory
cd technical-management-system

# Install development dependencies
composer require --dev laravel/pint
composer require --dev barryvdh/laravel-debugbar
```

### 1.3 Database Configuration

Edit `.env` file:
```env
APP_NAME="Technical Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=technical_management_db
DB_USERNAME=root
DB_PASSWORD=

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=file

# Queue (for async operations)
QUEUE_CONNECTION=database
```

### 1.4 Create Database

```sql
-- In MySQL (XAMPP phpMyAdmin or command line)
CREATE DATABASE technical_management_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 1.5 Install Frontend Dependencies

```bash
# Install Tailwind CSS (run each command separately)
npm install -D tailwindcss postcss autoprefixer

# Install additional UI dependencies
npm install alpinejs @tailwindcss/forms chart.js
```

**Note:** Tailwind CSS v4 doesn't require the `init` command. The configuration files (`tailwind.config.js` and `postcss.config.js`) are provided in the project structure below.

---

## Phase 2: Authentication & Authorization

### 2.1 Install Laravel Breeze (Recommended)

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run dev
php artisan migrate
```

### 2.2 Create Role-Based Access Control

#### Create Enums

Create `app/Enums/UserRole.php`:
```php
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
}
```

#### Create Middleware

```bash
php artisan make:middleware RoleMiddleware
php artisan make:middleware PermissionMiddleware
php artisan make:middleware AuditLogMiddleware
```

#### Register Middleware in `app/Http/Kernel.php`

```php
protected $middlewareAliases = [
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'permission' => \App\Http\Middleware\PermissionMiddleware::class,
    'audit' => \App\Http\Middleware\AuditLogMiddleware::class,
];
```

---

## Phase 3: Database Migrations

### 3.1 Create Core Migrations

```bash
# Users & Roles
php artisan make:migration create_roles_table
php artisan make:migration add_role_to_users_table

# Customers
php artisan make:migration create_customers_table
php artisan make:migration create_customer_contacts_table
php artisan make:migration create_customer_equipment_table

# Job Orders
php artisan make:migration create_job_orders_table
php artisan make:migration create_job_order_items_table
php artisan make:migration create_job_order_statuses_table
php artisan make:migration create_job_order_attachments_table

# Assignments
php artisan make:migration create_assignments_table
php artisan make:migration create_schedules_table
php artisan make:migration create_workload_allocations_table

# Calibrations
php artisan make:migration create_calibrations_table
php artisan make:migration create_calibration_data_table
php artisan make:migration create_measurement_points_table
php artisan make:migration create_uncertainty_calculations_table
php artisan make:migration create_calibration_reports_table

# Approvals
php artisan make:migration create_technical_reviews_table
php artisan make:migration create_signatory_approvals_table
php artisan make:migration create_approval_histories_table

# Certificates
php artisan make:migration create_certificates_table
php artisan make:migration create_certificate_revisions_table
php artisan make:migration create_certificate_verifications_table

# Releases
php artisan make:migration create_releases_table
php artisan make:migration create_accounting_releases_table
php artisan make:migration create_invoices_table

# Inventory
php artisan make:migration create_equipment_table
php artisan make:migration create_standards_table
php artisan make:migration create_standard_calibrations_table
php artisan make:migration create_equipment_maintenance_table

# Audit
php artisan make:migration create_audit_logs_table
```

### 3.2 Run Migrations

```bash
php artisan migrate
```

---

## Phase 4: Models & Relationships

### 4.1 Create Models

```bash
# Customer models
php artisan make:model Customer
php artisan make:model CustomerContact
php artisan make:model CustomerEquipment

# Job Order models
php artisan make:model JobOrder
php artisan make:model JobOrderItem
php artisan make:model JobOrderStatus
php artisan make:model JobOrderAttachment

# Assignment models
php artisan make:model Assignment
php artisan make:model Schedule
php artisan make:model WorkloadAllocation

# Calibration models
php artisan make:model Calibration
php artisan make:model CalibrationData
php artisan make:model MeasurementPoint
php artisan make:model UncertaintyCalculation
php artisan make:model CalibrationReport

# Approval models
php artisan make:model TechnicalReview
php artisan make:model SignatoryApproval
php artisan make:model ApprovalHistory

# Certificate models
php artisan make:model Certificate
php artisan make:model CertificateRevision
php artisan make:model CertificateVerification

# Release models
php artisan make:model Release
php artisan make:model AccountingRelease
php artisan make:model Invoice

# Inventory models
php artisan make:model Equipment
php artisan make:model Standard
php artisan make:model StandardCalibration
php artisan make:model EquipmentMaintenance

# Audit model
php artisan make:model AuditLog
```

### 4.2 Define Model Relationships Example

Example `app/Models/JobOrder.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'job_order_number',
        'customer_id',
        'requested_by',
        'request_date',
        'required_date',
        'priority',
        'status',
        'total_items',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'request_date' => 'date',
        'required_date' => 'date',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(JobOrderItem::class);
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(JobOrderStatus::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(JobOrderAttachment::class);
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    public function release(): HasOne
    {
        return $this->hasOne(Release::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'closed']);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Accessors & Mutators
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'assigned' => 'yellow',
            'in_progress' => 'indigo',
            'approved' => 'green',
            'released' => 'green',
            'rejected' => 'red',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
```

---

## Phase 5: Service Classes

### 5.1 Create Services

```bash
mkdir app/Services
touch app/Services/JobOrderService.php
touch app/Services/CalibrationService.php
touch app/Services/CertificateGenerationService.php
touch app/Services/PDFGenerationService.php
touch app/Services/QRCodeService.php
touch app/Services/NotificationService.php
touch app/Services/WorkflowService.php
touch app/Services/AuditService.php
```

### 5.2 Example Service: WorkflowService

```php
<?php

namespace App\Services;

use App\Models\JobOrder;
use App\Models\JobOrderStatus;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;

class WorkflowService
{
    public function __construct(
        private AuditService $auditService
    ) {}

    public function transitionJobOrder(JobOrder $jobOrder, string $newStatus, ?string $remarks = null): bool
    {
        return DB::transaction(function () use ($jobOrder, $newStatus, $remarks) {
            $oldStatus = $jobOrder->status;

            // Validate transition
            if (!$this->isValidTransition($oldStatus, $newStatus)) {
                throw new \Exception("Invalid status transition from {$oldStatus} to {$newStatus}");
            }

            // Update job order
            $jobOrder->status = $newStatus;
            $jobOrder->save();

            // Log status change
            JobOrderStatus::create([
                'job_order_id' => $jobOrder->id,
                'status' => $newStatus,
                'previous_status' => $oldStatus,
                'changed_by' => auth()->id(),
                'remarks' => $remarks,
            ]);

            // Audit log
            $this->auditService->log(
                'STATUS_CHANGE',
                JobOrder::class,
                $jobOrder->id,
                ['status' => $oldStatus],
                ['status' => $newStatus]
            );

            return true;
        });
    }

    private function isValidTransition(string $from, string $to): bool
    {
        $transitions = [
            'draft' => ['submitted', 'cancelled'],
            'submitted' => ['assigned', 'cancelled'],
            'assigned' => ['in_progress', 'on_hold', 'cancelled'],
            'in_progress' => ['awaiting_validation', 'on_hold'],
            'awaiting_validation' => ['awaiting_approval', 'in_progress'],
            'awaiting_approval' => ['approved', 'in_progress'],
            'approved' => ['certificate_ready'],
            'certificate_ready' => ['ready_for_release'],
            'ready_for_release' => ['released'],
            'released' => ['closed'],
            'on_hold' => ['assigned', 'in_progress', 'cancelled'],
        ];

        return in_array($to, $transitions[$from] ?? []);
    }
}
```

---

## Phase 6: Controllers & Routes

### 6.1 Create Controllers

```bash
# Dashboard
php artisan make:controller Dashboard/DashboardController

# Job Orders
php artisan make:controller JobOrder/JobOrderController --resource
php artisan make:controller JobOrder/JobOrderItemController
php artisan make:controller JobOrder/JobOrderStatusController

# Assignments
php artisan make:controller Assignment/AssignmentController --resource
php artisan make:controller Assignment/ScheduleController

# Calibration
php artisan make:controller Calibration/CalibrationController --resource
php artisan make:controller Calibration/CalibrationReportController

# Approvals
php artisan make:controller Approval/ValidationController
php artisan make:controller Approval/SignatoryApprovalController

# Certificates
php artisan make:controller Certificate/CertificateController --resource
php artisan make:controller Certificate/CertificateGenerationController
php artisan make:controller Certificate/CertificateVerificationController

# Releases
php artisan make:controller Release/ReleaseController --resource
php artisan make:controller Release/AccountingReleaseController

# Inventory
php artisan make:controller Inventory/EquipmentController --resource
php artisan make:controller Inventory/StandardController --resource

# Customers
php artisan make:controller Customer/CustomerController --resource

# Reports
php artisan make:controller Reports/ReportController
php artisan make:controller Reports/AnalyticsController
```

### 6.2 Define Routes

Edit `routes/web.php`:
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\JobOrder\JobOrderController;
// ... other imports

Route::get('/', function () {
    return redirect()->route('login');
});

// Public Certificate Verification
Route::get('/verify/{certificate_number}', [CertificateVerificationController::class, 'verify'])
    ->name('certificate.verify');

// Authenticated Routes
Route::middleware(['auth', 'audit'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Job Orders
    Route::middleware(['role:marketing,admin'])->group(function () {
        Route::resource('job-orders', JobOrderController::class);
    });

    // Assignments
    Route::middleware(['role:marketing,admin'])->group(function () {
        Route::resource('assignments', AssignmentController::class);
    });

    // Calibrations
    Route::middleware(['role:tec_personnel,admin'])->group(function () {
        Route::resource('calibrations', CalibrationController::class);
    });

    // Approvals
    Route::middleware(['role:signatory_reviewer,signatory_approver,admin'])->group(function () {
        Route::get('/approvals', [SignatoryApprovalController::class, 'index'])
            ->name('approvals.index');
        Route::post('/approvals/{calibration}/approve', [SignatoryApprovalController::class, 'approve'])
            ->name('approvals.approve');
    });

    // Certificates
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('certificates', CertificateController::class);
    });

    // Releases
    Route::middleware(['role:accounting,admin'])->group(function () {
        Route::resource('releases', ReleaseController::class);
    });

    // Inventory
    Route::resource('equipment', EquipmentController::class);
    Route::resource('standards', StandardController::class);

    // Customers
    Route::resource('customers', CustomerController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');
});

require __DIR__.'/auth.php';
```

---

## Phase 7: Views & UI Components

### 7.1 Configure Tailwind CSS

Edit `tailwind.config.js`:
```javascript
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0f9ff',
          100: '#e0f2fe',
          // ... add your color palette
          600: '#0284c7',
          700: '#0369a1',
        }
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
```

### 7.2 Create Base Layout

Create `resources/views/layouts/app.blade.php`:
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TMS') }} - @yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="py-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
```

### 7.3 Create Reusable Components

Create `resources/views/components/status-badge.blade.php`:
```blade
@props(['status', 'type' => 'job-order'])

@php
$colors = [
    'draft' => 'bg-gray-100 text-gray-800',
    'submitted' => 'bg-blue-100 text-blue-800',
    'assigned' => 'bg-yellow-100 text-yellow-800',
    'in_progress' => 'bg-indigo-100 text-indigo-800',
    'approved' => 'bg-green-100 text-green-800',
    'released' => 'bg-green-200 text-green-900',
    'rejected' => 'bg-red-100 text-red-800',
];

$color = $colors[$status] ?? 'bg-gray-100 text-gray-800';
@endphp

<span {{ $attributes->merge(['class' => "px-2 py-1 text-xs font-semibold rounded-full {$color}"]) }}>
    {{ ucfirst(str_replace('_', ' ', $status)) }}
</span>
```

### 7.4 Create Data Table Component

Create `resources/views/components/data-table.blade.php`:
```blade
@props(['headers' => [], 'rows' => []])

<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                @foreach($headers as $header)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            {{ $slot }}
        </tbody>
    </table>
</div>
```

---

## Phase 8: PDF & QR Code Generation

### 8.1 Install Required Packages

```bash
composer require barryvdh/laravel-dompdf
composer require simplesoftwareio/simple-qrcode
```

### 8.2 Publish Configuration

```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### 8.3 Create PDF Service

```php
<?php

namespace App\Services;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PDFGenerationService
{
    public function generateCertificate(Certificate $certificate): string
    {
        // Generate QR Code
        $qrCode = $this->generateQRCode($certificate);
        
        // Prepare data
        $data = [
            'certificate' => $certificate,
            'jobOrder' => $certificate->jobOrderItem->jobOrder,
            'calibration' => $certificate->calibration,
            'customer' => $certificate->jobOrderItem->jobOrder->customer,
            'qrCode' => $qrCode,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('certificates.pdf-template', $data);
        
        // Save to storage
        $filename = "certificates/{$certificate->certificate_number}.pdf";
        $pdf->save(storage_path("app/public/{$filename}"));

        return $filename;
    }

    private function generateQRCode(Certificate $certificate): string
    {
        $verificationUrl = route('certificate.verify', $certificate->certificate_number);
        return QrCode::size(150)->generate($verificationUrl);
    }
}
```

---

## Phase 9: Seeders for Testing

### 9.1 Create Seeders

```bash
php artisan make:seeder RoleSeeder
php artisan make:seeder UserSeeder
php artisan make:seeder CustomerSeeder
php artisan make:seeder StandardSeeder
```

### 9.2 Example: RoleSeeder

```php
<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Marketing',
                'slug' => 'marketing',
                'permissions' => json_encode([
                    'job_orders' => ['create', 'read', 'update'],
                    'customers' => ['create', 'read', 'update'],
                ]),
            ],
            [
                'name' => 'TEC Personnel',
                'slug' => 'tec_personnel',
                'permissions' => json_encode([
                    'calibrations' => ['create', 'read', 'update'],
                    'assignments' => ['read'],
                ]),
            ],
            [
                'name' => 'Signatory (Reviewer)',
                'slug' => 'signatory_reviewer',
                'permissions' => json_encode([
                    'approvals' => ['read', 'review'],
                    'calibrations' => ['read'],
                ]),
            ],
            [
                'name' => 'Signatory (Approver)',
                'slug' => 'signatory_approver',
                'permissions' => json_encode([
                    'approvals' => ['read', 'approve'],
                    'calibrations' => ['read'],
                ]),
            ],
            [
                'name' => 'Accounting',
                'slug' => 'accounting',
                'permissions' => json_encode([
                    'releases' => ['create', 'read', 'update'],
                    'invoices' => ['create', 'read', 'update'],
                ]),
            ],
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'permissions' => json_encode(['*' => ['*']]),
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
```

### 9.3 Run Seeders

```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
```

---

## Phase 10: Testing & Deployment

### 10.1 Create Feature Tests

```bash
php artisan make:test JobOrderTest
php artisan make:test CalibrationTest
php artisan make:test ApprovalTest
```

### 10.2 Run Tests

```bash
php artisan test
```

### 10.3 Development Server

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

### 10.4 Production Build

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Best Practices Implementation

### Security
✅ Use Form Request validation
✅ Implement CSRF protection
✅ Sanitize user inputs
✅ Use prepared statements (Eloquent)
✅ Implement role-based access control
✅ Store passwords hashed (bcrypt)
✅ Validate file uploads

### Performance
✅ Use eager loading to prevent N+1
✅ Implement caching where appropriate
✅ Add database indexes
✅ Use pagination for large datasets
✅ Optimize images and assets
✅ Use Laravel Octane for production (optional)

### Code Quality
✅ Follow PSR-12 coding standards
✅ Use type hints
✅ Write descriptive method names
✅ Keep controllers thin, use services
✅ Comment complex logic
✅ Use dependency injection

### Database
✅ Use migrations for version control
✅ Add proper indexes
✅ Use foreign key constraints
✅ Implement soft deletes for critical data
✅ Regular backups

### Testing
✅ Write feature tests for critical paths
✅ Test happy path and error scenarios
✅ Use factories for test data
✅ Mock external services

---

## Common Commands Reference

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Database
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed

# Queue workers (for background jobs)
php artisan queue:work

# Generate application key
php artisan key:generate

# Storage link
php artisan storage:link

# Code formatting
./vendor/bin/pint

# List routes
php artisan route:list
```

---

## Recommended VS Code Extensions

- PHP Intelephense
- Laravel Blade Snippets
- Laravel Extra Intellisense
- Tailwind CSS IntelliSense
- PHP Debug
- GitLens

---

## Next Steps After Basic Setup

1. **Implement Dashboard with widgets**
2. **Create Job Order CRUD**
3. **Build Assignment system**
4. **Develop Calibration interface**
5. **Implement Approval workflow**
6. **Setup Certificate generation**
7. **Create Release management**
8. **Add Reporting module**
9. **Implement Notifications**
10. **Add Advanced features**

---

## Support & Documentation

- Laravel Documentation: https://laravel.com/docs
- Tailwind CSS: https://tailwindcss.com/docs
- Laravel Best Practices: https://github.com/alexeymezenin/laravel-best-practices

---

This guide provides a solid foundation for building your calibration management system. Follow the phases sequentially for best results.
