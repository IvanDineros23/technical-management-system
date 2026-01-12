# Technical Management System (TMS)

## Industrial Calibration & LIMS Workflow Management

A comprehensive calibration management system designed for industrial laboratories following LIMS (Laboratory Information Management System) workflow principles. This system manages the complete lifecycle of calibration job orders from creation to certificate release and verification.

---

## 📋 Overview

This system handles:
- **Job Order Management** - Create and track calibration requests
- **Assignment & Scheduling** - Allocate work to technicians
- **Calibration Execution** - Perform and document calibrations
- **Multi-level Approval** - Technical review and signatory approval
- **Certificate Generation** - Automated PDF with QR codes
- **Release Control** - Accounting-controlled delivery
- **Public Verification** - QR-based certificate authenticity

---

## 🔄 Complete Workflow

```
Job Order Creation → Assignment & Scheduling → Calibration Execution → 
Report Upload → Technical Review → Signatory Approval → 
Certificate Generation → Payment Verification → Release → 
QR Verification
```

---

## 👥 User Roles

| Role | Responsibilities |
|------|-----------------|
| **Marketing** | Create job orders, manage customers |
| **TEC Personnel** | Execute calibrations, enter measurement data |
| **Signatory (Reviewer)** | Technical validation and data review |
| **Signatory (Approver)** | Final approval and authorization |
| **Accounting** | Payment verification and release control |
| **Admin** | System administration and configuration |
| **Public Customer** | Certificate verification via QR code |

---

## 🛠️ Tech Stack

- **Backend:** Laravel 10+
- **Database:** MySQL
- **Frontend:** Blade Templates with Tailwind CSS
- **PDF Generation:** DomPDF
- **QR Codes:** Simple QrCode
- **Environment:** XAMPP (MySQL only)

---

## 📁 Project Structure

```
technical-management-system/
├── app/
│   ├── Http/Controllers/      # Controllers organized by module
│   ├── Models/                # Eloquent models
│   ├── Services/              # Business logic layer
│   ├── Enums/                 # System constants
│   └── Observers/             # Model observers for events
├── resources/
│   └── views/                 # Blade templates
├── database/
│   ├── migrations/            # Database schema
│   └── seeders/               # Sample data
└── routes/
    └── web.php                # Application routes
```

---

## 📚 Documentation

- **[System Architecture](SYSTEM_ARCHITECTURE.md)** - Complete system design and module structure
- **[Database Design](DATABASE_DESIGN.md)** - Detailed schema and relationships
- **[Workflow Diagrams](WORKFLOW_DIAGRAMS.md)** - Visual workflow and state transitions
- **[Implementation Guide](IMPLEMENTATION_GUIDE.md)** - Step-by-step setup instructions

---

## 🚀 Quick Start

### Prerequisites

- PHP >= 8.1
- Composer
- XAMPP (MySQL)
- Node.js & NPM

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd technical-management-system
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Setup database**
   - Create database: `technical_management_db`
   - Update `.env` with database credentials

5. **Run migrations**
   ```bash
   php artisan migrate --seed
   ```

6. **Build assets**
   ```bash
   npm run dev
   ```

7. **Start development server**
   ```bash
   php artisan serve
   ```

Visit: `http://localhost:8000`

---

## 🔑 Key Features

### ✅ Job Order Management
- Multi-item job orders
- Priority-based workflow
- Status tracking with history
- Document attachments
- Customer equipment database

### ✅ Assignment & Scheduling
- Workload-based assignment
- Calendar view
- Resource allocation
- Conflict detection
- Automated notifications

### ✅ Calibration Execution
- Guided data entry
- Environmental conditions logging
- Standards traceability
- Uncertainty calculations
- Pass/fail determination

### ✅ Approval Workflow
- Two-tier approval (Reviewer + Approver)
- Digital signatures
- Rejection with comments
- Complete audit trail
- Email notifications

### ✅ Certificate Management
- Automated PDF generation
- QR code embedding
- Revision control
- Template management
- Public verification portal

### ✅ Release Control
- Payment verification requirement
- Accounting approval
- Delivery tracking
- Customer notification
- Conditional release

### ✅ Inventory Management
- Equipment tracking
- Reference standards management
- Calibration due dates
- Maintenance logging
- Traceability documentation

### ✅ Reporting & Analytics
- Dashboard widgets
- Performance metrics
- TAT analysis
- Revenue reports
- Audit trail export

### ✅ Audit & Compliance
- Complete change history
- User activity logging
- Data integrity checks
- ISO/IEC 17025 aligned
- Tamper-evident logs

