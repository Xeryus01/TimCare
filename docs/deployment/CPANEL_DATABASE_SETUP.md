# Database setup for cPanel

Create a MySQL database and user in cPanel and grant the user access to the
database. Set `DB_CONNECTION=mysql`, `DB_HOST=localhost`, `DB_PORT=3306`, and
the database name, username, and password in `~/timcare-dev/.env`. Keep those
credentials out of source control and outside `public_html`.

Before deploying, make a database backup. Then, from the Laravel application
directory, apply schema updates:

```bash
cd ~/timcare-dev
php artisan config:clear
php artisan migrate --force
```

Do not run `db:seed` on a production database unless a specific seeder has been
reviewed and is intentionally required. See the
[deployment guide](DEPLOYMENT_CPANEL.md) for the application layout, sessions,
assets, and deploy script.
