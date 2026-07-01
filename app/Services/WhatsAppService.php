<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $apiUrl;
    private ?string $apiKey;
    private bool $enabled;
    private bool $verifySsl;

    public function __construct()
    {
        // Fonnte.com API configuration
        $this->apiUrl = config('services.whatsapp.fonnte_url', 'https://api.fonnte.com/send');
        $this->apiKey = config('services.whatsapp.fonnte_key') ?? '';
        $this->enabled = config('services.whatsapp.enabled', false);
        $this->verifySsl = (bool) config('services.whatsapp.verify_ssl', true);
    }

    /**
     * Send WhatsApp message via Fonnte API
     * 
     * @param string $phoneNumber Phone number with country code (e.g., 62812345678 or +62812345678)
     * @param string $message Message content
     * @param string $title Optional title/header
     * @return array Response from WhatsApp API
     */
    public function send(string $phoneNumber, string $message, string $title = ''): array
    {
        // Return success if WhatsApp is disabled
        if (!$this->enabled) {
            Log::info('WhatsApp disabled - message not sent', [
                'phone' => $phoneNumber,
            ]);

            return [
                'success' => false,
                'status' => 'disabled',
                'message' => 'WhatsApp service is disabled',
            ];
        }

        // Check if API key is configured
        if (empty($this->apiKey)) {
            Log::warning('WhatsApp API key not configured', [
                'phone' => $phoneNumber,
            ]);

            return [
                'success' => false,
                'status' => 'not_configured',
                'error' => 'WhatsApp API key not configured',
            ];
        }

        try {
            // Format message dengan title jika ada
            $fullMessage = $title ? "*$title*\n\n$message" : $message;

            // Normalisasi nomor ke format internasional Indonesia (62xxx) untuk Fonnte.
            // Buang semua karakter selain angka (spasi, +, -, dll).
            $normalizedPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
            if (str_starts_with($normalizedPhone, '0')) {
                // 08xxx -> 628xxx
                $normalizedPhone = '62' . substr($normalizedPhone, 1);
            } elseif (str_starts_with($normalizedPhone, '8')) {
                // 8xxx (tanpa 0 di depan) -> 628xxx
                $normalizedPhone = '62' . $normalizedPhone;
            }
            // Jika sudah diawali 62, biarkan apa adanya.

            // Kirim ke Fonnte API memakai form-data (asForm), karena Fonnte membaca
            // 'target' & 'message' sebagai form field, bukan JSON.
            $response = Http::withOptions(['verify' => $this->verifySsl])
                ->withHeaders([
                    'Authorization' => $this->apiKey,
                ])->asForm()->post($this->apiUrl, [
                    'target' => $normalizedPhone,
                    'message' => $fullMessage,
                ]);

            $data = $response->json();
            // Lindungi dari respons non-JSON (null) agar tidak fatal saat akses array.
            if (! is_array($data)) {
                $data = [];
            }

            Log::info('Fonnte API response', [
                'phone' => $phoneNumber,
                'normalized' => $normalizedPhone,
                'status_code' => $response->status(),
                'response' => $data,
            ]);

            // Check if response indicates success
            $status = $data['status'] ?? null;
            if ($response->successful() && ($status === true || $status === 'success' || isset($data['data']['id']))) {
                Log::info('WhatsApp message sent via Fonnte', [
                    'phone' => $phoneNumber,
                    'response' => $data,
                ]);

                return [
                    'success' => true,
                    'message_id' => $data['data']['id'] ?? ($data['id'] ?? null),
                    'status' => 'sent',
                    'fonnte_response' => $data,
                ];
            }

            Log::warning('Fonnte API error', [
                'phone' => $phoneNumber,
                'status' => $response->status(),
                'response' => $data,
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'error' => $data['reason'] ?? $data['error'] ?? $data['message'] ?? 'Unknown error',
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp service exception', [
                'phone' => $phoneNumber,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send notification with template
     */
    public function sendNotification(string $phoneNumber, string $type, array $data): array
    {
        $message = $this->formatNotificationMessage($type, $data);
        $title = $this->getNotificationTitle($type);

        return $this->send($phoneNumber, $message, $title);
    }

    /**
     * Format notification message berdasarkan type
     */
    private function formatNotificationMessage(string $type, array $data): string
    {
        return match ($type) {
            'ticket_created' => "Tiket baru telah dibuat:\n" .
                "Kode: {$data['code']}\n" .
                "Judul: {$data['title']}\n" .
                "Kategori: {$data['category']}",

            'ticket_updated' => "Tiket telah diperbarui:\n" .
                "Kode: {$data['code']}\n" .
                "Status: {$data['status']}\n" .
                "Perubahan: {$data['change_description']}",

            'ticket_resolved' => "Tiket telah diselesaikan:\n" .
                "Kode: {$data['code']}\n" .
                "Judul: {$data['title']}",

            'reservation_created' => "Reservasi ruangan baru:\n" .
                "Ruangan: {$data['room_name']}\n" .
                "Tanggal: {$data['date']}\n" .
                "Waktu: {$data['time']}\n" .
                "Tujuan: {$data['purpose']}",

            'reservation_approved' => "Reservasi ruangan telah disetujui:\n" .
                "Ruangan: {$data['room_name']}\n" .
                "Tanggal: {$data['date']}",

            'asset_created' => "Aset baru telah ditambahkan:\n" .
                "Kode: {$data['asset_code']}\n" .
                "Nama: {$data['name']}\n" .
                "Tipe: {$data['type']}",

            default => "Notifikasi baru: " . json_encode($data),
        };
    }

    /**
     * Get notification title
     */
    private function getNotificationTitle(string $type): string
    {
        return match ($type) {
            'ticket_created' => '📋 Tiket Baru',
            'ticket_updated' => '🔄 Tiket Diperbarui',
            'ticket_resolved' => '✅ Tiket Diselesaikan',
            'reservation_created' => '🏢 Reservasi Ruangan Baru',
            'reservation_approved' => '✅ Reservasi Disetujui',
            'asset_created' => '📦 Aset Baru',
            default => '📬 Notifikasi',
        };
    }
}
