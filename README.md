# 🖥️ TimCare ITSM Dashboard

Sistem Manajemen Layanan TI (IT Service Management) enterprise-grade untuk manajemen tiket support, inventaris aset, reservasi ruangan, dan notifikasi real-time dengan interface modern dan responsive.

**Status**: ✅ **PRODUCTION READY** | **Version**: 1.0 | **Last Updated**: Juni 2026

## 🎯 Fitur Utama

### Core Features
- **✅ Manajemen Tiket Support** - CRUD lengkap dengan kategori, prioritas, status tracking (OPEN → IN_PROGRESS → RESOLVED → CLOSED), assignment ke teknisi, dan system comments
- **✅ Inventaris Aset IT** - Tracking komprehensif perangkat keras, lunak, server, printer dengan status (ACTIVE/INACTIVE) dan kondisi (GOOD/FAIR/POOR/DAMAGED)
- **✅ Reservasi Ruangan** - Booking ruangan meeting dengan datetime management, status approval (PENDING/CONFIRMED/CANCELLED), dan conflict detection
- **✅ Sistem Notifikasi Dual-Channel** - Email dan WhatsApp notifications real-time via Fonnte API dengan delivery tracking
- **✅ Role-Based Access Control (RBAC)** - 3 role siap pakai (Admin, Teknisi, User) dengan 15+ granular permissions
- **✅ Dashboard Analytics** - Statistik real-time (Total Assets, Active Assets, Total Tickets, Open Tickets) dengan latest activity feed
- **✅ Audit & Logging System** - Database logging untuk semua perubahan data dan aktivitas pengguna
- **✅ Mobile Responsive Design** - Full responsive UI dengan dark mode support

## 🛠️ Tech Stack & Requirements

### Backend Framework
- **Laravel** 10.x dengan Blade templating
- **PHP** 8.1 atau lebih tinggi
- **Composer** untuk dependency management

### Frontend & Styling
- **Tailwind CSS** 3.x untuk utility-first styling
- **Alpine.js** untuk interaktivitas ringan
- **TailAdmin Theme** untuk professional dashboard UI
- **Node.js** 16+ & NPM untuk asset bundling

### Database & Storage
- **MySQL** 5.7+ atau **MariaDB** 10.0+ (production)
- **SQLite** untuk development/testing
- **Local/Public Disk** untuk file storage

### Authentication & Authorization
- **Laravel Sanctum** untuk API authentication
- **Spatie Laravel-Permission** untuk role-based access control (RBAC)
- **Database-driven** permission management

### Notification System
- **Fonnte API** untuk WhatsApp notifications
- **Laravel Mail** untuk email notifications (SMTP compatible)
- **Database queue** untuk storing delivery status
- Support untuk dual-channel notifications

### Additional Libraries
- **Maatwebsite/Excel** untuk export functionality
- **GuzzleHTTP** untuk HTTP requests (Fonnte API)
- **Laravel Logging** untuk comprehensive logging system

## 📋 Persyaratan Sistem

### Minimum Requirements
- **PHP**: 8.1 atau lebih tinggi dengan extensions: PDO, mbstring, json, BCMath
- **MySQL**: 5.7+ atau MariaDB 10.0+
- **Composer**: 2.0+
- **Node.js**: 16+ dengan NPM 7+

### Recommended (Production)
- **PHP**: 8.2+
- **MySQL**: 8.0+
- **Apache** atau **Nginx** dengan mod_rewrite enabled
- **cPanel/WHM** (untuk shared hosting)
- **SSL Certificate** (HTTPS)
- **Minimum 2GB RAM** untuk smooth operation

### Development Environment
- **VS Code** atau code editor lainnya
- **Git** untuk version control
- **Postman** atau **Thunder Client** untuk API testing
- **DBeaver** atau **phpMyAdmin** untuk database management

## 🚀 Quick Start - Development Environment

### 1. Clone & Setup Repository
```bash
# Clone project
git clone <repository-url> timcare
cd timcare

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate Laravel Sanctum keys
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 3. Database Setup
```bash
# Configure .env dengan database credentials
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=timcare_db
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations & seeders
php artisan migrate --seed
```

### 4. WhatsApp & Email Configuration
```env
# .env - Fonnte WhatsApp API
WHATSAPP_ENABLED=true
WHATSAPP_FONNTE_URL=https://api.fonnte.com/send
WHATSAPP_FONNTE_KEY=your_fonnte_api_key

# Email Setup (untuk development)
MAIL_MAILER=log
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@timcare.local
```

Lihat [FONNTE_SETUP.md](FONNTE_SETUP.md) dan [EMAIL_SETUP.md](EMAIL_SETUP.md) untuk panduan lengkap.

### 5. Build Assets & Start Server
```bash
# Build Tailwind CSS + assets
npm run dev      # Development dengan watch mode
npm run build    # Production build

