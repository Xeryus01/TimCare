<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Backup Tiket Zoom {{ $reservation->code }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #111827; margin: 0; }
        .header { border-bottom: 3px solid #7c3aed; padding-bottom: 8px; margin-bottom: 14px; }
        .header h1 { font-size: 18px; margin: 0 0 2px; color: #5b21b6; }
        .muted { color: #6b7280; font-size: 10px; }
        table.meta { width: 100%; border-collapse: collapse; margin: 10px 0 4px; }
        table.meta td { padding: 5px 6px; vertical-align: top; border-bottom: 1px solid #eef2f7; }
        table.meta td.k { width: 160px; font-weight: bold; color: #374151; }
        .badge { display: inline-block; padding: 2px 10px; border: 1px solid #9ca3af; border-radius: 10px; font-size: 10px; }
        .section { margin-top: 16px; }
        .section h2 { font-size: 13px; color: #5b21b6; border-bottom: 2px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 8px; }
        .desc { white-space: pre-wrap; line-height: 1.5; }
        a { color: #2563eb; word-break: break-all; }
        .footer { margin-top: 22px; border-top: 1px solid #e5e7eb; padding-top: 6px; font-size: 9px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Tiket Zoom — {{ $reservation->code }}</h1>
        <div class="muted">Dibuat: {{ optional($reservation->created_at)->format('d/m/Y H:i') ?? '-' }} &nbsp;•&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table class="meta">
        <tr><td class="k">Status</td><td><span class="badge">{{ $reservation->status_label }}</span></td></tr>
        <tr><td class="k">Ruangan</td><td>{{ $reservation->room_name ?? '-' }}</td></tr>
        <tr><td class="k">Keperluan</td><td>{{ $reservation->purpose ?? '-' }}</td></tr>
        <tr><td class="k">Tim / Satker</td><td>{{ $reservation->team_name ?? '-' }}</td></tr>
        <tr><td class="k">Jumlah Peserta</td><td>{{ $reservation->participants_count ?? '-' }}</td></tr>
        <tr><td class="k">Butuh Operator</td><td>{{ $reservation->operator_needed ? 'Ya' : 'Tidak' }}</td></tr>
        <tr><td class="k">Butuh Ruang Istirahat</td><td>{{ $reservation->breakroom_needed ? 'Ya' : 'Tidak' }}</td></tr>
        <tr><td class="k">Waktu Mulai</td><td>{{ optional($reservation->start_time)->format('d/m/Y H:i') ?? '-' }}</td></tr>
        <tr><td class="k">Waktu Selesai</td><td>{{ optional($reservation->end_time)->format('d/m/Y H:i') ?? '-' }}</td></tr>
        <tr><td class="k">Pemohon</td><td>{{ optional($reservation->requester)->name ?? '-' }}</td></tr>
        <tr><td class="k">Penyetuju</td><td>{{ optional($reservation->approver)->name ?? '-' }}</td></tr>
    </table>

    <div class="section">
        <h2>Tautan Zoom</h2>
        <table class="meta">
            <tr><td class="k">Link Zoom</td><td>@if($reservation->zoom_link)<a href="{{ $reservation->zoom_link }}">{{ $reservation->zoom_link }}</a>@else - @endif</td></tr>
            <tr><td class="k">Link Rekaman</td><td>@if($reservation->zoom_record_link)<a href="{{ $reservation->zoom_record_link }}">{{ $reservation->zoom_record_link }}</a>@else - @endif</td></tr>
        </table>
    </div>

    <div class="section">
        <h2>Catatan</h2>
        <div class="desc">{{ $reservation->notes ?: '-' }}</div>
    </div>

    <div class="section">
        <h2>Nota Dinas</h2>
        <div class="muted">@if($reservation->nota_dinas_path)Berkas nota dinas (PDF) terlampir secara terpisah pada tiket ini dan dapat dihapus melalui File Manager setelah backup dibuat.@else Tidak ada berkas nota dinas. @endif</div>
    </div>

    <div class="footer">Dokumen backup ini dibuat otomatis oleh TimCare — {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
