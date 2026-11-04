#!/usr/bin/env bash
set -euo pipefail
cd -- "$(dirname -- "$0")"
PHP_BIN="${PHP_BIN:-php}"
if [[ ! -f artisan || ! -f .env || ! -f vendor/autoload.php ]]; then
  echo 'Gagal: jalankan dari root Laravel yang memiliki artisan, .env, dan vendor/autoload.php.' >&2; exit 1
fi
if [[ "${1:-}" != "--backup-confirmed" ]]; then
  echo 'Backup database dan file aplikasi dahulu. Jalankan: bash install-monitoring-ti.sh --backup-confirmed' >&2; exit 1
fi
"$PHP_BIN" -r 'if (PHP_VERSION_ID < 80200) {fwrite(STDERR,"Butuh PHP >= 8.2\n"); exit(1);}'
"$PHP_BIN" artisan down --retry=60
# Do NOT auto bring application online when installation fails halfway.
"$PHP_BIN" artisan migrate --path=database/migrations/2026_10_19_000001_create_timcare_ti_monitoring_tables.php --force
"$PHP_BIN" artisan timcare:import-ti-reference --apply
"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan view:cache
"$PHP_BIN" artisan up
echo 'Selesai. Login Admin > Monitoring TI > Tim kerja / Objek & penanggung jawab.'
