#!/bin/bash
# Patch script to remove PostgreSQL from Shipit's auto-generated Dockerfile
# This should be run during the Docker build process

set -e

DOCKERFILE_PATH="${1}"

if [ -z "$DOCKERFILE_PATH" ]; then
    # Try common locations
    for path in ".shipit/docker/Dockerfile" "Dockerfile" ".shipit/Dockerfile"; do
        if [ -f "$path" ]; then
            DOCKERFILE_PATH="$path"
            break
        fi
    done
fi

if [ -f "$DOCKERFILE_PATH" ]; then
    echo "Patching Dockerfile at $DOCKERFILE_PATH to remove PostgreSQL..."
    
    # Remove PostgreSQL extension installation
    sed -i.bak \
        -e '/pie install php\/pdo_pgsql/d' \
        -e '/RUN pie install php\/pdo_pgsql/d' \
        -e '/php\/pdo_pgsql/d' \
        -e '/libpq-dev/d' \
        -e '/postgresql-dev/d' \
        "$DOCKERFILE_PATH"
    
    echo "Dockerfile patched successfully"
    echo "Removed lines containing: pdo_pgsql, libpq-dev, postgresql"
else
    echo "Warning: Dockerfile not found at $DOCKERFILE_PATH"
    echo "This is okay if Shipit hasn't generated it yet"
fi

