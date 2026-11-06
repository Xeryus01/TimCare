# cPanel environment configuration

Configure production variables in `~/timcare-dev/.env` or in cPanel's
Application Manager. Do not copy production credentials into the repository,
`.env.example`, or `public_html`.

For the target `https://digistat.web.bps.go.id/timcare-dev`, configure at least:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://digistat.web.bps.go.id/timcare-dev
ASSET_URL=https://digistat.web.bps.go.id/timcare-dev
SESSION_PATH=/timcare-dev
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
```

Configure database, mail, SSO, WhatsApp, and optional Pusher values with the
credentials assigned to the production environment. Keep `BROADCAST_CONNECTION=log`
and `WHATSAPP_ENABLED=false` unless the corresponding services are configured.
Preserve the existing `APP_KEY` during updates; generate it only once for a new
application installation.

See [DEPLOYMENT_CPANEL.md](DEPLOYMENT_CPANEL.md) for the complete installation
and deployment procedure.
