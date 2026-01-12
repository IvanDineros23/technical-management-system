# Workflow Diagrams - Technical Management System

## Complete System Workflow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          CALIBRATION WORKFLOW                                 │
└─────────────────────────────────────────────────────────────────────────────┘

┌──────────────┐
│  MARKETING   │  1. Job Order Creation
└──────┬───────┘
       │
       ▼
┌─────────────────────┐
│  Create Job Order   │ ◄── Customer request received
│  - Customer info    │
│  - Equipment list   │
│  - Requirements     │
└──────┬──────────────┘
       │
       ▼ (Submit)
┌─────────────────────┐
│  JO: SUBMITTED      │
└──────┬──────────────┘
       │
       ▼
┌──────────────┐
│  MARKETING/  │  2. Assignment & Scheduling
│  SUPERVISOR  │
└──────┬───────┘
       │
       ▼
┌─────────────────────┐
│  Create Assignment  │ ◄── Assign to technician
│  - Select TEC       │     Set schedule
│  - Set schedule     │
│  - Set priority     │
└──────┬──────────────┘
       │
       ▼ (Assign)
┌─────────────────────┐
│  JO: ASSIGNED       │
│  Assignment created │
└──────┬──────────────┘
       │
       ▼
┌──────────────┐
│ TEC PERSONNEL│  3. Calibration Execution
└──────┬───────┘
       │
       ▼
┌─────────────────────┐
│ Start Calibration   │ ◄── Begin work
│  - Check equipment  │
│  - Prepare setup    │
└──────┬──────────────┘
       │
       ▼ (Start)
┌─────────────────────┐
│  JO: IN_PROGRESS    │
│  CAL: IN_PROGRESS   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Enter Calibration   │ ◄── Data entry
│ Data                │     Environmental conditions
│  - Measurements     │     Standards used
│  - Uncertainties    │     Readings
│  - Observations     │
└──────┬──────────────┘
       │
       ▼ (Complete data)
┌─────────────────────┐
│  CAL: DATA_COMPLETE │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Upload Report       │ ◄── Upload calibration report
│  - PDF/Excel file   │     Supporting documents
│  - Photos           │
└──────┬──────────────┘
       │
       ▼ (Upload)
┌─────────────────────┐
│ CAL: REPORT_UPLOADED│
└──────┬──────────────┘
       │
       ▼
┌──────────────┐
│  SIGNATORY   │  4. Technical Review
│  (Reviewer)  │
└──────┬───────┘
       │
       ▼
┌─────────────────────┐
│ Technical Review    │ ◄── Validate data
│  - Check data       │     Check calculations
│  - Verify calcs     │     Review standards
│  - Check standards  │
│  - Assess quality   │
└──────┬──────────────┘
       │
       ├─── (Reject) ────┐
       │                  │
       ▼ (Approve)        ▼
┌─────────────────────┐  ┌──────────────────┐
│  CAL: VALIDATED     │  │ CAL: REJECTED    │
└──────┬──────────────┘  │ Return to TEC    │
       │                  │ for rework       │
       │                  └─────────┬────────┘
       │                            │
       ▼                            │
┌──────────────┐                    │
│  SIGNATORY   │  5. Approval       │
│  (Approver)  │                    │
└──────┬───────┘                    │
       │                            │
       ▼                            │
┌─────────────────────┐             │
│ Signatory Approval  │             │
│  - Final review     │             │
│  - Digital sign     │             │
│  - Authorize cert   │             │
└──────┬──────────────┘             │
       │                            │
       ├─── (Reject) ───────────────┘
       │
       ▼ (Approve)
┌─────────────────────┐
│ CAL: APPROVED       │
└──────┬──────────────┘
       │
       ▼
┌──────────────┐
│   SYSTEM     │  6. Certificate Generation
└──────┬───────┘
       │
       ▼
┌─────────────────────┐
│ Generate Certificate│ ◄── Auto-generate PDF
│  - Create PDF       │     Add QR code
│  - Add QR code      │     Add signatures
│  - Add signatures   │
│  - Store securely   │
└──────┬──────────────┘
       │
       ▼ (Generated)
