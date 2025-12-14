#!/bin/bash
# Hook that runs after Shipit generates the Dockerfile
# This removes PostgreSQL installation attempts

DOCKERFILE="$1"

if [ -z "$DOCKERFILE" ]; then
    # Try to find the generated Dockerfile
    DOCKERFILE=$(find . -path "*/.shipit/docker/Dockerfile" -type f 2>/dev/null | head -1)
fi

if [ -f "$DOCKERFILE" ]; then
    echo "Removing PostgreSQL from generated Dockerfile: $DOCKERFILE"
    
    # Remove pdo_pgsql installation
    sed -i '/pie install php\/pdo_pgsql/d' "$DOCKERFILE"
    sed -i '/RUN pie install php\/pdo_pgsql/d' "$DOCKERFILE"
    sed -i '/php\/pdo_pgsql/d' "$DOCKERFILE"
    
    # Remove PostgreSQL packages
    sed -i '/libpq-dev/d' "$DOCKERFILE"
    sed -i '/postgresql-dev/d' "$DOCKERFILE"
    sed -i '/postgresql/d' "$DOCKERFILE"
    
    echo "PostgreSQL removed from Dockerfile"
fi

