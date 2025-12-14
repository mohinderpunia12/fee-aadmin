#!/bin/bash
# Hook that runs after Shipit generates Dockerfile
# This removes PostgreSQL installation to prevent build failures

DOCKERFILE="${1:-.shipit/docker/Dockerfile}"

# Try to find the generated Dockerfile in common locations
if [ ! -f "$DOCKERFILE" ]; then
    DOCKERFILE=$(find . -path "*/.shipit/docker/Dockerfile" -type f 2>/dev/null | head -1)
fi

if [ -f "$DOCKERFILE" ]; then
    echo "Removing PostgreSQL from Dockerfile: $DOCKERFILE"
    
    # Remove pdo_pgsql installation lines
    sed -i '/pie install php\/pdo_pgsql/d' "$DOCKERFILE"
    sed -i '/RUN pie install php\/pdo_pgsql/d' "$DOCKERFILE"
    sed -i '/php\/pdo_pgsql/d' "$DOCKERFILE"
    
    # Remove PostgreSQL dev packages
    sed -i '/libpq-dev/d' "$DOCKERFILE"
    sed -i '/postgresql-dev/d' "$DOCKERFILE"
    sed -i '/postgresql/d' "$DOCKERFILE"
    
    echo "PostgreSQL removed successfully"
else
    echo "Dockerfile not found at: $DOCKERFILE"
fi

