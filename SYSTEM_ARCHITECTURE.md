# Technical Management System - System Architecture

## System Overview
This is an industrial calibration and technical management system following LIMS (Laboratory Information Management System) workflow principles. The system manages the complete lifecycle of calibration job orders from creation to certificate release and verification.

---

## 1. Project Structure

```
technical-management-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── Dashboard/
│   │   │   │   └── DashboardController.php
│   │   │   ├── JobOrder/
│   │   │   │   ├── JobOrderController.php
│   │   │   │   ├── JobOrderItemController.php
│   │   │   │   └── JobOrderStatusController.php
│   │   │   ├── Assignment/
│   │   │   │   ├── AssignmentController.php
│   │   │   │   └── ScheduleController.php
│   │   │   ├── Calibration/
│   │   │   │   ├── CalibrationController.php
│   │   │   │   ├── CalibrationReportController.php
│   │   │   │   └── CalibrationDataController.php
│   │   │   ├── Approval/
│   │   │   │   ├── ValidationController.php
│   │   │   │   ├── SignatoryApprovalController.php
│   │   │   │   └── TechnicalReviewController.php
│   │   │   ├── Certificate/
│   │   │   │   ├── CertificateController.php
│   │   │   │   ├── CertificateGenerationController.php
│   │   │   │   └── CertificateVerificationController.php
│   │   │   ├── Release/
│   │   │   │   ├── ReleaseController.php
│   │   │   │   └── AccountingReleaseController.php
│   │   │   ├── Inventory/
│   │   │   │   ├── EquipmentController.php
│   │   │   │   ├── StandardController.php
│   │   │   │   └── CalibrationHistoryController.php
│   │   │   ├── Customer/
│   │   │   │   ├── CustomerController.php
│   │   │   │   └── CustomerEquipmentController.php
│   │   │   └── Reports/
│   │   │       ├── ReportController.php
│   │   │       ├── AnalyticsController.php
│   │   │       └── AuditReportController.php
│   │   ├── Middleware/
│   │   │   ├── RoleMiddleware.php
│   │   │   ├── PermissionMiddleware.php
│   │   │   └── AuditLogMiddleware.php
│   │   └── Requests/
│   │       ├── JobOrder/
│   │       ├── Calibration/
│   │       └── Certificate/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   ├── Customer/
│   │   │   ├── Customer.php
│   │   │   ├── CustomerContact.php
│   │   │   └── CustomerEquipment.php
│   │   ├── JobOrder/
│   │   │   ├── JobOrder.php
│   │   │   ├── JobOrderItem.php
│   │   │   ├── JobOrderStatus.php
│   │   │   └── JobOrderAttachment.php
│   │   ├── Assignment/
│   │   │   ├── Assignment.php
│   │   │   ├── Schedule.php
│   │   │   └── WorkloadAllocation.php
│   │   ├── Calibration/
│   │   │   ├── Calibration.php
│   │   │   ├── CalibrationData.php
│   │   │   ├── CalibrationReport.php
│   │   │   ├── MeasurementPoint.php
│   │   │   └── UncertaintyCalculation.php
│   │   ├── Approval/
│   │   │   ├── TechnicalReview.php
│   │   │   ├── SignatoryApproval.php
│   │   │   └── ApprovalHistory.php
│   │   ├── Certificate/
│   │   │   ├── Certificate.php
│   │   │   ├── CertificateRevision.php
│   │   │   └── CertificateVerification.php
│   │   ├── Release/
│   │   │   ├── Release.php
│   │   │   ├── AccountingRelease.php
│   │   │   └── Invoice.php
│   │   ├── Inventory/
│   │   │   ├── Equipment.php
│   │   │   ├── Standard.php
│   │   │   ├── StandardCalibration.php
│   │   │   └── EquipmentMaintenance.php
│   │   └── AuditLog.php
│   ├── Services/
│   │   ├── JobOrderService.php
│   │   ├── CalibrationService.php
│   │   ├── CertificateGenerationService.php
│   │   ├── PDFGenerationService.php
│   │   ├── QRCodeService.php
│   │   ├── NotificationService.php
│   │   ├── WorkflowService.php
│   │   └── AuditService.php
│   ├── Repositories/
│   │   ├── JobOrderRepository.php
│   │   ├── CalibrationRepository.php
│   │   ├── CertificateRepository.php
│   │   └── CustomerRepository.php
│   ├── Enums/
│   │   ├── JobOrderStatus.php
│   │   ├── CalibrationStatus.php
│   │   ├── CertificateStatus.php
│   │   ├── ApprovalStatus.php
│   │   ├── ReleaseStatus.php
│   │   └── UserRole.php
│   └── Observers/
│       ├── JobOrderObserver.php
│       ├── CalibrationObserver.php
│       └── CertificateObserver.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   ├── auth.blade.php
│       │   └── public.blade.php
│       ├── components/
│       │   ├── status-badge.blade.php
│       │   ├── workflow-tracker.blade.php
│       │   ├── data-table.blade.php
│       │   └── approval-card.blade.php
│       ├── dashboard/
│       │   ├── index.blade.php
│       │   └── widgets/
│       ├── job-orders/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   ├── show.blade.php
│       │   └── partials/
│       ├── assignments/
│       │   ├── index.blade.php
│       │   ├── calendar.blade.php
│       │   └── workload.blade.php
│       ├── calibration/
│       │   ├── index.blade.php
│       │   ├── execute.blade.php
│       │   ├── data-entry.blade.php
│       │   └── report-upload.blade.php
│       ├── approvals/
│       │   ├── index.blade.php
│       │   ├── review.blade.php
│       │   ├── pending.blade.php
│       │   └── history.blade.php
│       ├── certificates/
│       │   ├── index.blade.php
│       │   ├── preview.blade.php
│       │   ├── generate.blade.php
│       │   └── verify.blade.php (public)
│       ├── releases/
│       │   ├── index.blade.php
│       │   ├── pending.blade.php
│       │   └── accounting.blade.php
│       ├── inventory/
│       │   ├── equipment/
│       │   ├── standards/
│       │   └── maintenance/
│       ├── customers/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── show.blade.php
│       └── reports/
│           ├── index.blade.php
│           ├── analytics.blade.php
│           └── audit-trail.blade.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── routes/
│   ├── web.php
│   ├── api.php
│   └── public.php (for certificate verification)
└── config/
    ├── calibration.php
    └── workflow.php
```

