#!/bin/bash
# Prepare script for Wasmer deployment
# This runs before Shipit generates the Dockerfile

cd /app

# Create necessary directories
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache

# Set permissions
chmod -R 755 storage bootstrap/cache

# Function to remove PostgreSQL from Dockerfile
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
        return 0
    fi
    return 1
}

# Try to fix Dockerfile if it exists (might be generated already)
fix_dockerfile ".shipit/docker/Dockerfile" || true
fix_dockerfile "out/.shipit/docker/Dockerfile" || true

# Also check any Dockerfile in current directory
if [ -f "Dockerfile" ] && grep -q "pdo_pgsql\|libpq-dev\|postgresql" "Dockerfile" 2>/dev/null; then
    fix_dockerfile "Dockerfile"
fi

echo "Preparation complete. Ready for build."