┌─────────────────────┐
│ CERT: GENERATED     │
│ JO: CERT_READY      │
└──────┬──────────────┘
       │
       ▼
┌──────────────┐
│  ACCOUNTING  │  7. Payment Verification
└──────┬───────┘
       │
       ▼
┌─────────────────────┐
│ Verify Payment      │ ◄── Check payment status
│  - Check invoice    │     Confirm payment
│  - Verify payment   │     Authorize release
│  - Authorize release│
└──────┬──────────────┘
       │
       ├─── (Not paid) ──┐
       │                  │
       ▼ (Paid)           ▼
┌─────────────────────┐  ┌──────────────────┐
│ PAYMENT_VERIFIED    │  │ HOLD: Awaiting   │
└──────┬──────────────┘  │ Payment          │
       │                  └──────────────────┘
       ▼
┌──────────────┐
│  ACCOUNTING/ │  8. Release
│  ADMIN       │
└──────┬───────┘
       │
       ▼
┌─────────────────────┐
│ Release Certificate │ ◄── Physical/email delivery
│  - Prepare delivery │     Get signature
│  - Hand over        │     Update status
│  - Get signature    │
└──────┬──────────────┘
       │
       ▼ (Released)
┌─────────────────────┐
│ JO: RELEASED        │
│ CERT: RELEASED      │
└──────┬──────────────┘
       │
       ▼
┌──────────────┐
│    PUBLIC    │  9. Verification (Optional)
│   CUSTOMER   │
└──────┬───────┘
       │
       ▼
┌─────────────────────┐
│ Scan QR Code        │ ◄── Verify authenticity
│  - Public portal    │     View certificate
│  - Verify validity  │     Check status
│  - View details     │
└─────────────────────┘

┌─────────────────────┐
│  WORKFLOW COMPLETE  │
└─────────────────────┘
```

---

## Detailed State Transition Diagrams

### Job Order States

```
    ┌─────────┐
    │  DRAFT  │ (Initial state)
    └────┬────┘
         │
         ▼ [Submit]
    ┌──────────────┐
    │  SUBMITTED   │
    └────┬─────────┘
         │
         ▼ [Accept & Assign]
    ┌──────────────┐
    │   ASSIGNED   │ ◄──────────┐
    └────┬─────────┘            │
         │                       │
         ▼ [Start Work]          │
    ┌──────────────┐            │
    │ IN_PROGRESS  │            │ [Rework]
    └────┬─────────┘            │
         │                       │
         ▼ [All items done]      │
    ┌─────────────────────┐     │
    │ AWAITING_VALIDATION │     │
    └────┬────────────────┘     │
         │                       │
         ▼ [Technical Review]    │
    ┌─────────────────────┐     │
    │ AWAITING_APPROVAL   │     │
    └────┬────────────────┘     │
         │                       │
         ├─ [Reject] ────────────┘
         │
         ▼ [Approve]
    ┌──────────────┐
    │   APPROVED   │
    └────┬─────────┘
         │
         ▼ [Generate Cert]
    ┌────────────────────┐
    │ CERTIFICATE_READY  │
    └────┬───────────────┘
         │
         ▼ [Payment Check]
    ┌──────────────────────┐
    │ READY_FOR_RELEASE    │
    └────┬─────────────────┘
         │
         ▼ [Release]
    ┌──────────────┐
    │   RELEASED   │
    └────┬─────────┘
         │
         ▼ [Optional]
    ┌──────────────┐
    │    CLOSED    │
    └──────────────┘

