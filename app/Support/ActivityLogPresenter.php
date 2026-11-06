<?php

namespace App\Support;

use App\Models\Log;

class ActivityLogPresenter
{
    public function action(Log $log): string
    {
        return match (strtoupper($log->action)) {
            'LOGIN' => 'Masuk ke aplikasi',
            'LOGOUT' => 'Keluar dari aplikasi',
            'SSO_LOGIN' => 'Masuk menggunakan SSO',
            'SSO_LOGOUT' => 'Keluar menggunakan SSO',
            'SSO_TOKEN_REFRESHED' => 'Memperbarui sesi SSO',
            'CREATED' => 'Membuat data',
            'UPDATED' => 'Memperbarui data',
            'DELETED' => 'Menghapus data',
            'ASSIGNED' => $this->isReservation($log) ? 'Menetapkan petugas persetujuan' : 'Menetapkan petugas tiket',
            'STATUS_CHANGED' => 'Mengubah status',
            'COMMENTED' => 'Menambahkan komentar',
            'MAINTENANCE_PROCESSED' => 'Memproses pemeliharaan',
            'MAINTENANCE_COMPLETED' => 'Menyelesaikan pemeliharaan',
            'SERVICE_RATED' => 'Memberikan penilaian layanan',
            'PROFILE_UPDATED' => 'Memperbarui profil',
            'PASSWORD_UPDATED' => 'Memperbarui kata sandi',
            'TI_REFERENCE_IMPORTED' => 'Mengimpor data referensi Monitoring TI',
            'TI_MEMBERSHIPS_IMPORTED' => 'Mengimpor keanggotaan tim Monitoring TI',
            'TI_LEADER_IMPORTED' => 'Mengimpor penanggung jawab tim Monitoring TI',
            'TI_OBJECT_SAVED' => 'Menyimpan data aplikasi Monitoring TI',
            'TI_TEAM_MEMBERS_UPDATED' => 'Memperbarui anggota tim Monitoring TI',
            'TI_TEAM_SAVED' => 'Menyimpan data tim Monitoring TI',
            'TI_REPORT_CREATED' => 'Membuat laporan Monitoring TI',
            'TI_REPORT_UPDATED' => 'Memperbarui laporan Monitoring TI',
            default => 'Aktivitas tercatat',
        };
    }

    public function entity(Log $log): string
    {
        $type = strtolower($log->entity_type);

        return match ($type) {
            'ticket' => 'Tiket masalah TI',
            'reservation' => 'Reservasi Zoom',
            'asset' => 'Aset',
            'attachment' => 'Lampiran',
            'user' => 'Akun pengguna',
            'ti_team' => 'Tim Monitoring TI',
            'ti_object' => 'Aplikasi Monitoring TI',
            'ti_report' => 'Laporan Monitoring TI',
            default => 'Data',
        };
    }

    private function isReservation(Log $log): bool
    {
        return strtolower($log->entity_type) === 'reservation';
    }
}
