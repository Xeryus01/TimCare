# Sistem Manajemen Jadwal Piket - Dokumentasi Lengkap

## 📋 Ringkasan Sistem

Sistem manajemen jadwal piket mingguan yang terintegrasi dengan dashboard admin TimCare. Admin dapat mengelola jadwal piket mingguan melalui menu dashboard, dan jadwal akan ditampilkan secara real-time di landing page. Sistem sekarang menggunakan jadwal mingguan dengan 3 petugas yang dipilih dari teknisi untuk setiap minggu.

## 🚀 Setup & Instalasi

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Jalankan Seeder untuk Data Awal
```bash
php artisan db:seed --class=WeeklyPiketScheduleSeeder
```

Seeder akan membuat jadwal default untuk 12 minggu ke depan dengan rotasi petugas:
- **Minggu 1**: Fadil Rahman, Marko Santoso, Eji Wijaya
- **Minggu 2**: Marko Santoso, Eji Wijaya, Mesra Putri
- Dan seterusnya dengan rotasi

## 📁 Struktur File

### Models
- **`app/Models/PiketSchedule.php`** - Model utama untuk manajemen jadwal
  - `getCurrentWeek()` - Mendapatkan jadwal minggu saat ini
  - `createDefault($weekStart)` - Membuat jadwal default untuk minggu tertentu
  - `getTechnicians()` - Daftar teknisi yang tersedia

### Controllers
- **`app/Http/Controllers/PiketScheduleController.php`** - Controller untuk semua operasi CRUD
  - `index()` - Menampilkan grid 12 minggu
  - `edit($weekStart)` - Form edit jadwal minggu tertentu
  - `update()` - Menyimpan perubahan jadwal
  - `show()` - API endpoint untuk data minggu saat ini

### Middleware
- **`app/Http/Middleware/Admin.php`** - Proteksi akses admin-only

### Views
- **`resources/views/admin/piket/index.blade.php`** - Dashboard admin jadwal piket mingguan
- **`resources/views/admin/piket/edit.blade.php`** - Form edit jadwal piket mingguan
- **`resources/views/layouts/sidebar.blade.php`** - Menu sidebar (sudah ditambahkan)

### Database
- **`database/migrations/2026_04_08_130000_create_piket_schedules_table.php`** - Schema tabel asli
- **`database/migrations/2026_05_05_041811_alter_piket_schedules_table_for_weekly_schedule.php`** - Migration untuk mengubah ke jadwal mingguan
- **`database/seeders/WeeklyPiketScheduleSeeder.php`** - Seeder data awal mingguan

## 🔐 Akses & Keamanan

### Role-Based Access Control
- **Admin Only**: Menu "Jadwal Piket" hanya muncul untuk user dengan role 'Admin'
- **Public Access**: Landing page menampilkan jadwal untuk semua user
- **API Access**: Endpoint `/api/piket/current` tersedia untuk public

### Routes yang Dilindungi
```php
GET  /admin/piket              - Admin only (role:Admin)
GET  /admin/piket/{weekStart}/edit - Admin only (role:Admin)
PUT  /admin/piket/{weekStart}      - Admin only (role:Admin)
GET  /api/piket/current        - Public access
```

## 🎨 Antarmuka Pengguna

### 1. Menu Dashboard Admin
Menu "Jadwal Piket" ditambahkan di sidebar admin dengan ikon kalender.

### 2. Halaman Index Admin
- Grid 12 minggu responsive (1 kolom mobile → 3 kolom desktop)
- Minggu saat ini diberi highlight dengan ring biru
- Menampilkan 3 petugas untuk setiap minggu
- Tombol "Edit" untuk setiap minggu

### 3. Halaman Edit
- Form dengan 3 dropdown untuk memilih teknisi
- Preview real-time perubahan
- Validasi input wajib diisi
- Tombol "Kembali" dan "Simpan Perubahan"

### 4. Landing Page
- Tampilan jadwal piket mingguan sesuai desain sebelumnya
- Data diambil dari database secara real-time
- Tombol "Atur" hanya muncul untuk admin yang login

## 💾 Skema Database

```sql
CREATE TABLE piket_schedules (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    week_start_date DATE NOT NULL,        -- Tanggal Senin minggu tersebut
    technician_1 VARCHAR(255),            -- Petugas 1
    technician_2 VARCHAR(255),            -- Petugas 2
    technician_3 VARCHAR(255),            -- Petugas 3
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_week_start_date (week_start_date)
);
```

