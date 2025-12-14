# FeeAdmin - Multi-Tenant School Management SaaS

A Laravel 11 + Filament v3 application for managing multiple schools in a single database.

## Features

- **Single-Database Multi-Tenancy**: All schools share one database with `school_id` foreign keys
- **Two Admin Panels**:
  - `/admin` - Super Admin panel for managing schools
  - `/app` - Tenant-aware panel for school-specific management
- **Complete CRUD** for Students, Staff, Fee Structures, and Fee Payments
- **Automatic Tenant Filtering**: All tenant-scoped resources automatically filter by current school

## Installation

1. Install dependencies:
```bash
composer install
npm install
```

2. Copy environment file:
```bash
cp .env.example .env
php artisan key:generate
```

3. Configure your database in `.env`

4. Run migrations:
```bash
php artisan migrate
```

5. Create a super admin user:
```bash
php artisan tinker
>>> User::create(['name' => 'Super Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password'), 'school_id' => null])
```

6. Start the development server:
```bash
php artisan serve
```

## Usage

### Super Admin Panel (`/admin`)
- Access with a user that has `school_id = null`
- Manage schools (create, edit, delete)
- View all schools and their details

### App Panel (`/app/{school_slug}`)
- Access with a user assigned to a school
- All data is automatically filtered to the current school
- Manage:
  - Students
  - Staff
  - Fee Structures
  - Fee Payments

## Architecture

- **Tenant Model**: `School`
- **Tenant Scope**: All models use `school_id` foreign key and `BelongsToSchool` trait
- **Authentication**: Standard Laravel authentication with school assignment

## Tech Stack

- Laravel 11
- Filament v3
- MySQL
# fee-aadmin
