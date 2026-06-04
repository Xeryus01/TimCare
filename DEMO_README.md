# 🎯 DEMO README - TimCare ITSM Dashboard

Panduan lengkap untuk demo sistem TimCare ITSM Dashboard dari awal hingga setiap fitur dan fungsinya.

---

## 📋 Daftar Isi

1. [Setup Awal](#setup-awal)
2. [Login & Dashboard](#login--dashboard)
3. [Demo Fitur](#demo-fitur)
   - [Ticket Management](#1-ticket-management)
   - [Asset Management](#2-asset-management)
   - [Room Reservations](#3-room-reservations)
   - [Piket Schedule](#4-piket-schedule)
   - [Notifications](#5-notifications)
   - [User Management](#6-user-management)
4. [API Testing](#api-testing)
5. [Troubleshooting](#troubleshooting)

---

## 🚀 Setup Awal

### Langkah 1: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

**Edit `.env` dengan konfigurasi:**

```env
# Basic Config
APP_NAME=TimCare
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=timcare_db
DB_USERNAME=timcare_user
DB_PASSWORD=your_password

# Mail Configuration (untuk Email notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@timcare.local
MAIL_FROM_NAME="TimCare System"

# WhatsApp Configuration (Fonnte)
WHATSAPP_ENABLED=true
WHATSAPP_FONNTE_KEY=your_key_from_fonnte_dashboard

# Queue Configuration
QUEUE_CONNECTION=database
```

### Langkah 2: Database Setup

```bash
# Jalankan migration
php artisan migrate

# Seed database dengan data demo
php artisan db:seed
```

**Data Demo yang dibuat:**
- 1 Admin user: `admin@example.com` / `password`
- 5 Technician users
- 10 Sample tickets dengan berbagai status
- 15 Sample assets dengan berbagai tipe
- 5 Reservasi ruang meeting

### Langkah 3: Compile Assets

```bash
# Build CSS dan JavaScript
npm run build

# Atau untuk development dengan hot reload
npm run dev
```

### Langkah 4: Start Application

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: (Optional) Start queue worker untuk background jobs
php artisan queue:work

# Akses: http://localhost:8000
```

---

## 🔐 Login & Dashboard

### Test Accounts

```
┌─────────────────────────────────────────────────┐
│          DEFAULT TEST ACCOUNTS                  │
├─────────────────────────────────────────────────┤
│ Admin Role:                                     │
│   Email: admin@example.com                      │
│   Password: password                            │
│                                                 │
│ Technician Role:                                │
│   Email: teknisi@example.com                    │
│   Password: password                            │
│                                                 │
│ User Role:                                      │
│   Email: user@example.com                       │
│   Password: password                            │
└─────────────────────────────────────────────────┘
```

### Login Flow

1. **Buka** http://localhost:8000
2. **Klik** tombol "Login"
3. **Masukkan** email dan password
4. **Klik** "Sign In"
5. **Redirect otomatis** ke `/dashboard`

### Dashboard Overview

**URL:** `http://localhost:8000/dashboard`

**Konten Dashboard:**

```
┌────────────────────────────────────────────────────────────────┐
│                   📊 TIMCARE DASHBOARD                         │
├────────────────────────────────────────────────────────────────┤
│                                                                │
│  Quick Stats (Top Row):                                       │
│  ┌──────────────┬──────────────┬──────────────┬──────────────┐
│  │ Total Tickets│  Total Assets│ Total Rooms  │ This Week    │
│  │      125     │      200     │      8       │  Reserv: 45  │
│  └──────────────┴──────────────┴──────────────┴──────────────┘
│                                                                │
│  Charts (Bottom Row):                                         │
│  ┌──────────────────────┬──────────────────────────────────┐
│  │ Tickets by Status    │ Assets by Type                   │
│  │ (Bar Chart)          │ (Pie Chart)                      │
│  └──────────────────────┴──────────────────────────────────┘
│                                                                │
│  Recent Activities:                                           │
│  - Ticket #123 created by Admin                              │
│  - Asset "Printer-A1" maintenance started                    │
│  - Room reservation approved                                 │
└────────────────────────────────────────────────────────────────┘
```

---

## 📱 Demo Fitur

### 1. TICKET MANAGEMENT

**Access:** `http://localhost:8000/tickets`

#### 1.1 Lihat Daftar Tickets

**Halaman:** `/tickets` (GET)

**Apa yang terlihat:**
- Tabel berisi semua tickets
- Filter: Status, Priority, Category
- Kolom: ID, Title, Status, Priority, Category, Assignee, Created Date

**Demo Data:**
```
ID │ Title                          │ Status   │ Priority │ Category
───┼────────────────────────────────┼──────────┼──────────┼──────────
1  │ Printer tidak bisa print       │ RESOLVED │ High     │ Hardware
2  │ Server down                    │ OPEN     │ Critical │ Network
3  │ Email tidak bisa connect       │ IN_PROGRESS│ Medium  │ Software
4  │ Monitor rusak                  │ CLOSED   │ Low      │ Hardware
```

#### 1.2 Buat Ticket Baru

**Langkah:**

1. Klik tombol **"+ New Ticket"** (kanan atas)
2. Isi form:

```
┌──────────────────────────────────────┐
│ CREATE NEW TICKET                    │
├──────────────────────────────────────┤
│                                      │
│ Title *                              │
│ [Internet connection is slow]        │
│                                      │
│ Description *                        │
│ [Internet speed dropped from 100Mbps │
│  to 10Mbps. Affecting all users.]    │
│                                      │
│ Category *                           │
│ [Select: Network ▼]                  │
│                                      │
│ Priority *                           │
│ [Select: High ▼]                     │
│                                      │
│ Asset (Optional)                     │
│ [Select: Router-Main ▼]              │
│                                      │
│ [Save]  [Cancel]                     │
└──────────────────────────────────────┘
```

3. Klik **"Save"**
4. Sistem akan membuat ticket dan redirect ke detail view

**Response:**
```
✅ Ticket created successfully!
   Ticket #128 - Internet connection is slow
```

#### 1.3 Lihat Detail Ticket

**Langkah:**

1. Dari daftar tickets, klik pada judul ticket
2. **URL:** `/tickets/{id}`

**Informasi yang ditampilkan:**

```
┌────────────────────────────────────────────┐
│ TICKET #128 DETAILS                        │
├────────────────────────────────────────────┤
│                                            │
│ Title: Internet connection is slow         │
│ Status: OPEN [🔴]                          │
│ Priority: High                             │
│ Category: Network                          │
│ Created: 2024-06-03 10:30:00               │
│ Updated: 2024-06-03 10:30:00               │
│ Assigned To: Teknisi Rahman                │
│ Asset: Router-Main                         │
│                                            │
│ ─────────────────────────────────────────  │
│ Description:                               │
│ Internet speed dropped from 100Mbps        │
│ to 10Mbps. Affecting all users.            │
│                                            │
│ ─────────────────────────────────────────  │
│ [Edit] [Change Status ▼] [Add Comment]    │
│                                            │
└────────────────────────────────────────────┘
```

#### 1.4 Update Status Ticket

**Langkah:**

1. Di detail ticket, klik **"Change Status"** dropdown
2. Pilih status baru:

```
Status Workflow:
OPEN 
  ↓ [Click to change]
IN_PROGRESS
  ↓ [Click to change]
RESOLVED
  ↓ [Click to change]
CLOSED
```

3. Sistem update status secara real-time

**Contoh Flow:**
```
1. Ticket dibuat → Status: OPEN
2. Teknisi mulai kerjakan → IN_PROGRESS
3. Masalah sudah diperbaiki → RESOLVED
4. User confirm & feedback → CLOSED
```

#### 1.5 Add Comments & Attachments

**Comment:**

1. Scroll ke section "Comments"
2. Isi text area
3. Klik **"Post Comment"**

```
┌─────────────────────────────────────────┐
│ COMMENTS                                │
├─────────────────────────────────────────┤
│                                         │
│ Comment from Teknisi Rahman:            │
│ "Sudah cek router, perlu restart."      │
│ 2024-06-03 11:00:00                     │
│                                         │
│ Comment from Admin:                     │
│ "OK, lakukan restart sekarang."         │
│ 2024-06-03 11:15:00                     │
│                                         │
│ ─────────────────────────────────────   │
│ Add Comment:                             │
│ [Textarea untuk comment]                 │
│ [Post Comment]                           │
│                                         │
└─────────────────────────────────────────┘
```

**Attachment:**

1. Scroll ke "Attachments"
2. Klik **"Upload File"**
3. Pilih file (max 10MB)
4. File di-upload dan tersimpan

```
Supported: PDF, JPG, PNG, DOC, XLS
Example: screenshot-router-error.png (5MB)
```

#### 1.6 Edit Ticket

1. Klik tombol **"Edit"** pada detail ticket
2. Modify fields yang diperlukan
3. Klik **"Update"**

---

### 2. ASSET MANAGEMENT

**Access:** `http://localhost:8000/assets`

#### 2.1 Lihat Daftar Assets

**Halaman:** `/assets` (GET)

**Struktur Tabel:**

```
ID │ Name              │ Type      │ Status      │ Location │ Holder
───┼──────────────────┼───────────┼─────────────┼──────────┼──────────
1  │ Computer-001     │ Desktop   │ ACTIVE      │ Office 1 │ Budi
2  │ Printer-A1       │ Printer   │ MAINTENANCE │ Office 2 │ -
3  │ Router-Main      │ Network   │ ACTIVE      │ Server   │ Admin
4  │ Monitor-A2       │ Monitor   │ BROKEN      │ Store    │ -
```

**Filter tersedia:**
- Status: ACTIVE, MAINTENANCE, BROKEN
- Type: Desktop, Laptop, Printer, Monitor, Server, Network, etc.
- Location: Pilih lokasi

#### 2.2 Buat Asset Baru

**Langkah:**

1. Klik **"+ New Asset"**
2. Isi form:

```
┌──────────────────────────────────────┐
│ CREATE NEW ASSET                     │
├──────────────────────────────────────┤
│                                      │
│ Name *                               │
│ [Laptop-Dell-025]                    │
│                                      │
│ Type *                               │
│ [Select: Laptop ▼]                   │
│                                      │
│ Serial Number                        │
│ [DELL-12345678]                      │
│                                      │
│ Location *                           │
│ [Office 3]                           │
│                                      │
│ Purchase Date                        │
│ [2024-01-15]                         │
│                                      │
│ Status *                             │
│ [ACTIVE]                             │
│                                      │
│ [Save]  [Cancel]                     │
└──────────────────────────────────────┘
```

3. Klik **"Save"**

#### 2.3 Asset Status Management

**Tiga Status Utama:**

| Status | Icon | Meaning |
|--------|------|---------|
| ACTIVE | 🟢 | Working properly |
| MAINTENANCE | 🟡 | Under maintenance |
| BROKEN | 🔴 | Not operational |

**Change Status:**

1. Di detail asset, klik **"Change Status"** button
2. Pilih status baru
3. Sistem update otomatis

#### 2.4 Change Asset Holder (Admin Only)

**Akses:** Admin users saja

**Langkah:**

1. Di detail asset, klik **"Change Holder"**
2. Pilih user baru atau kosongkan

```
Current Holder: Budi
Change to:
[ ]  Unassigned
[Rp] Roni Prayogo
[Sh] Shinta Handayani
[Wr] Wayan Ristanto
```

3. Klik **"Update"**
4. History tercatat di log system

#### 2.5 Add Maintenance Record (Admin Only)

**Langkah:**

1. Di detail asset, klik **"+ Add Maintenance"**
2. Isi form:

```
┌──────────────────────────────────────┐
│ MAINTENANCE RECORD                   │
├──────────────────────────────────────┤
│                                      │
│ Type *                               │
│ [Repair ▼]                           │
│                                      │
│ Description *                        │
│ [Replaced keyboard]                  │
│                                      │
│ Cost                                 │
│ [Rp 500,000]                         │
│                                      │
│ Date                                 │
│ [2024-06-03]                         │
│                                      │
│ [Save]  [Cancel]                     │
└──────────────────────────────────────┘
```

3. Maintenance record disimpan dengan tanggal dan biaya

#### 2.6 Import/Export Assets

**Export Assets (CSV/XLS):**

1. Di halaman assets, klik **"Export"** button
2. Pilih format: CSV atau Excel
3. File otomatis di-download

**Import Assets (CSV/XLS):**

1. Klik **"Download Template"** untuk mendapat format yang benar
2. Edit file template dengan data assets
3. Klik **"Import"** button
4. Upload file
5. System validate dan import

**Template Format:**

```csv
name,type,serial_number,location,purchase_date,status
Laptop-001,Laptop,DELL-12345,Office 1,2024-01-15,ACTIVE
Printer-A1,Printer,HP-67890,Office 2,2024-02-20,MAINTENANCE
```

---

### 3. ROOM RESERVATIONS

**Access:** `http://localhost:8000/reservations`

#### 3.1 Lihat Daftar Reservasi

**Halaman:** `/reservations` (GET)

```
ID │ Room      │ Date       │ Time        │ Organizer      │ Status
───┼───────────┼────────────┼─────────────┼────────────────┼─────────
1  │ Meeting-1 │ 2024-06-05 │ 09:00-11:00 │ Admin          │ APPROVED
2  │ Meeting-2 │ 2024-06-05 │ 14:00-15:00 │ Budi           │ PENDING
3  │ Meeting-3 │ 2024-06-04 │ 10:00-12:00 │ Roni Prayogo   │ APPROVED
```

**Filter:**
- Status: PENDING, APPROVED, REJECTED
- Room: Meeting-1, Meeting-2, Meeting-3
- Date range

#### 3.2 Buat Reservasi Baru

**Langkah:**

1. Klik **"+ New Reservation"**
2. Isi form:

```
┌──────────────────────────────────────┐
│ NEW RESERVATION                      │
├──────────────────────────────────────┤
│                                      │
│ Room *                               │
│ [Select: Meeting-1 ▼]                │
│                                      │
│ Reservation Date *                   │
│ [2024-06-10]                         │
│                                      │
│ Start Time *                         │
│ [14:00]                              │
│                                      │
│ End Time *                           │
│ [16:00]                              │
│                                      │
│ Title *                              │
│ [Project Planning Meeting]           │
│                                      │
│ Description                          │
│ [Diskusi progress project...]        │
│                                      │
│ Attendees                            │
│ [Admin, Budi, Roni Prayogo]          │
│                                      │
│ [Save]  [Cancel]                     │
└──────────────────────────────────────┘
```

3. Klik **"Save"**

**Validasi Sistem:**
- ✅ Check konflik jadwal ruangan
- ✅ Check kapasitas ruangan
- ✅ Validasi waktu (end > start)

#### 3.3 Reservation Status Workflow

```
PENDING (Menunggu Persetujuan Admin)
    ↓
    ├→ APPROVED (Reservasi dikonfirmasi)
    │   ↓
    │   COMPLETED (Meeting selesai)
    │
    └→ REJECTED (Admin menolak)
```

#### 3.4 View Detail Reservasi

**URL:** `/reservations/{id}`

**Informasi:**

```
┌────────────────────────────────────────┐
│ RESERVATION #2 - Project Planning      │
├────────────────────────────────────────┤
│                                        │
│ Room: Meeting-1                        │
│ Date: 2024-06-10                       │
│ Time: 14:00 - 16:00 (2 hours)          │
│ Organizer: Admin                       │
│ Status: PENDING 🟡                     │
│                                        │
│ Description:                           │
│ Diskusi progress project Q2 2024       │
│                                        │
│ Attendees (5):                         │
│ - Admin                                │
│ - Budi (budi@example.com)              │
│ - Roni Prayogo                         │
│ - Shinta Handayani                     │
│ - Wayan Ristanto                       │
│                                        │
│ [Edit] [Approve] [Reject] [Print Nota] │
└────────────────────────────────────────┘
```

#### 3.5 Print Nota Dinas (Admin)

**Fitur:** Generate formal meeting note (Nota Dinas)

**Langkah:**

1. Di detail reservasi, klik **"Print Nota Dinas"**
2. Pop-up browser print dialog
3. Format A4 siap cetak

**Isi Nota Dinas:**
- Header dengan logo
- Detail ruangan & waktu
- Daftar peserta
- Tanda tangan digital (jika tersedia)

---

### 4. PIKET SCHEDULE

**Access:** `http://localhost:8000/admin/piket` (Admin Only)

#### 4.1 Lihat Piket Schedule

**Halaman:** `/admin/piket` (GET)

**Format Tampilan:**

```
┌─────────────────────────────────────────────────┐
│ PIKET SCHEDULE MANAGEMENT                       │
├─────────────────────────────────────────────────┤
│                                                 │
│ Week of 2024-06-03 (Monday)                    │
│                                                 │
│ Day      │ Name              │ Phone     │ Edit │
│──────────┼──────────────────┼───────────┼──────│
│ Monday   │ Roni Prayogo     │ 08xxxxxxx │ ✎   │
│ Tuesday  │ Shinta Handayani │ 08xxxxxxx │ ✎   │
│ Wednesday│ Wayan Ristanto   │ 08xxxxxxx │ ✎   │
│ Thursday │ Budi             │ 08xxxxxxx │ ✎   │
│ Friday   │ Admin            │ 08xxxxxxx │ ✎   │
│ Saturday │ Roni Prayogo     │ 08xxxxxxx │ ✎   │
│ Sunday   │ -                │ -         │ ✎   │
│                                                 │
│ [← Previous Week] [Next Week →]                │
│ [Create New Schedule] [Edit] [Delete]          │
└─────────────────────────────────────────────────┘
```

#### 4.2 Buat Piket Schedule Baru

**Langkah:**

1. Klik **"Create New Schedule"**
2. Pilih minggu awal (Monday)
3. Assign untuk setiap hari

```
┌──────────────────────────────────────┐
│ CREATE PIKET SCHEDULE                │
├──────────────────────────────────────┤
│                                      │
│ Week Starting *                      │
│ [2024-06-10] (Monday)                │
│                                      │
│ Piket Assignment:                    │
│                                      │
│ Monday:                              │
│ [Select: Roni Prayogo ▼]             │
│                                      │
│ Tuesday:                             │
│ [Select: Shinta Handayani ▼]         │
│                                      │
│ Wednesday:                           │
│ [Select: Wayan Ristanto ▼]           │
│                                      │
│ Thursday:                            │
│ [Select: Budi ▼]                     │
│                                      │
│ Friday:                              │
│ [Select: Admin ▼]                    │
│                                      │
│ Saturday:                            │
│ [Select: Roni Prayogo ▼]             │
│                                      │
│ Sunday:                              │
│ [Leave Empty / Select User ▼]        │
│                                      │
│ [Save]  [Cancel]                     │
└──────────────────────────────────────┘
```

4. Klik **"Save"**

#### 4.3 Edit Piket Schedule

**Langkah:**

1. Dari daftar schedule, klik **"Edit"** icon
2. Ubah assignment untuk hari-hari tertentu
3. Klik **"Update"**

#### 4.4 View Piket untuk Teknisi

**Access:** `/piket` (Teknisi users only)

**Tampilan Baca-saja (Read-only):**

```
┌──────────────────────────────────────┐
│ JADWAL PIKET MINGGU INI              │
├──────────────────────────────────────┤
│                                      │
│ Anda piket hari: SENIN               │
│ Tanggal: 2024-06-03                  │
│                                      │
│ Jadwal Piket Lengkap:                │
│ Senin    : Roni Prayogo ✓ (Today)    │
│ Selasa   : Shinta Handayani          │
│ Rabu     : Wayan Ristanto            │
│ Kamis    : Budi                      │
│ Jumat    : Admin                     │
│ Sabtu    : Roni Prayogo              │
│ Minggu   : -                         │
│                                      │
└──────────────────────────────────────┘
```

---

### 5. NOTIFICATIONS

**Access:** `/notifications` atau icon di header

#### 5.1 Notification Types

**Email Notifications:**
- Ticket created/updated
- Asset maintenance scheduled
- Reservation approved/rejected
- Piket assignment notification

**WhatsApp Notifications:**
- Ticket priority update
- Critical system alerts
- Urgent piket reminders

**In-App Notifications:**
- Real-time updates di dashboard
- Notification bell di header

#### 5.2 Lihat Semua Notifications

**Halaman:** `/notifications` (GET)

```
┌────────────────────────────────────────┐
│ ALL NOTIFICATIONS                      │
├────────────────────────────────────────┤
│                                        │
│ [Mark All as Read]  [Clear All]        │
│                                        │
│ Recent:                                │
│ ┌────────────────────────────────────┐
│ │ 🎫 Ticket #128 assigned to you     │
│ │ "Internet connection is slow"      │
│ │ 10 minutes ago                     │
│ │ [Mark as Read]                     │
│ └────────────────────────────────────┘
│                                        │
│ ┌────────────────────────────────────┐
│ │ 🏢 Your piket schedule confirmed   │
│ │ "You are assigned for Monday"      │
│ │ 1 hour ago                         │
│ │ [Mark as Read]                     │
│ └────────────────────────────────────┘
│                                        │
│ ┌────────────────────────────────────┐
│ │ 📅 Reservation approved            │
│ │ "Meeting-1 on 2024-06-05"          │
│ │ 3 hours ago                        │
│ │ [Mark as Read]                     │
│ └────────────────────────────────────┘
│                                        │
└────────────────────────────────────────┘
```

#### 5.3 Header Notification Dropdown

**Lokasi:** Top-right corner (bell icon)

```
┌─────────────────────────────────────┐
│ 🔔 NOTIFICATIONS (5 unread)         │
├─────────────────────────────────────┤
│                                     │
│ ✓ Ticket #128 assigned to you      │
│ ✓ Your piket schedule confirmed    │
│ ✓ Reservation approved             │
│ ✓ Asset maintenance scheduled      │
│ ✓ Server alert - CPU high          │
│                                     │
│ ─────────────────────────────────   │
│ [View All] [Mark All as Read]      │
│                                     │
└─────────────────────────────────────┘
```

**Actions:**
- Click notification → Detail view
- Mark as read → Icon update
- Delete → Remove dari list

---

### 6. USER MANAGEMENT

**Access:** `/users` (Admin Only)

#### 6.1 Lihat Daftar Users

**Halaman:** `/users` (GET)

```
ID │ Name                │ Email              │ Role       │ Active │ Action
───┼─────────────────────┼────────────────────┼────────────┼────────┼────────
1  │ Admin               │ admin@example.com  │ Admin      │ ✓      │ Edit
2  │ Roni Prayogo        │ teknisi@example.com│ Teknisi    │ ✓      │ Edit
3  │ Shinta Handayani    │ shinta@example.com │ Teknisi    │ ✓      │ Edit
4  │ Budi                │ user@example.com   │ User       │ ✓      │ Edit
```

**Filter:**
- Role: Admin, Teknisi, User
- Status: Active, Inactive

#### 6.2 Buat User Baru

**Langkah:**

1. Klik **"+ New User"**
2. Isi form:

```
┌──────────────────────────────────────┐
│ CREATE NEW USER                      │
├──────────────────────────────────────┤
│                                      │
│ Full Name *                          │
│ [Wayan Ristanto]                     │
│                                      │
│ Email *                              │
│ [wayan@example.com]                  │
│                                      │
│ Phone (WhatsApp)                     │
│ [+6281234567890]                     │
│                                      │
│ Role *                               │
│ [Select: Teknisi ▼]                  │
│                                      │
│ Password *                           │
│ [••••••••••]                         │
│                                      │
│ Confirm Password *                   │
│ [••••••••••]                         │
│                                      │
│ Status                               │
│ [☑] Active                           │
│                                      │
│ [Save]  [Cancel]                     │
└──────────────────────────────────────┘
```

3. Klik **"Save"**

**System akan:**
- ✅ Create user account
- ✅ Send welcome email
- ✅ Generate temporary password
- ✅ Log activity

#### 6.3 Edit User

**Langkah:**

1. Di daftar users, klik **"Edit"**
2. Modify informasi:
   - Name
   - Email
   - Phone
   - Role
   - Status

3. Klik **"Update"**

#### 6.4 Change User Password (Admin)

**Langkah:**

1. Di detail user, klik **"Change Password"** button
2. Isi form:

```
┌──────────────────────────────────────┐
│ CHANGE PASSWORD                      │
├──────────────────────────────────────┤
│                                      │
│ New Password *                       │
│ [••••••••••]                         │
│                                      │
│ Confirm Password *                   │
│ [••••••••••]                         │
│                                      │
│ [Update]  [Cancel]                   │
└──────────────────────────────────────┘
```

3. Klik **"Update"**

#### 6.5 User Roles & Permissions

**Available Roles:**

```
┌─────────────┬────────────────────────────────────────┐
│ Role        │ Permissions                            │
├─────────────┼────────────────────────────────────────┤
│ Admin       │ - View/Edit/Delete all resources      │
│             │ - Manage users                         │
│             │ - Manage piket schedule                │
│             │ - View system logs                     │
│             │ - Export/Import data                   │
│             │ - Configure email & WhatsApp           │
│             │                                        │
│ Teknisi     │ - View/Create tickets                 │
│ (Technician)│ - Assign to own tickets               │
│             │ - View/Edit assets                     │
│             │ - View piket schedule                  │
│             │ - Receive notifications               │
│             │                                        │
│ User        │ - Create tickets                      │
│             │ - View own tickets                     │
│             │ - View assets                         │
│             │ - Create reservations                 │
│             │                                        │
└─────────────┴────────────────────────────────────────┘
```

---

## 🔌 API TESTING

Semua endpoint API tersedia untuk integration dengan external apps.

### API Authentication

**Sanctum Token-based:**

```bash
# Get token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'

# Response:
{
  "token": "abc123xyz789...",
  "user": {
    "id": 1,
    "email": "admin@example.com",
    "name": "Admin"
  }
}
```

**Use token in requests:**

```bash
curl -H "Authorization: Bearer abc123xyz789..." \
     http://localhost:8000/api/tickets
```

### API Endpoints

#### Tickets API

**Get All Tickets:**
```bash
GET /api/tickets
Headers: Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Printer tidak bisa print",
      "description": "...",
      "status": "RESOLVED",
      "priority": "High",
      "category": "Hardware",
      "created_at": "2024-06-03T10:30:00Z"
    }
  ],
  "meta": {
    "total": 125,
    "per_page": 15,
    "current_page": 1
  }
}
```

**Create Ticket:**
```bash
POST /api/tickets
Headers: Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Network issue",
  "description": "Internet down di lantai 2",
  "category": "Network",
  "priority": "High",
  "asset_id": 3
}
```

**Update Ticket Status:**
```bash
PATCH /api/tickets/{id}
Headers: Authorization: Bearer {token}

{
  "status": "IN_PROGRESS"
}
```

**Get Ticket Details:**
```bash
GET /api/tickets/{id}
Headers: Authorization: Bearer {token}
```

#### Assets API

**Get All Assets:**
```bash
GET /api/assets
Headers: Authorization: Bearer {token}
```

**Create Asset:**
```bash
POST /api/assets
Headers: Authorization: Bearer {token}

{
  "name": "Laptop-Dell-025",
  "type": "Laptop",
  "serial_number": "DELL-12345678",
  "location": "Office 3",
  "status": "ACTIVE"
}
```

**Update Asset:**
```bash
PATCH /api/assets/{id}
Headers: Authorization: Bearer {token}

{
  "status": "MAINTENANCE"
}
```

#### Reservations API

**Get All Reservations:**
```bash
GET /api/reservations
Headers: Authorization: Bearer {token}
```

**Create Reservation:**
```bash
POST /api/reservations
Headers: Authorization: Bearer {token}

{
  "room_id": 1,
  "reservation_date": "2024-06-10",
  "start_time": "14:00",
  "end_time": "16:00",
  "title": "Project Planning",
  "description": "Diskusi progress project",
  "attendees": ["admin", "budi", "roni"]
}
```

#### Dashboard API

**Get Summary:**
```bash
GET /api/dashboard/summary
Headers: Authorization: Bearer {token}

Response:
{
  "total_tickets": 125,
  "total_assets": 200,
  "total_rooms": 8,
  "reservations_this_week": 45,
  "open_tickets": 18,
  "tickets_by_status": {
    "OPEN": 18,
    "IN_PROGRESS": 12,
    "RESOLVED": 75,
    "CLOSED": 20
  }
}
```

**Get Chart Data:**
```bash
GET /api/dashboard/charts
Headers: Authorization: Bearer {token}

Response:
{
  "tickets_by_status": [...],
  "assets_by_type": [...],
  "tickets_by_priority": [...]
}
```

---

## ⚙️ Configuration Deep Dive

### Email Notifications

**Setup SMTP (Mailtrap example):**

1. Edit `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=noreply@timcare.local
MAIL_FROM_NAME="TimCare System"
MAIL_ENCRYPTION=tls
```

2. Test kirim email:
```bash
php artisan tinker

$user = App\Models\User::first();
Mail::to($user)->send(new \App\Mail\TestMail());
```

**Email Events yang trigger:**
- New ticket created
- Ticket assigned
- Ticket status changed
- Asset maintenance scheduled
- Reservation approved/rejected

### WhatsApp Notifications

**Setup Fonnte (WhatsApp Gateway):**

1. Daftar di https://fonnte.com
2. Dapatkan API Key
3. Edit `.env`:

```env
WHATSAPP_ENABLED=true
WHATSAPP_FONNTE_KEY=your_key_here
```

4. Test kirim WhatsApp:

```bash
php artisan tinker

$service = app(\App\Services\WhatsAppService::class);
$result = $service->send('62812345678', 'Test message from TimCare');
dd($result);
```

**WhatsApp Events:**
- Ticket assigned (priority: High/Critical)
- Critical system alerts
- Piket reminder (day before)

### Queue Jobs (Background Processing)

**Configure untuk background jobs:**

```env
QUEUE_CONNECTION=database
```

**Background jobs tersedia:**
- Send email notifications
- Send WhatsApp messages
- Generate reports
- Archive old tickets

**Start queue worker:**

```bash
php artisan queue:work
```

**Monitor jobs:**

```bash
php artisan queue:monitor
```

---

## 🧪 Testing Scenarios

### Skenario 1: End-to-End Ticket Workflow

**Durasi:** ~10 menit

**Flow:**

```
1. [09:00] User membuat ticket "Printer tidak bisa"
   ↓
2. [09:05] Admin melihat notifikasi
   ↓
3. [09:10] Admin assign ke Teknisi Roni
   ↓
4. [09:15] Roni receive notifikasi & mulai kerjakan
   ↓
5. [09:30] Roni update status → IN_PROGRESS
   ↓
6. [09:45] Roni add comment "Perlu ganti cartridge"
   ↓
7. [10:00] Roni upload bukti (foto cartridge baru)
   ↓
8. [10:05] Roni change status → RESOLVED
   ↓
9. [10:10] Admin verify & CLOSE ticket
   ↓
10. [10:15] User receive email confirmation
```

**Testing:**

```bash
# Step 1: Create ticket
POST /api/tickets
{
  "title": "Printer tidak bisa",
  "description": "Printer di lantai 1 tidak bisa print",
  "priority": "High"
}

# Step 3: Assign to technician
PATCH /api/tickets/{id}
{
  "assigned_to": "roni"
}

# Step 5: Update status
PATCH /api/tickets/{id}
{
  "status": "IN_PROGRESS"
}

# Step 8: Mark resolved
PATCH /api/tickets/{id}
{
  "status": "RESOLVED"
}
```

### Skenario 2: Asset Management with Maintenance

**Durasi:** ~15 menit

**Flow:**

```
1. Admin import 5 aset baru via CSV
   ↓
2. System validate & create assets
   ↓
3. Assign aset ke teknisi
   ↓
4. Schedule maintenance record
   ↓
5. Generate maintenance report
   ↓
6. Export assets dengan status maintenance
```

**Testing:**

```bash
# Import CSV
POST /assets/import
file: assets.csv

# Change status to maintenance
PATCH /api/assets/{id}
{
  "status": "MAINTENANCE"
}

# Add maintenance record
POST /admin/assets/{id}/maintenance
{
  "type": "Repair",
  "description": "Ganti hard disk",
  "cost": 500000,
  "date": "2024-06-03"
}

# Export
GET /assets/export
```

### Skenario 3: Room Reservation Workflow

**Durasi:** ~20 menit

**Flow:**

```
1. User 1 create reservasi Meeting-1 (2024-06-05, 09:00-11:00)
   ↓
2. System cek konflik jadwal (OK)
   ↓
3. Reservasi status: PENDING
   ↓
4. Admin menerima notifikasi
   ↓
5. Admin approve reservasi
   ↓
6. Status → APPROVED
   ↓
7. All attendees receive email confirmation
   ↓
8. Admin generate & print Nota Dinas
```

**Testing:**

```bash
# Create reservation
POST /api/reservations
{
  "room_id": 1,
  "reservation_date": "2024-06-05",
  "start_time": "09:00",
  "end_time": "11:00",
  "title": "Planning Meeting",
  "attendees": ["admin", "budi", "roni"]
}

# Approve
PATCH /api/reservations/{id}
{
  "status": "APPROVED"
}

# Print nota dinas
GET /reservations/{id}/nota-dinas
```

---

## 🐛 Troubleshooting

### Issue: "Database connection error"

**Solution:**

```bash
# Check database credentials di .env
cat .env | grep DB_

# Test koneksi
php artisan migrate --dry-run

# Jika error, reset database
php artisan migrate:fresh --seed
```

### Issue: "Email not sending"

**Solution:**

```bash
# Check mail config
php artisan tinker
config('mail')

# Test kirim
Mail::raw('Test', function($m) {
  $m->to('admin@example.com');
});

# Check queue
php artisan queue:work
```

### Issue: "WhatsApp notification failed"

**Solution:**

```bash
# Verify API key
env('WHATSAPP_FONNTE_KEY')

# Test API
$service = app(\App\Services\WhatsAppService::class);
$result = $service->send('62812345678', 'Test');
dd($result);

# Check phone format (harus +62...)
```

### Issue: "File upload failed"

**Solution:**

```bash
# Check storage permissions
chmod -R 775 storage/app/public

# Create symlink
php artisan storage:link

# Verify disk config
config('filesystems.disks.public')
```

### Issue: "Notifications tidak muncul"

**Solution:**

```bash
# Check queue status
php artisan queue:failed

# Restart queue worker
pkill -f "queue:work"
php artisan queue:work

# Check notification settings
SELECT * FROM notifications WHERE user_id = 1;
```

---

## 📊 Demo Data Overview

### Seeded Data

```
Users (6 total):
- Admin (1)
- Teknisi (3)
- Users (2)

Tickets (10 total):
- OPEN: 2
- IN_PROGRESS: 3
- RESOLVED: 3
- CLOSED: 2

Assets (15 total):
- ACTIVE: 12
- MAINTENANCE: 2
- BROKEN: 1

Rooms: 3
- Meeting-1 (capacity: 6)
- Meeting-2 (capacity: 4)
- Meeting-3 (capacity: 8)

Reservations (5 total):
- PENDING: 1
- APPROVED: 3
- COMPLETED: 1

Piket Schedules (2 weeks):
- Week 1: Assigned
- Week 2: Assigned
```

---

## 📞 Support & Contact

- **Documentation:** `/docs`
- **API Docs:** `/api/documentation`
- **Issue Tracker:** Check GitHub issues
- **Email:** support@timcare.local

---

## 📝 Version Info

```
Application: TimCare ITSM v1.0
Laravel: 10.x
PHP: 8.1+
Database: MySQL 5.7+
Last Updated: 2024-06-03
```

---

**Happy Testing! 🚀**

Semua fitur sudah siap didemonstrasikan. Ikuti panduan step-by-step di atas untuk demo yang komprehensif.
