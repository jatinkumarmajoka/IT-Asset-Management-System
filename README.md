# IT Asset Management System

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="360" alt="Laravel">
</p>

<p align="center">
  <strong>IT Asset Management System</strong><br>
  A dynamic web-based system for managing, tracking, issuing, verifying, and maintaining organizational IT assets.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-PHP-red?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Tailwind_CSS-UI-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Vite-Build-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
</p>

---

## 📌 About the Project

The **IT Asset Management System** is a web-based application designed to manage an organization's IT and physical assets digitally.

The system replaces manual asset registers and spreadsheets with a centralized application for maintaining asset records, department assignments, locations, users, categories, issue history, verification records, and asset status.

The application is designed to be **dynamic, scalable, and database-driven**, allowing organizations to manage large numbers of assets while maintaining a complete history of asset movement and ownership.

---

## 🎯 Objectives

The main objectives of the system are:

* Digitize IT asset records
* Maintain a centralized asset database
* Track assets assigned to employees/departments
* Record asset issue and return information
* Track asset locations
* Maintain asset categories and manufacturers
* Perform physical asset verification
* Maintain asset history
* Reduce duplicate/manual data entry
* Improve asset accountability
* Generate reliable asset records and reports

---

## 🚀 Key Features

### 📦 Asset Management

Manage complete information about organizational assets, including:

* Asset name
* Asset category
* Make and model
* Serial number
* Asset ID / identification number
* Purchase information
* Asset status
* Current location
* Current occupant
* Department
* Remarks

### 👤 Asset Assignment

Track who is currently using an asset.

The system can maintain:

* Present occupant
* Department
* Date of issue
* Date of return
* Assignment history
* Asset status

### 🏢 Department Management

Manage organizational departments and associate assets with the appropriate department.

### 📍 Location Management

Track where assets are physically located.

Examples:

* Office
* Computer Lab
* Department
* Store
* Server Room
* Classroom

### 🗂️ Category Management

Create and manage asset categories such as:

* Desktop Computers
* Laptops
* Monitors
* Printers
* UPS
* Networking Equipment
* Servers
* Projectors
* Accessories
* Other IT Equipment

### 🔍 Physical Verification

The system supports physical verification of assets and can record information such as:

| Field               | Description                          |
| ------------------- | ------------------------------------ |
| Name of Item        | Asset/item name                      |
| Make & Model        | Manufacturer and model               |
| Issued Quantity     | Quantity according to stock register |
| Physically Verified | Quantity physically found            |
| Present Occupant    | Current user/custodian               |
| Date of Issue       | Asset issue date                     |
| Remarks             | Additional observations              |

### 📊 Dashboard

The dashboard can provide an overview of:

* Total assets
* Active assets
* Assigned assets
* Available assets
* Under-repair assets
* Retired assets
* Department-wise assets
* Location-wise assets

### 📝 Import & Data Management

The system supports structured asset data management and import workflows for transferring existing records into the application.

### 🔄 Asset History

Maintain historical information about asset movements and assignments instead of only storing the current state.

---

## 🛠️ Technology Stack

### Backend

* PHP
* Laravel
* Laravel Eloquent ORM
* RESTful application architecture

### Frontend

* Blade
* HTML5
* CSS3
* JavaScript
* Tailwind CSS
* Vite

### Database

* MySQL

### Development Tools

* Git
* GitHub
* Composer
* Node.js
* npm
* VS Code
* XAMPP

---

## 📁 Project Structure

```text
it-asset-management/
│
├── app/
│   ├── Http/
│   ├── Models/
│   ├── Services/
│   └── ...
│
├── bootstrap/
│
├── config/
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── public/
│
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│
├── routes/
│
├── storage/
│
├── tests/
│
├── artisan
├── composer.json
├── package.json
├── vite.config.js
└── README.md
```

---

# ⚙️ Installation & Setup

## Requirements

Before installing the project, make sure the following are installed:

* PHP 8.x
* Composer
* MySQL
* Node.js
* npm
* Git
* XAMPP / Apache

---

## 1. Clone the Repository

```bash
git clone YOUR_GITHUB_REPOSITORY_URL
```

Enter the project directory:

```bash
cd it-asset-management
```

---

## 2. Install PHP Dependencies

```bash
composer install
```

---

## 3. Install Frontend Dependencies

```bash
npm install
```

---

## 4. Create Environment File

Copy the example environment file:

### Windows

```powershell
copy .env.example .env
```

### macOS / Linux

```bash
cp .env.example .env
```

---

## 5. Generate Application Key

