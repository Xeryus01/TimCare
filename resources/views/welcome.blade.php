<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TimCare - Sistem Helpdesk IT Terintegrasi</title>
    <meta name="description" content="Sistem helpdesk IT terintegrasi untuk pengelolaan tiket permasalahan, pengajuan ruang Zoom, dan layanan IT lainnya.">
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('logo/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo/logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html {
            scroll-behavior: smooth;
        }

        .landing-hero {
            background:
                radial-gradient(ellipse at 82% 18%, rgb(191 219 254 / 55%), transparent 34rem),
                linear-gradient(145deg, #f8fbff 0%, #eff6ff 54%, #f8fafc 100%);
        }

        .landing-preview {
            background: linear-gradient(145deg, #0f172a, #172554 72%, #1e3a8a);
            box-shadow: 0 32px 80px -30px rgb(30 64 175 / 42%);
        }

        .feature-card {
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            border-color: #bfdbfe;
            box-shadow: 0 18px 35px -24px rgb(15 23 42 / 30%);
        }

        .landing-menu summary {
            list-style: none;
        }

        .landing-menu summary::-webkit-details-marker {
            display: none;
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            .feature-card {
                transition: none;
            }
        }
    </style>
</head>
<body class="h-full bg-gray-50">

    @include('partials.landing-navigation')

    <main>
        <section class="landing-hero relative overflow-hidden">
            <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-[1.05fr_.95fr] lg:gap-16 lg:px-8 lg:py-24">
                <div class="relative z-10">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-blue-200 bg-white/80 px-3.5 py-2 text-xs font-semibold text-blue-800 shadow-sm sm:text-sm">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                        Satu pintu untuk layanan teknologi informasi
                    </div>
                    <h1 class="max-w-2xl text-4xl font-extrabold leading-[1.08] tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                        Urusan IT lebih mudah bersama <span class="bg-gradient-to-r from-blue-700 to-indigo-600 bg-clip-text text-transparent">TimCare.</span>
                    </h1>
                    <p class="mt-6 max-w-xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">
                        Sampaikan kendala, ajukan kebutuhan rapat, dan pantau tindak lanjut layanan IT dari satu tempat yang praktis.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        @auth
                            <a href="{{ url()->to(route('tickets.create')) }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-700/20 transition hover:-translate-y-0.5 hover:bg-blue-800">
                                Buat tiket layanan
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17 17 7M7 7h10v10"/></svg>
                            </a>
                            <a href="{{ url()->to(route('reservations.create')) }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50">Ajukan ruang Zoom</a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-700/20 transition hover:-translate-y-0.5 hover:bg-blue-800">
                                Masuk ke TimCare
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17 17 7M7 7h10v10"/></svg>
                            </a>
                            <a href="#features" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-300 bg-white/80 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-white">Jelajahi layanan</a>
                        @endauth
                    </div>
                    <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-slate-600">
                        <span class="inline-flex items-center gap-2"><svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/></svg>Tiket tercatat rapi</span>
                        <span class="inline-flex items-center gap-2"><svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/></svg>Progres mudah dipantau</span>
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-xl">
                    <div class="absolute -left-8 top-12 h-32 w-32 rounded-full bg-cyan-300/50 blur-3xl" aria-hidden="true"></div>
                    <div class="absolute -bottom-8 -right-4 h-40 w-40 rounded-full bg-indigo-300/60 blur-3xl" aria-hidden="true"></div>
                    <div class="landing-preview relative overflow-hidden rounded-[1.75rem] p-4 text-white sm:p-6" aria-label="Ilustrasi tampilan layanan TimCare">
                        <div class="flex items-center justify-between border-b border-white/10 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10">
                                    <x-application-logo class="h-7 w-auto" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold">Pusat Layanan</p>
                                    <p class="text-xs text-blue-100/70">Ringkasan permintaan</p>
                                </div>
                            </div>
                            <!-- <span class="rounded-full border border-emerald-300/20 bg-emerald-300/10 px-2.5 py-1 text-[11px] font-semibold text-emerald-200">Ilustrasi</span> -->
                        </div>
                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4">
                                <p class="text-xs text-blue-100/70">Layanan tersedia</p>
                                <p class="mt-2 text-lg font-bold">Tiket & Zoom</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4">
                                <p class="text-xs text-blue-100/70">Status permintaan</p>
                                <p class="mt-2 flex items-center gap-2 text-lg font-bold"><span class="h-2 w-2 rounded-full bg-emerald-300"></span>Terpantau</p>
                            </div>
                        </div>
                        <div class="mt-5 rounded-2xl border border-white/10 bg-slate-950/25 p-4 sm:p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <p class="text-sm font-semibold">Alur layanan</p>
                                <span class="text-xs text-blue-100/60">Sederhana & jelas</span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 rounded-xl bg-white/[0.06] p-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-400/15 text-blue-200"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg></span>
                                    <div class="min-w-0 flex-1"><p class="text-sm font-semibold">Kirim kebutuhan</p><p class="mt-0.5 text-xs text-blue-100/60">Sampaikan kendala atau permintaan</p></div>
                                    <span class="text-xs font-medium text-blue-200">01</span>
                                </div>
                                <div class="flex items-center gap-3 rounded-xl bg-white/[0.06] p-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-400/15 text-violet-200"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                                    <div class="min-w-0 flex-1"><p class="text-sm font-semibold">Tim menindaklanjuti</p><p class="mt-0.5 text-xs text-blue-100/60">Progres tercatat di sistem</p></div>
                                    <span class="text-xs font-medium text-violet-200">02</span>
                                </div>
                                <div class="flex items-center gap-3 rounded-xl bg-white/[0.06] p-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-400/15 text-emerald-200"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 12 4 4L19 6"/></svg></span>
                                    <div class="min-w-0 flex-1"><p class="text-sm font-semibold">Pantau hasilnya</p><p class="mt-0.5 text-xs text-blue-100/60">Informasi tersimpan dalam satu tempat</p></div>
                                    <span class="text-xs font-medium text-emerald-200">03</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between px-1 text-xs text-blue-100/60">
                            <span>Layanan IT terintegrasi</span>
                            <span class="inline-flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>Siap membantu</span>
                        </div>
                    </div>
                    <div class="absolute -bottom-5 -left-5 hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-xl sm:block">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                            <div><p class="text-xs font-bold text-slate-900">Lebih terorganisir</p><p class="text-[11px] text-slate-500">Satu alur untuk setiap permintaan</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php
            $schedule = \App\Models\PiketSchedule::getCurrentWeek();
            $colorMap = \App\Models\PiketSchedule::getTechnicianColorMap();
            $piketData = [
                ['lokasi' => 'Petugas 1', 'nama' => $schedule->technician_1],
                ['lokasi' => 'Petugas 2', 'nama' => $schedule->technician_2],
                ['lokasi' => 'Petugas 3', 'nama' => $schedule->technician_3],
            ];
        ?>
        <section class="border-y border-slate-200 bg-white">
            <div class="mx-auto grid max-w-7xl gap-6 px-4 py-7 sm:px-6 md:grid-cols-[.8fr_2fr] md:items-center lg:px-8">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.16em] text-blue-700">Tim yang bertugas</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-900">Tim piket minggu ini</h2>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach($piketData as $item)
                        <?php $colors = $colorMap[$item['nama']] ?? ['dot' => 'bg-indigo-500', 'accent' => 'from-indigo-400 to-indigo-500']; ?>
                        <div class="flex min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $colors['dot'] }}"></span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ $item['lokasi'] }}</p>
                                <p class="truncate text-sm font-semibold text-slate-900" title="{{ $item['nama'] }}">{{ $item['nama'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @auth
                    @if(auth()->user()->hasRole('Admin'))
                        <div class="md:col-start-2">
                            <a href="{{ route('piket.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Kelola jadwal piket <span aria-hidden="true">→</span></a>
                        </div>
                    @endif
                @endauth
            </div>
        </section>
    <!-- Features Section -->
    <section id="features" class="bg-white py-16 sm:py-20 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-10 max-w-2xl text-center sm:mb-14">
                <p class="mb-3 text-xs font-bold uppercase tracking-[.18em] text-blue-700">Layanan dalam satu platform</p>
                <h2 class="mb-4 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                    Fitur Unggulan TimCare
                </h2>
                <p class="text-base leading-7 text-slate-600">Dari pengajuan hingga tindak lanjut, semua kebutuhan layanan IT dibuat lebih jelas dan mudah diakses.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Tiket Permasalahan -->
                <div class="feature-card rounded-2xl border border-slate-200 bg-slate-50/80 p-6 shadow-sm">
                    <div class="w-12 h-12 bg-brand-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Tiket Permasalahan IT</h3>
                    <p class="text-gray-600 text-sm sm:text-base">
                        Ajukan tiket permasalahan hardware, software, jaringan, dan masalah IT lainnya dengan mudah.
                    </p>
                </div>

                <!-- Pengajuan Zoom -->
                <div class="feature-card rounded-2xl border border-slate-200 bg-slate-50/80 p-6 shadow-sm">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Pengajuan Ruang Zoom</h3>
                    <p class="text-gray-600 text-sm sm:text-base">
                        Ajukan ruang meeting Zoom dengan nota dinas dan dapatkan link meeting dari petugas IT.
                    </p>
                </div>

                <!-- Real-time Updates -->
                <div class="feature-card rounded-2xl border border-slate-200 bg-slate-50/80 p-6 shadow-sm">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Update Real-time</h3>
                    <p class="text-gray-600 text-sm sm:text-base">
                        Terima notifikasi real-time tentang status tiket dan pengajuan melalui email dan dashboard.
                    </p>
                </div>

                <!-- Attachment Support -->
                <div class="feature-card rounded-2xl border border-slate-200 bg-slate-50/80 p-6 shadow-sm">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Dukungan Lampiran</h3>
                    <p class="text-gray-600 text-sm sm:text-base">
                        Upload gambar, dokumen PDF, dan file pendukung lainnya untuk memudahkan penyelesaian masalah.
                    </p>
                </div>

                <!-- Role-based Access -->
                <div class="feature-card rounded-2xl border border-slate-200 bg-slate-50/80 p-6 shadow-sm">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Akses Berbasis Role</h3>
                    <p class="text-gray-600 text-sm sm:text-base">
                        Sistem keamanan dengan role Admin, Teknisi, dan User untuk kontrol akses yang tepat.
                    </p>
                </div>

                <!-- Dashboard Analytics -->
                <div class="feature-card rounded-2xl border border-slate-200 bg-slate-50/80 p-6 shadow-sm">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Dashboard Analytics</h3>
                    <p class="text-gray-600 text-sm sm:text-base">
                        Pantau performa layanan IT dengan dashboard yang menampilkan statistik dan ringkasan lengkap.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-slate-50 py-16 sm:py-20 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-14 lg:mb-16">
                <p class="text-xs sm:text-sm font-semibold uppercase tracking-[0.3em] text-brand-600 mb-2 sm:mb-3">Statistik</p>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">Dampak TimCare</h2>
                <p class="max-w-2xl mx-auto text-sm sm:text-base text-gray-600 px-2">
                    Ringkasan kinerja sistem helpdesk IT yang menunjukkan aktivitas, penggunaan, dan layanan yang telah selesai.
                </p>
            </div>

            <div class="grid gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl sm:rounded-3xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm hover:shadow-lg transition-shadow">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-brand-100 text-brand-600 mb-4">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-2">{{ \App\Models\Ticket::count() }}</div>
                    <p class="text-sm text-gray-500">Tiket Tercatat</p>
                </div>

                <div class="rounded-2xl sm:rounded-3xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm hover:shadow-lg transition-shadow">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-green-100 text-green-600 mb-4">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-2">{{ \App\Models\Reservation::count() }}</div>
                    <p class="text-sm text-gray-500">Ruang Zoom Diajukan</p>
                </div>

                <div class="rounded-2xl sm:rounded-3xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm hover:shadow-lg transition-shadow">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-blue-100 text-blue-600 mb-4">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-2">{{ \App\Models\User::count() }}</div>
                    <p class="text-sm text-gray-500">Pengguna Terdaftar</p>
                </div>

                <div class="rounded-2xl sm:rounded-3xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm hover:shadow-lg transition-shadow">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-purple-100 text-purple-600 mb-4">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        </svg>
                    </div>
                    <div class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-2">
                        {{ \App\Models\Ticket::whereIn('status', [\App\Models\Ticket::STATUS_SOLVED, \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES])->count() + \App\Models\Reservation::where('status', 'COMPLETED')->count() }}
                    </div>
                    <p class="text-sm text-gray-500">Layanan Selesai</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-slate-950 py-16 text-white sm:py-20">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-blue-950 via-slate-900 to-indigo-950 px-6 py-10 text-center shadow-2xl shadow-slate-950/20 sm:px-12 sm:py-14">
                <p class="mb-3 text-xs font-bold uppercase tracking-[.18em] text-blue-300">Mulai sekarang</p>
                <h2 class="mb-4 text-3xl font-extrabold tracking-tight sm:text-4xl">Layanan IT yang lebih mudah dimulai di sini.</h2>
                <p class="mx-auto mb-7 max-w-2xl text-sm leading-7 text-slate-300 sm:text-base">Gunakan TimCare untuk menyampaikan kebutuhan dan mengikuti perkembangannya dengan lebih nyaman.</p>
                <div class="flex flex-col justify-center gap-3 sm:flex-row">
                @auth
                    <a href="{{ url()->to(route('tickets.create')) }}" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-blue-800 transition hover:bg-blue-50">Ajukan Tiket Sekarang</a>
                @else
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-blue-800 transition hover:bg-blue-50">Daftar Akun</a>
                    @endif
                    <a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-white/25 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Masuk</a>
                @endauth
                </div>
            </div>
        </div>
    </section>
    </main>

    @include('partials.landing-footer')

</body>
</html>
