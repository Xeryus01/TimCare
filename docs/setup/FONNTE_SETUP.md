# Setup WhatsApp dengan Fonnte.com

## Pendahuluan

Fonnte adalah layanan WhatsApp API yang mudah digunakan dan terjangkau. Berikut adalah langkah-langkah untuk mengintegrasikan Fonnte.com ke dalam aplikasi.

## Langkah 1: Daftar di Fonnte.com

1. Kunjungi https://dashboard.fonnte.com
2. Klik "Sign Up" atau "Daftar"
3. Isi data email dan password
4. Verifikasi email Anda
5. Login ke dashboard

## Langkah 2: Dapatkan API Key

1. Setelah login, buka menu **Settings** atau **Pengaturan**
2. Cari bagian **API Configuration** atau **Konfigurasi API**
3. Klik **Tampilkan API Key** atau **Show API Key**
4. Copy API Key Anda (format: biasanya dimulai dengan "xxxxxxxxxxx")

## Langkah 3: Konfigurasi Environment

Edit file `.env` dan tambahkan:

```env
# WhatsApp via Fonnte
WHATSAPP_ENABLED=true
WHATSAPP_FONNTE_URL=https://api.fonnte.com/send
WHATSAPP_FONNTE_KEY=your_api_key_here
```

Ganti `your_api_key_here` dengan API Key yang Anda dapatkan dari dashboard Fonnte.

## Langkah 4: Verifikasi Koneksi

### Test via Artisan Tinker

```bash
php artisan tinker
```

Dalam Tinker, jalankan:

```php
$service = app(\App\Services\WhatsAppService::class);
$result = $service->send('62812345678', 'Ini pesan test dari Fonnte');
dump($result);
```

### Format Nomor Telepon

- ✅ Benar: `62812345678` (tanpa +)
- ✅ Benar: `+62812345678` (dengan +)
- ✅ Benar: `0812345678` (format lokal)

Sistem akan otomatis mengkonversi ke format yang benar.

## Format Balasan API Fonnte

Respons sukses dari Fonnte:

```json
{
  "status": true,
  "data": {
    "id": "1234567890",
    "phone": "62812345678",
    "message": "Pesan Anda",
    "status": "sent"
  }
}
```

Respons error dari Fonnte:

```json
{
  "status": false,
  "reason": "Alasan error"
}
```

## Fitur Notifikasi

Sistem akan secara otomatis mengirim notifikasi WhatsApp saat:

1. **Ticket Dibuat**
   - Penerima: Pembuat ticket
   - Status: Langsung terkirim

2. **Ticket Diupdate**
   - Penerima: Assignee/Requester
   - Status: Saat status berubah

3. **Ticket Diselesaikan**
   - Penerima: Assignee/Requester
   - Status: Saat status RESOLVED

4. **Reservasi Dibuat**
   - Penerima: Pembuat reservasi
   - Status: Langsung terkirim

5. **Reservasi Disetujui**
   - Penerima: Pembuat reservasi
   - Status: Saat disetujui

6. **Asset Ditambahkan**
   - Penerima: Admin/Creator
   - Status: Langsung terkirim

## Menambah Nomor Telepon User

### Melalui Dashboard (Admin)

1. Buka halaman User Management
2. Edit user
3. Tambah field "Phone Number"
4. Simpan

### Melalui Profile User

1. User login ke aplikasi
2. Buka Profile Settings
3. Tambah nomor telepon (format: 62812345678)
4. Klik Save

Catatan: Nomor telepon optional - jika kosong, notifikasi WhatsApp tidak akan terkirim.

## Testing Notifikasi

### 1. Test Ticket Creation

```bash
# Login sebagai user di browser
# Buat ticket baru
# Tunggu 2-3 detik
# Cek WhatsApp untuk pesan notifikasi
```

### 2. Check Notification Database

```bash
php artisan tinker

# Check notifications
$notifications = \App\Models\Notification::where('whatsapp_sent', true)->latest()->get();
$notifications->each(fn($n) => dump($n->only(['title', 'message', 'whatsapp_status', 'whatsapp_response'])));
```

## Troubleshooting

### Pesan tidak terkirim

**Kemungkinan penyebab:**

1. **WHATSAPP_ENABLED tidak true**
   ```bash
   # Check di .env
   WHATSAPP_ENABLED=true
   ```

2. **API Key salah atau kosong**
   ```bash
   php artisan tinker
   config('services.whatsapp.fonnte_key')
   # Harus menampilkan API key Anda
   ```

3. **Nomor telepon user kosong**
   - Setiap user harus punya nomor telepon di profile mereka
   - Gunakan format: `62812345678` atau `0812345678` atau `+62812345678`

4. **WhatsApp Business Account belum disetup di Fonnte**
   - Login ke dashboard Fonnte
   - Pastikan WhatsApp Number sudah terkoneksi dan verified

### Cek Log

```bash
# Lihat log WhatsApp errors
tail -f storage/logs/laravel.log | grep -i whatsapp

# Atau gunakan file explorer untuk buka storage/logs/laravel.log
```

### Fonnte Account terbatas

- Akun baru Fonnte biasanya terbatas hingga 30-50 pesan/hari
- Update paket untuk unlimited messages
- Cek di dashboard Fonnte -> Account Status

## API Response Codes

| Status | Meaning |
|--------|---------|
| 200 | Success - pesan terkirim |
| 400 | Bad Request - parameter salah |
| 401 | Unauthorized - API key invalid |
| 403 | Forbidden - akun terbatas |
| 429 | Rate Limited - terlalu banyak request |
| 500 | Server Error - Fonnte down |

## Biaya

- **Gratis**: 30-50 pesan/bulan (tergantung paket)
- **Domain Fonnte**: Rp 2.000 per pesan
- **Private Domain**: Mulai dari Rp 1.000 per pesan (paket tahunan)

Kunjungi https://fonnte.com/harga untuk detail pricing.

## Support

- Website: https://fonnte.com
- Dashboard: https://dashboard.fonnte.com
- Dokumentasi: https://fonnte.com/dokumentasi
- Chat Support: Tersedia di dashboard

## Tips & Tricks

1. **Batch Send**: Untuk kirim banyak pesan, gunakan Queue jobs
   ```php
   // app/Jobs/SendWhatsAppNotification.php
   Queue::dispatch(new SendWhatsAppNotification($user, $message));
   ```

2. **Custom Message**: Edit template di WhatsAppService.php
   ```php
   'custom_type' => "Pesan custom dengan {$data['variable']}"
   ```

3. **Webhook Delivery**: Setup webhook di Fonnte untuk real-time status update
   - Endpoint: `/webhooks/whatsapp/status`
   - Buat route untuk handle webhook

4. **Rate Limiting**: Tambahkan delay antar pesan jika perlu
   ```php
   sleep(1); // 1 detik delay
   ```
