# Deployment Guide for Render

This guide will help you deploy FeeAdmin to Render using Docker.

## Prerequisites

- GitHub account with your code pushed
- Render account (sign up at https://render.com)

## Quick Deployment Steps

### 1. Push Code to GitHub

Make sure all your code is committed and pushed to GitHub:
```bash
git add .
git commit -m "Prepare for Render deployment"
git push origin main
```

### 2. Deploy Using Blueprint (Recommended)

1. Go to Render Dashboard: https://dashboard.render.com
2. Click "New +" → "Blueprint"
3. Connect your GitHub account if not already connected
4. Select your repository
5. Render will automatically detect `render.yaml` and create all services
6. Review the services and click "Apply"

### 3. Configure Environment Variables

After services are created, you need to set additional environment variables:

1. Go to your Web Service → Environment
2. Add these variables:
   ```
   APP_KEY=<generate-using-php-artisan-key-generate>
   APP_URL=https://your-service-name.onrender.com
   ```

To generate APP_KEY:
- Use Render Shell: `php artisan key:generate --show`
- Or run locally: `php artisan key:generate --show`
- Copy the output and paste it as APP_KEY value

### 4. Run Initial Setup

1. Open Render Shell for your web service
2. Run migrations:
   ```bash
   php artisan migrate --force
   ```
3. Create storage link:
   ```bash
   php artisan storage:link
   ```
4. Create super admin user:
   ```bash
   php artisan tinker
   >>> User::create(['name' => 'Super Admin', 'email' => 'admin@example.com', 'password' => Hash::make('your-secure-password'), 'school_id' => null])
   ```

### 5. Verify Deployment

1. Visit your service URL: `https://your-service-name.onrender.com`
2. You should see the landing page
3. Access admin panel: `https://your-service-name.onrender.com/admin`
4. Login with your super admin credentials

## Manual Deployment (Alternative)

If you prefer to set up services manually:

### Create Database

1. New → PostgreSQL or MySQL
2. Name: `fee-admin-db`
3. Plan: Starter (or higher)
4. Note the connection details

### Create Redis

1. New → Redis
2. Name: `fee-admin-redis`
3. Plan: Starter (or higher)
4. Note the connection details

### Create Web Service

1. New → Web Service
2. Connect your GitHub repository
3. Settings:
   - **Name**: `fee-admin`
   - **Environment**: `Docker`
   - **Dockerfile Path**: `./Dockerfile`
   - **Docker Context**: `.`
   - **Build Command**: (leave empty)
   - **Start Command**: (leave empty)
   - **Plan**: Starter or higher

4. Environment Variables:
   ```
   APP_NAME=FeeAdmin
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://fee-admin.onrender.com
   APP_KEY=<your-generated-key>
   
   DB_CONNECTION=mysql
   DB_HOST=<from-database-service>
   DB_PORT=<from-database-service>
   DB_DATABASE=<from-database-service>
   DB_USERNAME=<from-database-service>
   DB_PASSWORD=<from-database-service>
   
   REDIS_HOST=<from-redis-service>
   REDIS_PORT=<from-redis-service>
   REDIS_PASSWORD=<from-redis-service>
   
   SESSION_DRIVER=database
   SESSION_SECURE_COOKIE=true
   SESSION_HTTP_ONLY=true
   SESSION_SAME_SITE=lax
   
   CACHE_DRIVER=redis
   QUEUE_CONNECTION=redis
   
   LOG_CHANNEL=stderr
   LOG_LEVEL=error
   ```

5. Click "Create Web Service"

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

### Database Connection Errors

1. Verify database credentials in environment variables
2. Check database service is running
3. Ensure database allows connections from web service

### 500 Errors

1. Check application logs: `Render Shell → tail -f storage/logs/laravel.log`
2. Verify APP_KEY is set
3. Check file permissions: `chmod -R 755 storage bootstrap/cache`
4. Clear cache: `php artisan config:clear && php artisan cache:clear`

### Assets Not Loading

1. Verify `npm run build` completed successfully
2. Check `public/build` directory exists
3. Clear view cache: `php artisan view:clear`

### Storage Issues

1. Create storage link: `php artisan storage:link`
2. Set permissions: `chmod -R 755 storage`
3. Consider using S3 for persistent storage

## Updating Your Application

1. Push changes to GitHub
2. Render will automatically detect and deploy (if auto-deploy is enabled)
3. Or manually trigger deployment from Render Dashboard

## Scaling

- Upgrade plan in Render Dashboard for more resources
- Consider horizontal scaling for high traffic
- Monitor database and Redis usage

## Backup Strategy

1. Enable automatic backups for database in Render
2. Export database regularly: `pg_dump` or `mysqldump`
3. Backup uploaded files if using local storage

## Security Checklist

- [ ] APP_DEBUG=false in production
- [ ] Strong APP_KEY set
- [ ] Database credentials secure
- [ ] SESSION_SECURE_COOKIE=true
- [ ] HTTPS enabled (automatic on Render)
- [ ] File uploads secured
- [ ] Rate limiting configured
- [ ] Regular security updates

## Support

- Render Documentation: https://render.com/docs
- Laravel Documentation: https://laravel.com/docs
- Filament Documentation: https://filamentphp.com/docs

