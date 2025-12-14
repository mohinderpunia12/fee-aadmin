#!/bin/bash
# Prepare script for Wasmer deployment
# This runs before the build process and modifies Shipit's auto-generated Dockerfile

cd /app

# Create necessary directories
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache

# Set permissions
chmod -R 755 storage bootstrap/cache

# If Shipit generates a Dockerfile, remove PostgreSQL extension installation
if [ -f ".shipit/docker/Dockerfile" ]; then
    echo "Modifying auto-generated Dockerfile to remove PostgreSQL..."
    # Remove the line that installs pdo_pgsql
    sed -i '/pie install php\/pdo_pgsql/d' .shipit/docker/Dockerfile
    # Also remove any PostgreSQL-related package installations
    sed -i '/libpq-dev/d' .shipit/docker/Dockerfile
    sed -i '/postgresql/d' .shipit/docker/Dockerfile
    echo "Dockerfile modified - PostgreSQL extensions removed"
fi

echo "Preparation complete. Ready for build."
