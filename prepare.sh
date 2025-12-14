#!/bin/bash
# Prepare script for Wasmer deployment
# This runs before the build process

cd /app

# Create necessary directories
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache

# Set permissions
chmod -R 755 storage bootstrap/cache

# Fix auto-generated Dockerfile to remove PostgreSQL
# Shipit auto-generates this, so we need to modify it
if [ -f "fix-dockerfile.sh" ]; then
    chmod +x fix-dockerfile.sh
    ./fix-dockerfile.sh
fi

# Also check and fix if Dockerfile exists in Shipit output
if [ -f ".shipit/docker/Dockerfile" ]; then
    echo "Removing PostgreSQL from auto-generated Dockerfile..."
    sed -i '/pie install php\/pdo_pgsql/d' .shipit/docker/Dockerfile
    sed -i '/RUN pie install php\/pdo_pgsql/d' .shipit/docker/Dockerfile
    sed -i '/php\/pdo_pgsql/d' .shipit/docker/Dockerfile
    sed -i '/libpq-dev/d' .shipit/docker/Dockerfile
    sed -i '/postgresql-dev/d' .shipit/docker/Dockerfile
    echo "Dockerfile fixed"
fi

echo "Preparation complete. Ready for build."