---

## 📊 System Status Flow

### Job Order States
```
DRAFT → SUBMITTED → ASSIGNED → IN_PROGRESS → 
AWAITING_VALIDATION → AWAITING_APPROVAL → APPROVED → 
CERTIFICATE_READY → READY_FOR_RELEASE → RELEASED → CLOSED
```

### Calibration States
```
PENDING → IN_PROGRESS → DATA_COMPLETE → REPORT_UPLOADED → 
UNDER_REVIEW → VALIDATED → REVIEWER_APPROVED → 
APPROVER_APPROVED → CERTIFIED → RELEASED
```

---

## 🔐 Security Features

- Role-based access control (RBAC)
- Permission-based authorization
- Audit logging for all critical actions
- CSRF protection
- SQL injection prevention
- XSS protection
- Secure file uploads
- Session management

---

## 📈 Performance Optimization

- Eager loading to prevent N+1 queries
- Database indexing
- Query optimization
- Caching strategies
- Paginated results
- Asset optimization

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter JobOrderTest
```

---

## 📦 Deployment

### Production Build
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Required Permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 🔧 Configuration

### Key Configuration Files

- **`.env`** - Environment variables
- **`config/calibration.php`** - Calibration settings
- **`config/workflow.php`** - Workflow rules
- **`config/filesystems.php`** - Storage configuration

### Important Environment Variables

```env
APP_NAME="Technical Management System"
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=mysql
DB_DATABASE=technical_management_db

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
```

---

## 📝 Development Guidelines

### Code Style
- Follow PSR-12 coding standards
- Use type hints
- Write descriptive method names
- Keep controllers thin

### Database
- Always use migrations
- Add proper indexes
- Use foreign key constraints
- Implement soft deletes for critical data

### Security
- Validate all inputs
- Use Form Requests
- Sanitize output
- Never trust user input

### Git Workflow
```bash
# Create feature branch
git checkout -b feature/new-feature

# Commit changes
git add .
git commit -m "Add new feature"

# Push to remote
git push origin feature/new-feature
```

---

## 🐛 Troubleshooting

### Common Issues

**Database connection failed**
```bash
# Check MySQL service
# Verify .env database credentials
php artisan config:clear
```

**Assets not loading**
```bash
npm run dev
php artisan storage:link
```

**Permission denied errors**
```bash
chmod -R 775 storage bootstrap/cache
```

**Composer dependencies**
```bash
composer install --no-dev --optimize-autoloader
```

---

## 📞 Support

For issues and questions:
- Check the [Documentation](SYSTEM_ARCHITECTURE.md)
- Review [Implementation Guide](IMPLEMENTATION_GUIDE.md)
- Open an issue on GitHub

---

## 🗺️ Roadmap

### Phase 1: Foundation ✅
- ✅ System architecture design
- ✅ Database schema design
- ✅ Workflow documentation

### Phase 2: Core Development (In Progress)
- ⏳ Authentication & authorization
- ⏳ Job order management
- ⏳ Assignment system
- ⏳ Calibration execution

### Phase 3: Approval & Certificates
- ⏳ Review workflow
- ⏳ Approval system
- ⏳ Certificate generation
- ⏳ QR code integration

### Phase 4: Release & Accounting
- ⏳ Payment verification
- ⏳ Release management
- ⏳ Invoice generation

### Phase 5: Advanced Features
- ⏳ Email notifications
- ⏳ Advanced reporting
- ⏳ Dashboard analytics
- ⏳ Mobile responsiveness

### Future Enhancements
- 📱 Mobile app
- 🔌 REST API
- 📧 Email reminders
- 📊 Advanced analytics
- 🌐 Multi-language support

---

## 📄 License

This project is proprietary software developed for [Your Company Name].

---

## 👨‍💻 Development Team

- **Project Lead:** [Name]
- **Backend Developer:** [Name]
- **Frontend Developer:** [Name]
- **Database Administrator:** [Name]

---

## 📅 Project Timeline

- **Planning:** January 2026
- **Development Start:** January 2026
- **Alpha Release:** TBD
- **Beta Testing:** TBD
- **Production Release:** TBD

---

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS
- DomPDF
- Simple QrCode
- Open Source Community

---

## 📊 Project Status

**Current Version:** 0.1.0-alpha  
**Status:** 🚧 In Active Development  
**Last Updated:** January 12, 2026

---

**Built with ❤️ for calibration laboratories following ISO/IEC 17025 standards**

