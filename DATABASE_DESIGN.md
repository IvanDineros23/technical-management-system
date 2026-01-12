# Database Design - Technical Management System

## Entity Relationship Overview

This document provides detailed database schema design for the calibration and technical management system.

---

## Entity Relationship Diagram (Textual)

```
CUSTOMERS (1) ──→ (M) JOB_ORDERS
    ↓ (M)
CUSTOMER_EQUIPMENT

JOB_ORDERS (1) ──→ (M) JOB_ORDER_ITEMS
    ↓ (1)
ASSIGNMENTS (M) ──→ (1) USERS (TEC Personnel)
    ↓ (1)
CALIBRATIONS (1) ──→ (M) CALIBRATION_DATA
    ↓ (1)
CALIBRATION_REPORTS
    ↓ (M)
TECHNICAL_REVIEWS
    ↓ (M)
SIGNATORY_APPROVALS (M) ──→ (1) USERS (Signatories)
    ↓ (1)
CERTIFICATES
    ↓ (1)
RELEASES (1) ──→ (1) ACCOUNTING_RELEASES

STANDARDS (1) ──→ (M) STANDARD_CALIBRATIONS
EQUIPMENT (1) ──→ (M) EQUIPMENT_MAINTENANCE

USERS (M) ──→ (1) ROLES
AUDIT_LOGS ←─ (M) [All Critical Tables]
```

---

## Detailed Schema Design

### 1. Users & Authentication

#### **users**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
name                VARCHAR(255) NOT NULL
email               VARCHAR(255) UNIQUE NOT NULL
password            VARCHAR(255) NOT NULL
role_id             BIGINT UNSIGNED NOT NULL
department          VARCHAR(100)
employee_id         VARCHAR(50) UNIQUE
signature_path      VARCHAR(255)
phone               VARCHAR(50)
is_active           BOOLEAN DEFAULT TRUE
last_login_at       TIMESTAMP NULL
email_verified_at   TIMESTAMP NULL
remember_token      VARCHAR(100)
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_role_id (role_id)
INDEX idx_email (email)
INDEX idx_is_active (is_active)
```

#### **roles**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
name                VARCHAR(100) NOT NULL
slug                VARCHAR(100) UNIQUE NOT NULL
description         TEXT
permissions         JSON
is_active           BOOLEAN DEFAULT TRUE
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_slug (slug)
```

#### **permissions**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
role_id             BIGINT UNSIGNED NOT NULL
module              VARCHAR(100) NOT NULL
actions             JSON (create, read, update, delete, approve, etc.)
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_role_id (role_id)
INDEX idx_module (module)
```

---

### 2. Customer Management

#### **customers**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
code                VARCHAR(50) UNIQUE NOT NULL
name                VARCHAR(255) NOT NULL
business_name       VARCHAR(255)
address             TEXT
city                VARCHAR(100)
state               VARCHAR(100)
postal_code         VARCHAR(20)
country             VARCHAR(100) DEFAULT 'Philippines'
phone               VARCHAR(50)
email               VARCHAR(255)
contact_person      VARCHAR(255)
industry_type       VARCHAR(100)
tax_id              VARCHAR(100)
credit_terms        INT DEFAULT 0 (days)
is_active           BOOLEAN DEFAULT TRUE
notes               TEXT
created_by          BIGINT UNSIGNED
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_code (code)
INDEX idx_name (name)
INDEX idx_is_active (is_active)
```

#### **customer_contacts**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
customer_id         BIGINT UNSIGNED NOT NULL
name                VARCHAR(255) NOT NULL
position            VARCHAR(100)
phone               VARCHAR(50)
email               VARCHAR(255)
is_primary          BOOLEAN DEFAULT FALSE
notes               TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_customer_id (customer_id)
INDEX idx_is_primary (is_primary)
```

#### **customer_equipment**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
customer_id         BIGINT UNSIGNED NOT NULL
equipment_type      VARCHAR(255) NOT NULL
manufacturer        VARCHAR(255)
model               VARCHAR(255)
serial_number       VARCHAR(255)
id_number           VARCHAR(255)
description         TEXT
specifications      JSON
last_calibration    DATE
next_calibration    DATE
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_customer_id (customer_id)
INDEX idx_serial_number (serial_number)
```

