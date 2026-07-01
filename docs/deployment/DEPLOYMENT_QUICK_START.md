# HaiTI - Quick Reference Guide for Deployment

## 🚀 DEPLOYMENT QUICK START

### Prerequisites
- PHP 8.2+
- MySQL 5.7+ / MariaDB 10.0+
- Composer
- Node.js & NPM
- SSH access (for cPanel deployments)

---

## 📋 SETUP ON LOCAL MACHINE (Development)

```bash
# 1. Clone repository
git clone <repository-url>
cd haiti

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Database setup
# Edit .env with database credentials
php artisan migrate --seed

# 5. Generate assets
npm run build

# 6. Start development server
php artisan serve
# Access at http://localhost:8000
```

---

## 🌐 CPANEL DEPLOYMENT

### Folder Structure (Required)
```
/home/username/
├── public_html/              (Only Laravel's public folder)
│   ├── index.php
│   ├── .htaccess
│   ├── css/
│   ├── js/
│   └── storage → ../haiti/storage/app/public
│
└── haiti/                     (Full application, outside public_html)
    ├── app/
    ├── routes/
    ├── config/
    ├── .env
    ├── artisan
    └── vendor/
```

### Step-by-Step cPanel Setup

#### 1. Upload Files
```bash
# Via FTP/SCP - Upload entire project to ~/haiti folder
scp -r . user@host:~/haiti
```

#### 2. Create MySQL Database
- Login to cPanel
- MySQL Databases → Create Database
- Database: `timcare_db`
- Create User: `timcare_user`
- Set Password and assign all privileges

#### 3. Copy public folder to public_html
```bash
# SSH into server
ssh user@host

# Navigate to home directory
cd ~

# Create public_html structure
cp -r haiti/public/* public_html/
rm haiti/public/.htaccess  # Remove from source
```

#### 4. Create Symlink for Storage
```bash
# From public_html, link to storage
cd ~/public_html
ln -s ../haiti/storage/app/public storage
```

#### 5. Configure .env
```bash
cd ~/haiti
cp .env.example .env
nano .env
```

Update these values:
```env
APP_NAME="TimCare ITSM"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=timcare_db
DB_USERNAME=timcare_user
DB_PASSWORD=your_strong_password

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_FROM_ADDRESS=your-email@yourdomain.com

# If using WhatsApp
WHATSAPP_ENABLED=true
WHATSAPP_FONNTE_KEY=your_fonnte_api_key
```

#### 6. Run Migrations
```bash
cd ~/haiti
php artisan migrate --force
php artisan db:seed --force  # Optional: for test data
```

#### 7. Set Permissions
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod 644 .env
```

#### 8. Optimize for Production
```bash
cd ~/haiti
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

#### 9. Setup .htaccess (public_html)
Create `~/public_html/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [QSA,L]
</IfModule>
```

---

## 🔐 SECURITY CHECKLIST

Before going live:

- ✅ APP_DEBUG=false
- ✅ APP_ENV=production
- ✅ Strong database password
- ✅ HTTPS enabled (SSL certificate)
- ✅ .env file not accessible (chmod 644)
- ✅ Storage directory not publicly accessible
- ✅ Regular database backups configured
- ✅ Log rotation setup

---

## 📧 EMAIL CONFIGURATION

### Using cPanel Mail
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_cpanel_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="TimCare ITSM"
```

### Test Email
```bash
cd ~/haiti
php artisan tinker
Mail::raw('Test message', function ($m) {
    $m->to('test-email@example.com')
      ->subject('TimCare Test Email');
});
```

---

## 💬 WhatsApp CONFIGURATION

### Setup Fonnte.com Account
1. Register at https://fonnte.com
2. Get API key from dashboard
3. Add to .env:

```env
WHATSAPP_ENABLED=true
WHATSAPP_FONNTE_URL=https://api.fonnte.com/send
WHATSAPP_FONNTE_KEY=your_actual_api_key_here
```

### Test WhatsApp
```bash
cd ~/haiti
php artisan tinker

$user = App\Models\User::find(1);
$user->update(['phone_number' => '62812345678']);

$service = app(\App\Services\NotificationService::class);
$service->notify(
    $user,
    'test',
    'Test Title',
    'Test Message'
);
```

---

## 🔄 QUEUE PROCESSING (For Email/WhatsApp)

The system uses database queues. Process them with:

```bash
# Process jobs (run in screen/tmux for production)
cd ~/haiti
php artisan queue:work database

# Or via cron job (every minute)
* * * * * cd ~/haiti && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 DATABASE BACKUP

### Manual Backup
```bash
cd ~/haiti
php artisan db:backup  # If available

# Or manual mysqldump
mysqldump -u timcare_user -p timcare_db > backup.sql
```

### Automated cPanel Backup
- cPanel → Backups
- Configure daily automated backups
- Ensure backups stored off-server

---

## 🐛 TROUBLESHOOTING

### Website shows errors
```bash
cd ~/haiti
tail -f storage/logs/laravel-*.log  # View real-time errors
php artisan config:cache  # Clear config cache
php artisan clear-compiled  # Clear compiled files
```

### Database connection error
```bash
cd ~/haiti
php artisan tinker
DB::connection()->getPdo();  # Test connection
```

### Permissions denied on upload
```bash
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/
chmod -R 777 public/storage/
```

### Email not sending
```bash
# Test SMTP connection
php artisan mail:test your-email@example.com
```

---

## 📞 SUPPORT CONTACTS

- **Laravel Documentation**: https://laravel.com/docs
- **Fonnte WhatsApp API**: https://fonnte.com/docs
- **cPanel Help**: Your hosting provider's support

---

## 🎯 MAINTENANCE TASKS

### Weekly
- Check error logs: `storage/logs/`
- Verify database backups completed

### Monthly
- Review user access logs
- Update Laravel packages: `composer update`
- Test WhatsApp notifications
- Test email notifications

### Quarterly
- Security audit
- Performance optimization
- Dependency updates
- Backup restoration test

---

## ✨ DONE!

Your HaiTI system is ready to serve your organization!

Visit: `https://yourdomain.com`

**Default Login** (if using seeders):
- Email: admin@example.com
- Password: password

⚠️ **IMPORTANT**: Change default passwords immediately!
