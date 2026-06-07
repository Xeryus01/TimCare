<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kontak - TimCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('logo/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        .contact-grid { grid-template-columns: 1fr 1fr; }
        @media (max-width: 1024px) { .contact-grid { grid-template-columns: 1fr; } }

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

        .card h2 { font-size: 1.25rem; font-weight: 700; color: var(--slate-900); margin-bottom: 18px; }
        .card p { font-size: .95rem; color: var(--slate-600); line-height: 1.75; }
        .meta-list { display: grid; gap: 18px; }
        .meta-item p:first-child { font-weight: 700; color: var(--slate-900); margin-bottom: 4px; }

        .support-list { display: flex; flex-direction: column; gap: 14px; }
        .support-list span { font-weight: 600; color: var(--slate-800); }
    </style>
</head>
<body>
    @include('partials.landing-navigation')

<main>
    <div class="container">

        <div class="hero">
            <div class="hero-tag">Kontak</div>
            <h1>Hubungi Tim Support TimCare</h1>
            <p>Butuh bantuan? Hubungi kami melalui email, telepon, atau menu dukungan untuk mendapatkan respons cepat.</p>
        </div>

        <div class="grid contact-grid">
            <div class="card">
                <h2>Informasi Kontak</h2>
                <div class="meta-list">
                    <div class="meta-item">
                        <p>Email</p>
                        <p>ridhoakbar@bps.go.id</p>
                    </div>
                    <div class="meta-item">
                        <p>Telepon</p>
                        <p>+62 851 1136 1900</p>
                    </div>
                    <div class="meta-item">
                        <p>Jam Operasional</p>
                        <p>Senin - Kamis, 07.30 - 16.00 WIB</p>
                        <p>Jumat, 07:30 - 16:30 WIB</p>
                    </div>
                </div>
            </div>
            <div class="card">
                <h2>Dukungan Online</h2>
                <p>Jika mengalami kendala saat menggunakan TimCare, silakan kirim pesan melalui channel berikut:</p>
                <br>
                <div class="support-list">
                    <div><span>Email Support:</span> ridhoakbar@bps.go.id</div>
                    <div><span>WhatsApp:</span> +62 851 1136 1900</div>
                    <div><span>Formulir Tiket:</span> Gunakan menu tiket di dashboard untuk laporan masalah teknis.</div>
                </div>
            </div>
        </div>

        <div class="card card-accent" style="margin-top: 32px;">
            <h2>Lokasi dan Layanan</h2>
            <p>Jika membutuhkan bantuan on-site atau konsultasi langsung, koordinasikan dengan tim IT internal Anda untuk jadwal kunjungan dan dukungan teknis.</p>
        </div>
    </div>
</main>

    @include('partials.landing-footer')

</body>
</html>
