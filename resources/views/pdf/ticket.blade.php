<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Backup Tiket {{ $ticket->code }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #111827; margin: 0; }
        .header { border-bottom: 3px solid #2563eb; padding-bottom: 8px; margin-bottom: 14px; }
        .header h1 { font-size: 18px; margin: 0 0 2px; color: #1e3a8a; }
        .muted { color: #6b7280; font-size: 10px; }
        table.meta { width: 100%; border-collapse: collapse; margin: 10px 0 4px; }
        table.meta td { padding: 5px 6px; vertical-align: top; border-bottom: 1px solid #eef2f7; }
        table.meta td.k { width: 150px; font-weight: bold; color: #374151; }
        .badge { display: inline-block; padding: 2px 10px; border: 1px solid #9ca3af; border-radius: 10px; font-size: 10px; }
        .section { margin-top: 16px; }
        .section h2 { font-size: 13px; color: #1e3a8a; border-bottom: 2px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 8px; }
        .desc { white-space: pre-wrap; line-height: 1.5; }
        .att { margin: 10px 0; page-break-inside: avoid; }
        .att .cap { font-size: 10px; color: #6b7280; margin-bottom: 3px; }
        .att img { max-width: 100%; max-height: 380px; border: 1px solid #d1d5db; }
        .comment { border-bottom: 1px solid #eef2f7; padding: 7px 0; page-break-inside: avoid; }
        .comment .meta2 { font-size: 10px; color: #6b7280; margin-bottom: 2px; }
        .footer { margin-top: 22px; border-top: 1px solid #e5e7eb; padding-top: 6px; font-size: 9px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Tiket Masalah — {{ $ticket->code }}</h1>
        <div class="muted">Dibuat: {{ optional($ticket->created_at)->format('d/m/Y H:i') ?? '-' }} &nbsp;•&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table class="meta">
        <tr><td class="k">Judul</td><td>{{ $ticket->title }}</td></tr>
        <tr><td class="k">Status</td><td><span class="badge">{{ $ticket->status }}</span></td></tr>
        <tr><td class="k">Kategori</td><td>{{ $ticket->category ?? '-' }}</td></tr>
        <tr><td class="k">Prioritas</td><td>{{ $ticket->priority ?? '-' }}</td></tr>
        <tr><td class="k">Pemohon</td><td>{{ optional($ticket->requester)->name ?? '-' }}</td></tr>
        <tr><td class="k">Teknisi</td><td>{{ optional($ticket->assignee)->name ?? '-' }}</td></tr>
        <tr><td class="k">Aset</td><td>{{ optional($ticket->asset)->name ?? '-' }}</td></tr>
        <tr><td class="k">Selesai pada</td><td>{{ optional($ticket->resolved_at)->format('d/m/Y H:i') ?? '-' }}</td></tr>
    </table>

    <div class="section">
        <h2>Deskripsi</h2>
        <div class="desc">{{ $ticket->description ?: '-' }}</div>
    </div>

    <div class="section">
        <h2>Lampiran ({{ count($images) }})</h2>
        @forelse($images as $img)
            <div class="att">
                <div class="cap">{{ $img['name'] }}</div>
                @if(!empty($img['dataUri']))
                    <img src="{{ $img['dataUri'] }}" alt="{{ $img['name'] }}">
                @else
                    <div class="muted">[Berkas non-gambar: {{ $img['mime'] ?? 'file' }} — tidak dapat disematkan ke PDF, namun tercatat sebagai lampiran tiket]</div>
                @endif
            </div>
        @empty
            <div class="muted">Tidak ada lampiran.</div>
        @endforelse
    </div>

    @if($ticket->comments && $ticket->comments->count())
        <div class="section">
            <h2>Komentar ({{ $ticket->comments->count() }})</h2>
            @foreach($ticket->comments as $c)
                <div class="comment">
                    <div class="meta2">{{ optional($c->user)->name ?? 'Pengguna' }} • {{ optional($c->created_at)->format('d/m/Y H:i') }}@if($c->is_internal) • <em>internal</em>@endif</div>
                    <div class="desc">{{ $c->message }}</div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="footer">Dokumen backup ini dibuat otomatis oleh TimCare — {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
