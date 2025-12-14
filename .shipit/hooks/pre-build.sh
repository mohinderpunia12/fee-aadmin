#!/bin/bash
# Pre-build hook for Wasmer Shipit
# This runs before the Docker build to modify the auto-generated Dockerfile

echo "Pre-build hook: Removing PostgreSQL extension installation from Dockerfile..."

# Find and modify the auto-generated Dockerfile
DOCKERFILE=".shipit/docker/Dockerfile"

if [ -f "$DOCKERFILE" ]; then
    echo "Found Dockerfile, removing PostgreSQL extensions..."
    
    # Remove the line that installs pdo_pgsql
    sed -i '/pie install php\/pdo_pgsql/d' "$DOCKERFILE"
    sed -i '/RUN pie install php\/pdo_pgsql/d' "$DOCKERFILE"
    
    # Remove PostgreSQL package installations
    sed -i '/libpq-dev/d' "$DOCKERFILE"
    sed -i '/postgresql-dev/d' "$DOCKERFILE"
    sed -i '/postgresql/d' "$DOCKERFILE"
    
    echo "Dockerfile modified successfully"
else
    echo "Dockerfile not found yet, will be modified during build"
fi