Side transitions:
- ON_HOLD (from any active state)
- CANCELLED (from any state before RELEASED)
```

### Calibration States

```
    ┌─────────┐
    │ PENDING │ (Created with assignment)
    └────┬────┘
         │
         ▼ [Technician starts]
    ┌──────────────┐
    │ IN_PROGRESS  │
    └────┬─────────┘
         │
         ▼ [Data entry complete]
    ┌────────────────┐
    │ DATA_COMPLETE  │
    └────┬───────────┘
         │
         ▼ [Upload report]
    ┌─────────────────┐
    │ REPORT_UPLOADED │
    └────┬────────────┘
         │
         ▼ [Submit for review]
    ┌──────────────┐
    │ UNDER_REVIEW │
    └────┬─────────┘
         │
         ├─ [Reject] ──┐
         │              │
         ▼ [Approve]    │
    ┌──────────────┐   │
    │  VALIDATED   │   │
    └────┬─────────┘   │
         │              │
         ▼              │
    ┌───────────────────┐
    │ REVIEWER_APPROVED │
    └────┬──────────────┘
         │              │
         ├─ [Reject] ───┘
         │
         ▼ [Final approval]
    ┌───────────────────┐
    │ APPROVER_APPROVED │
    └────┬──────────────┘
         │
         ▼ [Generate cert]
    ┌──────────────┐
    │  CERTIFIED   │
    └────┬─────────┘
         │
         ▼ [Release]
    ┌──────────────┐
    │  RELEASED    │
    └──────────────┘

Error states:
- FAILED_VALIDATION → Return to IN_PROGRESS
- SUSPENDED → Temporary hold
```

### Certificate States

```
    ┌─────────┐
    │  DRAFT  │ (Being generated)
    └────┬────┘
         │
         ▼ [PDF created]
    ┌──────────────┐
    │  GENERATED   │
    └────┬─────────┘
         │
         ▼ [Payment verified]
    ┌───────────────────┐
    │ AWAITING_RELEASE  │
    └────┬──────────────┘
         │
         ▼ [Released to customer]
    ┌──────────────┐
    │   RELEASED   │
    └────┬─────────┘
         │
         ▼ [Certificate valid]
    ┌──────────────┐
    │    ACTIVE    │
    └────┬─────────┘
         │
         ├─ [Revised] ──→ SUPERSEDED
         ├─ [Invalid] ──→ REVOKED
         └─ [Expired] ──→ EXPIRED
```

---

## User Role Workflows

### Marketing Workflow

```
┌──────────────────────────┐
│  MARKETING ACTIVITIES    │
└──────────────────────────┘

1. Customer Management
   ├─→ Add new customer
   ├─→ Update customer info
   └─→ Maintain equipment list

2. Job Order Creation
   ├─→ Create JO
   ├─→ Add items
   ├─→ Set priority
   └─→ Submit for assignment

3. Monitoring
   ├─→ Track JO status
   ├─→ View progress
   └─→ Customer communication

4. Reporting
   └─→ View sales reports
```

### TEC Personnel Workflow

```
┌──────────────────────────┐
│  TEC PERSONNEL WORKFLOW  │
└──────────────────────────┘

1. View Assignments
   ├─→ My pending calibrations
   ├─→ Schedule
   └─→ Workload

2. Execute Calibration
   ├─→ Start calibration
   ├─→ Record environmental conditions
   ├─→ Select standards
   ├─→ Enter measurement data
   ├─→ Calculate uncertainties
   └─→ Mark complete

3. Upload Report
   ├─→ Upload PDF/Excel
   ├─→ Add photos
   └─→ Submit for review

4. Rework (if rejected)
   └─→ Review comments
       └─→ Repeat execution
```

### Signatory Workflow

```
┌──────────────────────────┐
│  SIGNATORY WORKFLOW      │
└──────────────────────────┘

REVIEWER:
1. Technical Review
   ├─→ View pending reviews
   ├─→ Check calibration data
   ├─→ Verify calculations
   ├─→ Review standards used
   ├─→ Assess conformance
   └─→ Approve / Reject

APPROVER:
2. Final Approval
   ├─→ View pending approvals
   ├─→ Review technical review
   ├─→ Final assessment
   ├─→ Digital signature
   └─→ Approve / Reject

3. Dashboard
   └─→ Monitor approval queue
```

### Accounting Workflow

```
┌──────────────────────────┐
│  ACCOUNTING WORKFLOW     │
└──────────────────────────┘

