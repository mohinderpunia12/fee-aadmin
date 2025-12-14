#!/bin/bash
# This script fixes the auto-generated Dockerfile by Shipit
# It removes PostgreSQL installation attempts

DOCKERFILE="${1:-.shipit/docker/Dockerfile}"

if [ ! -f "$DOCKERFILE" ]; then
    # Try to find the Dockerfile
    DOCKERFILE=$(find . -path "*/.shipit/docker/Dockerfile" -type f 2>/dev/null | head -1)
fi

if [ -f "$DOCKERFILE" ]; then
    echo "Fixing Dockerfile: $DOCKERFILE"
    
    # Create backup
    cp "$DOCKERFILE" "$DOCKERFILE.bak"
    
    # Remove PostgreSQL installation
    sed -i '/pie install php\/pdo_pgsql/d' "$DOCKERFILE"
    sed -i '/RUN pie install php\/pdo_pgsql/d' "$DOCKERFILE"
    sed -i '/php\/pdo_pgsql/d' "$DOCKERFILE"
    sed -i '/libpq-dev/d' "$DOCKERFILE"
    sed -i '/postgresql-dev/d' "$DOCKERFILE"
    sed -i '/postgresql/d' "$DOCKERFILE"
    
    echo "Dockerfile fixed - PostgreSQL removed"
else
    echo "Dockerfile not found, will fix when generated"
fi