---

### 3. Job Order Management

#### **job_orders**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
job_order_number    VARCHAR(50) UNIQUE NOT NULL
customer_id         BIGINT UNSIGNED NOT NULL
requested_by        VARCHAR(255)
request_date        DATE NOT NULL
required_date       DATE
priority            ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal'
status              VARCHAR(50) NOT NULL
total_items         INT DEFAULT 0
total_amount        DECIMAL(10,2)
discount            DECIMAL(10,2) DEFAULT 0
tax_amount          DECIMAL(10,2) DEFAULT 0
grand_total         DECIMAL(10,2)
notes               TEXT
special_instructions TEXT
created_by          BIGINT UNSIGNED NOT NULL
approved_by         BIGINT UNSIGNED NULL
approved_at         TIMESTAMP NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULL

INDEX idx_job_order_number (job_order_number)
INDEX idx_customer_id (customer_id)
INDEX idx_status (status)
INDEX idx_priority (priority)
INDEX idx_required_date (required_date)
INDEX idx_created_at (created_at)
```

#### **job_order_items**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
job_order_id        BIGINT UNSIGNED NOT NULL
item_number         INT NOT NULL
equipment_type      VARCHAR(255) NOT NULL
manufacturer        VARCHAR(255)
model               VARCHAR(255)
serial_number       VARCHAR(255)
id_number           VARCHAR(255)
range               VARCHAR(255)
resolution          VARCHAR(100)
accuracy            VARCHAR(100)
calibration_type    VARCHAR(100) (On-site/In-house)
calibration_points  INT
quantity            INT DEFAULT 1
unit_price          DECIMAL(10,2)
total_price         DECIMAL(10,2)
remarks             TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_job_order_id (job_order_id)
INDEX idx_serial_number (serial_number)
```

#### **job_order_statuses**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
job_order_id        BIGINT UNSIGNED NOT NULL
status              VARCHAR(50) NOT NULL
previous_status     VARCHAR(50)
changed_by          BIGINT UNSIGNED NOT NULL
changed_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
remarks             TEXT
metadata            JSON

INDEX idx_job_order_id (job_order_id)
INDEX idx_status (status)
INDEX idx_changed_at (changed_at)
```

#### **job_order_attachments**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
job_order_id        BIGINT UNSIGNED NOT NULL
file_name           VARCHAR(255) NOT NULL
file_path           VARCHAR(500) NOT NULL
file_type           VARCHAR(100)
file_size           INT
description         TEXT
uploaded_by         BIGINT UNSIGNED NOT NULL
uploaded_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_job_order_id (job_order_id)
```

---

### 4. Assignment & Scheduling

#### **assignments**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
job_order_id        BIGINT UNSIGNED NOT NULL
assigned_to         BIGINT UNSIGNED NOT NULL (user_id)
assigned_by         BIGINT UNSIGNED NOT NULL
assigned_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP
scheduled_date      DATE
scheduled_time      TIME
priority            ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal'
status              VARCHAR(50) NOT NULL
estimated_duration  INT (in hours)
actual_duration     INT (in hours)
location            VARCHAR(255)
notes               TEXT
started_at          TIMESTAMP NULL
completed_at        TIMESTAMP NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_job_order_id (job_order_id)
INDEX idx_assigned_to (assigned_to)
INDEX idx_scheduled_date (scheduled_date)
INDEX idx_status (status)
```

#### **schedules**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
assignment_id       BIGINT UNSIGNED NOT NULL
technician_id       BIGINT UNSIGNED NOT NULL
scheduled_start     DATETIME NOT NULL
scheduled_end       DATETIME NOT NULL
actual_start        DATETIME NULL
actual_end          DATETIME NULL
status              VARCHAR(50) DEFAULT 'scheduled'
notes               TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_assignment_id (assignment_id)
INDEX idx_technician_id (technician_id)
INDEX idx_scheduled_start (scheduled_start)
```

