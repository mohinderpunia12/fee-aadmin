# Deployment Guide for Render

This guide will help you deploy FeeAdmin to Render using Docker. This approach uses a single Web Service, allowing you to use the free tier for the web service while creating database and Redis services separately.

## Prerequisites

- GitHub account with your code pushed
- Render account (sign up at https://render.com)

## Step-by-Step Deployment

### Step 1: Push Code to GitHub

Make sure all your code is committed and pushed to GitHub:
```bash
git add .
git commit -m "Prepare for Render deployment"
git push origin main
```

### Step 2: Create Database Service

1. Go to Render Dashboard: https://dashboard.render.com
2. Click "New +" → "PostgreSQL" or "MySQL"
3. Configure:
   - **Name**: `fee-admin-db`
   - **Database**: `feeadmin` (or your preferred name)
   - **User**: `feeadmin` (or your preferred username)
   - **Plan**: Free tier available (or Starter for production)
   - **Region**: Choose closest to your users
4. Click "Create Database"
5. **Important**: Note down the connection details shown:
   - Internal Database URL
   - Host
   - Port
   - Database name
   - Username
   - Password

### Step 3: Create Redis Service (Optional but Recommended)

1. Click "New +" → "Redis"
2. Configure:
   - **Name**: `fee-admin-redis`
   - **Plan**: Free tier available (or Starter for production)
   - **Region**: Same as your database
   - **IP Allow List**: Leave empty `[]` to allow all connections
3. Click "Create Redis"
4. **Important**: Note down the connection details:
   - Internal Redis URL (connectionString)
   - Host
   - Port

**Note**: If you want to skip Redis for now, you can use `file` driver for cache and `database` for sessions. You can add Redis later.

### Step 4: Create Web Service

1. Click "New +" → "Web Service"
2. Connect your GitHub account if not already connected
3. Select your repository
4. Configure the service:

   **Basic Settings:**
   - **Name**: `fee-admin` (or your preferred name)
   - **Region**: Same as your database
   - **Branch**: `main` (or your default branch)
   - **Root Directory**: (leave empty)
   - **Environment**: `Docker`
   - **Dockerfile Path**: `./Dockerfile`
   - **Docker Context**: `.`
   - **Plan**: Free tier available (or Starter for production)

   **Build & Deploy:**
   - **Build Command**: (leave empty - handled by Dockerfile)
   - **Start Command**: (leave empty - handled by Dockerfile)
   - **Health Check Path**: `/`

5. **Environment Variables** - Add these one by one:

   **Application Settings:**
   ```
   APP_NAME=FeeAdmin
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://fee-admin.onrender.com
   APP_KEY=<will-generate-later>
   ```

   **Database Settings** (use values from Step 2):
   ```
   DB_CONNECTION=mysql
   DB_HOST=<your-database-host>
   DB_PORT=3306
   DB_DATABASE=feeadmin
   DB_USERNAME=feeadmin
   DB_PASSWORD=<your-database-password>
   ```

   **Session Settings:**
   ```
   SESSION_DRIVER=database
   SESSION_SECURE_COOKIE=true
   SESSION_HTTP_ONLY=true
   SESSION_SAME_SITE=lax
   ```

   **Cache Settings** (if using Redis from Step 3):
   ```
   CACHE_DRIVER=redis
   QUEUE_CONNECTION=redis
   REDIS_URL=<your-redis-connection-string>
   ```

   **OR** (if not using Redis):
   ```
   CACHE_DRIVER=file
   QUEUE_CONNECTION=sync
   ```

   **Logging:**
   ```
   LOG_CHANNEL=stderr
   LOG_LEVEL=error
   ```

6. Click "Create Web Service"

### Step 5: Generate APP_KEY

After the service is created:

1. Wait for the first deployment to complete (it may fail, that's okay)
2. Open "Shell" in your web service
3. Run:
   ```bash
   php artisan key:generate --show
   ```
4. Copy the generated key
5. Go to Environment → Edit
6. Update `APP_KEY` with the generated value
7. Save changes (this will trigger a new deployment)

### Step 6: Run Initial Setup

1. Open "Shell" in your web service
2. Run migrations:
   ```bash
   php artisan migrate --force
   ```
3. Create storage link:
   ```bash
   php artisan storage:link
   ```
4. Set proper permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

### Step 7: Create Super Admin User

1. In the Shell, run:
   ```bash
   php artisan tinker
   ```
2. Then run:
   ```php
   User::create([
       'name' => 'Super Admin',
       'email' => 'admin@example.com',
       'password' => Hash::make('your-secure-password'),
       'school_id' => null
   ]);
   ```
3. Exit tinker: `exit`

### Step 8: Verify Deployment

1. Visit your service URL: `https://fee-admin.onrender.com`
2. You should see the landing page
3. Access admin panel: `https://fee-admin.onrender.com/admin`
4. Login with your super admin credentials

## Free Tier Limitations

### Web Service (Free Tier)
- ✅ 750 hours/month (enough for 24/7 operation)
- ✅ Automatic SSL
- ⚠️ Spins down after 15 minutes of inactivity
- ⚠️ Limited resources (512MB RAM)

### Database (Free Tier)
- ✅ 90 days free trial
- ⚠️ After trial, requires paid plan
- ✅ Automatic backups

### Redis (Free Tier)
- ✅ 25MB memory
- ✅ Suitable for small applications

**Recommendation**: Start with free tier, upgrade when needed.

## Environment Variables Reference

### Required Variables

```env
APP_NAME=FeeAdmin
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-service.onrender.com
APP_KEY=<generated-key>

DB_CONNECTION=mysql
DB_HOST=<database-host>
DB_PORT=3306
DB_DATABASE=<database-name>
DB_USERNAME=<database-user>
DB_PASSWORD=<database-password>

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

LOG_CHANNEL=stderr
LOG_LEVEL=error
```

### Optional Variables (if using Redis)

```env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_URL=redis://:<password>@<host>:<port>
```

### Alternative (without Redis)

```env
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

## Post-Deployment Tasks

### File Storage

Render uses ephemeral storage. For production, consider:

1. **Option 1: Use S3-compatible storage**
   - Install: `composer require league/flysystem-aws-s3-v3`
   - Update `config/filesystems.php`
   - Set environment variables:
     ```
     FILESYSTEM_DISK=s3
     AWS_ACCESS_KEY_ID=your-key
     AWS_SECRET_ACCESS_KEY=your-secret
     AWS_DEFAULT_REGION=us-east-1
     AWS_BUCKET=your-bucket-name
     ```

2. **Option 2: Use Render Disk (for small files)**
   - Add persistent disk in Render dashboard
   - Mount to `/var/www/html/storage`

### SSL/TLS

Render automatically provides SSL certificates. No action needed.

### Monitoring

- Check logs in Render Dashboard → Logs
- Set up alerts for service failures
- Monitor database and Redis usage

## Troubleshooting

### Build Fails

1. Check build logs in Render Dashboard
2. Verify Dockerfile syntax
3. Ensure all dependencies are in `composer.json` and `package.json`
4. Check that Dockerfile path is correct: `./Dockerfile`

### Database Connection Errors

1. Verify database credentials in environment variables
2. Check database service is running
3. Ensure you're using **Internal Database URL** (not external)
4. Verify database name, username, and password are correct
5. Check that database and web service are in the same region

### Redis Connection Errors

1. Verify `REDIS_URL` is set correctly
2. Use the **Internal Redis URL** from Redis service
3. Check Redis service is running
4. If not using Redis, set `CACHE_DRIVER=file` and `QUEUE_CONNECTION=sync`

### 500 Errors

1. Check application logs: `Render Shell → tail -f storage/logs/laravel.log`
2. Verify APP_KEY is set correctly
3. Check file permissions: `chmod -R 755 storage bootstrap/cache`
4. Clear cache: `php artisan config:clear && php artisan cache:clear`
5. Verify all environment variables are set

### Service Spins Down (Free Tier)

- Free tier services spin down after 15 minutes of inactivity
- First request after spin-down takes ~30 seconds to wake up
- Consider upgrading to Starter plan for always-on service

### Assets Not Loading

1. Verify `npm run build` completed successfully in build logs
2. Check `public/build` directory exists
3. Clear view cache: `php artisan view:clear`
4. Verify Vite build completed without errors

### Storage Issues

1. Create storage link: `php artisan storage:link`
2. Set permissions: `chmod -R 755 storage`
3. Consider using S3 for persistent storage
4. Remember: Render storage is ephemeral (files are lost on redeploy)

### APP_KEY Issues

1. Generate new key: `php artisan key:generate --show`
2. Update environment variable in Render Dashboard
3. Clear config cache: `php artisan config:clear`

## Updating Your Application

1. Push changes to GitHub
2. Render will automatically detect and deploy (if auto-deploy is enabled)
3. Or manually trigger deployment from Render Dashboard → Manual Deploy

## Scaling

- **Free Tier**: 750 hours/month, spins down after inactivity
- **Starter Plan**: Always-on, more resources, better performance
- Upgrade plan in Render Dashboard for more resources
- Monitor database and Redis usage
- Consider horizontal scaling for high traffic

## Backup Strategy

1. Enable automatic backups for database in Render
2. Export database regularly via Shell:
   ```bash
   mysqldump -h <host> -u <user> -p <database> > backup.sql
   ```
3. Backup uploaded files if using local storage (consider S3 instead)

## Security Checklist

- [ ] APP_DEBUG=false in production
- [ ] Strong APP_KEY set
- [ ] Database credentials secure (use Internal URLs)
- [ ] SESSION_SECURE_COOKIE=true
- [ ] HTTPS enabled (automatic on Render)
- [ ] File uploads secured
- [ ] Rate limiting configured
- [ ] Regular security updates
- [ ] Environment variables not exposed in logs

## Cost Optimization Tips

1. **Start with Free Tier**: Test with free tier first
2. **Use Database Free Trial**: 90 days free for database
3. **Optimize Resources**: Use file cache if Redis isn't critical
4. **Monitor Usage**: Check Render dashboard for usage stats
5. **Upgrade When Needed**: Only upgrade when you need always-on service

## Support

- Render Documentation: https://render.com/docs
- Laravel Documentation: https://laravel.com/docs
- Filament Documentation: https://filamentphp.com/docs
- Render Support: https://render.com/support

## Quick Reference Commands

```bash
# Generate APP_KEY
php artisan key:generate --show

# Run migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Check application status
php artisan about

# View logs
tail -f storage/logs/laravel.log
```
