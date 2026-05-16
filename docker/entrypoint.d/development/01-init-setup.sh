#!/bin/sh

set -e

# Only the primary app container runs setup; horizon/scheduler skip.
if [ "${RUN_SETUP:-0}" != "1" ]; then
    exit 0
fi

if [ ! -f "$APP_BASE_DIR/artisan" ]; then
    echo "Artisan file not found in $APP_BASE_DIR"
    exit 1
fi

cd "$APP_BASE_DIR"

if [ ! -f .env ] || [ ! -d vendor ] || [ -z "$(ls -A vendor 2>/dev/null)" ] || ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    echo "Running first-time setup via composer setup..."
    composer setup
fi
