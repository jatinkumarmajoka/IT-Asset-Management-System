# Complete Setup Guide — IT Asset Management System

> **Follow this guide exactly** to set up the project on any new machine (Windows, macOS, Linux) or server.

---

## 📋 Table of Contents

1. [System Requirements](#-system-requirements)
2. [Install Prerequisites](#-install-prerequisites)
3. [Clone & Install](#-clone--install)
4. [Configure Environment](#-configure-environment)
5. [Database Setup](#-database-setup)
6. [Build Frontend Assets](#-build-frontend-assets)
7. [Create First Super Admin](#-create-first-super-admin)
8. [Run Development Server](#-run-development-server)
9. [Verify Installation](#-verify-installation)
10. [Production Deployment](#-production-deployment)
11. [Troubleshooting](#-troubleshooting)
12. [Useful Commands](#-useful-commands)

---

## 🖥️ System Requirements

| Component | Minimum Version | Recommended | Notes |
|-----------|-----------------|-------------|-------|
| **PHP** | 8.5+ (ZTS) | 8.5.12 | Must have extensions below |
| **Composer** | 2.x | Latest | Dependency manager |
| **Node.js** | 20+ | 22 LTS | For Vite/build |
| **npm** | 10+ | Latest | Comes with Node |
| **Git** | 2.x | Latest | Version control |
| **Database** | SQLite 3 / MySQL 8+ / MariaDB 10.6+ | MySQL 8+ | Production requires MySQL/MariaDB |

### Required PHP Extensions
```ini
extension=openssl
extension=mbstring
extension=curl
extension=fileinfo
extension=pdo_mysql
extension=pdo_sqlite
extension=zip
extension=gd
extension=sqlite3
extension=bcmath
extension=ctype
extension=dom
extension=xml
extension=xmlreader
extension=xmlwriter
extension=tokenizer
extension=json
extension=iconv
```

---

## 🔧 Install Prerequisites

### Windows (XAMPP Recommended)

1. **Install XAMPP** (includes PHP, MySQL, Apache)
   - Download: https://www.apachefriends.org/download.html
   - Choose **PHP 8.5+** version
   - Install to `C:\xampp` (default)

2. **Enable PHP Extensions**
   - Open `C:\xampp\php\php.ini`
   - Uncomment (remove `;`) these lines:
   ```ini
   extension=openssl
   extension=mbstring
   extension=curl
   extension=fileinfo
   extension=pdo_mysql
   extension=pdo_sqlite
   extension=zip
   extension=gd
   extension=sqlite3
   extension=bcmath
   ```
   - Set `extension_dir = "ext"`
   - Save and restart Apache via XAMPP Control Panel

3. **Add PHP to PATH** (PowerShell as Admin):
   ```powershell
   [Environment]::SetEnvironmentVariable("Path", $env:Path + ";C:\xampp\php", "Machine")
   ```
   - Restart terminal

4. **Install Composer**
   - Download: https://getcomposer.org/download/
   - Run `Composer-Setup.exe` (installs globally)

5. **Install Node.js**
   - Download: https://nodejs.org/ (LTS version)
   - Run installer (includes npm)

6. **Install Git**
   - Download: https://git-scm.com/download/win
   - Default options are fine

### macOS (Homebrew)

```bash
# Install Homebrew first: https://brew.sh
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install dependencies
brew install php@8.5 composer node git mysql

# Enable PHP extensions (usually enabled by default)
# Link PHP if needed:
brew link --force --overwrite php@8.5

# Start MySQL service
brew services start mysql
```

### Linux (Ubuntu/Debian)

```bash
# Update & install
sudo apt update && sudo apt install -y \
    php8.5 php8.5-cli php8.5-common php8.5-mysql php8.5-sqlite3 \
    php8.5-mbstring php8.5-xml php8.5-curl php8.5-zip php8.5-gd \
    php8.5-bcmath php8.5-tokenizer php8.5-fileinfo \
    composer nodejs npm git mysql-server

# Verify PHP version
php -v  # Should show 8.5.x

# Start MySQL
sudo systemctl start mysql
sudo systemctl enable mysql
```

---

## 📥 Clone & Install

```bash
# 1. Clone the repository
git clone https://github.com/jatinkumarmajoka/IT-Asset-Management-System.git
cd IT-Asset-Management-System

# 2. Install PHP dependencies (Composer)
composer install
# If you get "platform requirements" error:
composer install --ignore-platform-req=ext-gd  # or other missing ext

# 3. Install JavaScript dependencies
npm ci
# Or: npm install

# 4. Verify installations
php -v
composer --version
node -v
npm -v
git --version
```

---

## ⚙️ Configure Environment

### 1. Copy Environment File
```bash
cp .env.example .env
```

### 2. Generate Application Key
```bash
php artisan key:generate
# Output: Application key set successfully.
```

### 3. Edit `.env` for Your Environment

#### **For Local Development (SQLite — Default)**
```env
APP_NAME="IT Asset Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite  (default, no change needed)

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=false

MAIL_MAILER=log
```

#### **For Local Development (MySQL)**
```env
APP_NAME="IT Asset Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=it_asset_management
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=false

MAIL_MAILER=log
```

#### **For Production (Required Changes)**
```env
APP_NAME="Your Organization - Asset Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=yourdomain.com
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your_smtp_user
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Optional: Redis for queues/cache
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

> **⚠️ Never commit real `.env` to Git!** Use `.env.example` as template.

---

## 🗄️ Database Setup

### Option A: SQLite (Easiest for Local Dev)

```bash
# Create database file (if not exists)
touch database/database.sqlite

# Run migrations
php artisan migrate --force

# (Optional) Seed demo data
php artisan db:seed
```

### Option B: MySQL/MariaDB (Production & Teams)

```bash
# 1. Create database & user (MySQL shell)
mysql -u root -p
# Enter root password, then:
CREATE DATABASE it_asset_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'asset_user'@'%' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON it_asset_management.* TO 'asset_user'@'%';
FLUSH PRIVILEGES;
EXIT;

# 2. Update .env with credentials (see above)

# 3. Run migrations
php artisan migrate --force

# 4. (Optional) Seed demo data
php artisan db:seed
```

### Option C: Fresh Install (Clean Slate)
```bash
# Drops all tables & re-runs migrations + seeders
php artisan migrate:fresh --seed --force
```

---

## 🎨 Build Frontend Assets

### Development (Hot Reload)
```bash
# Terminal 1: Start Vite dev server
npm run dev

# Terminal 2: Start Laravel server (see next section)
php artisan serve
```

### Production Build
```bash
# Builds minified, versioned assets to public/build/
npm run build

# Verify build output
ls -la public/build/
# Should show: manifest.json, assets/app-*.css, assets/app-*.js
```

> **Important:** Run `npm run build` **before** deploying to production!

---

## 👑 Create First Super Admin

The first Super Admin must be created via CLI (no web registration for security).

```bash
php artisan app:create-super-admin
```

**Interactive Prompts:**
```
Super Admin name: John Admin
Super Admin email: admin@yourorg.com
Super Admin password: ************  (min 12 chars, hidden)
Confirm password: ************
```

**Output:**
```
Super Admin created successfully.
You can now sign in using the email you registered.
```

> **Note:** If a Super Admin already exists, command will fail. Delete from DB or use different email.

---

## 🚀 Run Development Server

### Quick Start (All-in-One)
```bash
# Starts: Laravel + Queue + Logs + Vite (requires concurrently)
composer run dev
```

### Manual (Separate Terminals)

**Terminal 1 — Laravel Server:**
```bash
php artisan serve
# Or specific port: php artisan serve --port=8080
# Output: Server running on [http://127.0.0.1:8000]
```

**Terminal 2 — Queue Worker (for background jobs):**
```bash
php artisan queue:listen --tries=1 --timeout=0
```

**Terminal 3 — Logs (Laravel Pail):**
```bash
php artisan pail --timeout=0
```

**Terminal 4 — Vite Dev Server:**
```bash
npm run dev
```

### Access Application
Open browser: **http://127.0.0.1:8000**

- Redirects to `/login`
- Sign in with Super Admin credentials
- You'll land on `/dashboard`

---

## ✅ Verify Installation

### Checklist
| Test | Command / Action | Expected |
|------|------------------|----------|
| **PHP Version** | `php -v` | 8.5.x |
| **Composer** | `composer --version` | 2.x |
| **Node/npm** | `node -v && npm -v` | 20+ / 10+ |
| **Laravel** | `php artisan --version` | 12.x |
| **Migrations** | `php artisan migrate:status` | All "Ran" |
| **Tests** | `php artisan test` | 2 passed |
| **Code Style** | `./vendor/bin/pint --test` | passed |
| **Security Audit** | `composer audit && npm audit` | 0 vulnerabilities |
| **Build** | `npm run build` | Assets in `public/build/` |
| **Login** | Visit `/login`, sign in | Dashboard loads |

### Quick Health Check
```bash
# Single command verification
php artisan test && ./vendor/bin/pint --test && composer audit && npm audit
```

---

## 🌐 Production Deployment

### Shared Hosting (cPanel, InfinityFree, etc.)

```bash
# 1. Set Document Root to /public folder
#    (In cPanel: "Addon Domain" → Document Root = public_html/yourproject/public)

# 2. Upload project via FTP/SFTP (exclude):
#    - vendor/
#    - node_modules/
#    - .git/
#    - .env
#    - database/*.sqlite
#    - storage/logs/*
#    - bootstrap/cache/*

# 3. On server (SSH or cPanel Terminal):
composer install --no-dev --optimize-autoloader

# 4. Copy .env.example → .env and EDIT with production values
cp .env.example .env
# Edit .env (see "Configure Environment → Production" above)

# 5. Generate key
php artisan key:generate

# 6. Run migrations
php artisan migrate --force

# 7. Build assets
npm run build

# 8. Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9. Set permissions (Linux)
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # or your web user
```

### VPS / Dedicated Server (Nginx + PHP-FPM)

**Nginx Config (`/etc/nginx/sites-available/asset-management`):**
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/asset-management/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.5-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Enable & SSL:**
```bash
sudo ln -s /etc/nginx/sites-available/asset-management /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx

# SSL with Certbot
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### Docker (Optional)

**`Dockerfile`:**
```dockerfile
FROM php:8.5-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    nodejs npm \
    $PHPIZE_DEPS \
    linux-headers \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    sqlite-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql pdo_sqlite zip gd bcmath mbstring xml tokenizer

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader
RUN npm ci && npm run build

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 755 storage bootstrap/cache

# Configure Nginx & Supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

---

## 🔧 Troubleshooting

### Common Issues & Fixes

| Error | Cause | Fix |
|-------|-------|-----|
| `could not find driver` (SQLite) | Missing `pdo_sqlite` / `sqlite3` extensions | Enable in `php.ini`, restart web server |
| `could not find driver` (MySQL) | Missing `pdo_mysql` extension | Enable `extension=pdo_mysql` in `php.ini` |
| `No application encryption key` | `APP_KEY` not set | Run `php artisan key:generate` |
| `Access denied for user` (MySQL) | Wrong credentials in `.env` | Check `DB_USERNAME`, `DB_PASSWORD`, `DB_HOST` |
| `Permission denied` (storage) | Web server can't write to `storage/` | `chmod -R 755 storage bootstrap/cache` |
| `Vite manifest not found` | Assets not built | Run `npm run build` |
| `Class "MongoDB\Client" not found` | MongoDB extension missing | Not needed — remove from `composer.json` if present |
| `Maximum execution time exceeded` | Large import/operation | Increase `max_execution_time` in `php.ini` or `set_time_limit()` |
| `npm ERR! code ENOLOCK` | No `package-lock.json` | Run `npm i --package-lock-only` then `npm ci` |

### Reset Everything (Clean Slate)
```bash
# ⚠️ DESTROYS ALL DATA — only for local dev!
rm -f database/database.sqlite
rm -rf vendor node_modules public/build
composer install
npm ci
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed --force
npm run build
```

### Debug Mode
```bash
# Enable detailed errors in .env
APP_DEBUG=true
LOG_LEVEL=debug

# Clear caches
php artisan optimize:clear
# Or individually:
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## 📋 Useful Commands Reference

### Daily Development
```bash
# Start all services (Laravel + Queue + Logs + Vite)
composer run dev

# Run tests
php artisan test

# Check code style
./vendor/bin/pint --test

# Fix code style
./vendor/bin/pint

# Clear all caches
php artisan optimize:clear

# View routes
php artisan route:list

# View migration status
php artisan migrate:status

# Tinker (REPL)
php artisan tinker
```

### Database
```bash
# Run migrations
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Fresh + seed
php artisan migrate:fresh --seed

# Create migration
php artisan make:migration create_table_name

# Create seeder
php artisan make:seeder TableNameSeeder
```

### Code Generation
```bash
# Controller (resourceful)
php artisan make:controller ModelNameController --resource --model=ModelName

# Model + migration + factory + seeder + controller
php artisan make:model ModelName -mfsc

# Form Request
php artisan make:request StoreModelNameRequest

# Middleware
php artisan make:middleware MiddlewareName

# Service (manual — create in app/Services)
```

### Queue & Schedule
```bash
# Process queue
php artisan queue:work

# Listen queue (dev)
php artisan queue:listen

# Failed jobs
php artisan queue:failed
php artisan queue:retry all

# Run scheduler (cron)
php artisan schedule:run
```

### Assets & Storage
```bash
# Build production assets
npm run build

# Dev with hot reload
npm run dev

# Link storage (for public files)
php artisan storage:link

# Clear compiled views
php artisan view:clear
```

### Security & Maintenance
```bash
# Security audit
composer audit
npm audit

# Check outdated packages
composer outdated
npm outdated

# Optimize autoloader (production)
composer dump-autoload -o

# Backup database (SQLite)
cp database/database.sqlite backups/backup_$(date +%F).sqlite

# Backup database (MySQL)
mysqldump -u user -p db_name > backups/backup_$(date +%F).sql
```

---

## 📁 Project-Specific Notes

### Key Files to Know
| File | Purpose |
|------|---------|
| `routes/web.php` | All 71 web routes |
| `app/Http/Middleware/EnsureModuleAccess.php` | Module permission checks |
| `app/Http/Middleware/EnsureApprovedAndActive.php` | 48hr session timeout |
| `app/Services/AssetService.php` | Asset business logic |
| `app/Services/Transfers/AssetTransferService.php` | Transfer workflow |
| `app/Services/Imports/AssetImportService.php` | Import processing |
| `resources/views/layouts/app.blade.php` | Main layout (sidebar, navbar) |
| `config/branding.php` | Org name, logo, colors |

### Module Permissions (Super Admin → Permissions tab)
| Module | Controls Access To |
|--------|-------------------|
| `assets` | Asset CRUD, image/bill upload |
| `departments` | Department CRUD |
| `categories` | Category CRUD |
| `employees` | Employee CRUD |
| `imports` | Upload, process, validate, confirm imports |
| `transfers` | Create, view, cancel transfers |
| `bills` | Bill/invoice upload, replace, delete |
| `verification` | (Future) Physical verification |
| `reports` | (Future) Reporting |

---

## 🆘 Getting Help

1. **Check this guide** — most issues are covered above
2. **Run verification commands** — `php artisan test && ./vendor/bin/pint --test`
3. **Check logs** — `storage/logs/laravel.log`
4. **GitHub Issues** — https://github.com/jatinkumarmajoka/IT-Asset-Management-System/issues
5. **Laravel Docs** — https://laravel.com/docs/12.x

---

## ✅ Final Checklist Before Going Live

- [ ] `.env` has production values (`APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, etc.)
- [ ] `APP_KEY` is set and kept secret
- [ ] Database is MySQL/MariaDB (not SQLite)
- [ ] Migrations run: `php artisan migrate --force`
- [ ] Assets built: `npm run build`
- [ ] Caches cleared then cached: `php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache`
- [ ] Storage & cache writable: `chmod -R 755 storage bootstrap/cache`
- [ ] Super Admin account created and tested
- [ ] SSL/HTTPS configured (required for secure cookies)
- [ ] Queue worker running (systemd/supervisor/cron)
- [ ] Scheduler cron added: `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`
- [ ] Backups configured (database + storage)

---

> **You're ready!** 🎉 The IT Asset Management System is now installed and configured. Visit your domain, sign in as Super Admin, and start managing assets.

*Last updated: October 2026 | For Laravel 12.x / PHP 8.5+*