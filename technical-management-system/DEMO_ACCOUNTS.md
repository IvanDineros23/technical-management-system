# Demo Accounts for Technical Management System

## Created Users

All demo accounts use the password: **password**

### 1. Administrator
- **Email**: admin@gemarcph.com
- **Role**: Administrator
- **Department**: Administration
- **Permissions**: Full system access

### 2. Marketing
- **Email**: marketing@gemarcph.com
- **Name**: Maria Santos
- **Role**: Marketing
- **Department**: Marketing
- **Permissions**: Create/manage job orders, view customers

### 3. Technical Personnel
- **Email**: technician@gemarcph.com
- **Name**: Juan Dela Cruz
- **Role**: Technical Personnel
- **Department**: Technical
- **Permissions**: View assigned jobs, update status, submit reports

### 4. Technical Head
- **Email**: techhead@gemarcph.com
- **Name**: Robert Gonzales
- **Role**: Technical Head
- **Department**: Technical
- **Permissions**: Assign technicians, review reports, manage schedules

### 5. Signatory
- **Email**: signatory@gemarcph.com
- **Name**: Ana Reyes
- **Role**: Signatory
- **Department**: Management
- **Permissions**: Approve job orders, view reports

### 6. Accounting
- **Email**: accounting@gemarcph.com
- **Name**: Carlos Mendoza
- **Role**: Accounting
- **Department**: Accounting
- **Permissions**: Manage billing, view financial reports

## How to Run the Seeders

```bash
# Run migrations first (if not done already)
php artisan migrate

# Run the seeders
php artisan db:seed

# Or run specific seeders
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=DemoUserSeeder
```

## Testing Different Role Access

You can now log in with any of the demo accounts to test role-based access control in your system.
