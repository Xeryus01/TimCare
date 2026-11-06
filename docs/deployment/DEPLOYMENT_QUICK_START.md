# Quick deployment reference

Use the canonical [cPanel subdirectory deployment guide](DEPLOYMENT_CPANEL.md)
for `https://digistat.web.bps.go.id/timcare-dev`.

The required layout is `~/timcare-dev` for the Laravel project and
`~/public_html/timcare-dev` for the public entry point and assets. Configure the
production `.env` on the server, build the frontend with `VITE_BASE=/timcare-dev/`,
then run:

```bash
cd ~/timcare-dev
bash deploy-cpanel-subdirectory.sh
```

Back up the database before applying schema changes. Run
`php artisan migrate --force` explicitly when needed. Routine deployment does
not rotate `APP_KEY` or execute seeders.
