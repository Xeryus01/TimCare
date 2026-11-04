# Quick Start Guide - TimCare ITSM Dashboard

## 🚀 Mulai dalam 5 Menit

### 1. Clone & Install (2 menit)

```bash
# Clone repository
git clone <repo-url>
cd haiti-app

# Install dependencies
composer install
npm install

# Generate key
php artisan key:generate
```

### 2. Database (1 menit)

```bash
# Create migrations
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed
```

### 3. WhatsApp Setup (1 menit)

Edit `.env`:
```env
WHATSAPP_ENABLED=true
WHATSAPP_FONNTE_KEY=your_key_from_fonnte_com
```

Dapatkan key:
1. Buka https://dashboard.fonnte.com
2. Daftar/login
3. Settings → API Key → Copy

### 4. Compile & Run (1 menit)

```bash
# Build CSS/JS
npm run build

# Start server
php artisan serve

# Akses: http://localhost:8000
```

**Login dengan:**
- Email: admin@example.com (jika seed)
- Password: password

---

## 📱 Test WhatsApp

```bash
# Open tinker
php artisan tinker

# Test send
$service = app(\App\Services\WhatsAppService::class);
$result = $service->send('62812345678', 'Test from app');
dd($result);
```

---

## 📋 Main Features

### Dashboard
- Akses: http://localhost:8000/dashboard
- Statistik & latest tickets

### Tickets
- List: http://localhost:8000/tickets
- Create: http://localhost:8000/tickets/create
- View: http://localhost:8000/tickets/{id}

### Assets
- List: http://localhost:8000/assets
- Create: http://localhost:8000/assets/create

### Reservations
- List: http://localhost:8000/reservations
- Create: http://localhost:8000/reservations/create

### Notifications
- List: http://localhost:8000/notifications
- Add phone to profile untuk WhatsApp

---

## 🔧 Common Tasks

### Add User Phone Number

```bash
php artisan tinker

$user = \App\Models\User::find(1);
$user->update(['phone_number' => '62812345678']);
```

### Create Test Ticket

```bash
php artisan tinker

\App\Models\Ticket::create([
    'code' => 'TKT-001',
    'title' => 'Test Ticket',
    'category' => 'HARDWARE',
    'description' => 'Test',
    'priority' => 'HIGH',
    'requester_id' => 1,
    'status' => 'OPEN'
]);
```

### Check WhatsApp Logs

```bash
# Show last 50 lines
tail -50 storage/logs/laravel.log | grep -i whatsapp

# Or open file directly
# storage/logs/laravel.log
```

### Clear Cache

```bash
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

---

## 🎨 Customize

### Change Theme Color

Edit `tailwind.config.js`:
```javascript
colors: {
    'brand': {
        // Change from sky blue to your color
        600: '#your-color',
        // ...
    }
}
```

### Custom Notification Message

Edit `app/Services/WhatsAppService.php`:
```php
'custom_type' => "Pesan custom berisi: {$data['custom_field']}"
```

### Add New Notification Type

1. Create method di `NotificationService`:
```php
public function notifyCustomEvent(User $user, $entity) {
    return $this->notify($user, 'custom', 'Title', 'Message', 'type', $entity->id);
}
```

2. Call dari controller:
```php
$this->notificationService->notifyCustomEvent($user, $entity);
```

---

## 🧪 Testing Endpoints

### Get Unread Count
```bash
curl -X GET http://localhost:8000/api/notifications/unread-count \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### List Notifications
```bash
curl -X GET http://localhost:8000/api/notifications \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Mark as Read
```bash
curl -X PATCH http://localhost:8000/api/notifications/1/mark-as-read \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🐛 Troubleshooting

### "Unauthenticated" Error
```bash
# Make sure you're logged in
# Check your session: php artisan tinker → auth()->user()
```

### WhatsApp Doesn't Send
```bash
# Check:
1. WHATSAPP_ENABLED=true
2. WHATSAPP_FONNTE_KEY is set
3. User has phone_number
4. Phone format correct (62xxx)
5. Check logs: tail -f storage/logs/laravel.log
```

### Assets Not Loading
```bash
# Rebuild:
npm run build

# Clear cache:
php artisan cache:clear
php artisan view:clear
```

### Database Error
```bash
# Reset:
php artisan migrate:fresh --seed
```

---

## 📚 Learn More

- **Setup Guide**: Read `.env.setup`
- **WhatsApp Guide**: Read `FONNTE_SETUP.md`
- **API Docs**: Read `NOTIFICATIONS_SETUP.md`
- **Full Docs**: Read `README_ID.md`
- **Changes**: Read `CHANGELOG.md`

---

## 🎯 Next Steps

1. ✅ Setup & install (done above)
2. ✅ Test dashboard
3. ✅ Create test data
4. ✅ Add phone number to user
5. ✅ Test WhatsApp notification
6. ✅ Deploy to staging
7. ✅ Get user feedback
8. ✅ Deploy to production

---

## 🆘 Need Help?

### Check Files
- Logs: `storage/logs/laravel.log`
- Config: `.env`, `config/services.php`
- Database: `database/database.sqlite`

### Debug Commands
```bash
# Check package versions
php artisan -v

# List all routes
php artisan route:list

# Check config
php artisan config:show services

# Test database
php artisan tinker → \App\Models\User::count()
```

### Contact Support
- Email: support@example.com
- WhatsApp: +62812345678 (update untuk real number)

---

**Happy coding! 🚀**
