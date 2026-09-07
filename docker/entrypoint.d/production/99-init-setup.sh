#!/bin/sh
#
# First-time application provisioning (seed data + tenant setup).
#
# Schema migrations, the database-readiness wait, and the storage:link are
# handled by serversideup's Laravel automations (AUTORUN_LARAVEL_MIGRATION,
# AUTORUN_LARAVEL_STORAGE_LINK). Migrations run with --isolated, so the web,
# queue and scheduler containers never race each other during a deploy.
#
# This script only performs the *one-time* install, and only when an operator
# opts in via RUN_SETUP=1. That keeps the heavy migrate+seed+tenant work off
# every deploy and off the queue/scheduler containers, where it would race the
# web container and stall the rollout.

set -eu

# Opt-in guard: skip entirely unless this is an intentional first-time install.
if [ "${RUN_SETUP:-0}" != "1" ]; then
    exit 0
fi

if [ ! -f "$APP_BASE_DIR/artisan" ]; then
    echo "❌ Artisan file not found in $APP_BASE_DIR"
    exit 1
fi

cd "$APP_BASE_DIR"

# perscom:install is idempotent — it short-circuits when the app is already
# installed, so a repeated RUN_SETUP=1 boot is safe.
php artisan perscom:install -n

# Hand storage back to www-data: the automations and the web server run as
# www-data, but this script runs as root.
chown -R www-data:www-data "$APP_BASE_DIR/storage" "$APP_BASE_DIR/bootstrap/cache"
