#!/bin/sh
#
# First-time application provisioning (seed data + tenant setup).
#
# Schema migrations, the database-readiness wait, and the storage:link are
# handled by serversideup's Laravel automations (AUTORUN_LARAVEL_MIGRATION,
# AUTORUN_LARAVEL_STORAGE_LINK). Migrations run with --isolated, so the web,
# queue and scheduler containers never race each other during a deploy.
#
# This script runs perscom:install (seed + tenant provisioning), gated on
# RUN_SETUP=1. Set RUN_SETUP=1 *permanently on the web service only* — the
# install self-skips once the app is installed (isInstalled()), so it installs
# on first launch and no-ops on every boot after. Leaving it unset on the
# queue/scheduler containers keeps them from racing the first-launch seed.

set -eu

# Gate: only the container told to run setup (the web service) installs.
if [ "${RUN_SETUP:-0}" != "1" ]; then
    exit 0
fi

if [ ! -f "$APP_BASE_DIR/artisan" ]; then
    echo "❌ Artisan file not found in $APP_BASE_DIR"
    exit 1
fi

cd "$APP_BASE_DIR"

# Idempotent (short-circuits when installed) and --isolated (an atomic Redis
# lock), so concurrent web replicas can't double-install on first launch.
php artisan perscom:install -n --isolated

# Hand storage back to www-data: the automations and the web server run as
# www-data, but this script runs as root.
chown -R www-data:www-data "$APP_BASE_DIR/storage" "$APP_BASE_DIR/bootstrap/cache"
