# Render Environment Variables Configuration

This document lists the exact environment variables to set in Render Dashboard for FeeAdmin deployment.

## Required Environment Variables

### Application Configuration
```
APP_ENV=production
APP_DEBUG=false
APP_KEY=<generate-locally-using-php-artisan-key-generate--show>
APP_URL=https://your-service-name.onrender.com
```

**To generate APP_KEY:**
```bash
php artisan key:generate --show
```
Copy the output and paste it as the value for `APP_KEY`.

### Database Configuration
```
DB_CONNECTION=pgsql
DATABASE_URL=<from-render-postgres-internal-url>
```

**Important:** 
- Get `DATABASE_URL` from your Render PostgreSQL service dashboard
- Use the **Internal Database URL** (not external)
- Format: `postgresql://user:password@host:port/database`
- Render automatically provides this in the PostgreSQL service details

### Logging
```
LOG_CHANNEL=stderr
LOG_LEVEL=error
```

## Optional Environment Variables

### Session Driver
Choose one based on your needs:

**Option 1: Cookie-based (Recommended for single instance)**
```
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

**Option 2: Database-based (Recommended for multiple instances)**
```
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

**Tradeoffs:**
- **Cookie**: Faster, no database queries, but limited size (~4KB)
- **Database**: Scalable across instances, can store more data, but requires database queries

### Cache Driver
```
CACHE_DRIVER=file
```
Or if using Redis:
```
CACHE_DRIVER=redis
REDIS_URL=<from-render-redis-service>
```

### Deployment Automation
```
RUN_MIGRATIONS=true
RUN_STORAGE_LINK=true
```

Set these to `true` if you want migrations and storage link creation to run automatically on container start.

**Note:** For first deployment, you may want to set `RUN_MIGRATIONS=true`. After that, you can set it to `false` and run migrations manually via Render Shell when needed.

## How to Set in Render Dashboard

1. Go to your Web Service in Render Dashboard
2. Navigate to **Environment** tab
3. Click **Add Environment Variable**
4. Enter the **Key** and **Value** for each variable above
5. Click **Save Changes**
6. Render will automatically redeploy with new environment variables

## Verification

After setting environment variables, verify in Render Shell:
```bash
php artisan config:show
```

This will show your configuration (without sensitive values).

