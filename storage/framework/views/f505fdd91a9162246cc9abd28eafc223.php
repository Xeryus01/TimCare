<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" x-data="{ dark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches) }" :class="{ 'dark': dark }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'ITSM Dashboard')); ?></title>

        <link rel="icon" type="image/png" href="<?php echo e(asset('logo/logo.png')); ?>">
        <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('logo/logo.png')); ?>">
        <link rel="apple-touch-icon" href="<?php echo e(asset('logo/logo.png')); ?>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="bg-gray-50 font-sans text-gray-900 antialiased dark:bg-dark-900 dark:text-gray-100">
        <div class="flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-md">
                <!-- Logo/Branding -->
                <div class="mb-8 text-center">
                    <a href="/" class="inline-flex items-center justify-center">
                        <img src="<?php echo e(asset(file_exists(public_path('logo/full-logo.png')) ? 'logo/full-logo.png' : 'logo/logo.png')); ?>" alt="<?php echo e(config('app.name', 'TimCare')); ?>" class="h-12 w-auto object-contain" />
                    </a>
                    <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(config('app.name', 'ITSM Dashboard')); ?></h1>
                </div>

                <!-- Card -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-dark-800 sm:p-8">
                    <?php echo e($slot); ?>

                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    </body>
</html>
    </body>
</html>
<?php /**PATH D:\projects\timcare - Copy\resources\views/layouts/guest.blade.php ENDPATH**/ ?>