---

## 2. Database Entities & Relationships

### Core Entities

#### **Users & Authentication**
- **users**
  - id, name, email, password, role_id, department, signature_path, is_active, timestamps
  - Relations: belongsTo(Role), hasMany(AuditLogs), hasMany(Assignments)

- **roles**
  - id, name, slug, description, permissions (JSON)
  - Predefined: marketing, tec_personnel, signatory, accounting, admin

- **permissions**
  - id, role_id, module, actions (JSON)

#### **Customer Management**
- **customers**
  - id, code, name, address, phone, email, contact_person, industry_type, is_active, timestamps
  - Relations: hasMany(CustomerContacts), hasMany(CustomerEquipment), hasMany(JobOrders)

- **customer_contacts**
  - id, customer_id, name, position, phone, email, is_primary, timestamps

- **customer_equipment**
  - id, customer_id, equipment_type, manufacturer, model, serial_number, description, timestamps

#### **Job Order Management**
- **job_orders**
  - id, job_order_number, customer_id, requested_by, request_date, required_date, priority, status, total_items, notes, created_by, timestamps
  - Relations: belongsTo(Customer), hasMany(JobOrderItems), hasMany(JobOrderStatuses), hasOne(Assignment)

- **job_order_items**
  - id, job_order_id, equipment_type, manufacturer, model, serial_number, id_number, range, calibration_type, quantity, remarks, timestamps
  - Relations: belongsTo(JobOrder), hasOne(Calibration)

- **job_order_statuses**
  - id, job_order_id, status, changed_by, changed_at, remarks, timestamps
  - Status flow: draft → submitted → assigned → in_progress → completed → released

- **job_order_attachments**
  - id, job_order_id, file_name, file_path, file_type, uploaded_by, timestamps

#### **Assignment & Scheduling**
- **assignments**
  - id, job_order_id, assigned_to (user_id), assigned_by, assigned_at, scheduled_date, priority, status, estimated_duration, notes, timestamps
  - Relations: belongsTo(JobOrder), belongsTo(User), hasMany(Schedules)