#### **workload_allocations**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
user_id             BIGINT UNSIGNED NOT NULL
date                DATE NOT NULL
allocated_hours     DECIMAL(5,2) DEFAULT 0
available_hours     DECIMAL(5,2) DEFAULT 8
utilization_rate    DECIMAL(5,2) DEFAULT 0
assignments_count   INT DEFAULT 0
created_at          TIMESTAMP
updated_at          TIMESTAMP

UNIQUE KEY unique_user_date (user_id, date)
INDEX idx_user_id (user_id)
INDEX idx_date (date)
```

---

### 5. Calibration Execution

#### **calibrations**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
calibration_number  VARCHAR(50) UNIQUE NOT NULL
job_order_item_id   BIGINT UNSIGNED NOT NULL
assignment_id       BIGINT UNSIGNED NOT NULL
performed_by        BIGINT UNSIGNED NOT NULL (user_id)
calibration_date    DATE NOT NULL
start_time          TIME
end_time            TIME
location            VARCHAR(255)
procedure_reference VARCHAR(255)
standards_used      JSON (array of standard IDs)
environmental_conditions JSON {
    temperature: value,
    humidity: value,
    pressure: value,
    conditions_acceptable: boolean
}
status              VARCHAR(50) NOT NULL
pass_fail           ENUM('pass', 'fail', 'conditional') NULL
remarks             TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_calibration_number (calibration_number)
INDEX idx_job_order_item_id (job_order_item_id)
INDEX idx_performed_by (performed_by)
INDEX idx_calibration_date (calibration_date)
INDEX idx_status (status)
```

#### **calibration_data**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
calibration_id      BIGINT UNSIGNED NOT NULL
measurement_number  INT NOT NULL
measurement_point   VARCHAR(100)
nominal_value       DECIMAL(15,6)
measured_value      DECIMAL(15,6)
error               DECIMAL(15,6)
unit                VARCHAR(50)
uncertainty         DECIMAL(15,6)
tolerance           DECIMAL(15,6)
pass_fail           ENUM('pass', 'fail')
readings            JSON (for multiple readings)
notes               TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_calibration_id (calibration_id)
INDEX idx_measurement_number (measurement_number)
```

#### **measurement_points**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
calibration_id      BIGINT UNSIGNED NOT NULL
point_number        INT NOT NULL
reference_value     DECIMAL(15,6) NOT NULL
uut_reading         DECIMAL(15,6) NOT NULL
error               DECIMAL(15,6)
uncertainty         DECIMAL(15,6)
acceptance_criteria VARCHAR(255)
status              ENUM('pass', 'fail')
readings_ascending  JSON
readings_descending JSON
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_calibration_id (calibration_id)
```

#### **uncertainty_calculations**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
calibration_id      BIGINT UNSIGNED NOT NULL
component           VARCHAR(255) NOT NULL (e.g., standard, resolution, repeatability)
value               DECIMAL(15,8) NOT NULL
distribution        VARCHAR(50) (normal, rectangular, triangular)
divisor             DECIMAL(5,2)
standard_uncertainty DECIMAL(15,8)
sensitivity_coefficient DECIMAL(10,4)
uncertainty_contribution DECIMAL(15,8)
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_calibration_id (calibration_id)
```

#### **calibration_reports**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
calibration_id      BIGINT UNSIGNED NOT NULL
report_number       VARCHAR(50) UNIQUE NOT NULL
file_name           VARCHAR(255) NOT NULL
file_path           VARCHAR(500) NOT NULL
file_size           INT
version             INT DEFAULT 1
status              VARCHAR(50)
uploaded_by         BIGINT UNSIGNED NOT NULL
uploaded_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP
verified_by         BIGINT UNSIGNED NULL
verified_at         TIMESTAMP NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_calibration_id (calibration_id)
INDEX idx_report_number (report_number)
```

---

### 6. Approval & Validation

