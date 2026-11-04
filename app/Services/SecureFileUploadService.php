<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SecureFileUploadService
{
    public const MAX_ATTACHMENT_KB = 10240;
    public const MAX_INITIAL_ATTACHMENT_KB = 1024;
    public const MAX_NOTA_DINAS_KB = 1024;

    private const ALLOWED_ATTACHMENT_MIMES = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'text/plain' => 'txt',
    ];

    private const ALLOWED_NOTA_DINAS_MIMES = [
        'application/pdf' => 'pdf',
    ];

    /**
     * Store a ticket attachment after validating its real MIME type and extension.
     */
    public function storeTicketAttachment(UploadedFile $file, string $directory = 'attachments', int $maxKb = self::MAX_ATTACHMENT_KB): array
    {
        return $this->storeAllowedFile($file, $directory, self::ALLOWED_ATTACHMENT_MIMES, $maxKb);
    }

    /**
     * Store a nota dinas PDF after validating its real MIME type and extension.
     */
    public function storeNotaDinas(UploadedFile $file, string $directory = 'nota_dinas'): array
    {
        return $this->storeAllowedFile($file, $directory, self::ALLOWED_NOTA_DINAS_MIMES, self::MAX_NOTA_DINAS_KB);
    }

    private function storeAllowedFile(UploadedFile $file, string $directory, array $allowedMimes, int $maxKb): array
    {
        if (! $file->isValid()) {
            throw ValidationException::withMessages(['file' => 'File upload tidak valid.']);
        }

        if (($file->getSize() / 1024) > $maxKb) {
            throw ValidationException::withMessages(['file' => "Ukuran file maksimal {$maxKb}KB."]);
        }

        $clientExtension = strtolower((string) $file->getClientOriginalExtension());
        $detectedMime = (string) ($file->getMimeType() ?: $file->getClientMimeType());
        $expectedExtension = $allowedMimes[$detectedMime] ?? null;

        if (! $expectedExtension) {
            throw ValidationException::withMessages(['file' => 'Tipe file tidak diizinkan. Gunakan PDF, gambar JPG/PNG, dokumen Office, Excel, atau TXT.']);
        }

        $allowedExtensions = array_values(array_unique($allowedMimes));
        if (! in_array($clientExtension, $allowedExtensions, true)) {
            throw ValidationException::withMessages(['file' => 'Ekstensi file tidak sesuai dengan tipe yang diizinkan.']);
        }

        $extension = $expectedExtension === 'jpg' && $clientExtension === 'jpeg' ? 'jpg' : $expectedExtension;
        $safeOriginalName = Str::limit(preg_replace('/[^A-Za-z0-9._ -]/', '_', $file->getClientOriginalName()), 180, '');
        $storedName = Str::uuid()->toString() . '.' . $extension;
        // Tiket dan nota dinas mengandung data internal. Simpan di disk privat
        // dan sajikan hanya melalui controller yang melakukan otorisasi.
        $path = $file->storeAs($directory, $storedName, 'local');

        return [
            'path' => $path,
            'original_name' => $safeOriginalName,
            'mime_type' => $detectedMime,
            'size_bytes' => $file->getSize(),
        ];
    }
}