# Start Laravel development server (di terminal baru)
php artisan serve
# Akses di: http://localhost:8000
```

### 6. Default Login Credentials
```
Email: admin@example.com
Password: password
Role: Admin (full access)
```

### 7. Verify Installation
```bash
# Check database connection
php artisan migrate --dry-run

# List all routes
php artisan route:list

# Test API (dengan token)
curl -H "Accept: application/json" http://localhost:8000/api/dashboard/summary
```

## 🌐 Deployment to Production

### Pre-Deployment Checklist
- ✅ APP_DEBUG=false
- ✅ APP_ENV=production  
- ✅ Database backed up
- ✅ Environment variables configured
- ✅ SSL Certificate installed
- ✅ Cron jobs configured
- ✅ All dependencies installed

### Deployment via cPanel

#### Struktur Folder yang Benar
**PENTING:** Untuk keamanan, aplikasi Laravel HARUS dipisah dari public_html:

```
/home/username/
├── public_html/                # Hanya folder public Laravel
│   ├── index.php
│   ├── .htaccess
│   ├── css/
│   ├── js/
│   ├── images/
│   └── storage/ → symlink ke ../timcare/storage/app/public
├── timcare/                    # Aplikasi utama (di luar public_html)
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env (production)
│   └── artisan
```

#### Langkah-Langkah Deployment

**1. Prepare Database**
```bash
# Di cPanel → MySQL Databases:
# - Create database: timcare_production
# - Create user: timcare_user
# - Grant all privileges
```

**2. Upload Files**
```bash
# Upload project ke folder sementara
# Ekstrak ke ~/timcare folder
# Jangan upload langsung ke public_html
```

**3. Run Setup Script**
```bash
cd ~/timcare
bash setup-cpanel-structure.sh
```

Atau **Manual Setup**:
```bash
# Production dependencies
cd ~/timcare
composer install --no-dev --optimize-autoloader
npm run build

# Generate key & cache
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Database migration
php artisan migrate --force

# Seed initial data (opsional)
php artisan db:seed --force

# Setup storage symlink
cd ~/public_html
ln -s ../timcare/storage/app/public storage

# Set permissions
cd ~/timcare
chmod -R 755 storage bootstrap/cache
```

**4. Update .env untuk Production**
```env
APP_NAME="TimCare ITSM"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=timcare_production
DB_USERNAME=timcare_user
DB_PASSWORD=strong_password_here

# Cache & Session
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=sync

# Mail untuk production
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=admin@yourdomain.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com

# WhatsApp
WHATSAPP_ENABLED=true
WHATSAPP_FONNTE_KEY=your_api_key
```

**5. Setup .htaccess di public_html**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [QSA,L]
</IfModule>
```

**6. Configure Cron Jobs**
Di cPanel → Cron Jobs, tambahkan:
```bash
# Laravel scheduler (setiap menit)
* * * * * cd /home/username/timcare && php artisan schedule:run >> /dev/null 2>&1

# Optional: Queue worker (jika perlu background jobs)
* * * * * cd /home/username/timcare && php artisan queue:work --sleep=3 --tries=3 >> /dev/null 2>&1
```

### Automated Deployment Script
```bash
cd ~/timcare
bash deploy.sh
```

Lihat [DEPLOYMENT_CPANEL.md](DEPLOYMENT_CPANEL.md) untuk panduan detail.

## 📚 Documentation & Guides

### Core Documentation
- **[Setup Guide](SETUP_GUIDE.md)** - Panduan setup lengkap untuk development & production
- **[Deployment cPanel](DEPLOYMENT_CPANEL.md)** - Detailed deployment guide untuk cPanel hosting
- **[Quick Start](QUICK_START.md)** - Quick reference untuk setup cepat

### Feature Guides
- **[WhatsApp Setup (Fonnte)](FONNTE_SETUP.md)** - Konfigurasi Fonnte API untuk WhatsApp notifications
- **[Email Setup](EMAIL_SETUP.md)** - Konfigurasi email SMTP untuk email notifications
- **[Notification System](NOTIFICATIONS_SETUP.md)** - Panduan sistem notifikasi lengkap
- **[Cron Jobs Setup](CRON_SETUP.md)** - Konfigurasi scheduled tasks

### Technical Documentation
- **[API Documentation](docs/API.md)** - REST API endpoints & usage examples
- **[Database ERD](docs/ERD.md)** - Entity relationship diagram
- **[Database Schema](DATABASE_DOCUMENTATION.md)** - Database structure & relationships
- **[Complete Documentation](COMPLETE_DOCUMENTATION.md)** - Full system documentation

