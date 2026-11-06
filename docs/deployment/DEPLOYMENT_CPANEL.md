# Deploy TimCare to cPanel (`/timcare-dev`)

This deployment keeps Laravel's application files outside the web root and exposes
only the public entry point and built static assets. Do not use the old workflow
that moved files out of the project, copied `.env` into `public_html`, generated a
new key on every deploy, or automatically seeded the database.

## Required layout

```text
/home/CPANEL_USER/
├── timcare-dev/                 # Laravel project, including .env and vendor
│   ├── public/build/             # Vite production build
│   └── ...
└── public_html/timcare-dev/      # Web-accessible files only
    ├── index.php                 # Laravel front controller
    ├── .htaccess
    ├── build/
    └── storage -> /home/CPANEL_USER/timcare-dev/storage/app/public
```

The configured URL and Apache rewrite base are
`https://digistat.web.bps.go.id/timcare-dev`. The application folder name and
document-root subdirectory are intentionally fixed to match this setup.

## 1. Prepare the database and application files

1. In cPanel, create a MySQL database and user, and grant the user the required
   privileges.
2. Upload the Laravel project to `~/timcare-dev`. Do not upload `.env`,
   `.env.cpanel`, database dumps, `.git`, or `node_modules`. Include
   `public/build/` after completing the frontend build below.
3. Keep `~/public_html/timcare-dev` for public files only. Do not place the full
   Laravel project or its `.env` in `public_html`.
4. Configure `~/timcare-dev/.env` on the server. Keep the existing `APP_KEY`
   during subsequent deployments; generate it only once if this is a new install.

Minimum production settings:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://digistat.web.bps.go.id/timcare-dev
ASSET_URL=https://digistat.web.bps.go.id/timcare-dev
VITE_BASE=/timcare-dev/

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=CPANEL_DATABASE
DB_USERNAME=CPANEL_DATABASE_USER
DB_PASSWORD=SET_ON_SERVER

SESSION_DRIVER=database
SESSION_PATH=/timcare-dev
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
CACHE_STORE=database
QUEUE_CONNECTION=sync
BROADCAST_CONNECTION=log
WHATSAPP_ENABLED=false
```

Use the real cPanel mail settings if the application must send email. Keep
broadcasting on `log` unless Pusher credentials are configured. Never commit or
send server credentials through source control.

If `APP_KEY` has not yet been set on a new installation, generate it once from
the application directory with `php artisan key:generate`. Never run this during
routine deployments, because it invalidates existing sessions and encrypted data.

## 2. Build frontend assets

Run this in the project directory on a machine with Node.js dependencies
installed. In PowerShell:

```powershell
$env:VITE_BASE = '/timcare-dev/'
npm ci
npm run build
```

Upload the resulting `public/build/` directory to `~/timcare-dev/public/build/`.
The Vite base must include the `/timcare-dev/` prefix so Vue API requests and
links remain inside the subdirectory.

## 3. Deploy from cPanel Terminal / SSH

```bash
cd ~/timcare-dev
bash deploy-cpanel-subdirectory.sh
```

The script checks production settings and the Vite manifest, publishes public
files without copying the app or `.env`, preserves an existing correct storage
symlink, and rebuilds Laravel caches. It does **not** rotate `APP_KEY` or seed
the database.

For a first install or an intentional schema update, back up the database first,
then run migrations explicitly:

```bash
cd ~/timcare-dev
php artisan migrate --force
```

Alternatively, after making and verifying the backup, invoke the deploy script
with `RUN_MIGRATIONS=1`; it runs only `migrate --force`, never seeders:

```bash
cd ~/timcare-dev
RUN_MIGRATIONS=1 bash deploy-cpanel-subdirectory.sh
```

The cPanel account must provide the PHP version and extensions required by
`composer.json`, a writable `storage/` and `bootstrap/cache/`, and Apache
`mod_rewrite`. Ensure the document root is exactly
`~/public_html/timcare-dev`.

## 4. Verify after deployment

- Open `https://digistat.web.bps.go.id/timcare-dev` and sign in.
- Check browser developer tools for failed `/timcare-dev/build/...` and
  `/timcare-dev/api/...` requests.
- Test an asset upload/download and verify `public_html/timcare-dev/storage` is
  a symlink to `~/timcare-dev/storage/app/public`.
- Check `storage/logs/laravel.log` and the cPanel PHP/Apache error logs.
- Confirm HTTPS is active and `APP_DEBUG=false`.

## Rollback and maintenance

Take a database backup before migrations and retain the previous release files.
If deployment fails, restore the previous application/build files and the
database backup as appropriate. Do not run `db:seed` on a production database
unless a specific, reviewed seeder is intended.

For cache changes:

```bash
cd ~/timcare-dev
php artisan optimize:clear
php artisan optimize
```
