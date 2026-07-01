# Cron Jobs Setup for cPanel - TimCare ITSM Dashboard

## Setup di cPanel

1. Login ke cPanel
2. Buka **Cron Jobs** (di Advanced section)
3. Pilih **Common Settings**: Once per minute (* * * * *)
4. Masukkan command berikut:

### Untuk Queue Worker (jika menggunakan queue untuk notifications)
```
* * * * * cd /home/username/timcare && php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

### Untuk Cache Clearing (opsional, sekali sehari)
```
0 2 * * * cd /home/username/timcare && php artisan cache:clear
```

### Untuk Log Rotation (opsional, sekali sehari)
```
0 3 * * * cd /home/username/timcare && php artisan log:clear
```

## Penjelasan Cron Jobs

### Queue Worker
- Menjalankan queue worker setiap menit
- `--sleep=3`: Tunggu 3 detik jika tidak ada job
- `--tries=3`: Coba ulang job yang gagal maksimal 3 kali
- `--timeout=90`: Timeout 90 detik per job

### Cache Clearing
- Membersihkan cache aplikasi setiap hari jam 2 pagi
- Mencegah cache menjadi terlalu besar

### Log Rotation
- Membersihkan log files setiap hari jam 3 pagi
- Mencegah folder logs menjadi terlalu besar

## Troubleshooting Cron Jobs

### Cron tidak berjalan
1. Pastikan path ke PHP benar: `/usr/local/bin/php` atau `/usr/bin/php`
2. Cek log cron di cPanel
3. Test manual: `cd /home/username/public_html/timcare && php artisan queue:work --once`

### Queue worker tidak memproses
1. Cek apakah queue connection benar di .env
2. Pastikan jobs ada di database: `php artisan queue:failed`
3. Restart queue worker jika perlu

### Memory issues
Jika cron menggunakan memory terlalu banyak, tambahkan:
```
* * * * * cd /home/username/public_html/timcare && /usr/local/bin/php -d memory_limit=512M artisan queue:work --sleep=3 --tries=3
```