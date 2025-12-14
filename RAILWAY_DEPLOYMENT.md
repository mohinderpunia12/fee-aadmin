# Railway Deployment Guide

This guide will help you deploy the FeeAdmin application to Railway.

## Prerequisites

1. GitHub repository with your code (already done ✅)
2. Railway account ([railway.app](https://railway.app))
3. Access to Railway dashboard

## Step-by-Step Deployment

### 1. Create Railway Project

1. Go to [railway.app](https://railway.app) and sign in
2. Click **"New Project"**
3. Select **"Deploy from GitHub repo"**
4. Connect your GitHub account if not already connected
5. Select the `fee-aadmin` repository
6. Railway will automatically detect it's a Laravel application

### 2. Add MySQL Database

1. In your Railway project dashboard, click **"+ New"**
2. Select **"Database"**
3. Choose **"MySQL"** (or PostgreSQL if preferred)
4. Railway will automatically provision the database
5. Note: Railway automatically provides database connection variables

### 3. Configure Environment Variables

In Railway → Your Project → Variables, add the following:

#### Required Variables:

```env
APP_NAME="FeeAdmin"
APP_ENV=production
APP_KEY=                    # Run: php artisan key:generate --show (or Railway will auto-generate)
APP_DEBUG=false
APP_URL=https://your-app.railway.app  # Update after getting your Railway URL

# Database - Railway automatically provides these:
# Use Railway's MYSQL_URL or set individually:
DB_CONNECTION=mysql
DB_HOST=${MYSQLHOST}
DB_PORT=${MYSQLPORT}
DB_DATABASE=${MYSQLDATABASE}
DB_USERNAME=${MYSQLUSER}
DB_PASSWORD=${MYSQLPASSWORD}

# Session
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120

# JWT Authentication
JWT_SECRET=                 # Run: php artisan jwt:secret (locally, then copy the secret)

# Logging (Railway uses stderr)
LOG_CHANNEL=stderr
LOG_LEVEL=error

# Filesystem
FILESYSTEM_DISK=public

# Queue
QUEUE_CONNECTION=database
```

#### How to Get APP_KEY and JWT_SECRET:

**Option 1: Generate locally**
```bash
php artisan key:generate --show
php artisan jwt:secret --show
```

**Option 2: Let Railway generate (for APP_KEY only)**
- Railway can auto-generate APP_KEY, but you'll need to generate JWT_SECRET manually

### 4. Configure Build Settings

Railway should auto-detect Laravel, but verify these settings:

**Build Command:**
```bash
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev && npm ci && npm run build
```

**Start Command:**
```bash
php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && chmod -R 775 storage bootstrap/cache && php artisan storage:link || true && vendor/bin/heroku-php-apache2 public/
```

### 5. Storage Setup

Laravel needs writable storage directories. The start command includes:
- `chmod -R 775 storage bootstrap/cache` - Sets permissions
- `php artisan storage:link` - Creates symbolic link for public storage

**Note:** Railway has ephemeral filesystem. For persistent file storage, consider:
- Using Railway volumes for `/storage` directory
- Or using cloud storage (S3, DigitalOcean Spaces, etc.)

### 6. Deploy

1. Railway will automatically deploy when you push to the main branch
2. Or manually trigger deployment from Railway dashboard
3. Watch the build logs for any issues
4. Once deployed, Railway will provide a public URL

### 7. Post-Deployment Steps

1. **Update APP_URL** in environment variables with your Railway URL
2. **Create Super Admin User:**
   ```bash
   # Use Railway's shell/console feature
   php artisan tinker
   >>> User::create([
       'name' => 'Super Admin',
       'email' => 'admin@example.com',
       'password' => Hash::make('your-secure-password'),
       'school_id' => null
   ])
   ```

3. **Access your application:**
   - Admin Panel: `https://your-app.railway.app/admin`
   - App Panel: `https://your-app.railway.app/app/{school-slug}`

### 8. Custom Domain (Optional)

1. In Railway → Settings → Domains
2. Click **"Generate Domain"** for a custom subdomain
3. Or add your own custom domain
4. Update `APP_URL` environment variable with the new domain

## Environment Variables Reference

### Database Variables (Auto-provided by Railway)
Railway automatically provides these when you add a MySQL service:
- `MYSQLHOST` - Database host
- `MYSQLPORT` - Database port
- `MYSQLDATABASE` - Database name
- `MYSQLUSER` - Database username
- `MYSQLPASSWORD` - Database password
- `MYSQL_URL` - Full connection URL

### Security Best Practices

1. **Never commit `.env` file** - Already in `.gitignore` ✅
2. **Set `APP_DEBUG=false`** in production
3. **Use `SESSION_SECURE_COOKIE=true`** for HTTPS-only cookies
4. **Generate strong secrets** for `APP_KEY` and `JWT_SECRET`

## Troubleshooting

### Build Fails
- Check build logs in Railway dashboard
- Ensure all dependencies are in `composer.json` and `package.json`
- Verify PHP version compatibility (requires PHP 8.2+)

### Database Connection Fails
- Verify database service is running in Railway
- Check environment variables are correctly set
- Ensure database credentials match Railway's provided values

### Storage Permissions Error
- The start command includes `chmod -R 775 storage bootstrap/cache`
- If issues persist, Railway volumes may be needed

### 500 Errors
- Check Railway logs
- Verify `APP_KEY` is set
- Ensure database migrations ran successfully
- Check `LOG_LEVEL=error` and view logs

### Assets Not Loading
- Verify `npm run build` completed successfully
- Check `public/build` directory exists
- Ensure `php artisan storage:link` ran

## Monitoring

- View logs in Railway dashboard → Logs tab
- Monitor resource usage in Railway dashboard
- Set up alerts for deployment failures

## Additional Resources

- [Railway Documentation](https://docs.railway.app)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [Filament Documentation](https://filamentphp.com/docs)

