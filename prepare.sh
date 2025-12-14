#!/bin/bash
# Prepare script for Wasmer deployment
# This runs before the build process

cd /app

# Create necessary directories
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache

# Set permissions
chmod -R 755 storage bootstrap/cache

echo "Preparation complete. Ready for build."
