# Database Migrations dan Seeders - Dokumentasi Lengkap

## Daftar Isi
1. [Struktur Database](#struktur-database)
2. [Migrations](#migrations)
3. [Seeders](#seeders)
4. [Cara Menggunakan](#cara-menggunakan)

---

## Struktur Database

### Tabel-Tabel Utama

#### 1. **Users**
Menyimpan data pengguna sistem dengan autentikasi dan roles.

**Kolom:**
- `id` - Primary key
- `name` - Nama pengguna
- `email` - Email unik
- `email_verified_at` - Timestamp verifikasi email
- `password` - Password terenkripsi (hashed)
- `phone_number` - Nomor telepon (nullable)
- `remember_token` - Token untuk "remember me"
- `created_at`, `updated_at`

**Relasi:**
- `hasMany` Tickets (sebagai requester dan assignee)
- `hasMany` Reservations (sebagai requester dan approver)
- `hasMany` TicketComments
- `hasMany` Attachments
- `hasMany` Notifications
- `hasMany` Logs
- `hasRoles` (dari Spatie Permission)

---

#### 2. **Assets**
Inventaris peralatan IT perusahaan.

**Kolom:**
- `id` - Primary key
- `asset_code` - Kode unik aset (misal: AST-001)
- `name` - Nama aset
- `type` - Tipe (Laptop, Printer, Server, Monitor, etc)
- `brand` - Merek/Manufacturer
- `model` - Model perangkat
- `serial_number` - Nomor seri unik
- `specs` - JSON dengan spesifikasi teknis
- `location` - Lokasi fisik aset
- `holder` - PIC/pemegang aset
- `status` - Status (ACTIVE, INACTIVE, MAINTENANCE)
- `condition` - Kondisi (GOOD, FAIR, POOR)
- `purchased_at` - Tanggal pembelian
- `created_at`, `updated_at`

**Relasi:**
- `hasMany` Tickets

---

#### 3. **Tickets**
Sistem tiket perbaikan/support untuk aset dan layanan IT.

**Kolom:**
- `id` - Primary key
- `code` - Kode tiket unik (misal: TKT-2026-001)
- `requester_id` - FK ke Users (yang membuat tiket)
- `assignee_id` - FK ke Users (teknisi yang ditugaskan, nullable)
- `asset_id` - FK ke Assets (aset yang bermasalah, nullable)
- `category` - Kategori (HARDWARE_SUPPORT, SOFTWARE_SUPPORT, etc)
- `title` - Judul tiket
- `description` - Deskripsi masalah
- `priority` - Prioritas (LOW, MEDIUM, HIGH, CRITICAL)
- `status` - Status tiket (OPEN, ASSIGNED_DETECT, SOLVED_WITH_NOTES, SOLVED)
- `resolved_at` - Waktu resolusi (nullable)
- `created_at`, `updated_at`

**Relasi:**
- `belongsTo` User (requester)
- `belongsTo` User (assignee)
- `belongsTo` Asset
- `hasMany` TicketComments
- `hasMany` Attachments

---

#### 4. **Reservations**
Sistem booking ruang/resources dan Zoom meetings.

**Kolom:**
- `id` - Primary key
- `code` - Kode reservasi unik (misal: RES-2026-001)
- `requester_id` - FK ke Users (yang membuat reservasi)
- `approver_id` - FK ke Users (yang menyetujui, nullable)
- `room_name` - Nama ruangan/resource
- `purpose` - Tujuan/keperluan reservasi
- `start_time` - Waktu mulai
- `end_time` - Waktu berakhir
- `status` - Status (PENDING, APPROVED, REJECTED)
- `notes` - Catatan tambahan
- `zoom_link` - Link Zoom meeting (nullable)
- `zoom_record_link` - Link recording Zoom (nullable)
- `nota_dinas_path` - Path file nota dinas (nullable)
- `created_at`, `updated_at`

**Relasi:**
- `belongsTo` User (requester)
- `belongsTo` User (approver)

---

#### 5. **TicketComments**
Komentar/diskusi pada tiket support.

**Kolom:**
- `id` - Primary key
- `ticket_id` - FK ke Tickets (cascade delete)
- `user_id` - FK ke Users
- `message` - Isi komentar
- `is_internal` - Flag untuk internal notes (hanya terlihat teknisi)
- `created_at`, `updated_at`

**Relasi:**
- `belongsTo` Ticket
- `belongsTo` User
- `hasMany` Attachments

---

#### 6. **Attachments**
File-file yang di-upload untuk tickets atau comments.

**Kolom:**
- `id` - Primary key
- `ticket_id` - FK ke Tickets (nullable, cascade delete)
- `comment_id` - FK ke TicketComments (nullable, cascade delete)
- `uploader_id` - FK ke Users (siapa yang upload)
- `file_path` - Path file di storage
- `file_name` - Nama file asli
- `mime_type` - Tipe MIME file
- `size_bytes` - Ukuran file dalam bytes
- `created_at`, `updated_at`

**Relasi:**
- `belongsTo` Ticket
- `belongsTo` TicketComment
- `belongsTo` User (uploader)

---

#### 7. **Notifications**
Sistem notifikasi multi-channel (email, WhatsApp).

**Kolom:**
- `id` - Primary key
- `user_id` - FK ke Users (penerima notifikasi)
- `type` - Tipe notifikasi (info, warning, success, error)
- `title` - Judul notifikasi
- `message` - Isi pesan
- `action_type` - Tipe action (ticket, asset, reservation)
- `action_id` - ID dari action yang terkait
- `is_read` - Status dibaca
- `whatsapp_sent` - Apakah sudah dikirim via WhatsApp
- `whatsapp_status` - Status pengiriman WhatsApp (pending, sent, failed)
- `whatsapp_response` - Response dari API WhatsApp (JSON)
- `email_sent` - Apakah sudah dikirim via Email
- `email_status` - Status pengiriman Email (pending, sent, failed)
- `email_response` - Response dari email service (JSON)
- `created_at`, `updated_at`

**Relasi:**
- `belongsTo` User

---

#### 8. **PiketSchedules**
Jadwal piket teknisi per bulan.

**Kolom:**
- `id` - Primary key
- `month` - Bulan (1-12)
- `year` - Tahun (default 2026)
- `lantai_1` - Nama teknisi piket Lantai 1
- `lantai_2` - Nama teknisi piket Lantai 2
- `tu` - Nama teknisi piket TU
- `created_at`, `updated_at`

**Unique Constraint:** `(month, year)`

---

#### 9. **Logs**
Audit trail untuk tracking semua aktivitas sistem.

**Kolom:**
- `id` - Primary key
- `actor_id` - FK ke Users (siapa yang melakukan action)
- `entity_type` - Tipe entity (Ticket, Reservation, Asset, User, System)
- `entity_id` - ID dari entity yang diaction
- `action` - Action yang dilakukan (created, updated, deleted, assigned, etc)
- `meta` - JSON dengan metadata/detail action
- `created_at` - Timestamp

**Relasi:**
- `belongsTo` User (actor)

---

#### 10. **CodeSequences**
Tracking untuk generate kode tiket dan reservasi per hari.

**Kolom:**
- `id` - Primary key
- `date` - Tanggal (unique)
- `ticket_count` - Jumlah tiket yang dibuat hari itu
- `reservation_count` - Jumlah reservasi yang dibuat hari itu
- `created_at`, `updated_at`

**Unique Constraint:** `date`

---

#### 11. **Permission Tables** (Spatie Permission)
- `roles` - Daftar roles (Admin, Teknisi, User)
- `permissions` - Daftar permissions
- `role_has_permissions` - Mapping role ke permissions
- `model_has_roles` - Mapping users ke roles
- `model_has_permissions` - Mapping users ke permissions

---

## Migrations

### Urutan Eksekusi Migrations

Migrations akan dijalankan otomatis dalam urutan timestamp:

1. **2026_03_03_000100** - `create_assets_table` ✅
2. **2026_03_03_000200** - `create_tickets_table` ✅
3. **2026_03_03_000300** - `create_reservations_table` ✅
4. **2026_03_03_000400** - `create_ticket_comments_table` ✅
5. **2026_03_03_000500** - `create_attachments_table` ✅
6. **2026_03_03_000600** - `create_logs_table` ✅
7. **2026_03_03_051025** - `create_permission_tables` (Spatie) ✅
8. **2026_03_03_051026** - `create_personal_access_tokens_table` (Sanctum) ✅
9. **2026_03_04_000001** - `update_ticket_statuses` ✅
10. **2026_03_04_000002** - `add_resolved_at_to_tickets` ✅
11. **2026_03_04_023652** - `create_notifications_table` ✅
12. **2026_03_04_100000** - `add_phone_number_to_users_table` ✅
13. **2026_03_31_101500** - `add_zoom_link_to_reservations_table` ✅
14. **2026_04_01_004445** - `add_nota_dinas_to_reservations_table` ✅
15. **2026_04_01_023308** - `add_email_fields_to_notifications_table` ✅
16. **2026_04_08_120000** - `add_zoom_record_link_to_reservations_table` ✅
17. **2026_04_08_130000** - `create_piket_schedules_table` ✅
18. **2026_04_08_163730** - `create_code_sequences_table` ✅

**Total: 18 Migrations** ✅

---

## Seeders

### Daftar Seeders Lengkap

#### 1. **RoleSeeder** 
Membuat role dasar: Admin, Teknisi, User

**Data:**
- Admin - Full access
- Teknisi - Technical staff
- User - Regular user

---

#### 2. **PermissionSeeder**
Membuat permissions dan assign ke roles

**Permissions:**
- view tickets
- create tickets
- update tickets
- assign tickets
- comment tickets
- view assets
- manage assets
- view reservations
- create reservations
- manage reservations
- view dashboard

---

#### 3. **UserSeeder**
Membuat user akun untuk testing

**Users yang dibuat:**
- **Admin:** admin@example.com (semua akses)
- **Teknisi (4 orang):**
  - Fadil Rahman (fadil@example.com)
  - Marko Santoso (marko@example.com)
  - Eji Wijaya (eji@example.com)
  - Mesra Putri (mesra@example.com)
- **Regular Users (6 orang):**
  - Ahmad Surya
  - Siti Nurhaliza
  - Budi Hartono
  - Ratna Dewi
  - Handri Pranoto
  - Dina Kusuma
- **Test User:** test@example.com (untuk development)

**Password default:** `password` (semua user)

---

#### 4. **AssetSeeder**
Membuat 16 sample aset IT perusahaan

**Aset yang dibuat:**
- 4 Laptops (Dell, Lenovo, MacBook, HP)
- 2 Printers (HP LaserJet, Canon)
- 1 Server (Dell PowerEdge)
- 1 Network Switch (Cisco)
- 1 Network Device (Ubiquiti)
- 3 Monitors (Samsung, Dell)
- 1 Access Point (Cisco Meraki)
- 1 Projector (Epson)
- 1 UPS (APC)
- 1 External Storage (Seagate)
- 1 Keyboard & Mouse (Logitech)

---

#### 5. **TicketSeeder**
Membuat 12 sample tickets dengan berbagai status dan priority

**Tickets yang dibuat:**
- TKT-2026-001 - Printer tidak bisa mencetak (OPEN, HIGH)
- TKT-2026-002 - Konfigurasi email client (ASSIGNED_DETECT, MEDIUM)
- TKT-2026-003 - Backup server gagal (SOLVED, CRITICAL)
- TKT-2026-004 - Perpanjangan lisensi Adobe (SOLVED, MEDIUM)
- TKT-2026-005 - Monitor tidak mendeteksi HDMI (ASSIGNED_DETECT, HIGH)
- TKT-2026-006 - VPN tidak terkoneksi (OPEN, HIGH)
- TKT-2026-007 - Install Adobe Illustrator (SOLVED_WITH_NOTES, LOW)
- TKT-2026-008 - WiFi putus di lantai 3 (OPEN, MEDIUM)
- TKT-2026-009 - Password reset SSO (SOLVED, HIGH)
- TKT-2026-010 - Keyboard bermasalah (ASSIGNED_DETECT, MEDIUM)
- TKT-2026-011 - Request akses database (OPEN, MEDIUM)
- TKT-2026-012 - Laptop lambat/slow performance (SOLVED_WITH_NOTES, MEDIUM)

---

#### 6. **ReservationSeeder**
Membuat 10 sample reservasi ruang dan Zoom

**Reservasi yang dibuat:**
- RES-2026-001 - Meeting Room A (APPROVED)
- RES-2026-002 - Board Room (PENDING)
- RES-2026-003 - Training Room (APPROVED)
- RES-2026-004 - Meeting Room B (APPROVED)
- RES-2026-005 - Conference Room (PENDING)
- RES-2026-006 - Virtual Meeting Room (APPROVED)
- RES-2026-007 - Training Room Workshop (APPROVED)
- RES-2026-008 - Meeting Room A Brainstorming (APPROVED)
- RES-2026-009 - Board Room Performance Review (PENDING)
- RES-2026-010 - Meeting Room B Weekly Update (APPROVED)

---

#### 7. **TicketCommentSeeder** (NEW)
Membuat komentar/diskusi pada tickets

**Fitur:**
- Generates realistic comments dari teknisi dan users
- Mix dari public dan internal notes
- Linked to specific tickets dengan timestamps

**Jumlah:** ~13 sample comments

---

#### 8. **AttachmentSeeder** (NEW)
Membuat file attachments untuk tickets dan comments

**File types:**
- Screenshots (.png)
- PDF documents
- Excel spreadsheets (.xlsx)
- Word documents (.docx)
- Text files (.txt)
- ZIP archives

**Jumlah:** ~13 sample attachments

---

#### 9. **LogSeeder** (NEW)
Membuat audit trail logs untuk tracking aktivitas

**Log types:**
- Ticket creation, assignment, status changes
- Reservation creation dan approval
- Asset updates
- User role assignments
- System configuration changes
- Backup execution logs

**Jumlah:** 30+ sample logs

---

#### 10. **PiketScheduleSeeder**
Membuat jadwal piket untuk 12 bulan 2026

**Aturan:**
- Lantai 1: Marko Santoso
- Lantai 2: Fadil Rahman
- TU: Eji Wijaya
- Repeat untuk setiap bulan Januari - Desember 2026

---

#### 11. **CodeSequenceSeeder** (NEW)
Membuat tracking untuk code generation per hari

**Fitur:**
- Membuat entries untuk 30 hari terakhir + hari ini
- Random ticket dan reservation counts
- Untuk support generate kode unik seperti TKT-2026-001

---

#### 12. **NotificationSeeder**
Membuat sample notifications dengan berbagai tipe

**Notifications yang dibuat:**
- Admin notifications (tiket baru, urgent items)
- Technician notifications (assigned tickets, reminders)
- User notifications (approval, status updates, rejections)
- System notifications (maintenance, backups)

**Jumlah:** 15+ sample notifications

---

## Cara Menggunakan

### 1. **Running Fresh Migrations**
```bash
# Fresh migration (drop semua tables dan recreate)
php artisan migrate:fresh

# Atau migration biasa
php artisan migrate
```

### 2. **Running Database Seeding**
```bash
# Seed dengan fresh migration
php artisan migrate:fresh --seed

# Atau seed saja tanpa migrate
php artisan db:seed

# Seed class tertentu
php artisan db:seed --class=UserSeeder

# Seed dengan force (di production)
php artisan db:seed --force
```

### 3. **Testing**
```bash
# Run tests dengan fresh DB dan seeding
php artisan test --env=testing --migrate --seed

# Run specific test
php artisan test tests/Feature/TicketTest.php
```

### 4. **Development Workflow**
```bash
# Start development dengan fresh DB dan all data
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_new_table

# Create new seeder
php artisan make:seeder NewSeeder

# Register seeder di DatabaseSeeder.php
# Add: NewSeeder::class to $this->call() array
```

---

## Notes Penting

### ✅ Completed Tasks
- [x] All 18 migrations created dan properly ordered
- [x] 12 seeders dengan comprehensive test data
- [x] Relationships properly configured
- [x] Timestamps dan audit trails included
- [x] Support untuk multi-channel notifications (WhatsApp & Email)
- [x] Role-based permissions system (Spatie)
- [x] Asset inventory tracking
- [x] Ticket & Reservation management
- [x] Attachment file management
- [x] Activity logging system

### 🚀 Quick Start
```bash
# 1. Setup fresh database
php artisan migrate:fresh --seed

# 2. Test data is now ready
# 3. Login dengan credentials:
#    Email: admin@example.com
#    Password: password

# Test Users
# Email: test@example.com
# Email: fadil@example.com (Teknisi)
# Email: ahmad.surya@example.com (Regular User)
```

### 📝 Default Credentials untuk Testing
```
Email: admin@example.com
Password: password

Email: test@example.com  
Password: password

Email: fadil@example.com (Teknisi)
Password: password
```

Semua users memiliki password yang sama: **`password`**

---

## File Locations

```
database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 2026_03_03_000100_create_assets_table.php
│   ├── 2026_03_03_000200_create_tickets_table.php
│   ├── 2026_03_03_000300_create_reservations_table.php
│   ├── ... (18 total)
│
└── seeders/
    ├── DatabaseSeeder.php
    ├── RoleSeeder.php
    ├── PermissionSeeder.php
    ├── UserSeeder.php
    ├── AssetSeeder.php
    ├── TicketSeeder.php
    ├── ReservationSeeder.php
    ├── TicketCommentSeeder.php
    ├── AttachmentSeeder.php
    ├── LogSeeder.php
    ├── PiketScheduleSeeder.php
    ├── CodeSequenceSeeder.php
    └── NotificationSeeder.php
```

---

## Summary

**Total Database Components:**
- ✅ 18 Migrations (schemas)
- ✅ 12 Seeders (test data)
- ✅ 11 Tables (plus Spatie/Sanctum tables)
- ✅ ~100+ Sample Data Records
- ✅ Complete Audit Trail
- ✅ Multi-channel Notifications Support
- ✅ Role-based Access Control

Database siap untuk development, testing, dan dapat di-scale untuk production! 🎉
