#!/bin/bash
# Prepare script for Wasmer deployment
# This runs before the build process and fixes auto-generated Dockerfile

cd /app

# Create necessary directories
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache

# Set permissions
chmod -R 755 storage bootstrap/cache

# Function to remove PostgreSQL from any Dockerfile
fix_dockerfile() {
    local file="$1"
    if [ -f "$file" ]; then
        echo "Fixing Dockerfile: $file"
        # Remove pdo_pgsql installation
        sed -i '/pie install php\/pdo_pgsql/d' "$file"
        sed -i '/RUN pie install php\/pdo_pgsql/d' "$file"
        sed -i '/php\/pdo_pgsql/d' "$file"
        # Remove PostgreSQL packages
        sed -i '/libpq-dev/d' "$file"
        sed -i '/postgresql-dev/d' "$file"
        sed -i '/postgresql/d' "$file"
        echo "Fixed: $file"
    fi
}

# Fix Dockerfile in common Shipit locations
fix_dockerfile ".shipit/docker/Dockerfile"
fix_dockerfile "out/.shipit/docker/Dockerfile"

# Also fix any Dockerfile in current or parent directories
find . -maxdepth 3 -name "Dockerfile" -type f 2>/dev/null | while read dockerfile; do
    if grep -q "pdo_pgsql\|libpq-dev\|postgresql" "$dockerfile" 2>/dev/null; then
        fix_dockerfile "$dockerfile"
    fi
done

echo "Preparation complete. Ready for build."
