#!/usr/bin/env bash
set -Eeuo pipefail

# Usage:
#   ./scripts/release_shared_hosting.sh [project_dir]
#
# Example:
#   ./scripts/release_shared_hosting.sh /home/u123456/gosyen-disc-test

PROJECT_DIR="${1:-$(pwd)}"
cd "$PROJECT_DIR"

echo "[release] Project dir: $PROJECT_DIR"
echo "[release] Enabling maintenance mode..."
php artisan down --retry=60 || true

cleanup() {
  echo "[release] Ensuring app is up..."
  php artisan up || true
}
trap cleanup EXIT

echo "[release] Installing composer dependencies..."
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

echo "[release] Running migrations..."
php artisan migrate --force

echo "[release] Refreshing caches..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[release] Bringing app online..."
php artisan up
trap - EXIT

echo "[release] Done."