#### **technical_reviews**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
calibration_id      BIGINT UNSIGNED NOT NULL
reviewer_id         BIGINT UNSIGNED NOT NULL (user_id)
review_date         DATE NOT NULL
review_time         TIME
result              ENUM('approved', 'rejected', 'conditional')
findings            TEXT
recommendations     TEXT
data_reviewed       BOOLEAN DEFAULT FALSE
calculations_verified BOOLEAN DEFAULT FALSE
standards_checked   BOOLEAN DEFAULT FALSE
status              VARCHAR(50)
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_calibration_id (calibration_id)
INDEX idx_reviewer_id (reviewer_id)
INDEX idx_result (result)
```

#### **signatory_approvals**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
calibration_id      BIGINT UNSIGNED NOT NULL
signatory_id        BIGINT UNSIGNED NOT NULL (user_id)
approval_level      ENUM('reviewer', 'approver') NOT NULL
approval_order      INT DEFAULT 1
approved_at         TIMESTAMP NULL
signature_path      VARCHAR(255)
signature_data      TEXT (base64 encoded signature)
comments            TEXT
status              ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'
rejection_reason    TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_calibration_id (calibration_id)
INDEX idx_signatory_id (signatory_id)
INDEX idx_status (status)
INDEX idx_approval_level (approval_level)
```

#### **approval_histories**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
approvable_type     VARCHAR(255) NOT NULL (polymorphic)
approvable_id       BIGINT UNSIGNED NOT NULL
approved_by         BIGINT UNSIGNED NOT NULL
action              VARCHAR(50) NOT NULL (approved, rejected, reviewed)
previous_status     VARCHAR(50)
new_status          VARCHAR(50)
comments            TEXT
metadata            JSON
approved_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_approvable (approvable_type, approvable_id)
INDEX idx_approved_by (approved_by)
```

---

### 7. Certificate Management

#### **certificates**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
certificate_number  VARCHAR(50) UNIQUE NOT NULL
job_order_item_id   BIGINT UNSIGNED NOT NULL
calibration_id      BIGINT UNSIGNED NOT NULL
issue_date          DATE NOT NULL
expiry_date         DATE
valid_until         DATE
qr_code             TEXT
qr_code_path        VARCHAR(255)
pdf_path            VARCHAR(500)
pdf_hash            VARCHAR(255) (for integrity check)
template_used       VARCHAR(100)
status              VARCHAR(50) NOT NULL
version             INT DEFAULT 1
revision_number     INT DEFAULT 0
is_current          BOOLEAN DEFAULT TRUE
issued_by           BIGINT UNSIGNED NOT NULL
reviewed_by         BIGINT UNSIGNED
approved_by         BIGINT UNSIGNED
supersedes_certificate_id BIGINT UNSIGNED NULL
notes               TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_certificate_number (certificate_number)
INDEX idx_job_order_item_id (job_order_item_id)
INDEX idx_calibration_id (calibration_id)
INDEX idx_status (status)
INDEX idx_qr_code (qr_code(100))
```

#### **certificate_revisions**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
certificate_id      BIGINT UNSIGNED NOT NULL
version             INT NOT NULL
revision_reason     TEXT NOT NULL
changes_made        TEXT
previous_pdf_path   VARCHAR(500)
revised_pdf_path    VARCHAR(500)
revised_by          BIGINT UNSIGNED NOT NULL
revised_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
approved_by         BIGINT UNSIGNED
approved_at         TIMESTAMP NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_certificate_id (certificate_id)
INDEX idx_version (version)
```

#### **certificate_verifications**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
certificate_id      BIGINT UNSIGNED NOT NULL
certificate_number  VARCHAR(50) NOT NULL
verified_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP
ip_address          VARCHAR(50)
user_agent          TEXT
location            VARCHAR(255)
verification_result ENUM('valid', 'invalid', 'expired', 'revoked')
qr_scanned          BOOLEAN DEFAULT TRUE
created_at          TIMESTAMP

INDEX idx_certificate_id (certificate_id)
INDEX idx_certificate_number (certificate_number)
INDEX idx_verified_at (verified_at)
```

---

### 8. Release & Accounting

