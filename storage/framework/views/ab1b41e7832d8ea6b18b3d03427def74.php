<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>TimCare - Sistem Helpdesk IT Terintegrasi</title>
    <meta name="description" content="Sistem helpdesk IT terintegrasi untuk pengelolaan tiket permasalahan, pengajuan ruang Zoom, dan layanan IT lainnya.">
    <link rel="icon" type="image/png" href="<?php echo e(asset('logo/logo.png')); ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('logo/logo.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('logo/logo.png')); ?>">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .feature-card:hover {
            transform: translateY(-4px);
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="h-full bg-gray-50">

    <?php echo $__env->make('partials.landing-navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Hero Section -->
    <section class="hero-gradient text-white min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="text-center mt-4">
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-bold mb-6 sm:mb-8 leading-tight">
                    Tim<span class="text-brand-200">Care</span>
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl mb-8 sm:mb-12 text-gray-100 max-w-4xl mx-auto px-2 leading-relaxed">
                    Kelola tiket permasalahan IT, ajukan ruang Zoom, dan pantau layanan IT dengan mudah dan efisien.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-5 justify-center px-2 mb-8">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(url('/dashboard')); ?>" class="bg-white text-gray-900 px-8 sm:px-10 py-3 sm:py-4 rounded-lg font-bold hover:bg-gray-100 transition-colors text-base sm:text-lg">
                            Buka Dashboard
                        </a>
                        <a href="<?php echo e(url()->to(route('tickets.create'))); ?>" class="border-2 border-white text-white px-8 sm:px-10 py-3 sm:py-4 rounded-lg font-bold hover:bg-white hover:text-gray-900 transition-colors text-base sm:text-lg">
                            Ajukan Tiket Baru
                        </a>
                        <a href="<?php echo e(url()->to(route('reservations.create'))); ?>" class="border-2 border-white text-white px-8 sm:px-10 py-3 sm:py-4 rounded-lg font-bold hover:bg-white hover:text-gray-900 transition-colors text-base sm:text-lg hidden sm:inline-block">
                            Ajukan Ruang Zoom
                        </a>
                    <?php else: ?>
                        <?php if(Route::has('register')): ?>
                            <a href="<?php echo e(url()->to(route('register'))); ?>" class="bg-white text-gray-900 px-8 sm:px-10 py-3 sm:py-4 rounded-lg font-bold hover:bg-gray-100 transition-colors text-base sm:text-lg">
                                Mulai Sekarang
                            </a>
                        <?php endif; ?>
                        <a href="#features" class="border-2 border-white text-white px-8 sm:px-10 py-3 sm:py-4 rounded-lg font-bold hover:bg-white hover:text-gray-900 transition-colors text-base sm:text-lg">
                            Pelajari Lebih Lanjut
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Jadwal Piket Clean -->
                <?php
                    $schedule = \App\Models\PiketSchedule::getCurrentWeek();
                    $colorMap = \App\Models\PiketSchedule::getTechnicianColorMap();
                    $piketData = [
                        ['lokasi' => 'Petugas 1', 'nama' => $schedule->technician_1],
                        ['lokasi' => 'Petugas 2', 'nama' => $schedule->technician_2],
                        ['lokasi' => 'Petugas 3', 'nama' => $schedule->technician_3],
                    ];
                ?>
                <div class="pt-8 mb-8 border-t border-white/20">
                    <div class="text-center mb-4">
                        <p class="text-xs font-semibold text-gray-200 uppercase tracking-widest">Tim Piket Hari Ini</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <?php $__currentLoopData = $piketData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php 
                                $colors = $colorMap[$item['nama']] ?? ['dot' => 'bg-indigo-400', 'accent' => 'from-indigo-400 to-indigo-500'];
                            ?>
                            <div class="group relative w-full">
                                <div class="absolute inset-0 bg-gradient-to-r <?php echo e($colors['accent']); ?> rounded-md blur opacity-20 group-hover:opacity-40 transition-all duration-300"></div>
                                <div class="relative bg-white/5 backdrop-blur-sm border border-white/20 rounded-md px-4 py-4 hover:border-white/40 hover:bg-white/10 transition-all duration-300 flex flex-col items-center gap-2">
                                    <div class="h-2 w-2 <?php echo e($colors['dot']); ?> rounded-full"></div>
                                    <div class="text-center w-full min-w-0">
                                        <p class="text-xs text-gray-300 font-medium uppercase tracking-wider"><?php echo e($item['lokasi']); ?></p>
                                        <p class="text-sm font-bold text-white truncate" title="<?php echo e($item['nama']); ?>">
                                            <?php echo e($item['nama']); ?>

                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(auth()->user()->hasRole('Admin')): ?>
                            <div class="flex justify-center mt-6">
                                <a href="<?php echo e(url()->to(route('piket.index'))); ?>" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition font-medium text-sm">
                                    Kelola Jadwal Piket
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-12 sm:py-16 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10 sm:mb-14 lg:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Fitur Unggulan TimCare
                </h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Tiket Permasalahan -->
                <div class="feature-card bg-gray-50 p-6 rounded-xl border border-gray-200">
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
                <div class="feature-card bg-gray-50 p-6 rounded-xl border border-gray-200">
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
                <div class="feature-card bg-gray-50 p-6 rounded-xl border border-gray-200">
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
                <div class="feature-card bg-gray-50 p-6 rounded-xl border border-gray-200">
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
                <div class="feature-card bg-gray-50 p-6 rounded-xl border border-gray-200">
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
                <div class="feature-card bg-gray-50 p-6 rounded-xl border border-gray-200">
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
    <section class="py-12 sm:py-16 lg:py-20 bg-slate-50">
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
                    <div class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-2"><?php echo e(\App\Models\Ticket::count()); ?></div>
                    <p class="text-sm text-gray-500">Tiket Ditangani</p>
                </div>

                <div class="rounded-2xl sm:rounded-3xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm hover:shadow-lg transition-shadow">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-green-100 text-green-600 mb-4">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-2"><?php echo e(\App\Models\Reservation::count()); ?></div>
                    <p class="text-sm text-gray-500">Ruang Zoom Diajukan</p>
                </div>

                <div class="rounded-2xl sm:rounded-3xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm hover:shadow-lg transition-shadow">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-blue-100 text-blue-600 mb-4">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-2"><?php echo e(\App\Models\User::count()); ?></div>
                    <p class="text-sm text-gray-500">Pengguna Aktif</p>
                </div>

                <div class="rounded-2xl sm:rounded-3xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm hover:shadow-lg transition-shadow">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-purple-100 text-purple-600 mb-4">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        </svg>
                    </div>
                    <div class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-2">
                        <?php echo e(\App\Models\Ticket::whereIn('status', [\App\Models\Ticket::STATUS_SOLVED, \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES])->count() + \App\Models\Reservation::where('status', 'COMPLETED')->count()); ?>

                    </div>
                    <p class="text-sm text-gray-500">Layanan Selesai</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 sm:py-16 lg:py-20 bg-brand-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4">Siap Menggunakan TimCare?</h2>
            <p class="text-base sm:text-lg md:text-xl mb-6 sm:mb-8 text-brand-100">Bergabunglah dengan sistem helpdesk IT terintegrasi untuk kemudahan layanan IT Anda.</p>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center px-2">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(url()->to(route('tickets.create'))); ?>" class="bg-white text-brand-600 px-6 sm:px-8 py-2 sm:py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors text-sm sm:text-base">Ajukan Tiket Sekarang</a>
                <?php else: ?>
                    <?php if(Route::has('register')): ?>
                        <a href="<?php echo e(url()->to(route('register'))); ?>" class="bg-white text-brand-600 px-6 sm:px-8 py-2 sm:py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors text-sm sm:text-base">Daftar Akun</a>
                    <?php endif; ?>
                    <a href="<?php echo e(url()->to(route('login'))); ?>" class="border-2 border-white text-white px-6 sm:px-8 py-2 sm:py-3 rounded-lg font-semibold hover:bg-white hover:text-brand-600 transition-colors text-sm sm:text-base">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php echo $__env->make('partials.landing-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>
</html>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/welcome.blade.php ENDPATH**/ ?>