- **schedules**
  - id, assignment_id, technician_id, scheduled_start, scheduled_end, actual_start, actual_end, status, timestamps

- **workload_allocations**
  - id, user_id, date, allocated_hours, available_hours, utilization_rate, timestamps

#### **Calibration Execution**
- **calibrations**
  - id, job_order_item_id, assignment_id, performed_by, calibration_date, location, environmental_conditions (JSON), status, timestamps
  - Relations: belongsTo(JobOrderItem), hasMany(CalibrationData), hasMany(MeasurementPoints), hasOne(CalibrationReport)

- **calibration_data**
  - id, calibration_id, measurement_point, nominal_value, measured_value, unit, uncertainty, pass_fail, timestamps

- **measurement_points**
  - id, calibration_id, point_number, reference_value, unit_under_test_value, error, uncertainty, acceptance_criteria, status, timestamps

- **uncertainty_calculations**
  - id, calibration_id, component, value, distribution, sensitivity_coefficient, contribution, timestamps

- **calibration_reports**
  - id, calibration_id, report_number, file_path, uploaded_by, uploaded_at, version, status, timestamps

#### **Approval & Validation**
- **technical_reviews**
  - id, calibration_id, reviewer_id, review_date, result, findings, recommendations, status, timestamps
  - Relations: belongsTo(Calibration), belongsTo(User)

- **signatory_approvals**
  - id, calibration_id, signatory_id, approval_level (reviewer/approver), approved_at, signature_path, comments, status, timestamps
  - Relations: belongsTo(Calibration), belongsTo(User)

- **approval_histories**
  - id, approvable_type, approvable_id, approved_by, action, comments, timestamps
  - Polymorphic relation for audit trail

#### **Certificate Management**
- **certificates**
  - id, certificate_number, job_order_item_id, calibration_id, issue_date, expiry_date, qr_code, pdf_path, status, version, issued_by, timestamps
  - Relations: belongsTo(JobOrderItem), belongsTo(Calibration), hasMany(CertificateRevisions)

- **certificate_revisions**
  - id, certificate_id, version, revision_reason, revised_by, revised_at, pdf_path, timestamps

- **certificate_verifications**
  - id, certificate_id, verified_at, ip_address, user_agent, verification_result, timestamps
  - For QR code verification tracking

#### **Release & Accounting**
- **releases**
  - id, job_order_id, released_by, released_to, release_date, delivery_method, tracking_number, status, timestamps
  - Relations: belongsTo(JobOrder), belongsTo(AccountingRelease)

- **accounting_releases**
  - id, release_id, invoice_id, payment_status, payment_date, verified_by, verified_at, remarks, timestamps

- **invoices**
  - id, job_order_id, invoice_number, issue_date, due_date, amount, tax, total, payment_status, timestamps