## 🔧 Konfigurasi

### Menambah Teknisi Baru
Edit `app/Models/PiketSchedule.php`:

```php
public static function getTechnicians()
{
    return [
        'Marko',
        'Fadil',
        'Eji',
        'Mesra',
        'Nama Teknisi Baru', // Tambahkan di sini
    ];
}
```

### Mengubah Warna Teknisi
Edit `resources/views/welcome.blade.php`:

```php
$colorMap = [
    'Fadil' => ['dot' => 'bg-blue-400', 'accent' => 'from-blue-400 to-blue-500'],
    'Marko' => ['dot' => 'bg-emerald-400', 'accent' => 'from-emerald-400 to-emerald-500'],
    'Eji' => ['dot' => 'bg-purple-400', 'accent' => 'from-purple-400 to-purple-500'],
    'Mesra' => ['dot' => 'bg-rose-400', 'accent' => 'from-rose-400 to-rose-500'],
    // Tambahkan warna untuk teknisi baru
];
```

### Menambah Lokasi Piket
1. Update migration untuk kolom baru
2. Update model dengan field baru
3. Update controller dan views
4. Update welcome.blade.php

## 📊 Fitur Tambahan

### Real-time Preview
Form edit memiliki JavaScript untuk preview perubahan secara real-time.

### Responsive Design
- Mobile: Grid 1 kolom, cards stacked
- Tablet: Grid 2 kolom
- Desktop: Grid 3 kolom

### Error Handling
- Validasi input di backend
- Error messages ditampilkan di form
- Fallback untuk data yang belum ada

## 🐛 Troubleshooting

### Menu Tidak Muncul
- Pastikan user memiliki role 'Admin'
- Check database `model_has_roles` table

### Data Tidak Muncul di Landing Page
- Jalankan seeder: `php artisan db:seed --class=PiketScheduleSeeder`
- Check logs Laravel untuk error

### Form Edit Blank
- Pastikan migration sudah dijalankan
- Check route parameters

## 📈 Monitoring & Maintenance

### Mengecek Data
```bash
php artisan tinker
>>> \App\Models\PiketSchedule::count()
>>> \App\Models\PiketSchedule::where('month', date('n'))->first()
```

### Backup Data
```bash
php artisan db:seed --class=PiketScheduleSeeder
# Akan update data tanpa duplikasi
```

## 🎯 Workflow Lengkap

1. **Admin Login** → Akses dashboard
2. **Klik Menu "Jadwal Piket"** → Lihat grid 12 bulan
3. **Klik "Edit" pada bulan tertentu** → Buka form edit
4. **Pilih teknisi dari dropdown** → Preview otomatis
5. **Klik "Simpan Perubahan"** → Data tersimpan
6. **Landing page otomatis update** → User melihat jadwal terbaru

## ✅ Status Implementasi

- ✅ Migration tabel database
- ✅ Model dengan helper methods
- ✅ Controller dengan CRUD operations
- ✅ Admin middleware protection
- ✅ Menu sidebar admin
- ✅ Views admin (index & edit)
- ✅ Landing page integration
- ✅ Seeder data awal
- ✅ Real-time preview
- ✅ Responsive design
- ✅ Error handling
- ✅ Role-based access

Sistem siap digunakan! 🚀

## 📁 File yang Dibuat

### Models
- **`app/Models/PiketSchedule.php`** - Model untuk manajemen jadwal piket
  - Method: `getCurrentMonth()` - Dapatkan jadwal bulan saat ini
  - Method: `createDefault()` - Buat jadwal default untuk bulan tertentu
  - Method: `getTechnicians()` - Daftar teknisi yang tersedia

### Controllers
- **`app/Http/Controllers/PiketScheduleController.php`** - Controller untuk mengeola jadwal piket
  - `index()` - Tampilkan daftar semua jadwal piket
  - `edit($month)` - Form edit jadwal bulan tertentu
  - `update()` - Update jadwal piket
  - `show()` - API endpoint untuk mendapatkan jadwal saat ini

### Middleware
- **`app/Http/Middleware/Admin.php`** - Middleware untuk mengecek role admin

### Views
- **`resources/views/admin/piket/index.blade.php`** - Halaman list jadwal piket untuk admin
- **`resources/views/admin/piket/edit.blade.php`** - Halaman edit jadwal piket untuk admin

### Database
- **`database/migrations/2026_04_08_130000_create_piket_schedules_table.php`** - Migration tabel jadwal piket

