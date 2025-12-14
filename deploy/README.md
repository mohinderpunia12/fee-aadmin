# Deployment Guide for FeeAdmin on Render

This directory contains deployment-specific documentation and configuration files.

## Files

- `render-env.md` - Complete list of environment variables to set in Render Dashboard

## Quick Deployment Steps

### 1. Pre-Deploy Testing (Local)

Run these commands locally to ensure everything works:

```bash
# Install dependencies
composer install

# Generate application key
php artisan key:generate

# Run migrations (if you have a local database)
php artisan migrate

# Build Docker image
docker build -t feeadmin .

# Run container locally (requires .env file)
docker run -p 8080:80 --env-file .env feeadmin
```

Visit `http://localhost:8080` to verify the application works.

### 2. Deploy to Render

1. **Push code to GitHub**
   ```bash
   git add .
   git commit -m "Prepare for Render deployment"
   git push origin main
   ```

2. **Create PostgreSQL Database in Render**
   - Go to Render Dashboard
   - Click "New +" → "PostgreSQL"
   - Name: `fee-admin-db`
   - Plan: Free (or Starter for production)
   - Note the **Internal Database URL**

3. **Create Web Service in Render**
   - Click "New +" → "Web Service"
   - Connect your GitHub repository
   - Configure:
     - **Name**: `fee-admin`
     - **Environment**: `Docker`
     - **Dockerfile Path**: `./Dockerfile`
     - **Docker Context**: `.`
     - **Build Command**: (leave empty)
     - **Start Command**: (leave empty)
     - **Plan**: Free (or Starter for production)

4. **Set Environment Variables**
   - Go to your Web Service → **Environment** tab
   - Add all variables from `render-env.md`
   - **Important**: Set `DATABASE_URL` from your PostgreSQL service

5. **First Deployment**
   - After first deployment completes, open **Shell**
   - Run migrations:
     ```bash
     php artisan migrate --force
     ```
   - Create storage link:
     ```bash
     php artisan storage:link
     ```
   - Create super admin user:
     ```bash
     php artisan tinker
     >>> User::create(['name' => 'Super Admin', 'email' => 'admin@example.com', 'password' => Hash::make('secure-password'), 'school_id' => null])
     ```

6. **Verify Deployment**
   - Visit your service URL: `https://your-service-name.onrender.com`
   - Access admin panel: `https://your-service-name.onrender.com/admin`

## Troubleshooting

### Build Fails
- Check Docker logs in Render Dashboard
- Verify all PHP extensions are installed in Dockerfile
- Ensure composer.lock is committed

### Database Connection Errors
- Verify `DATABASE_URL` is set correctly
- Use Internal Database URL (not external)
- Check database service is running

### 500 Errors
- Check application logs: `tail -f storage/logs/laravel.log`
- Verify `APP_KEY` is set
- Check file permissions: `chmod -R 755 storage bootstrap/cache`

### Assets Not Loading
- Verify `npm run build` completed in build logs
- Check `public/build` directory exists
- Clear view cache: `php artisan view:clear`

## Production Checklist

- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] APP_KEY is set
- [ ] DATABASE_URL is set (Internal URL)
- [ ] DB_CONNECTION=pgsql
- [ ] SESSION_SECURE_COOKIE=true
- [ ] LOG_CHANNEL=stderr
- [ ] Migrations run successfully
- [ ] Storage link created
- [ ] Super admin user created
- [ ] Application accessible via HTTPS

