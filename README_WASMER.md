# Wasmer Deployment Guide

## Issue: PostgreSQL Auto-Detection

Wasmer's Shipit tool auto-detects database drivers and tries to install PostgreSQL extensions, but:
- We're using MySQL, not PostgreSQL
- The installation fails because `phpize` is missing
- We need to prevent PostgreSQL extension installation

## Solution

The repository includes several scripts to fix this:

1. **`.shipit/prepare`** - Runs before packaging to remove PostgreSQL from Dockerfile
2. **`prepare.sh`** - General preparation script
3. **`fix-dockerfile.sh`** - Utility to fix Dockerfile
4. **`patch-dockerfile.sh`** - Alternative patching script

## Manual Fix (if scripts don't work)

If the automatic fixes don't work, you can manually modify the Dockerfile during build:

1. In Wasmer dashboard, go to your service
2. Check the build logs
3. If you see `pie install php/pdo_pgsql` failing, you need to:
   - Either install phpize (add `php-dev` package)
   - Or remove the PostgreSQL installation line

## Alternative: Use Custom Dockerfile

If Shipit continues to auto-detect PostgreSQL, you can:
1. Use a custom Dockerfile (Dockerfile.wasmer)
2. Configure Wasmer to use it instead of auto-generation
3. Ensure it only installs MySQL extensions

## Database Configuration

Make sure your `.env` or environment variables in Wasmer are set to:
```
DB_CONNECTION=mysql
DB_HOST=<your-mysql-host>
DB_PORT=3306
DB_DATABASE=<your-database>
DB_USERNAME=<your-username>
DB_PASSWORD=<your-password>
```

## Current Status

- ✅ PostgreSQL config removed from `config/database.php`
- ✅ Wasmer configuration files created
- ✅ Prepare scripts added to remove PostgreSQL
- ⚠️ Shipit may still auto-detect from composer.lock

If the issue persists, the prepare scripts should catch and fix it during the build process.

