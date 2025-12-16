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
- PHP 8.1+

## Deployment on Hostinger

This application is configured for deployment on Hostinger using Git.

### Prerequisites
- Hostinger hosting account with PHP 8.1+ support
- Git repository (GitHub, GitLab, or Bitbucket)
- MySQL database created in Hostinger control panel

### Deployment Steps

1. **Prepare Your Repository:**
   - Ensure all code is committed and pushed to your Git repository
   - Make sure `.env` is in `.gitignore` (it should be by default)

2. **Connect Git to Hostinger:**
   - Log in to your Hostinger control panel (hPanel)
   - Navigate to **Website** → **Git Version Control**
   - Click **Connect Repository**
   - Connect your Git provider (GitHub, GitLab, or Bitbucket)
   - Select your repository and branch (usually `main` or `master`)
   - Set the deployment path (usually `public_html` or your domain folder)
   - Click **Deploy**

3. **Configure Environment Variables:**
   - In Hostinger hPanel, navigate to your domain's file manager
   - Create or edit `.env` file in the root directory
   - Add the following configuration:
     ```env
     APP_NAME="FeeAdmin"
     APP_ENV=production
     APP_DEBUG=false
     APP_URL=https://yourdomain.com
     APP_KEY=<generate-this-locally-or-via-ssh>
     
     DB_CONNECTION=mysql
     DB_HOST=localhost
     DB_PORT=3306
     DB_DATABASE=<your-database-name>
     DB_USERNAME=<your-database-username>
     DB_PASSWORD=<your-database-password>
     
     SESSION_DRIVER=database
     SESSION_SECURE_COOKIE=true
     SESSION_HTTP_ONLY=true
     SESSION_SAME_SITE=lax
     
     CACHE_DRIVER=file
     QUEUE_CONNECTION=sync
     
     LOG_CHANNEL=daily
     LOG_LEVEL=error
     ```

4. **Set Up Database:**
   - In Hostinger hPanel, go to **Databases** → **MySQL Databases**
   - Create a new database and user
   - Note down the database credentials
   - Update your `.env` file with these credentials

5. **Deploy via SSH (Recommended):**
   - Access your Hostinger account via SSH
   - Navigate to your project directory:
     ```bash
     cd ~/domains/yourdomain.com/public_html
     ```
   - Install dependencies:
     ```bash
     composer install --no-dev --optimize-autoloader
     npm install
     npm run build
     ```
   - Generate application key:
     ```bash
     php artisan key:generate
     ```
   - Run migrations:
     ```bash
     php artisan migrate --force
     ```
   - Create storage link:
     ```bash
     php artisan storage:link
     ```
   - Set proper permissions:
     ```bash
     chmod -R 755 storage bootstrap/cache
     chmod -R 755 public
     ```
   - Clear and cache configuration:
     ```bash
     php artisan config:cache
     php artisan route:cache
     php artisan view:cache
     ```

6. **Configure Web Server:**
   - Ensure your `.htaccess` file in `public` directory is properly configured
   - Point your domain's document root to the `public` directory
   - In Hostinger, this is usually done automatically, but verify in **Domains** → **Your Domain** → **Document Root**

7. **Create Super Admin User:**
   - Via SSH, run:
     ```bash
     php artisan tinker
     ```
   - Then execute:
     ```php
     User::create([
         'name' => 'Super Admin',
         'email' => 'admin@yourdomain.com',
         'password' => Hash::make('your-secure-password'),
         'school_id' => null
     ]);
     ```

### Post-Deployment

1. **Verify Deployment:**
   - Visit your domain: `https://yourdomain.com`
   - Check if the landing page loads correctly
   - Access admin panel: `https://yourdomain.com/admin`
   - Login with your super admin credentials

2. **Set Up File Storage:**
   - Ensure `storage` directory has write permissions
   - For production, consider using cloud storage (S3, DigitalOcean Spaces, etc.)
   - Update `config/filesystems.php` if using cloud storage

3. **Enable SSL:**
   - Hostinger usually provides free SSL certificates
   - Enable it in **SSL** section of hPanel
   - Ensure `APP_URL` in `.env` uses `https://`

### Environment Variables for Production

Required environment variables:
```env
APP_NAME="FeeAdmin"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_KEY=<generated-key>

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=<database-name>
DB_USERNAME=<database-user>
DB_PASSWORD=<database-password>

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

LOG_CHANNEL=daily
LOG_LEVEL=error
```

### Troubleshooting

- **500 Internal Server Error**: Check file permissions (`chmod -R 755 storage bootstrap/cache`)
- **Database connection errors**: Verify database credentials in `.env` and ensure database exists
- **Assets not loading**: Run `npm run build` and ensure `public/build` directory exists
- **Storage issues**: Ensure storage directory has write permissions and `storage:link` is created
- **Route not found**: Clear route cache: `php artisan route:clear`
- **Permission denied**: Check file ownership and permissions via SSH

### Updating Your Application

1. Push changes to your Git repository
2. In Hostinger hPanel, go to **Git Version Control**
3. Click **Pull** or **Deploy** to update your files
4. Via SSH, run:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Production Considerations

- Set `APP_DEBUG=false` in production
- Use strong passwords for database and admin accounts
- Enable SSL/TLS certificates (usually automatic on Hostinger)
- Configure regular database backups via Hostinger control panel
- Set up file storage for uploaded files (consider cloud storage for scalability)
- Review security settings in `SECURITY_CONFIG.md`
- Monitor application logs in `storage/logs/`
