#!/bin/sh

set -e

# Wait for the DB to be ready to accept connections
echo "⏳ Waiting for MySQL..."
until php -r "try { new PDO(
    getenv('DB_CONNECTION') . ':host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE'),
    getenv('DB_USERNAME'),
    getenv('DB_PASSWORD')
); } catch (Exception $e) { exit(1); }"; do
  echo "❌ Database not ready..."
  sleep 2
done
echo "✅ Database ready!"

if [ -f "$APP_BASE_DIR/artisan" ]; then
    # Switch to app dir
    cd "$APP_BASE_DIR"

    # Enable maintenance mode
    php artisan down

    # Migrate database
    php artisan migrate --force
    php artisan tenants:migrate --force

    # Optimize
    php artisan optimize

    # Run any operations
    php artisan operations:process --sync

    # Disable maintenance mode
    php artisan up
else
  echo "❌ Artisan file not found in $APP_BASE_DIR"
  exit 1
fi