```bash
php artisan key:generate
```

---

## 6. Configure Database

Create a MySQL database using phpMyAdmin or MySQL.

Example:

```text
Database Name: it_asset_management
```

Update the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=it_asset_management
DB_USERNAME=root
DB_PASSWORD=
```

> Never commit your `.env` file to GitHub.

---

## 7. Run Database Migrations

```bash
php artisan migrate
```

If seed data is available:

```bash
php artisan db:seed
```

Or:

```bash
php artisan migrate --seed
```

---

## 8. Build Frontend Assets

For development:

```bash
npm run dev
```

For production:

```bash
npm run build
```

---

## 9. Start Laravel

```bash
php artisan serve
```

The application will normally be available at:

```text
http://127.0.0.1:8000
```

---

# 💻 Development Workflow

This project is intended to be developed across multiple computers using Git and GitHub.

### First computer

After making changes:

```bash
git status
git add .
git commit -m "Describe your changes"
git push
```

### Second computer

Before starting work:

```bash
git pull
```

After making changes:

```bash
git add .
git commit -m "Describe your changes"
git push
```

### Recommended workflow

```text
                ┌───────────────┐
                │    GitHub     │
                │  Repository   │
                └───────┬───────┘
                        │
              ┌─────────┴─────────┐
              │                   │
              ▼                   ▼
       ┌─────────────┐     ┌─────────────┐
       │ Office PC   │     │ Personal PC │
       │   XAMPP     │     │   XAMPP     │
       └─────────────┘     └─────────────┘
              │                   │
              └─────── Git ───────┘
```

---

# 🔐 Security

Sensitive configuration files should never be committed to the repository.

The following files/directories are intentionally ignored:

```text
.env
/vendor
/node_modules
/public/build
/storage/*.key
```

Make sure passwords, API keys, database credentials, and other secrets remain inside `.env`.

---

# 🗄️ Database

The application uses a relational MySQL database.

Database structure is maintained through Laravel migrations:

```bash
php artisan migrate
```

To create a new migration:

```bash
php artisan make:migration create_example_table
```

To rollback the latest migration:

```bash
php artisan migrate:rollback
```

---

# 🧪 Testing

Run the application's test suite with:

```bash
php artisan test
```

Or:

```bash
vendor/bin/phpunit
```

---

# 🔧 Useful Laravel Commands

### Clear application cache

```bash
php artisan optimize:clear
```

### Check routes

```bash
php artisan route:list
```

### Start development server

```bash
php artisan serve
```

### Run migrations

```bash
php artisan migrate
```

### Create model

```bash
php artisan make:model ModelName
```

### Create controller

```bash
php artisan make:controller ControllerName
```

### Create migration

```bash
php artisan make:migration create_table_name_table
```

---

# 📌 Current Development Status

| Module                 | Status            |
| ---------------------- | ----------------- |
| Laravel Application    | 🟢 Active         |
| Database               | 🟢 Active         |
| Asset Management       | 🟢 Active         |
| Categories             | 🟢 Active         |
| Locations              | 🟢 Active         |
| Departments            | 🟢 Active         |
| Asset Assignment       | 🟢 Active         |
| Asset Verification     | 🟢 Active         |
| Import System          | 🟡 In Development |
| Reports                | 🟡 In Development |
| Dashboard              | 🟡 In Development |
| Authentication & Roles | 🟡 In Development |

> Update this table as development progresses.

---

# 🗺️ Future Improvements

Planned improvements may include:

* Advanced dashboard analytics
* Role-based access control
* User management
* Asset QR codes
* Barcode scanning
* Asset transfer workflow
* Asset return workflow
* Maintenance management
* Warranty tracking
* Purchase records
* Depreciation tracking
* PDF reports
* Excel exports
* Audit logs
* Notifications
* Advanced search and filtering
* Department-wise reports
* Location-wise reports
* Import/export improvements

---

# 🤝 Development

This project is maintained as a continuously evolving application.

Development workflow:

```text
Feature Development
        ↓
Local Testing
        ↓
Git Commit
        ↓
GitHub Push
        ↓
Other Computer
        ↓
Git Pull
        ↓
Continue Development
```

---

# 📄 License

This project is intended for organizational/internal use.

The Laravel framework itself is open-source and licensed under the MIT License.

---

## 👨‍💻 Project

**IT Asset Management System**

Built with:

**Laravel + PHP + MySQL + Tailwind CSS + JavaScript**

---

<p align="center">
  <strong>IT Asset Management System</strong><br>
  Digital asset tracking • Assignment • Verification • Management
</p>
