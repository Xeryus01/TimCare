# Email Notification Setup

Sistem TimCare sekarang mendukung notifikasi email selain WhatsApp. Berikut adalah panduan untuk mengkonfigurasi email notifications.

## Konfigurasi Email

### 1. SMTP Configuration

Edit file `.env` Anda untuk mengkonfigurasi SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@haiti.com"
MAIL_FROM_NAME="TimCare Helpdesk"
```

### 2. Gmail Setup (Contoh)

Untuk menggunakan Gmail sebagai SMTP server:

1. Aktifkan 2-Factor Authentication di akun Gmail Anda
2. Buat App Password:
   - Pergi ke Google Account Settings
   - Security > 2-Step Verification > App passwords
   - Generate password untuk "TimCare Helpdesk"
3. Gunakan App Password tersebut sebagai MAIL_PASSWORD

### 3. Mailgun Setup (Production Recommended)

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-mailgun-secret
MAILGUN_ENDPOINT=api.mailgun.net
```

### 4. Testing Email Configuration

Jalankan command berikut untuk test email:

```bash
php artisan tinker
```

Kemudian jalankan:

```php
Mail::raw('Test email from TimCare', function ($message) {
    $message->to('your-email@example.com')
            ->subject('TimCare Email Test');
});
```

## Cara Kerja Email Notifications

### 1. Automatic Email Sending

Sistem akan otomatis mengirim email ke user jika:
- User memiliki email address yang valid
- Email notifications diaktifkan (default: true)
- Email service berhasil dikonfigurasi

### 2. Email Templates

Email notifications menggunakan template yang sudah diformat untuk berbagai jenis notifikasi:

- **Tiket Baru**: Informasi lengkap tiket yang dibuat
- **Tiket Diperbarui**: Status perubahan dan deskripsi
- **Tiket Diselesaikan**: Konfirmasi penyelesaian
- **Reservasi Baru**: Detail pengajuan Zoom
- **Reservasi Disetujui**: Konfirmasi approval dengan link Zoom
- **Aset Baru**: Informasi aset yang ditambahkan

### 3. Email Status Tracking

Setiap email notification akan dilacak statusnya:
- `sent`: Email berhasil dikirim
- `failed`: Email gagal dikirim
- Response dari email service disimpan untuk debugging

## Troubleshooting

### Email Tidak Terkirim

1. **Cek Log Files**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Verifikasi Konfigurasi**:
   - Pastikan MAIL_MAILER diset ke 'smtp' bukan 'log'
   - Cek kredensial SMTP
   - Test koneksi SMTP

3. **Cek Queue (jika menggunakan queue)**:
   ```bash
   php artisan queue:work
   ```

### Development Mode

Untuk development, email akan disimpan ke log file (`storage/logs/laravel.log`) jika MAIL_MAILER=log.

### Production Deployment

Pastikan untuk:
1. Menggunakan SMTP service terpercaya (Mailgun, SendGrid, dll)
2. Mengatur proper MAIL_FROM_ADDRESS
3. Menggunakan queue untuk email sending
4. Monitor email delivery rates

## Fitur Tambahan

### 1. Email Preferences

User dapat memilih jenis notifikasi yang ingin diterima:
- Hanya WhatsApp
- Hanya Email
- WhatsApp dan Email
- Tidak ada notifikasi

### 2. Bulk Notifications

Sistem mendukung pengiriman notifikasi massal untuk:
- Broadcast messages
- System announcements
- Maintenance notifications

### 3. Email Templates Customization

Email templates dapat dikustomisasi melalui:
- `resources/views/emails/` directory
- Menggunakan Laravel Mail classes
- Customizable subjects dan content