#### **releases**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
release_number      VARCHAR(50) UNIQUE NOT NULL
job_order_id        BIGINT UNSIGNED NOT NULL
released_by         BIGINT UNSIGNED NOT NULL
released_to         VARCHAR(255) NOT NULL
release_date        DATE NOT NULL
release_time        TIME
delivery_method     ENUM('pickup', 'courier', 'email', 'hand_carry')
tracking_number     VARCHAR(255)
recipient_name      VARCHAR(255)
recipient_signature VARCHAR(255) (signature path)
status              VARCHAR(50)
notes               TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_release_number (release_number)
INDEX idx_job_order_id (job_order_id)
INDEX idx_release_date (release_date)
INDEX idx_status (status)
```

#### **accounting_releases**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
release_id          BIGINT UNSIGNED NOT NULL UNIQUE
invoice_id          BIGINT UNSIGNED NULL
payment_status      ENUM('unpaid', 'partial', 'paid', 'overdue') NOT NULL
amount_due          DECIMAL(10,2)
amount_paid         DECIMAL(10,2) DEFAULT 0
payment_date        DATE NULL
payment_method      VARCHAR(100)
payment_reference   VARCHAR(255)
verified_by         BIGINT UNSIGNED NOT NULL
verified_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP
can_release         BOOLEAN DEFAULT FALSE
remarks             TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_release_id (release_id)
INDEX idx_payment_status (payment_status)
INDEX idx_verified_by (verified_by)
```

#### **invoices**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
invoice_number      VARCHAR(50) UNIQUE NOT NULL
job_order_id        BIGINT UNSIGNED NOT NULL
customer_id         BIGINT UNSIGNED NOT NULL
issue_date          DATE NOT NULL
due_date            DATE NOT NULL
payment_terms       VARCHAR(100)
subtotal            DECIMAL(10,2) NOT NULL
discount            DECIMAL(10,2) DEFAULT 0
tax_rate            DECIMAL(5,2) DEFAULT 0
tax_amount          DECIMAL(10,2) DEFAULT 0
total               DECIMAL(10,2) NOT NULL
amount_paid         DECIMAL(10,2) DEFAULT 0
balance             DECIMAL(10,2)
payment_status      VARCHAR(50)
notes               TEXT
created_by          BIGINT UNSIGNED NOT NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_invoice_number (invoice_number)
INDEX idx_job_order_id (job_order_id)
INDEX idx_customer_id (customer_id)
INDEX idx_payment_status (payment_status)
INDEX idx_due_date (due_date)
```

---

### 9. Inventory & Standards

#### **equipment** (Lab's internal equipment)
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
equipment_code      VARCHAR(50) UNIQUE NOT NULL
name                VARCHAR(255) NOT NULL
category            VARCHAR(100)
manufacturer        VARCHAR(255)
model               VARCHAR(255)
serial_number       VARCHAR(255) UNIQUE
asset_number        VARCHAR(100)
purchase_date       DATE
purchase_cost       DECIMAL(10,2)
location            VARCHAR(255)
responsible_person  BIGINT UNSIGNED
status              ENUM('available', 'in_use', 'maintenance', 'retired')
specifications      JSON
calibration_required BOOLEAN DEFAULT FALSE
last_maintenance    DATE
next_maintenance    DATE
notes               TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_equipment_code (equipment_code)
INDEX idx_serial_number (serial_number)
INDEX idx_status (status)
INDEX idx_category (category)
```

#### **standards** (Reference standards)
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
standard_code       VARCHAR(50) UNIQUE NOT NULL
name                VARCHAR(255) NOT NULL
type                VARCHAR(100) (Master/Working/Check)
manufacturer        VARCHAR(255)
model               VARCHAR(255)
serial_number       VARCHAR(255) UNIQUE
range               VARCHAR(255)
accuracy            VARCHAR(255)
resolution          VARCHAR(100)
certificate_number  VARCHAR(255)
calibration_date    DATE
next_calibration_date DATE NOT NULL
calibration_interval INT DEFAULT 12 (months)
traceability        TEXT (NIST, PTB, etc.)
location            VARCHAR(255)
status              ENUM('valid', 'due', 'overdue', 'retired')
usage_count         INT DEFAULT 0
notes               TEXT
alert_days_before   INT DEFAULT 30
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_standard_code (standard_code)
INDEX idx_serial_number (serial_number)
INDEX idx_next_calibration_date (next_calibration_date)
INDEX idx_status (status)
```

#### **standard_calibrations**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
standard_id         BIGINT UNSIGNED NOT NULL
calibration_date    DATE NOT NULL
certificate_number  VARCHAR(255) NOT NULL
performed_by        VARCHAR(255) (External lab name)
next_due_date       DATE NOT NULL
certificate_path    VARCHAR(500)
measurement_results JSON
traceability        TEXT
cost                DECIMAL(10,2)
notes               TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_standard_id (standard_id)
INDEX idx_calibration_date (calibration_date)
INDEX idx_next_due_date (next_due_date)
```