### Additional Resources
- **[System Finalization Report](SYSTEM_FINALIZATION_REPORT.md)** - Final audit & production readiness
- **[Getting Started](GETTING_STARTED.md)** - First steps guide
- **[README (Bahasa Indonesia)](README_ID.md)** - Documentation in Indonesian

## 🔧 Configuration & Features Detail

### 1. Ticket Management System
**Status Workflow:** OPEN → IN_PROGRESS → RESOLVED → CLOSED

- Create tickets dengan kategori, deskripsi detail, dan prioritas (LOW/MEDIUM/HIGH/CRITICAL)
- Assign ticket ke teknisi specific
- Track semua perubahan status dengan timestamp
- Add comments untuk komunikasi team
- Link ticket ke asset yang berkaitan
- Full audit trail untuk tracking perubahan

**API Routes:**
```
GET    /tickets                    - List all tickets
POST   /tickets                    - Create new ticket
GET    /tickets/{id}               - View ticket detail
PATCH  /tickets/{id}               - Update ticket
DELETE /tickets/{id}               - Delete ticket
POST   /tickets/{id}/comments      - Add comment
```

### 2. Asset Management System
**Status:** ACTIVE / INACTIVE  
**Condition:** GOOD / FAIR / POOR / DAMAGED

- Kelola inventaris perangkat keras (computers, printers, servers, networking equipment)
- Track lokasi dan kondisi asset
- JSON-based technical specifications storage
- Maintenance history tracking
- Unique asset codes untuk identification

**API Routes:**
```
GET    /assets                     - List all assets
POST   /assets                     - Create new asset
GET    /assets/{id}                - View asset detail
PATCH  /assets/{id}                - Update asset
DELETE /assets/{id}                - Delete asset
```

### 3. Reservation Management System
**Status:** PENDING / CONFIRMED / CANCELLED

- Book ruangan meeting dengan date & time
- Otomatis durasi calculation
- Conflict detection untuk prevent double-booking
- Organizer tracking
- Notification otomatis saat reservation diapprove

**API Routes:**
```
GET    /reservations               - List all reservations
POST   /reservations               - Create new reservation
GET    /reservations/{id}          - View detail
PATCH  /reservations/{id}          - Update reservation
DELETE /reservations/{id}          - Cancel reservation
```

### 4. Dual-Channel Notification System
**Channels:** WhatsApp + Email  
**Status Tracking:** sent / failed / delivered

Sistem notifikasi lengkap dengan tracking untuk:
- **Ticket Created**: Notifikasi saat ticket baru dibuat
- **Ticket Updated**: Notifikasi saat status berubah
- **Ticket Resolved**: Notifikasi ticket selesai
- **Reservation Approved**: Notifikasi approvement reservasi
- **Asset Created**: Notifikasi asset baru

**API Routes:**
```
GET    /api/notifications                    - List all notifications
GET    /api/notifications/unread-count       - Get unread count
GET    /api/notifications/latest-unread      - Get 5 latest unread
GET    /api/notifications/{id}               - View notification
PATCH  /api/notifications/{id}/mark-as-read - Mark as read
DELETE /api/notifications/{id}               - Delete notification
```

### 5. Role-Based Access Control (RBAC)
**Pre-configured Roles:**

| Role | Permissions |
|------|-------------|
| **Admin** | Full system access, user management, all CRUD operations |
| **Technician** | Create/update/view tickets & assets, update statuses |
| **User** | Create tickets, view own records, make reservations |

**Granular Permissions:**
- create_ticket, read_ticket, update_ticket, delete_ticket
- create_asset, read_asset, update_asset, delete_asset
- create_reservation, update_reservation, delete_reservation
- assign_ticket, close_ticket, view_logs, manage_users

### 6. Dashboard & Analytics
Real-time statistics dengan:
- Total Assets & Active Assets count
- Total Tickets & Open Tickets count
- Latest 5 tickets activity
- Color-coded metrics untuk quick status
- Quick action links untuk common operations

### 7. Security Features
- ✅ No hardcoded secrets atau API keys
- ✅ Input validation comprehensive untuk semua forms
- ✅ SQL injection prevention via Eloquent ORM
- ✅ CSRF protection di semua forms
- ✅ Password hashing dengan bcrypt
- ✅ Rate limiting untuk API endpoints
- ✅ Audit logging untuk semua data changes

## 🔒 Security Best Practices

### Development
- ✅ APP_DEBUG=false di .env
- ✅ Gunakan HTTPS di development tools
- ✅ Update dependencies regularly: `composer update`
- ✅ Use environment variables untuk sensitive data

