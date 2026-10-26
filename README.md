# IT Asset Management System

> A comprehensive Laravel-based IT Asset Management System for tracking assets, departments, employees, transfers, and operational requests — built with role-based access control and audit trails.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.5+-777BB4?style=flat&logo=php&logoColor=white)
![SQLite](https://img.shields.io/badge/Database-SQLite%20%7C%20MySQL-003B57?style=flat&logo=sqlite&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4-06B6D4?style=flat&logo=tailwindcss&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

---

## 📋 Overview

The **IT Asset Management System** helps organizations manage their IT inventory lifecycle — from procurement to disposal. It provides a complete workflow for asset tracking, department/employee assignments, transfer approvals, document management, and operational requests.

### Key Capabilities

| Module | Description |
|--------|-------------|
| **Assets** | Full CRUD with categories, departments, occupants, quantities, serial/barcode, condition, status |
| **Transfers** | Multi-step workflow: Request → Super Admin Approve → Complete (with audit trail) |
| **Documents** | Secure private storage for asset images & bills/invoices (locked after upload) |
| **Departments** | Hierarchical with sub-departments, academic/non-teaching types |
| **Employees** | Linked to departments, employee codes, designations |
| **Categories** | Auto-created during imports, status tracking |
| **Imports** | Upload CSV/XLSX/PDF/DOCX → Parse → Validate → Confirm (transactional) |
| **Operational Requests** | Staff submit requests (hardware, software, repairs) → Admin approve/reject |
| **Super Admin** | Staff management, permissions, password resets, transfer/request approvals |
| **Authentication** | Email/password, account approval flow, session timeout (48h), CSRF protection |

---

## 🏗️ Tech Stack

| Layer | Technology |
|-------|------------|
| **Framework** | Laravel 12.x |
| **Language** | PHP 8.5+ (ZTS) |
| **Database** | SQLite (dev) / MySQL / MariaDB (prod) |
| **Frontend** | Bootstrap 5.3 + TailwindCSS 4 |
| **Build Tool** | Vite 7.x |
| **Testing** | PHPUnit 12.x |
| **Code Style** | Laravel Pint |
| **Icons** | Bootstrap Icons |

---

## ✨ Features in Detail

### 🔐 Authentication & Authorization
- **Account Approval Flow**: Users register → status `pending` → Super Admin approves → `approved`
- **Role-Based Access**: `super_admin` (full access) vs `staff` (module permissions)
- **Module Permissions**: Granular control per module (`assets`, `departments`, `categories`, `employees`, `imports`, `transfers`, `bills`, `verification`, `reports`)
- **Session Security**: 48-hour inactivity auto-logout, HTTP-only cookies, SameSite=lax
- **Rate Limiting**: 5 attempts/minute on login & password reset

### 📦 Asset Management
- Unique asset codes, serial numbers, barcodes
- Issued vs. physically verified quantities
- Present/previous occupant tracking (employee or department)
- Location + room number with "Other" custom entry
- Condition: New / Good / Fair / Damaged / Critical
- Status: Active, Assigned, Unassigned, Missing, Damaged, Disposed, Return to Store, Under Repair, Retired

### 🔄 Asset Transfers
```
Staff creates request (select asset → destination dept/occupant)
    ↓
Super Admin approves (validates source unchanged)
    ↓
Super Admin completes (atomically updates asset + creates occupancy history + audit log)
```
- Prevents concurrent transfers on same asset
- Validates destination occupant belongs to destination department
- Locks asset row during completion (`lockForUpdate`)

### 📄 Document Management
- **Asset Images**: JPG/PNG/WebP, replaceable, not locked
- **Bills/Invoices**: JPG/PNG/WebP/PDF, **locked after upload** (immutable)
- All files stored on **private disk** (not publicly accessible)
- Served via authorized routes with `X-Content-Type-Options: nosniff`

### 📥 Import System
1. **Upload** → File stored locally
2. **Process** → Parse (Excel/CSV/PDF/DOCX) → Extract rows to staging table
3. **Validate** → Normalize (resolve departments, categories, employees, dates, quantities) → Mark valid/invalid/duplicate
4. **Confirm** → Transactional import of all valid rows → Generate `IMP-{batch}-{seq}` asset codes

### 🛡️ Security Highlights
- ✅ No SQL injection (Eloquent + parameter binding)
- ✅ No XSS (Blade `{{ }}` escaping, secure headers on downloads)
- ✅ CSRF on all state-changing forms
- ✅ Mass assignment protection (`$fillable` on all models)
- ✅ File upload validation (MIME, size, extension, private storage)
- ✅ Authorization checks on every route (middleware chain)
- ✅ Dependency scanning clean (`composer audit`, `npm audit`)

---

## 🚀 Quick Start (Local Development)

### Prerequisites
- **PHP 8.5+** with extensions: `openssl`, `mbstring`, `curl`, `fileinfo`, `pdo_mysql`, `pdo_sqlite`, `zip`, `gd`, `sqlite3`
- **Composer 2.x**
- **Node.js 20+** & **npm 10+**
- **Git**

### Installation
```bash
# 1. Clone repository
git clone <repository-url>
cd it-asset-management

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies & build assets
npm ci && npm run build

# 4. Configure environment
cp .env.example .env
# Edit .env with your settings (see Configuration section)

# 5. Generate application key
php artisan key:generate

# 6. Run migrations (creates SQLite database by default)
php artisan migrate --force

# 7. (Optional) Seed demo data
php artisan db:seed

# 8. Start development server
php artisan serve
# → Visit http://127.0.0.1:8000
```

### First Super Admin
```bash
php artisan app:create-super-admin
# Follow prompts: name, email, password (min 12 chars)
```

---

## ⚙️ Configuration

### Environment Variables (`.env`)

| Variable | Description | Default | Production |
|----------|-------------|---------|------------|
| `APP_NAME` | Application name | `Laravel` | Your org name |
| `APP_ENV` | Environment | `local` | `production` |
| `APP_DEBUG` | Debug mode | `true` | **`false`** |
| `APP_URL` | Base URL | `http://localhost` | `https://yourdomain.com` |
| `APP_KEY` | Encryption key | (generated) | Keep secure! |
| `DB_CONNECTION` | Database driver | `sqlite` | `mysql` |
| `DB_DATABASE` | Database path/name | `database/database.sqlite` | Your MySQL DB |
| `SESSION_DRIVER` | Session storage | `database` | `database` or `redis` |
| `SESSION_SECURE_COOKIE` | HTTPS-only cookies | `null` | **`true`** |
| `SESSION_ENCRYPT` | Encrypt session data | `false` | **`true`** |
| `SESSION_DOMAIN` | Cookie domain | `null` | `yourdomain.com` |
| `MAIL_MAILER` | Mail driver | `log` | `smtp` / `mailgun` / etc. |

> **⚠️ Production Checklist:** Set `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, use real DB credentials, and configure a proper mail driver.

---

## 📁 Project Structure

```
app/
├── Console/Commands/          # Artisan commands (CreateSuperAdmin)
├── Http/
│   ├── Controllers/           # All controllers (Auth, Asset, Transfer, Admin, etc.)
│   ├── Middleware/            # EnsureApprovedAndActive, EnsureModuleAccess, EnsureSuperAdmin
│   └── Requests/              # FormRequests for validation
├── Models/                    # Eloquent models (User, Asset, Department, etc.)
├── Services/                  # Business logic (AssetService, AssetTransferService, Import services)
│   └── Imports/
│       └── Parsers/           # ExcelAssetParser, DocxAssetParser
├── Providers/                 # AppServiceProvider
└── Http/Middleware/           # Custom middleware

database/
├── migrations/                # 23 migrations (users, assets, transfers, imports, etc.)
├── seeders/                   # DatabaseSeeder, DepartmentSeeder, CategorySeeder
└── factories/                 # UserFactory

resources/
├── views/
│   ├── layouts/app.blade.php  # Main layout (sidebar, navbar, confirm modal)
│   ├── auth/                  # Login, register, reset password
│   ├── admin/                 # Super Admin panel
│   ├── assets/                # Index, create, edit, show
│   ├── transfers/             # Index, create, show
│   ├── departments/           # Index, create, edit
│   ├── categories/            # Index, create, edit
│   ├── employees/             # Index, create, edit
│   ├── imports/               # Index, show
│   ├── operational-requests/  # Index, create
│   └── dashboard/             # Dashboard
├── css/app.css                # TailwindCSS entry
├── js/app.js                  # Bootstrap + Alpine.js entry
└── views/                     # Compiled Blade cache

routes/
├── web.php                    # All web routes (71 routes)
└── console.php                # Artisan commands

tests/
├── Feature/                   # Feature tests
└── Unit/                      # Unit tests
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# With coverage (requires Xdebug or PCOV)
php artisan test --coverage

# Code style check
./vendor/bin/pint --test

# Fix code style
./vendor/bin/pint
```

### Current Test Status
```
Tests:  2 passed (4 assertions)
Duration: ~0.9s
```
> **Note:** Test coverage is minimal. Add feature tests for critical workflows (see `tests/Feature/ExampleTest.php`).

---

## 📦 Deployment

### Shared Hosting (InfinityFree, cPanel, etc.)
```bash
# 1. Set document root to /public
# 2. Upload project (exclude: vendor/, node_modules/, .git/, .env, database/*.sqlite)
# 3. On server:
composer install --no-dev --optimize-autoloader

# 4. Configure production .env (see Configuration)
# 5. Run migrations
php artisan migrate --force

# 6. Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Ensure writable directories
chmod -R 755 storage bootstrap/cache
```

### VPS / Docker / Laravel Forge
- Use `php-fpm` + Nginx
- Configure queue worker: `php artisan queue:work`
- Schedule: `* * * * * php artisan schedule:run`
- Use Redis for sessions/cache/queues in production

### Required Production `.env` Changes
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true
SESSION_DOMAIN=yourdomain.com
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_secure_password
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your_user
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

---

## 🔒 Security

### Reporting Vulnerabilities
If you discover a security vulnerability, please email **security@yourdomain.com** instead of opening an issue.

### Security Features Implemented
- **Authentication**: bcrypt, rate limiting, account approval
- **Authorization**: middleware chain (`auth` → `approved.session` → `module.access`/`super.admin`)
- **Data Protection**: encrypted sessions, private file storage, secure headers
- **Input Validation**: FormRequests on all endpoints
- **Audit Trail**: AssetHistory on create/update/delete/transfer/import
- **Dependency Scanning**: `composer audit` & `npm audit` in CI

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Follow code style: `./vendor/bin/pint`
4. Write tests for new functionality
5. Ensure all tests pass: `php artisan test`
6. Submit a Pull Request

### Coding Standards
- PSR-12 + Laravel Pint (run `./vendor/bin/pint` before committing)
- Strict types (`declare(strict_types=1)`) in new files
- Eloquent over raw queries
- FormRequests for validation
- Service classes for business logic

---

## 📝 License

This project is licensed under the **MIT License** — see [LICENSE](LICENSE) for details.

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) — The PHP framework for web artisans
- [Bootstrap](https://getbootstrap.com) — Responsive CSS framework
- [TailwindCSS](https://tailwindcss.com) — Utility-first CSS
- [Vite](https://vitejs.dev) — Next-gen frontend tooling
- [OpenSpout](https://openspout.org) — CSV/XLSX reading
- [PhpOffice/PHPWord](https://github.com/PHPOffice/PHPWord) — DOCX parsing

---

## 📞 Support

- **Documentation**: This README + inline code comments
- **Issues**: GitHub Issues for bugs/features
- **Security**: Email security@yourdomain.com

---

> **Built with ❤️ using Laravel 12** — *A robust, secure, and maintainable asset management solution.*