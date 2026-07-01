CPANEL / Deployment - Environment variables for TimCare SSO

Set the following environment variables for the TimCare Laravel application on your cPanel server (Application Manager -> Environment Variables) or by editing the `.env` file in the app root.

Required variables:

SSO_URL=https://sso.bps.go.id
SSO_REALM=pegawai-bps
SSO_SCOPE="openid profile-pegawai"
SSO_CLIENT_ID=11900-timcare-h9f
SSO_CLIENT_SECRET=b2a78abe-ea01-4c99-8ed9-b1255d76d4a1
SSO_REDIRECT_URI=https://digistat.web.bps.go.id/timcare/sso/callback
MANUAL_REGISTER_ENABLED=false

Notes:
- Prefer using cPanel's "Environment Variables" (Application Manager) so values are not stored in plain `.env` under web root.
- Ensure `APP_URL` matches the public URL `https://digistat.web.bps.go.id/timcare`.
- After setting variables (or updating `.env`) run these commands via SSH in the project folder:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

- If you changed `.env` but used config cache, run `php artisan config:clear` then `php artisan config:cache`.
- Ensure storage and bootstrap/cache have correct writable permissions.

If you want, I can also prepare a `.env.production` snippet or help with exact cPanel steps/screenshots if you grant access or paste cPanel UI details.
