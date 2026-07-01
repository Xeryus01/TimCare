<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send email notification.
     *
     * Dikirim sebagai HTML rapi + header pendukung (From name & Reply-To) dan
     * subjek yang sudah dibersihkan dari emoji agar tidak mudah masuk folder spam.
     */
    public function send(string $email, string $subject, string $message, ?string $actionUrl = null, ?string $actionText = null): array
    {
        try {
            if (!$email) {
                return [
                    'success' => false,
                    'status' => 'failed',
                    'error' => 'Email address is required',
                ];
            }

            // Bersihkan subjek (buang emoji/simbol) & beri konteks aplikasi.
            $cleanSubject = $this->sanitizeSubject($subject);

            // Susun isi HTML rapi + versi teks (plain) sebagai alternatif.
            $htmlBody = $this->buildHtmlBody($cleanSubject, $message, $actionUrl, $actionText);
            $textBody = $this->buildTextBody($message, $actionUrl, $actionText);

            $fromAddress = config('mail.from.address');
            $fromName = config('mail.from.name', config('app.name', 'TimCare'));

            // Kirim sebagai multipart (HTML + teks) dan set From + Reply-To.
            // Email multipart yang punya versi teks lebih kecil kemungkinannya
            // dicap spam dibanding email HTML-only.
            Mail::html($htmlBody, function ($mail) use ($email, $cleanSubject, $fromAddress, $fromName, $textBody) {
                $mail->to($email)
                     ->subject($cleanSubject);

                if (!empty($fromAddress)) {
                    $mail->from($fromAddress, $fromName);
                    $mail->replyTo($fromAddress, $fromName);
                }

                // Tambahkan alternatif teks polos (multipart/alternative).
                $mail->getSymfonyMessage()->text($textBody);
            });

            Log::info('Email sent successfully', [
                'email' => $email,
                'subject' => $cleanSubject,
            ]);

            return [
                'success' => true,
                'status' => 'sent',
                'email_response' => 'Email sent successfully',
            ];
        } catch (\Exception $e) {
            Log::error('Email service exception', [
                'email' => $email,
                'subject' => $subject,
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
     * Buang emoji/simbol di subjek & pastikan ada nama aplikasi.
     */
    private function sanitizeSubject(string $subject): string
    {
        $clean = preg_replace('/[\x{1F000}-\x{1FFFF}\x{2600}-\x{27BF}\x{2190}-\x{21FF}\x{2B00}-\x{2BFF}\x{FE0F}]/u', '', $subject);
        $clean = trim($clean ?? '');
        if ($clean === '') {
            $clean = 'Notifikasi';
        }
        if (stripos($clean, 'TimCare') === false) {
            $clean = 'TimCare - ' . $clean;
        }

        return $clean;
    }

    /**
     * Bangun body email HTML sederhana & rapi (responsif, inline style).
     */
    private function buildHtmlBody(string $subject, string $message, ?string $actionUrl, ?string $actionText): string
    {
        $appName = config('app.name', 'TimCare');
        $safeMessage = nl2br(e($message));

        $button = '';
        if ($actionUrl && $actionText) {
            $button = '<p style="margin:24px 0;"><a href="' . e($actionUrl) . '" style="background:#2563eb;color:#ffffff;text-decoration:none;padding:10px 18px;border-radius:6px;display:inline-block;">' . e($actionText) . '</a></p>';
        }

        return '<!DOCTYPE html>'
            . '<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>'
            . '<body style="margin:0;padding:0;background:#f4f4f7;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">'
            . '<div style="max-width:600px;margin:0 auto;padding:24px;">'
            . '<div style="background:#ffffff;border-radius:10px;padding:28px;border:1px solid #e5e7eb;">'
            . '<h2 style="margin:0 0 16px;font-size:18px;color:#111827;">' . e($subject) . '</h2>'
            . '<div style="font-size:14px;line-height:1.6;">' . $safeMessage . '</div>'
            . $button
            . '<hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">'
            . '<p style="font-size:12px;color:#6b7280;margin:0;">Email ini dikirim otomatis oleh sistem ' . e($appName) . '. Mohon tidak membalas email ini.</p>'
            . '</div></div></body></html>';
    }

    /**
     * Versi teks polos dari email (alternatif untuk multipart/alternative).
     */
    private function buildTextBody(string $message, ?string $actionUrl, ?string $actionText): string
    {
        $text = $message;
        if ($actionUrl && $actionText) {
            $text .= "\n\n" . $actionText . ': ' . $actionUrl;
        }
        $text .= "\n\n---\nEmail ini dikirim otomatis oleh sistem " . config('app.name', 'TimCare') . '. Mohon tidak membalas email ini.';

        return $text;
    }

    /**
     * Send notification with template
     */
    public function sendNotification(string $email, string $type, array $data): array
    {
        $message = $this->formatNotificationMessage($type, $data);
        $subject = $this->getNotificationSubject($type);
        $actionUrl = $data['action_url'] ?? null;
        $actionText = $data['action_text'] ?? null;

        return $this->send($email, $subject, $message, $actionUrl, $actionText);
    }

    /**
     * Format notification message berdasarkan type
     */
    private function formatNotificationMessage(string $type, array $data): string
    {
        return match ($type) {
            'ticket_created' => "Tiket baru telah dibuat:\n\n" .
                "Kode: {$data['code']}\n" .
                "Judul: {$data['title']}\n" .
                "Kategori: {$data['category']}\n\n" .
                "Deskripsi: {$data['description']}\n\n" .
                "Silakan login ke sistem TimCare untuk melihat detail tiket.",

            'ticket_updated' => "Tiket telah diperbarui:\n\n" .
                "Kode: {$data['code']}\n" .
                "Status: {$data['status']}\n" .
                "Perubahan: {$data['change_description']}\n\n" .
                "Silakan login ke sistem TimCare untuk melihat detail perubahan.",

            'ticket_resolved' => "Tiket telah diselesaikan:\n\n" .
                "Kode: {$data['code']}\n" .
                "Judul: {$data['title']}\n\n" .
                "Terima kasih telah menggunakan sistem TimCare.",

            'reservation_created' => "Reservasi ruangan baru telah dibuat:\n\n" .
                "Ruangan: {$data['room_name']}\n" .
                "Tanggal: {$data['date']}\n" .
                "Waktu: {$data['time']}\n" .
                "Tujuan: {$data['purpose']}\n\n" .
                "Silakan login ke sistem TimCare untuk melihat detail reservasi.",

            'reservation_approved' => "Reservasi ruangan telah disetujui:\n\n" .
                "Ruangan: {$data['room_name']}\n" .
                "Tanggal: {$data['date']}\n\n" .
                "Reservasi Anda telah disetujui. Silakan login ke sistem TimCare untuk detail lebih lanjut.",

            'asset_created' => "Aset baru telah ditambahkan:\n\n" .
                "Kode: {$data['asset_code']}\n" .
                "Nama: {$data['name']}\n" .
                "Tipe: {$data['type']}\n\n" .
                "Silakan login ke sistem TimCare untuk melihat detail aset.",

            default => "Notifikasi baru: " . json_encode($data),
        };
    }

    /**
     * Get notification subject
     */
    private function getNotificationSubject(string $type): string
    {
        return match ($type) {
            'ticket_created' => 'Tiket Baru - TimCare Helpdesk',
            'ticket_updated' => 'Tiket Diperbarui - TimCare Helpdesk',
            'ticket_resolved' => 'Tiket Diselesaikan - TimCare Helpdesk',
            'reservation_created' => 'Reservasi Ruangan Baru - TimCare Helpdesk',
            'reservation_approved' => 'Reservasi Disetujui - TimCare Helpdesk',
            'asset_created' => 'Aset Baru - TimCare Helpdesk',
            default => 'Notifikasi - TimCare Helpdesk',
        };
    }
}