1. Payment Verification
   ├─→ View pending releases
   ├─→ Check invoice
   ├─→ Verify payment
   └─→ Authorize release

2. Release Management
   ├─→ Approve release
   ├─→ Track delivery
   └─→ Update status

3. Financial Reports
   ├─→ Revenue reports
   ├─→ Outstanding payments
   └─→ Collection reports
```

---

## Data Flow Diagram

```
┌───────────┐
│ CUSTOMER  │
└─────┬─────┘
      │ Request
      ▼
┌────────────────┐      ┌──────────────┐
│  JOB ORDER     │─────→│   CUSTOMER   │
│  - Items       │      │   DATABASE   │
│  - Requirements│      └──────────────┘
└────┬───────────┘
     │
     ▼ Assignment
┌────────────────┐      ┌──────────────┐
│  ASSIGNMENT    │─────→│    USERS     │
│  - Technician  │      │ (TEC Staff)  │
│  - Schedule    │      └──────────────┘
└────┬───────────┘
     │
     ▼ Execution
┌────────────────┐      ┌──────────────┐
│  CALIBRATION   │─────→│  STANDARDS   │
│  - Data        │      │  EQUIPMENT   │
│  - Report      │      └──────────────┘
└────┬───────────┘
     │
     ▼ Review
┌────────────────┐      ┌──────────────┐
│   APPROVAL     │─────→│    USERS     │
│  - Review      │      │ (Signatories)│
│  - Signature   │      └──────────────┘
└────┬───────────┘
     │
     ▼ Generate
┌────────────────┐      ┌──────────────┐
│  CERTIFICATE   │─────→│   PDF + QR   │
│  - PDF         │      │   STORAGE    │
│  - QR Code     │      └──────────────┘
└────┬───────────┘
     │
     ▼ Payment
┌────────────────┐      ┌──────────────┐
│   ACCOUNTING   │─────→│   INVOICE    │
│  - Verify      │      │   SYSTEM     │
└────┬───────────┘      └──────────────┘
     │
     ▼ Release
┌────────────────┐
│    RELEASE     │
│  - Delivery    │
└────┬───────────┘
     │
     ▼
┌───────────┐
│ CUSTOMER  │
└───────────┘
```

---

## Critical Control Points

### Quality Gates

1. **Assignment Gate**
   - Job order must be approved
   - Technician must be available
   - Required standards must be valid

2. **Data Validation Gate**
   - All measurement points completed
   - Calculations verified
   - Pass/fail determined
   - Report uploaded

3. **Technical Review Gate**
   - Data accuracy check
   - Standards verification
   - Traceability confirmed
   - Documentation complete

4. **Approval Gate**
   - Technical review approved
   - All signatures obtained
   - No outstanding issues

5. **Release Gate**
   - Certificate generated
   - Payment verified
   - Accounting approval obtained

---

## Notification Triggers

```
Event                          → Notify
─────────────────────────────────────────────
Job Order Created              → Marketing Manager
Assignment Created             → Assigned Technician
Calibration Started            → Supervisor
Calibration Completed          → Reviewer
Ready for Review               → Signatory (Reviewer)
Review Completed               → Signatory (Approver)
Approved                       → Certificate Generator
Certificate Generated          → Accounting
Payment Verified               → Release Team
Released                       → Customer + Marketing
Rejected (any stage)           → Responsible Person
Overdue                        → All Stakeholders
Standard Expiring Soon         → Lab Manager
```

---

## Audit Points

Every workflow transition logs:
- Who performed the action
- When it occurred
- What changed (before/after)
- Why (comments/reason)
- Where (IP address)

Critical audit points:
✓ Job order creation/modification
✓ Assignment changes
✓ Data entry/modification
✓ Report uploads
✓ All approval actions
✓ Certificate generation
✓ Release actions
✓ Payment verification

---

This workflow design ensures:
- Clear accountability at each stage
- Quality control through multi-level review
- Compliance with ISO/IEC 17025 principles
- Complete traceability
- Financial control before release
- Customer verification capability
