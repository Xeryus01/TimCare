# Pembaruan TimCare — September 2026

## Sebelum memperbarui

1. Backup database dan direktori `storage`.
2. Jangan menimpa `.env` produksi dengan file dari paket.
3. Rotasi `APP_KEY`, kredensial database, SMTP, SSO, dan token WhatsApp apabila paket lama pernah dibagikan kepada pihak yang tidak berwenang. Perubahan `APP_KEY` akan mengakhiri sesi aktif dan dapat memengaruhi data terenkripsi; lakukan sesuai prosedur operasional.

## Langkah pembaruan

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Migrasi `2026_09_09_000001_move_sensitive_uploads_to_private_storage` memindahkan lampiran tiket dan nota dinas lama dari penyimpanan publik ke penyimpanan privat. File tersebut selanjutnya hanya disajikan melalui route yang memeriksa autentikasi dan otorisasi.

## Instalasi baru

1. Salin `.env.example` menjadi `.env`.
2. Isi konfigurasi database dan integrasi dengan nilai milik lingkungan tujuan.
3. Jalankan `php artisan key:generate`.
4. Jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

## Verifikasi

```bash
php artisan test
php artisan route:list
```

Pastikan pengguna biasa tidak dapat:

- menghapus aset;
- membuka tiket, reservasi, nota dinas, atau lampiran milik pengguna lain;
- membaca catatan internal;
- menambahkan jadwal piket atau reservasi pada rentang yang sudah penuh.