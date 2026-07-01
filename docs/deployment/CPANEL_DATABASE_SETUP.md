# Setup Database untuk cPanel

## Status Terkini:
- **Local (Anda):** SQLite (database/database.sqlite) - SUDAH BERJALAN ✅
- **cPanel:** MySQL (digistat_timcare) - PERLU SETUP

---

## Langkah Setup di cPanel:

### 1️⃣ **SSH ke cPanel**
```bash
ssh username@your-cpanel-domain.com
cd ~/public_html/timcare  # atau folder aplikasi Anda
```

### 2️⃣ **Copy .env dari .env.cpanel**
```bash
cp .env.cpanel .env
```

### 3️⃣ **Verifikasi konfigurasi .env di cPanel**
```bash
nano .env
```

Pastikan konfigurasi MySQL ini ada:
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=digistat_timcare
DB_USERNAME=digistat_bps1900
DB_PASSWORD=bps19jaya
```

**CATATAN:** Database name, username, password bisa dilihat di cPanel → MySQL Databases

### 4️⃣ **Clear Cache**
```bash
php artisan config:clear
php artisan cache:clear
```

### 5️⃣ **Run Migrations**
```bash
php artisan migrate --force
php artisan db:seed --force
```

### 6️⃣ **Verifikasi Koneksi MySQL**
```bash
php artisan tinker
```

Lalu ketik:
```php
>>> DB::connection()->getPdo()
>>> DB::select('SELECT 1')
```

Jika OK, Anda akan melihat output positif. Exit dengan `exit`.

---

## Debugging jika ada error:

### Error: "auth_gssapi_client"
- Pastikan MySQL server running di cPanel
- Verifikasi username/password di cPanel → MySQL Databases
- Hubungi hosting support untuk credentials

### Error: "Cannot connect to MySQL"
- MySQL tidak berjalan: Hubungi hosting support
- Firewall issue: Hubungi hosting support
- Port 3306 terbuka hanya untuk localhost (normal)

### Error: "Access denied for user"
- Username/password salah → cek di cPanel MySQL Databases
- User belum di-grant untuk database tersebut → Hubungi hosting support

---

## File Development vs Production:

| File | Digunakan Untuk | Database |
|------|-----------------|----------|
| `.env` | **Local development** | SQLite (database/database.sqlite) |
| `.env.cpanel` | **cPanel production** | MySQL (digistat_timcare) |
| `.env.production` | **Production template** | MySQL (customizable) |

---

## Jika Pindah Database di Local:

Jika ingin test MySQL lokal juga (opsional):

1. Edit `.env` dan ubah ke MySQL
2. Install MySQL lokal atau gunakan Docker
3. Buat database `digistat_timcare`
4. Jalankan `php artisan migrate`

Tapi untuk development, SQLite lebih mudah. Cukup gunakan `.env` untuk local!
