<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Sistem - TimCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo e(asset('logo/logo.png')); ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('logo/logo.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('logo/logo.png')); ?>">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brand: #2563eb;
            --brand-light: #eff6ff;
            --brand-mid: #bfdbfe;
            --brand-dark: #1d4ed8;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --white: #ffffff;
            --shadow-sm: 0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.05);
            --shadow: 0 4px 16px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.05);
            --shadow-lg: 0 12px 40px rgba(0,0,0,.10), 0 4px 12px rgba(0,0,0,.06);
            --radius: 20px;
            --radius-sm: 12px;
            --radius-xs: 8px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--slate-50);
            color: var(--slate-800);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }


        main { flex: 1; padding: 72px 24px 96px; }
        .container { max-width: 1280px; margin: 0 auto; }

        .hero { text-align: center; margin-bottom: 64px; }
        .hero-tag {
            display: inline-block;
            font-size: .7rem; font-weight: 700; letter-spacing: .18em;
            text-transform: uppercase; color: var(--brand);
            background: var(--brand-light); padding: 4px 14px;
            border-radius: 999px; margin-bottom: 16px;
        }
        .hero h1 {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800; color: var(--slate-900);
            letter-spacing: -.5px; line-height: 1.15;
        }
        .hero p {
            margin-top: 14px; font-size: 1.05rem;
            color: var(--slate-500); max-width: 580px;
            margin-left: auto; margin-right: auto; line-height: 1.75;
        }

        .grid { display: grid; gap: 28px; }
        .status-grid { grid-template-columns: 1fr 1fr; }
        @media (max-width: 1024px) { .status-grid { grid-template-columns: 1fr; } }

        .card {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            padding: 44px 48px;
            transition: transform .2s, box-shadow .2s;
        }
        .card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
        .status-pill {
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 999px; padding: 8px 14px;
            font-size: .85rem; font-weight: 700;
        }
        .status-ok { background: #d1fae5; color: #047857; }
        .status-title { font-size: 1.25rem; font-weight: 700; color: var(--slate-900); margin-bottom: 8px; }
        .status-desc { font-size: .95rem; color: var(--slate-600); line-height: 1.75; }
        .status-list { display: grid; gap: 16px; margin-top: 18px; }
        .status-item {
            background: #ecfdf5; border: 1px solid #d1fae5;
            border-radius: var(--radius-sm); padding: 20px 22px;
        }
        .status-item p:first-child { font-weight: 700; color: var(--slate-900); margin-bottom: 6px; }
        .status-item p:last-child { font-size: .92rem; color: var(--slate-600); line-height: 1.65; }
    </style>
</head>
<body>
    <?php echo $__env->make('partials.landing-navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main>
    <div class="container">

        <div class="hero">
            <div class="hero-tag">Status Sistem</div>
            <h1>Status Operasional TimCare</h1>
            <p>Pantau ketersediaan dan performa layanan yang berjalan saat ini. Semua sistem terpantau secara real-time.</p>
        </div>

        <div class="grid status-grid">
            <div class="card">
                <div class="status-title">Layanan Utama</div>
                <div class="status-desc">Informasi status layanan yang paling sering digunakan.</div>
                <div class="status-pill status-ok">Normal</div>
                <div class="status-list">
                    <div class="status-item">
                        <p>Platform TimCare</p>
                        <p>Sistem utama berjalan dengan normal tanpa gangguan.</p>
                    </div>
                    <div class="status-item">
                        <p>Layanan Tiket</p>
                        <p>Form tiket, lampiran, dan update status berfungsi baik.</p>
                    </div>
                    <div class="status-item">
                        <p>Pengajuan Zoom</p>
                        <p>Pengajuan ruangan Zoom dan nota dinas aktif tanpa kendala.</p>
                    </div>
                    <div class="status-item">
                        <p>Notifikasi</p>
                        <p>Notifikasi email dan sistem terkirim sesuai rencana.</p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="status-title">Catatan Pemeliharaan</div>
                <div class="status-desc">Tidak ada jadwal pemeliharaan terjadwal yang mempengaruhi layanan saat ini.</div>
                <div class="status-list" style="margin-top: 18px;">
                    <div class="status-item">
                        <p>Backup Database</p>
                        <p>Backup dan pemantauan database berjalan dengan baik.</p>
                    </div>
                    <div class="status-item">
                        <p>Pemantauan Kinerja</p>
                        <p>CPU dan memori server berada dalam batas aman.</p>
                    </div>
                    <div class="status-item">
                        <p>Integrasi Pesan</p>
                        <p>Sistem notifikasi dan whatsapp saat ini berfungsi dengan baik.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

    <?php echo $__env->make('partials.landing-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>
</html>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/pages/status.blade.php ENDPATH**/ ?>