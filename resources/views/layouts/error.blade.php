<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error - TimCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brand: #2563eb;
            --brand-light: #eff6ff;
            --brand-mid: #bfdbfe;
            --brand-dark: #1d4ed8;
            --red: #dc2626;
            --red-light: #fef2f2;
            --red-mid: #fecaca;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --white: #ffffff;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--slate-50);
            color: var(--slate-800);
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        .page-content {
            flex: 1;
            overflow-y: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            width: 100%;
        }

        .error-card {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,.08);
            width: 100%;
            max-width: 680px;
            overflow: hidden;
        }

        .error-card-inner {
            display: grid;
            grid-template-columns: 200px 1fr;
        }

        @media (max-width: 600px) {
            .error-card-inner { grid-template-columns: 1fr; }
            .error-visual { border-right: none; border-bottom: 1px solid var(--slate-100); padding: 32px 24px; }
        }

        .error-visual {
            background: var(--red-light);
            border-right: 1px solid var(--red-mid);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
            gap: 16px;
        }

        .error-icon-wrap {
            width: 80px;
            height: 80px;
            background: var(--white);
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(220,38,38,.15);
        }

        .error-icon-wrap svg { width: 40px; height: 40px; color: var(--red); }

        .error-code {
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .15em;
            text-transform: uppercase;
            color: var(--red);
            background: var(--red-mid);
            padding: 4px 12px;
            border-radius: 999px;
        }

        .error-body { padding: 44px 40px; }

        .error-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--slate-900);
            margin-bottom: 10px;
        }

        .error-msg {
            font-size: .85rem;
            color: var(--slate-500);
            line-height: 1.65;
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }

        .error-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: .875rem;
            font-weight: 600;
            padding: 9px 18px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-family: inherit;
            transition: background .15s, transform .1s;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn svg { width: 15px; height: 15px; }

        .btn-primary {
            background: var(--brand);
            color: var(--white);
        }

        .btn-primary:hover { background: var(--brand-dark); }

        .btn-outline {
            background: var(--white);
            color: var(--slate-700);
            border: 1px solid var(--slate-200);
        }

        .btn-outline:hover {
            background: var(--slate-50);
            border-color: var(--slate-300);
        }

        .btn-ghost {
            background: transparent;
            color: var(--slate-500);
        }

        .btn-ghost:hover {
            background: var(--slate-100);
            color: var(--slate-700);
        }

        .error-hint {
            font-size: .78rem;
            color: var(--slate-400);
        }

        .error-hint a {
            color: var(--brand);
            text-decoration: none;
        }

        .error-hint a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    {{ $slot }}
</body>
</html>
