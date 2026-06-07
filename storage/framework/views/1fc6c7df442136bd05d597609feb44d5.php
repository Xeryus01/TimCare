<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dokumentasi - TimCare</title>
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

        .doc-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 28px;
            align-items: start;
        }
        @media (max-width: 1024px) { .doc-grid { grid-template-columns: 1fr; } }

        .left-col { display: flex; flex-direction: column; gap: 28px; }
        .right-col { display: flex; flex-direction: column; gap: 28px; }

        .card {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            padding: 44px 48px;
            transition: transform .2s, box-shadow .2s;
        }
        .card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
        .card-accent { background: var(--brand-light); border-color: var(--brand-mid); }
        .card-plain { background: var(--white); border-color: var(--slate-200); }

        .card-tag {
            font-size: .65rem; font-weight: 700; letter-spacing: .2em;
            text-transform: uppercase; color: var(--brand); margin-bottom: 10px;
        }
        .card-tag-muted { color: var(--slate-400); }
        .card-num {
            width: 44px; height: 44px;
            background: var(--brand-light); color: var(--brand);
            border-radius: 14px; display: flex; align-items: center;
            justify-content: center; font-size: 1rem; font-weight: 800;
            flex-shrink: 0;
        }
        .card-header {
            display: flex; align-items: flex-start;
            justify-content: space-between; gap: 16px; margin-bottom: 28px;
        }
        .card-title { font-size: 1.3rem; font-weight: 700; color: var(--slate-900); margin-bottom: 6px; }
        .card-desc { font-size: .9rem; color: var(--slate-500); line-height: 1.65; }

        .step-list { display: flex; flex-direction: column; gap: 12px; }
        .step-item {
            display: flex; align-items: flex-start; gap: 16px;
            background: var(--slate-50); border: 1px solid var(--slate-200);
            border-radius: var(--radius-sm); padding: 20px 22px;
            transition: border-color .15s;
        }
        .step-item:hover { border-color: var(--brand-mid); }
        .step-badge {
            width: 38px; height: 38px; flex-shrink: 0;
            background: var(--brand); color: var(--white);
            border-radius: 10px; display: flex; align-items: center;
            justify-content: center; font-size: .85rem; font-weight: 700;
        }
        .step-title { font-size: .925rem; font-weight: 600; color: var(--slate-900); margin-bottom: 3px; }
        .step-desc { font-size: .85rem; color: var(--slate-500); line-height: 1.6; }

        .feat-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
        }
        .feat-item {
            background: var(--slate-50); border: 1px solid var(--slate-200);
            border-radius: var(--radius-sm); padding: 20px 22px;
            transition: border-color .15s, background .15s;
        }
        .feat-item:hover { background: var(--brand-light); border-color: var(--brand-mid); }
        .feat-title { font-size: .875rem; font-weight: 700; color: var(--slate-800); margin-bottom: 4px; }
        .feat-desc { font-size: .82rem; color: var(--slate-500); line-height: 1.55; }

        .notice-list { display: flex; flex-direction: column; gap: 12px; margin-top: 6px; }
        .notice-item {
            background: var(--white); border: 1px solid rgba(255,255,255,.8);
            border-radius: var(--radius-sm); padding: 18px 22px;
            box-shadow: var(--shadow-sm);
        }
        .notice-title { font-size: .875rem; font-weight: 700; color: var(--slate-800); margin-bottom: 3px; }
        .notice-desc { font-size: .82rem; color: var(--slate-500); line-height: 1.55; }

        .comp-list { display: flex; flex-direction: column; gap: 10px; margin-top: 6px; }
        .comp-item {
            background: var(--slate-50); border: 1px solid var(--slate-100);
            border-radius: var(--radius-sm); padding: 16px 20px;
            display: flex; align-items: center; gap: 14px;
        }
        .comp-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: var(--brand-light); display: flex;
            align-items: center; justify-content: center; flex-shrink: 0;
        }
        .comp-icon svg { width: 17px; height: 17px; color: var(--brand); }
        .comp-title { font-size: .875rem; font-weight: 600; color: var(--slate-800); }
        .comp-desc { font-size: .8rem; color: var(--slate-400); }
    </style>
