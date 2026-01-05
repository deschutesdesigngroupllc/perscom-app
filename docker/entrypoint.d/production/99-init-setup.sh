#!/bin/sh

# Make sure artisan exists
if [ -f "$APP_BASE_DIR/artisan" ]; then

    # Generate the encryption keys
    php "$APP_BASE_DIR/artisan" passport:keys -n || true

    # Make sure the storage directory is owned by www-data
    chown -R www-data:www-data "$APP_BASE_DIR/storage"

    # Run the installation command
    php "$APP_BASE_DIR/artisan" perscom:install -n
else
  echo "❌ Artisan file not found in $APP_BASE_DIR"
  exit 1
fi