#### **equipment_maintenance**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
equipment_id        BIGINT UNSIGNED NOT NULL
maintenance_type    ENUM('preventive', 'corrective', 'calibration', 'repair')
performed_by        BIGINT UNSIGNED
performed_at        TIMESTAMP NOT NULL
description         TEXT
parts_replaced      TEXT
cost                DECIMAL(10,2)
downtime_hours      INT
next_maintenance_date DATE
status              VARCHAR(50)
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_equipment_id (equipment_id)
INDEX idx_maintenance_type (maintenance_type)
INDEX idx_performed_at (performed_at)
```

---

### 10. Audit & Compliance

#### **audit_logs**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
user_id             BIGINT UNSIGNED NULL
action              VARCHAR(100) NOT NULL
model_type          VARCHAR(255) NOT NULL
model_id            BIGINT UNSIGNED NOT NULL
old_values          JSON NULL
new_values          JSON NULL
changed_fields      JSON NULL
ip_address          VARCHAR(50)
user_agent          TEXT
session_id          VARCHAR(255)
url                 VARCHAR(500)
method              VARCHAR(10)
description         TEXT
created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP

INDEX idx_user_id (user_id)
INDEX idx_action (action)
INDEX idx_model (model_type, model_id)
INDEX idx_created_at (created_at)
```

---

## Relationships Summary

```
customers
  → has many job_orders
  → has many customer_contacts
  → has many customer_equipment

job_orders
  → belongs to customer
  → has many job_order_items
  → has many job_order_statuses
  → has many job_order_attachments
  → has one assignment
  → has one release

job_order_items
  → belongs to job_order
  → has one calibration
  → has one certificate

assignments
  → belongs to job_order
  → belongs to user (assigned_to)
  → has many schedules
  → has many calibrations

calibrations
  → belongs to job_order_item
  → belongs to assignment
  → belongs to user (performed_by)
  → has many calibration_data
  → has many measurement_points
  → has many uncertainty_calculations
  → has one calibration_report
  → has many technical_reviews
  → has many signatory_approvals
  → has one certificate

certificates
  → belongs to calibration
  → belongs to job_order_item
  → has many certificate_revisions
  → has many certificate_verifications
  → has one release (through job_order)

releases
  → belongs to job_order
  → has one accounting_release

standards
  → has many standard_calibrations

equipment
  → has many equipment_maintenance

users
  → belongs to role
  → has many audit_logs
  → has many assignments (as technician)
  → has many calibrations (as performer)
  → has many reviews (as reviewer)
  → has many approvals (as signatory)
```

---

## Indexes Strategy

### Primary Indexes (already in schema)
- All IDs are indexed by default as PRIMARY KEY

### Foreign Key Indexes
- All foreign key columns have INDEX

### Query Optimization Indexes
- Status fields (frequently filtered)
- Date fields (ranges and sorting)
- Code/Number fields (unique lookups)
- Active/Boolean flags
- Composite indexes for common query patterns

---

## Data Integrity Constraints

### Referential Integrity
- All foreign keys should have ON DELETE RESTRICT or CASCADE based on business logic
- Critical data (calibrations, certificates) should use RESTRICT
- Dependent data (attachments, statuses) can use CASCADE

### Business Rules
- certificate_number must be unique
- job_order_number must be unique and follow pattern
- calibration_date cannot be in future (validated at application level)
- Standards must be valid (not expired) when used in calibration
- Release requires payment verification from accounting

### Data Validation
- Decimal precision appropriate for measurement data
- Enum values for fixed states
- JSON validation for complex structures
- Date ranges must be logical (start < end)

---

This schema design supports:
- Complete workflow tracking
- Audit trail for compliance
- Flexible measurement data storage
- Role-based access control
- Certificate versioning and traceability
- Payment and release control
- Equipment and standards management
