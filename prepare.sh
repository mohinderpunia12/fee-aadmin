#!/bin/bash
# Prepare script for Wasmer deployment
# This runs before the build process

cd /app

# Create necessary directories
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache

# Set permissions
chmod -R 755 storage bootstrap/cache

# Note: pdo_mysql is included in PHP by default, no need to install it
# The build will use the PHP extensions that come with the PHP installation

echo "Preparation complete. Ready for build."