### Production
- ✅ APP_DEBUG=false (WAJIB)
- ✅ APP_ENV=production
- ✅ SSL Certificate required (HTTPS)
- ✅ Strong database password (min 16 karakter)
- ✅ Regular database backups
- ✅ File permissions: 755 (folders), 644 (files)
- ✅ .env file NOT accessible via web
- ✅ Enable password reset token expiration
- ✅ Monitor logs untuk suspicious activities

### API Security
- ✅ Laravel Sanctum untuk token-based authentication
- ✅ Rate limiting enabled
- ✅ CORS properly configured
- ✅ Input validation comprehensive
- ✅ SQL injection prevention via Eloquent

## 📊 Backup & Maintenance

### Database Backup
```bash
# Automatic backup script
bash backup.sh

# Manual backup
mysqldump -u timcare_user -p timcare_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore backup
mysql -u timcare_user -p timcare_db < backup_file.sql
```

### Regular Maintenance
```bash
# Clear application caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check system health
php artisan tinker
>>> \DB::connection()->getPdo();  // Test DB connection
```

### Log Management
Logs tersimpan di: `storage/logs/`

```bash
# View logs
tail -f storage/logs/laravel.log

# Archive old logs
find storage/logs -name "*.log" -mtime +30 -delete
```

## 🐛 Troubleshooting

### Common Issues & Solutions

#### 1. "Unauthorized" saat API call
- ✅ Check API token validity
- ✅ Verify Sanctum middleware configuration
- ✅ Clear token cache: `php artisan cache:clear`

#### 2. Database connection error
```bash
# Test connection
php artisan tinker
>>> \DB::connection()->getPdo();
```

#### 3. File permission denied
```bash
# Fix permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 4. WhatsApp notifications not sending
- ✅ Check Fonnte API key di .env
- ✅ Verify user phone number format (+6281234567890)
- ✅ Check Fonnte dashboard untuk delivery status
- ✅ Review logs: `storage/logs/laravel.log`

#### 5. Email not sending
- ✅ Verify SMTP settings di .env
- ✅ Test connection: `php artisan mail:send`
- ✅ Check firewall/port 587 accessibility
- ✅ Review mail logs untuk error details

#### 6. Assets not building
```bash
# Rebuild assets
rm -rf node_modules package-lock.json
npm install
npm run build
```

## 📞 Support & Help

### Resources
- Review logs di `storage/logs/`
- Check cPanel error logs
- Verify PHP version compatibility
- Run system diagnostics

### Common Commands
```bash
# Test environment setup
php artisan migrate --dry-run

# List all routes
php artisan route:list

# Check Eloquent models
php artisan tinker

# Generate app documentation
php artisan about

# Database status
php artisan db:show
```

### Getting Help
1. Check relevant documentation file
2. Review system logs
3. Verify .env configuration
4. Test database connection
5. Check Laravel error logs

## � Version History

| Version | Release Date | Status | Notes |
|---------|-------------|--------|-------|
| 1.0 | Juni 2026 | ✅ Production Ready | Final stable release dengan semua fitur lengkap |
| 0.9 | April 2026 | ✅ Completed | Audit selesai, bugs fixed, system finalized |
| 0.8 | Maret 2026 | ✅ Completed | Email notifications added, notification system enhanced |
| 0.5 | Februari 2026 | ✅ Completed | WhatsApp integration dengan Fonnte, core features complete |

## 📄 License

This project is **proprietary software** - Hak cipta dilindungi.

Penggunaan, distribusi, dan modifikasi tanpa izin terlarang.

## 🤝 Contributing

Kontribusi hanya tersedia untuk tim development resmi.

Untuk reporting bugs atau feature requests, hubungi tim development.

## 🔐 Security Vulnerability Disclosure

Jika Anda menemukan security vulnerability, segera laporkan ke:
- Email: security@timcare.local
- Jangan publish vulnerability di public forum/social media
- Berikan detailed reproduction steps dan impact analysis

## 📌 System Status Dashboard

**Current Status**: 🟢 **PRODUCTION READY**

- ✅ Core Features: 100% Complete
- ✅ Security Audit: PASSED (April 2026)
- ✅ Database Schema: Optimized
- ✅ API Documentation: Complete
- ✅ UI/UX: Modern & Responsive
- ✅ Notification System: Dual-Channel (Email + WhatsApp)
- ✅ Performance: Optimized
- ✅ Deployment: Tested & Ready

---

**Built with ❤️ for IT Service Management**

**Last Updated**: Juni 2026  
**Status**: Production Ready ✅  
**Version**: 1.0