</head>
<body>
    <?php echo $__env->make('partials.landing-navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main>
    <div class="container">

        <div class="hero">
            <div class="hero-tag">Panduan</div>
            <h1>Dokumentasi TimCare</h1>
            <p>Pelajari cara menggunakan fitur tiket, pengajuan ruang Zoom, manajemen pengguna, dan pemantauan status sistem di platform helpdesk IT ini.</p>
        </div>

        <div class="doc-grid">
            <div class="left-col">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-tag">Langkah Awal</div>
                            <div class="card-title">Mulai menggunakan TimCare</div>
                            <div class="card-desc">Ikuti langkah berikut untuk memulai penggunaan sistem helpdesk IT secara cepat dan mudah.</div>
                        </div>
                        <div class="card-num">1</div>
                    </div>
                    <div class="step-list">
                        <div class="step-item">
                            <div class="step-badge">A</div>
                            <div>
                                <div class="step-title">Daftar atau masuk</div>
                                <div class="step-desc">Akses halaman utama, lalu masuk dengan akun yang sudah ada atau daftar bila belum memiliki akun.</div>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-badge">B</div>
                            <div>
                                <div class="step-title">Ajukan tiket</div>
                                <div class="step-desc">Buka menu Tiket, pilih "Ajukan Tiket Baru", lalu isi detail masalah dan lampirkan bukti bila diperlukan.</div>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-badge">C</div>
                            <div>
                                <div class="step-title">Pengajuan Zoom</div>
                                <div class="step-desc">Gunakan menu Reservasi untuk meminta ruang Zoom lengkap dengan tanggal, waktu, dan dokumen pendukung.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-tag">Ringkasan</div>
                            <div class="card-title">Fitur utama TimCare</div>
                            <div class="card-desc">TimCare hadir dengan fitur inti yang dirancang untuk memudahkan semua kebutuhan IT di satu platform.</div>
                        </div>
                        <div class="card-num">2</div>
                    </div>
                    <div class="feat-grid">
                        <div class="feat-item">
                            <div class="feat-title">Tiket Permasalahan</div>
                            <div class="feat-desc">Laporkan semua masalah IT, dari perangkat keras hingga jaringan.</div>
                        </div>
                        <div class="feat-item">
                            <div class="feat-title">Pengajuan Zoom</div>
                            <div class="feat-desc">Pesan ruang meeting Zoom dengan nota dinas dan detail lengkap.</div>
                        </div>
                        <div class="feat-item">
                            <div class="feat-title">Notifikasi Real-time</div>
                            <div class="feat-desc">Dapatkan pemberitahuan saat status tiket diperbarui.</div>
                        </div>
                        <div class="feat-item">
                            <div class="feat-title">Akses Berbasis Role</div>
                            <div class="feat-desc">Kontrol hak akses Admin, Teknisi, dan User secara jelas.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-col">
                <div class="card card-accent">
                    <div class="card-tag">Penting</div>
                    <div class="card-title" style="color:#1e3a8a; margin-bottom:6px;">Apa yang harus diketahui dulu</div>
                    <div class="notice-list">
                        <div class="notice-item">
                            <div class="notice-title">Gunakan user resmi</div>
                            <div class="notice-desc">Login menggunakan akun terdaftar agar hak akses fitur tampil sesuai dengan peran Anda.</div>
                        </div>
                        <div class="notice-item">
                            <div class="notice-title">Lampirkan informasi lengkap</div>
                            <div class="notice-desc">Detail tiket atau pengajuan Zoom yang lengkap mempercepat respons tim IT.</div>
                        </div>
                        <div class="notice-item">
                            <div class="notice-title">Pantau notifikasi</div>
                            <div class="notice-desc">Perhatikan update di dashboard dan notifikasi untuk perkembangan tiket atau jadwal Zoom.</div>
                        </div>
                    </div>
                </div>

                <div class="card card-plain">
                    <div class="card-tag card-tag-muted">Komponen Utama</div>
                    <div class="comp-list">
                        <div class="comp-item">
                            <div class="comp-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                                    <path d="M9 12h6m-6 4h4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="comp-title">Tiket</div>
                                <div class="comp-desc">Kelola permintaan perbaikan, dukungan, dan tindak lanjut dalam satu halaman.</div>
                            </div>
                        </div>
                        <div class="comp-item">
                            <div class="comp-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                    <path d="M16 2v4M8 2v4M3 10h18"/>
                                </svg>
                            </div>
                            <div>
                                <div class="comp-title">Reservasi</div>
                                <div class="comp-desc">Ajukan dan pantau ruang meeting Zoom melalui sistem yang terintegrasi.</div>
                            </div>
                        </div>
                        <div class="comp-item">
                            <div class="comp-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                                    <path d="M8 21h8m-4-4v4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="comp-title">Dashboard</div>
                                <div class="comp-desc">Lihat ringkasan status terkini dan statistik layanan secara cepat.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

    <?php echo $__env->make('partials.landing-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>
</html>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/pages/documentation.blade.php ENDPATH**/ ?>