#### **Inventory & Standards**
- **equipment** (Lab's internal equipment)
  - id, equipment_code, name, manufacturer, model, serial_number, location, category, status, timestamps

- **standards** (Reference standards)
  - id, standard_code, name, type, manufacturer, serial_number, certificate_number, calibration_date, next_calibration_date, traceability, status, timestamps
  - Relations: hasMany(StandardCalibrations)

- **standard_calibrations**
  - id, standard_id, calibration_date, certificate_number, performed_by, next_due_date, certificate_path, timestamps

- **equipment_maintenance**
  - id, equipment_id, maintenance_type, performed_by, performed_at, description, cost, next_maintenance_date, timestamps

#### **Audit & Compliance**
- **audit_logs**
  - id, user_id, action, model_type, model_id, old_values (JSON), new_values (JSON), ip_address, user_agent, timestamps
  - Comprehensive audit trail for all critical operations

---

## 3. Core System Modules & Pages

### **Module 1: Dashboard**
**Purpose:** Executive overview and quick actions
- **Pages:**
  - Main Dashboard (role-based widgets)
- **Widgets:**
  - Active Job Orders count
  - Pending Approvals count
  - Overdue Calibrations
  - Revenue metrics (for management)
  - Workload distribution chart
  - Recent activities feed
  - Upcoming schedules

### **Module 2: Job Order Management**
**Purpose:** Handle job order lifecycle from creation to completion
- **Pages:**
  - Job Order List (with advanced filters)
  - Create Job Order
  - Edit Job Order
  - View Job Order Details
  - Job Order Timeline/Workflow Tracker
- **Features:**
  - Multi-item job orders
  - Priority management
  - Status tracking
  - Attachment uploads
  - Customer equipment database linking

### **Module 3: Assignment & Scheduling**
**Purpose:** Allocate work to technicians and manage schedules
- **Pages:**
  - Assignment Dashboard
  - Create Assignment
  - Schedule Calendar View
  - Workload Management
  - Technician Availability
- **Features:**
  - Drag-and-drop scheduling
  - Capacity planning
  - Conflict detection
  - Automated workload balancing

### **Module 4: Calibration Execution**
**Purpose:** Execute and document calibration activities
- **Pages:**
  - My Calibrations (for technicians)
  - Execute Calibration
  - Data Entry Interface
  - Environmental Conditions Log
  - Report Upload
  - Calibration History
- **Features:**
  - Guided data entry forms
  - Auto-calculation of errors/uncertainties
  - Pass/Fail determination
  - Equipment standards selection
  - Photo/document attachments

### **Module 5: Validation & Approval**
**Purpose:** Multi-level technical review and signatory approval
- **Pages:**
  - Pending Approvals Dashboard
  - Technical Review Interface
  - Signatory Approval Interface
  - Approval History
  - Rejection Management
- **Features:**
  - Two-tier approval (reviewer + approver)
  - Comments and findings
  - Conditional approvals
  - Rejection with reason
  - Digital signature capture

### **Module 6: Certificate Management**
**Purpose:** Generate, manage, and verify calibration certificates
- **Pages:**
  - Certificate List
  - Generate Certificate
  - Certificate Preview
  - Certificate Download
  - Public QR Verification Page
  - Revision Management
- **Features:**
  - Automated PDF generation
  - QR code embedding
  - Revision control
  - Batch certificate generation
  - Template management
  - Public verification portal

### **Module 7: Release & Delivery**
**Purpose:** Control final release with accounting verification
- **Pages:**
  - Pending Releases
  - Accounting Verification
  - Release Management
  - Delivery Tracking
  - Release History
- **Features:**
  - Payment status check
  - Conditional release (payment verified)
  - Delivery method selection
  - Customer notification
  - Release documentation

### **Module 8: Inventory Management**
**Purpose:** Manage lab equipment and reference standards
- **Pages:**
  - Equipment List
  - Standards List
  - Equipment Details
  - Calibration History
  - Maintenance Schedule
  - Standards Due List
- **Features:**
  - Equipment tracking
  - Standards traceability
  - Calibration due dates
  - Maintenance logging
  - Asset depreciation

### **Module 9: Customer Management**
**Purpose:** Maintain customer database and relationships
- **Pages:**
  - Customer List
  - Add/Edit Customer
  - Customer Profile
  - Customer Equipment Database
  - Customer History
- **Features:**
  - Customer equipment registry
  - Contact management
  - Job order history
  - Credit terms
  - Communication log

### **Module 10: Reports & Analytics**
**Purpose:** Business intelligence and compliance reporting
- **Pages:**
  - Report Generator
  - Analytics Dashboard
  - Audit Trail Viewer
  - Performance Metrics
  - Compliance Reports
- **Reports:**
  - Job order summary
  - Technician productivity
  - Revenue analysis
  - TAT (Turnaround Time) analysis
  - Standards due report
  - Customer activity report
  - Audit trail export

---

## 4. System States & Status Flows

### **Job Order Status Flow**
```
DRAFT
  ↓ (Submit)
SUBMITTED
  ↓ (Accept & Assign)
ASSIGNED
  ↓ (Start Calibration)
IN_PROGRESS
  ↓ (Complete All Items)
AWAITING_VALIDATION
  ↓ (Technical Review Pass)
AWAITING_APPROVAL
  ↓ (Signatory Approve)
APPROVED
  ↓ (Generate Certificate)
CERTIFICATE_READY
  ↓ (Accounting Verify)
READY_FOR_RELEASE
  ↓ (Release)
RELEASED
  ↓ (Optional)
CLOSED

Side branches:
- REJECTED (from any approval stage → back to IN_PROGRESS)
- ON_HOLD (from any active stage)
- CANCELLED (from any stage before RELEASED)
```

### **Calibration Status Flow**
```
PENDING
  ↓ (Start)
IN_PROGRESS
  ↓ (Complete Data Entry)
DATA_COMPLETE
  ↓ (Upload Report)
REPORT_UPLOADED
  ↓ (Technical Review)
UNDER_REVIEW
  ↓ (Review Pass)
VALIDATED
  ↓ (Signatory Level 1)
REVIEWER_APPROVED
  ↓ (Signatory Level 2)
APPROVER_APPROVED
  ↓ (Generate Certificate)
CERTIFIED
  ↓
RELEASED

Side branches:
- FAILED_VALIDATION (→ IN_PROGRESS for rework)
- REJECTED (→ IN_PROGRESS with comments)
- SUSPENDED (temporary hold)
```

### **Certificate Status Flow**
```
DRAFT
  ↓
GENERATED
  ↓
AWAITING_RELEASE
  ↓
RELEASED
  ↓
ACTIVE
  ↓ (if revised)
SUPERSEDED
  ↓ (if invalid)
REVOKED
```

### **Release Status Flow**
```
PENDING_PAYMENT
  ↓
PAYMENT_VERIFIED
  ↓
READY_FOR_RELEASE
  ↓
RELEASED
  ↓
DELIVERED
```

### **Approval Status**
```
PENDING
  ↓
UNDER_REVIEW
  ↓
APPROVED / REJECTED / CONDITIONAL
```

---

## 5. Role-Based Access Control (RBAC)

### **Role Definitions**

#### **Marketing**
- **Permissions:**
  - Create/edit job orders
  - View customer information
  - Add/edit customers
  - View job order status
  - Access reports (limited)
- **Dashboard View:** Job orders, customer activity, revenue metrics

#### **TEC Personnel (Technicians)**
- **Permissions:**
  - View assigned calibrations
  - Execute calibrations
  - Enter measurement data
  - Upload calibration reports
  - View equipment/standards
  - View own workload
- **Dashboard View:** My assignments, pending calibrations, schedule

#### **Signatory (Reviewer & Approver)**
- **Permissions:**
  - Review calibration data
  - Approve/reject calibrations
  - View technical details
  - Add review comments
  - Access certificate preview
  - View approval history
- **Dashboard View:** Pending approvals, completed reviews, workload

#### **Accounting**
- **Permissions:**
  - Verify payments
  - Approve releases
  - View invoices
  - Generate financial reports
  - Release certificates
- **Dashboard View:** Pending releases, payment status, revenue

#### **Admin**
- **Permissions:**
  - Full system access
  - User management
  - System configuration
  - Audit log access
  - Master data management
- **Dashboard View:** System health, user activity, comprehensive metrics

#### **Public Customer (Limited)**
- **Permissions:**
  - Verify certificate authenticity via QR code
  - No login required
- **Access:** Public verification page only

### **Permission Matrix**

| Module | Marketing | TEC | Signatory | Accounting | Admin |
|--------|-----------|-----|-----------|------------|-------|
| Job Orders | Create/Edit | View Assigned | View | View | Full |
| Assignments | View | View Own | View | - | Full |
| Calibration | View Status | Execute | Review | - | Full |
| Approvals | - | - | Approve | - | Full |
| Certificates | View | View | Preview | Release | Full |
| Releases | - | - | - | Approve | Full |
| Inventory | View | View/Use | View | - | Full |
| Customers | Full | View | - | View | Full |
| Reports | Limited | Limited | Full | Financial | Full |
| Audit Logs | - | - | - | - | View |

---

## 6. Audit Logging & Compliance

### **Audit Requirements**

#### **What to Log:**
- User authentication (login/logout)
- Job order creation, modification, status changes
- Assignment changes
- Calibration data entry and modifications
- Report uploads
- All approval actions (approve/reject/comment)
- Certificate generation and revisions
- Release actions
- Payment verification
- Critical data modifications
- Failed access attempts
- Configuration changes

#### **Audit Log Structure:**
```php
[
    'user_id' => 'Who performed the action',
    'action' => 'CREATE|UPDATE|DELETE|APPROVE|REJECT|RELEASE',
    'model_type' => 'JobOrder|Calibration|Certificate etc.',
    'model_id' => 'Related record ID',
    'old_values' => 'JSON of previous state',
    'new_values' => 'JSON of new state',
    'ip_address' => 'User IP',
    'user_agent' => 'Browser info',
    'timestamp' => 'Exact time',
]
```

#### **Compliance Features:**
- Immutable audit logs
- Tamper-evident logging
- Searchable audit trail
- Export capability (CSV, PDF)
- Retention policy enforcement
- Change diff visualization
- User activity reports

#### **Data Integrity:**
- Version control for certificates
- Digital signatures for approvals
- Checksums for uploaded documents
- Timestamp verification
- Change history preservation

---

## 7. Workflow Automation

### **Automated Notifications**
- Job order assigned → Notify technician
- Calibration completed → Notify reviewer
- Review completed → Notify signatory
- Approval granted → Notify certificate generator
- Certificate ready → Notify accounting
- Payment verified → Notify release team
- Released → Notify customer
- Standards due soon → Notify admin

### **Automated Status Updates**
- All items completed → Update job order status
- All approvals received → Trigger certificate generation
- Payment verified → Enable release

### **Automated Reminders**
- Overdue calibrations
- Pending approvals
- Standards calibration due
- Equipment maintenance due
- Unpaid invoices

### **Automated Calculations**
- Measurement errors
- Uncertainty budget
- Pass/fail determination
- Workload metrics
- TAT calculations

---

## 8. Key Features & Best Practices

### **Security**
- Strong password policies
- Session timeout
- CSRF protection
- XSS prevention
- SQL injection prevention
- File upload validation
- Rate limiting
- IP whitelisting (optional)

### **Data Validation**
- Server-side validation
- Form request validation classes
- Business rule enforcement
- Data type checking
- Range validation

### **Performance**
- Eager loading to prevent N+1 queries
- Query optimization
- Caching strategies
- Database indexing
- Pagination for large datasets

### **User Experience**
- Responsive design (mobile-friendly)
- Intuitive navigation
- Role-based dashboards
- Contextual help
- Toast notifications
- Loading states
- Error handling

### **Code Organization**
- Service classes for business logic
- Repository pattern for data access
- Form request classes for validation
- Resource classes for API responses
- Enums for constants
- Observers for model events

---

## 9. Configuration Files

### **config/calibration.php**
```php
// Configuration for:
- Default uncertainty levels
- Pass/fail criteria
- Environmental limits
- Calibration types
- Certificate templates
- QR code settings
```

### **config/workflow.php**
```php
// Configuration for:
- Status flows
- Approval levels
- Notification triggers
- Escalation rules
- SLA definitions
```

---

## 10. Next Steps

### **Phase 1: Foundation**
1. Set up Laravel project
2. Configure database connection (XAMPP MySQL)
3. Create migrations for core entities
4. Set up authentication (Laravel Breeze/Jetstream)
5. Implement RBAC system
6. Create base layout with Tailwind

### **Phase 2: Core Modules**
1. Job Order Management
2. Customer Management
3. Assignment & Scheduling
4. Dashboard implementation

### **Phase 3: Calibration & Approval**
1. Calibration execution interface
2. Data entry forms
3. Report upload
4. Review and approval workflow

### **Phase 4: Certificate & Release**
1. Certificate generation (PDF)
2. QR code integration
3. Release management
4. Public verification portal

### **Phase 5: Support Modules**
1. Inventory management
2. Reporting and analytics
3. Audit trail viewer
4. System administration

### **Phase 6: Enhancement**
1. Email notifications
2. Advanced reporting
3. API development (if needed)
4. Performance optimization
5. User documentation

---

## Summary

This architecture provides:
✅ Clear separation of concerns with modular structure
✅ Comprehensive entity relationships aligned with LIMS workflow
✅ Well-defined status flows for process control
✅ Role-based access control for security
✅ Audit logging for compliance
✅ Scalable and maintainable codebase
✅ Industry best practices for calibration management

The system is designed to handle the complete lifecycle from job order creation through certificate release, with proper controls, approvals, and audit trails suitable for an industrial calibration laboratory.
