#!/bin/sh
set -e

APP_DIR=/var/www/html

# create runtime dirs and files
mkdir -p "$APP_DIR/storage" "$APP_DIR/storage/logs" "$APP_DIR/bootstrap/cache" "$APP_DIR/database"
touch "$APP_DIR/storage/logs/laravel.log" "$APP_DIR/database/database.sqlite" || true

# ensure writable permissions for app directories
chmod -R u+rwX,g+rwX "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" "$APP_DIR/database" || true

# run command directly (container already runs as the correct user)
exec "$@"
