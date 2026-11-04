# Panduan Deployment cPanel - TimCare ITSM Dashboard

## Struktur Folder yang Benar

### Struktur cPanel yang Aman:
```
/home/username/
├── public_html/          # Hanya folder public Laravel
│   ├── index.php
│   ├── .htaccess
│   ├── assets/
│   └── storage/          # Symlink ke ../timcare/storage/app/public
├── timcare/              # Folder aplikasi utama (di luar public_html)
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── artisan
│   ├── composer.json
│   ├── .env
│   └── ...
└── backups/              # Folder backup (opsional)
```

### Mengapa Struktur Ini Penting:
- **Keamanan**: File sensitif (config, .env) tidak accessible via web
- **Performance**: Cache files tidak di public_html
- **Best Practice**: Standar untuk shared hosting

## Persiapan di cPanel

### 1. Buat Database MySQL
1. Login ke cPanel
2. Buka **MySQL Databases**
3. Buat database baru (contoh: `timcare_db`)
4. Buat user database (contoh: `timcare_user`)
5. Berikan semua privileges ke user tersebut

### 2. Setup Struktur Folder
1. **Buat folder aplikasi** di luar public_html:
   ```bash
   mkdir ~/timcare
   ```

2. **Upload files aplikasi** ke folder `timcare`:
   - Upload semua file kecuali folder `public/`
   - Folder `public/` akan dipindah ke `public_html/`

3. **Pindah folder public** ke public_html:
   ```bash
   # Dari folder timcare
   mv public/* ~/public_html/
   mv public/.* ~/public_html/ 2>/dev/null || true
   rmdir public
   ```

4. **Update path di index.php**:
   ```php
   // public_html/index.php - update baris 24
   require __DIR__.'/../timcare/vendor/autoload.php';
   $app = require_once __DIR__.'/../timcare/bootstrap/app.php';
   ```

### 3. Konfigurasi Environment
1. Copy `.env.example` menjadi `.env` di folder `timcare`
2. Edit `.env` dengan konfigurasi berikut:

```env
APP_NAME="TimCare ITSM Dashboard"
APP_ENV=production
APP_KEY=  # Generate dengan: php artisan key:generate
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=timcare_db
DB_USERNAME=timcare_user
DB_PASSWORD=your_password

# Mail (Gmail SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=timcare@gmail.com
MAIL_PASSWORD=your_gmail_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="timcare@gmail.com"
MAIL_FROM_NAME="TimCare ITSM"

# Cache & Session untuk cPanel
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=sync
```

## Setup Aplikasi

### Via SSH/Terminal cPanel

```bash
# Masuk ke folder aplikasi
cd ~/timcare

# ⚠️ CRITICAL: Update .env ke MySQL (copy dari .env.cpanel)
cp .env.cpanel .env
# Edit dan pastikan credentials MySQL benar
nano .env

# Install dependencies
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate

# ⭐ CLEAR CONFIG CACHE (SANGAT PENTING!)
php artisan config:clear
php artisan cache:clear

# Jalankan migrations
php artisan migrate --force

# Buat symbolic link storage (dari public_html)
cd ~/public_html
ln -s ../timcare/storage/app/public storage

# Kembali ke folder aplikasi
cd ~/timcare

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Cache konfigurasi untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verifikasi database connection
php artisan tinker
# Ketik: DB::connection()->getPdo()
# Kemudian: exit
```

**⚠️ PENTING:**
1. **Jangan lupa `php artisan config:clear`** - Cache lama bisa menyebabkan error
2. **Update `.env` dengan MySQL credentials** - Jangan gunakan SQLite di production
3. **Verify database connection** - Pastikan MySQL terhubung sebelum migrations

### Atau Via File Manager cPanel

1. **Upload files**:
   - Upload semua kecuali `public/` ke `timcare/`
   - Upload isi `public/` ke `public_html/`

2. **Edit index.php** di public_html:
   ```php
   // Baris 24: update path
   require __DIR__.'/../timcare/vendor/autoload.php';
   $app = require_once __DIR__.'/../timcare/bootstrap/app.php';
   ```

3. **Buat storage symlink**:
   ```bash
   cd ~/public_html
   ln -s ../timcare/storage/app/public storage
   ```

## Konfigurasi Tambahan

### Cron Job untuk Queue (Opsional)
Jika menggunakan queue untuk notifications:

```bash
# Tambahkan cron job di cPanel
* * * * * cd /home/username/public_html/timcare && php artisan queue:work --sleep=3 --tries=3
```

### SSL Certificate
1. Install SSL certificate di cPanel
2. Pastikan `APP_URL` menggunakan `https://`

### Backup Database
Setup backup otomatis di cPanel untuk database MySQL.

## Troubleshooting

### ⚠️ ERROR: "Database file at path [...database.sqlite] does not exist"
**Penyebab:** Aplikasi masih menggunakan SQLite padahal seharusnya MySQL.

**Solusi:**
```bash
# 1. Verifikasi .env menggunakan MySQL
cat .env | grep DB_

# 2. Jika masih SQLite, update:
nano .env
# Ubah: DB_CONNECTION=mysql (bukan sqlite)

# 3. CLEAR CACHE (KRITIS!)
php artisan config:clear
php artisan cache:clear
rm bootstrap/cache/config.php 2>/dev/null

# 4. Verify koneksi
php artisan tinker
>>> DB::connection()->getPdo()
>>> exit
```

### Error 500
- Cek file `.env` ada dan benar
- Jalankan `php artisan config:clear`
- Cek permissions folder
- Lihat log: `tail -50 storage/logs/laravel.log`

### Database Connection Error
- Pastikan DB credentials benar (cek cPanel MySQL Databases)
- Cek apakah database dan user sudah dibuat
- Pastikan user punya privileges
- Test MySQL: `php -r "$c=new mysqli('localhost','user','pass','db');echo $c->connect_error?:'OK'"`

### Storage Link Error
- Jalankan `php artisan storage:link` via SSH
- Atau buat symbolic link manual di file manager: `ln -s ../timcare/storage/app/public storage`

## Performance Optimization

### Untuk cPanel Shared Hosting
```bash
# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear cache jika ada masalah
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Monitoring
- Monitor log files di `storage/logs/`
- Cek error log cPanel
- Monitor penggunaan resources

## Security Checklist
- [ ] APP_DEBUG=false
- [ ] APP_KEY sudah di-generate
- [ ] Database password kuat
- [ ] File permissions benar (755 untuk folders, 644 untuk files)
- [ ] SSL certificate terinstall
- [ ] Backup database aktif
- [ ] .env tidak accessible via web (pastikan di luar public_html)

## Support
Jika ada masalah, cek:
1. Laravel logs: `storage/logs/laravel.log`
2. cPanel error logs
3. PHP error logs