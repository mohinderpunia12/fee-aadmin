#!/bin/bash
# Script to fix auto-generated Dockerfile by removing PostgreSQL installation
# This should be called during the build process

DOCKERFILE="${1:-.shipit/docker/Dockerfile}"

if [ -f "$DOCKERFILE" ]; then
    echo "Fixing Dockerfile: Removing PostgreSQL extension installation..."
    
    # Create a backup
    cp "$DOCKERFILE" "${DOCKERFILE}.bak"
    
    # Remove PostgreSQL-related lines
    sed -i '/pie install php\/pdo_pgsql/d' "$DOCKERFILE"
    sed -i '/RUN pie install php\/pdo_pgsql/d' "$DOCKERFILE"
    sed -i '/php\/pdo_pgsql/d' "$DOCKERFILE"
    sed -i '/libpq-dev/d' "$DOCKERFILE"
    sed -i '/postgresql-dev/d' "$DOCKERFILE"
    
    echo "Dockerfile fixed - PostgreSQL extensions removed"
    echo "Diff:"
    diff "${DOCKERFILE}.bak" "$DOCKERFILE" || true
else
    echo "Dockerfile not found at $DOCKERFILE"
fi

