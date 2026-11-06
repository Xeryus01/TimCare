#!/bin/bash
set -euo pipefail

HOME_DIR="$(cd "$HOME" && pwd -P)"
APP_DIR="${HOME_DIR}/timcare-dev"
PUBLIC_PARENT="${HOME_DIR}/public_html"
PUBLIC_DIR="${PUBLIC_PARENT}/timcare-dev"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd -P)"

if [ "$SCRIPT_DIR" != "$APP_DIR" ]; then
    echo "Run this script from its installed location: ${APP_DIR}" >&2
    exit 1
fi

if [ ! -f "${APP_DIR}/.env" ]; then
    echo "Missing ${APP_DIR}/.env. Create and configure it before deploying." >&2
    exit 1
fi

if [ ! -d "$PUBLIC_PARENT" ]; then
    echo "Missing cPanel document root: ${PUBLIC_PARENT}" >&2
    exit 1
fi

if [ ! -f "${APP_DIR}/public/build/manifest.json" ]; then
    echo "Missing public/build/manifest.json. Build the frontend for /timcare-dev/ first." >&2
    exit 1
fi

if ! grep -R -F -q '/timcare-dev/' "${APP_DIR}/public/build/assets"; then
    echo "The frontend build does not contain the /timcare-dev/ base path; rebuild it before deploying." >&2
    exit 1
fi

cd "$APP_DIR"

composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

if [ ! -w "${APP_DIR}/storage" ] || [ ! -w "${APP_DIR}/bootstrap/cache" ]; then
    echo "The cPanel account needs write access to storage/ and bootstrap/cache/." >&2
    exit 1
fi

php artisan config:clear
php artisan route:clear
php artisan view:clear

APP_SETTINGS="$(php -r 'require "vendor/autoload.php"; $app = require "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo config("app.url")."|".config("app.asset_url")."|".config("app.env")."|".(config("app.debug") ? "true" : "false")."|".(config("app.key") ? "set" : "missing")."|".config("session.path")."|".(config("session.secure") ? "true" : "false");')"
EXPECTED_SETTINGS="https://digistat.web.bps.go.id/timcare-dev|https://digistat.web.bps.go.id/timcare-dev|production|false|set|/timcare-dev|true"
if [ "$APP_SETTINGS" != "$EXPECTED_SETTINGS" ]; then
    echo "Incorrect production settings in .env (check APP_URL, ASSET_URL, APP_ENV, APP_DEBUG, APP_KEY, and session settings)." >&2
    exit 1
fi

if [ "${RUN_MIGRATIONS:-0}" = "1" ]; then
    php artisan migrate --force
fi

mkdir -p "${APP_DIR}/storage/app/public"
mkdir -p "$PUBLIC_DIR"

if [ -L "${PUBLIC_DIR}/storage" ]; then
    CURRENT_STORAGE="$(readlink -f "${PUBLIC_DIR}/storage")"
    EXPECTED_STORAGE="$(readlink -f "${APP_DIR}/storage/app/public")"
    if [ "$CURRENT_STORAGE" != "$EXPECTED_STORAGE" ]; then
        echo "Existing public storage symlink points elsewhere; refusing to replace it." >&2
        exit 1
    fi
elif [ -e "${PUBLIC_DIR}/storage" ]; then
    echo "A non-symlink public/storage already exists; move/backup it manually before deployment." >&2
    exit 1
else
    ln -s "${APP_DIR}/storage/app/public" "${PUBLIC_DIR}/storage"
fi

find "${APP_DIR}/public" -mindepth 1 -maxdepth 1 \
    ! -name index.php ! -name storage ! -name hot \
    -exec cp -R {} "$PUBLIC_DIR/" \;
cp "${APP_DIR}/public/index.php" "${PUBLIC_DIR}/index.php"
php artisan optimize

echo "Deployment files are ready at https://digistat.web.bps.go.id/timcare-dev"
echo "Confirm the application, login, API requests, uploads, mail, and logs in cPanel."
