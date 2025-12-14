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
- Docker (for deployment)
- Nginx + PHP-FPM

## Docker Development

### Prerequisites
- Docker and Docker Compose installed

### Quick Start with Docker

1. Clone the repository:
```bash
git clone <repository-url>
cd fee-aadmin
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Update `.env` with your database credentials (or use defaults for Docker Compose)

4. Start services:
```bash
docker-compose up -d
```

5. Run migrations and create storage link:
```bash
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan storage:link
```

6. Create a super admin user:
```bash
docker-compose exec app php artisan tinker
>>> User::create(['name' => 'Super Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password'), 'school_id' => null])
```

7. Access the application:
- Application: http://localhost:8080
- Database: localhost:3306
- Redis: localhost:6379

### Docker Commands

```bash
# View logs
docker-compose logs -f app

# Stop services
docker-compose down

# Rebuild containers
docker-compose build --no-cache

# Access app container
docker-compose exec app sh

# Run artisan commands
docker-compose exec app php artisan <command>
```

## Deployment on Render

This application is configured for deployment on Render using Docker.

### Prerequisites
- Render account
- GitHub repository with the code

### Deployment Steps

#### Option 1: Using render.yaml (Recommended)

1. Push your code to GitHub

2. In Render Dashboard:
   - Click "New +" → "Blueprint"
   - Connect your GitHub repository
   - Render will automatically detect `render.yaml` and create all services

3. Configure environment variables in Render Dashboard:
   - `APP_KEY`: Generate with `php artisan key:generate`
   - `APP_URL`: Your Render service URL
   - Add any other required environment variables

4. After deployment, run migrations:
   ```bash
   # Via Render Shell or SSH
   php artisan migrate --force
   php artisan storage:link
   ```

#### Option 2: Manual Setup

1. **Create Database Service:**
   - Type: PostgreSQL or MySQL
   - Name: `fee-admin-db`
   - Plan: Starter or higher

2. **Create Redis Service:**
   - Type: Redis
   - Name: `fee-admin-redis`
   - Plan: Starter or higher

3. **Create Web Service:**
   - Environment: Docker
   - Dockerfile Path: `./Dockerfile`
   - Build Command: (leave empty, handled by Dockerfile)
   - Start Command: (leave empty, handled by Dockerfile)
   - Environment Variables:
     ```
     APP_ENV=production
     APP_DEBUG=false
     APP_URL=https://your-app.onrender.com
     DB_HOST=<from-database-service>
     DB_DATABASE=<from-database-service>
     DB_USERNAME=<from-database-service>
     DB_PASSWORD=<from-database-service>
     REDIS_HOST=<from-redis-service>
     REDIS_PORT=<from-redis-service>
     REDIS_PASSWORD=<from-redis-service>
     SESSION_DRIVER=database
     SESSION_SECURE_COOKIE=true
     CACHE_DRIVER=redis
     LOG_CHANNEL=stderr
     LOG_LEVEL=error
     ```

4. **Run Initial Setup:**
   - Use Render Shell to run:
     ```bash
     php artisan migrate --force
     php artisan storage:link
     php artisan key:generate --force
     ```

### Post-Deployment

1. Create super admin user via Render Shell:
   ```bash
   php artisan tinker
   >>> User::create(['name' => 'Super Admin', 'email' => 'admin@example.com', 'password' => Hash::make('secure-password'), 'school_id' => null])
   ```

2. Set up file storage:
   - Render uses ephemeral storage, consider using S3 or similar for file uploads
   - Update `config/filesystems.php` to use S3 for production

### Environment Variables for Production

Required environment variables:
```env
APP_NAME="FeeAdmin"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.onrender.com
APP_KEY=<generated-key>

DB_CONNECTION=mysql
DB_HOST=<database-host>
DB_PORT=3306
DB_DATABASE=<database-name>
DB_USERNAME=<database-user>
DB_PASSWORD=<database-password>

REDIS_HOST=<redis-host>
REDIS_PORT=6379
REDIS_PASSWORD=<redis-password>

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

LOG_CHANNEL=stderr
LOG_LEVEL=error
```

### Troubleshooting

- **Build fails**: Check Docker logs in Render dashboard
- **Database connection errors**: Verify database credentials and network access
- **Storage issues**: Ensure storage directory has correct permissions
- **Asset build fails**: Check Node.js version compatibility

## Production Considerations

- Use Redis for caching and sessions in production
- Configure proper file storage (S3, etc.) for uploaded files
- Set up SSL/TLS certificates (handled by Render)
- Configure backup strategy for database
- Set up monitoring and logging
- Review security settings in `SECURITY_CONFIG.md`