## 🔐 Kontrol Akses

Sistem ini menggunakan role-based access control (RBAC) dengan Spatie Permission:

### Akses Admin
- **Admin** dapat membuka halaman `/admin/piket` untuk mengelola jadwal
- Route dilindungi dengan middleware `role:Admin`
- Hanya admin yang melihat tombol "Atur" di landing page

### Akses Public
- Semua user (termasuk guest) dapat melihat jadwal piket di landing page
- API endpoint `/api/piket/current` tersedia untuk siapa saja

## 🛣️ Routes

```
GET  /admin/piket              - Daftar semua jadwal piket (Admin only)
GET  /admin/piket/{month}/edit - Form edit jadwal bulan tertentu (Admin only)
PUT  /admin/piket/{month}      - Update jadwal piket (Admin only)
GET  /api/piket/current        - API: Dapatkan jadwal bulan saat ini (Public)
```

## 💾 Database Schema

```sql
CREATE TABLE piket_schedules (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    month INT NOT NULL,
    year INT DEFAULT 2026,
    lantai_1 VARCHAR(255) NULLABLE,
    lantai_2 VARCHAR(255) NULLABLE,
    tu VARCHAR(255) NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_month_year (month, year)
);
```

## 🎯 Cara Penggunaan

### Untuk Admin

1. **Masuk ke Admin Panel**
   - Login dengan akun admin
   - Buka `/admin/piket`

2. **Edit Jadwal Piket**
   - Klik tombol "Edit" pada bulan yang ingin diubah
   - Pilih teknisi untuk setiap lokasi (Lantai 1, Lantai 2, TU)
   - Lihat preview perubahan
   - Klik "Simpan Perubahan"

3. **Menambah Teknisi**
   - Edit file `app/Models/PiketSchedule.php`
   - Tambahkan nama di method `getTechnicians()`
   - Perbarui di semua tempat yang diperlukan

### Untuk User Biasa

1. Lihat jadwal piket di landing page
2. Jika admin, klik tombol "Atur" untuk mengelola jadwal

## 📊 Data yang Ditampilkan

### Di Landing Page

Jadwal piket bulan saat ini ditampilkan dalam 3 kotak:
- **Lantai 1** - Teknisi yang bertugas di Lantai 1
- **Lantai 2** - Teknisi yang bertugas di Lantai 2  
- **TU** - Teknisi yang bertugas di TU/Admin

Setiap kotak memiliki:
- Colored dot untuk identifikasi teknisi
- Nama lokasi
- Nama teknisi yang bertugas
- Hover effect untuk interaksi

## 🔧 Customization

### Mengubah Daftar Teknisi

Edit `app/Models/PiketSchedule.php`:

```php
public static function getTechnicians()
{
    return [
        'Nama Teknisi 1',
        'Nama Teknisi 2',
        'Nama Teknisi 3',
        // Tambahkan lebih banyak
    ];
}
```

### Menambah Lokasi Piket

Jika ingin menambah lokasi selain Lantai 1, Lantai 2, dan TU:

1. Update migration untuk menambah kolom baru
2. Update model dengan field baru
3. Update view untuk menampilkan lokasi baru
4. Update welcome.blade.php untuk menampilkan di landing page

## 🐛 Troubleshooting

### Jadwal tidak muncul di landing page
- Pastikan data sudah ada di database `piket_schedules`
- Check apakah `PiketSchedule::getCurrentMonth()` mengembalikan data yang benar
- Lihat logs di `storage/logs/`

### Tombol "Atur" tidak muncul
- Pastikan user sudah login sebagai admin
- Check apakah user memiliki role "admin" di database `model_has_roles`
- Verify middleware `role:Admin` sudah bekerja

### Halaman blank saat edit jadwal
- Pastikan migration sudah dijalankan
- Check apakah file view di `resources/views/admin/piket/` ada
- Periksa error di Laravel logs

## 📝 Catatan

- Sistem menggunakan `date('Y')` untuk tahun saat ini (2026)
- Data default jika jadwal tidak ada adalah Fadil, Marko, Eji
- Gunakan seeder untuk populate initial data untuk semua 12 bulan
- Role "admin" harus sudah ada di sistem sebelum user dapat mengakses

## 🔐 Keamanan

- Hanya admin yang bisa mengubah jadwal piket
- API endpoint public hanya read-only
- Semua input sudah di-validate sebelum disimpan
- CSRF protection aktif untuk semua form
