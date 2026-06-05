<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'ITSM Dashboard')); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('logo/logo.png')); ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('logo/logo.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('logo/logo.png')); ?>">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php if(auth()->guard()->check()): ?>
        <script>window.Auth = { user: <?php echo json_encode(auth()->user()); ?> };</script>
    <?php endif; ?>
    <script>
        window.baseUrl = '<?php echo e(url('')); ?>';
        window.apiBaseUrl = '<?php echo e(url('/api')); ?>';
    </script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    this.theme = savedTheme || systemTheme;
                    this.updateTheme();
                },
                theme: 'light',
                get isDark() {
                    return this.theme === 'dark';
                },
                updateTheme() {
                    const html = document.documentElement;
                    if (this.isDark) {
                        html.classList.add('dark');
                    } else {
                        html.classList.remove('dark');
                    }
                    localStorage.setItem('theme', this.theme);
                },
                toggle() {
                    this.theme = this.isDark ? 'light' : 'dark';
                    this.updateTheme();
                }
            });

            Alpine.store('sidebar', {
                init() {
                    const savedState = localStorage.getItem('sidebarOpen');
                    const isMobile = window.innerWidth < 1024;
                    this.isOpen = isMobile ? false : (savedState === null ? true : savedState === 'true');
                    
                    window.addEventListener('resize', () => {
                        const nowMobile = window.innerWidth < 1024;
                        if (nowMobile && this.isOpen) {
                            this.close();
                        }
                    });
                },
                isOpen: true,
                toggle() {
                    this.isOpen = !this.isOpen;
                    localStorage.setItem('sidebarOpen', this.isOpen);
                },
                open() {
                    this.isOpen = true;
                    localStorage.setItem('sidebarOpen', 'true');
                },
                close() {
                    this.isOpen = false;
                    localStorage.setItem('sidebarOpen', 'false');
                },
                closeOnMobile() {
                    if (window.innerWidth < 1024) {
                        this.close();
                    }
                }
            });
        });
    </script>
</head>
<body class="h-full bg-gray-50 dark:bg-dark-900" x-data x-init="$store.theme.init(); $store.sidebar.init()">
    <div class="flex h-full bg-gray-50 dark:bg-dark-900">
        <!-- Sidebar -->
        <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        
        <!-- Main Content -->
        <div class="flex flex-col h-full overflow-hidden transition-all duration-300" :style="{ marginLeft: $store.sidebar.isOpen ? '16rem' : '0', width: $store.sidebar.isOpen ? 'calc(100% - 16rem)' : '100%' }">
            <!-- Top Header -->
            <?php echo $__env->make('layouts.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-auto overflow-x-hidden">
                <!-- Flash Messages -->
                <div class="fixed inset-x-0 top-20 z-50 flex justify-center px-4 sm:px-6 lg:px-8 pointer-events-none">
                    <div class="w-full max-w-3xl">
                        <?php if(session('success')): ?>
                            <div x-data="{show: true}" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="rounded-xl border border-green-500 bg-green-50 p-4 dark:border-green-500/30 dark:bg-green-500/15 shadow-lg ring-1 ring-green-500/20 pointer-events-auto">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        <div class="text-green-500">
                                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18.75C14.9706 18.75 19 14.7206 19 9.75C19 4.77944 14.9706 0.75 10 0.75C5.02944 0.75 1 4.77944 1 9.75C1 14.7206 5.02944 18.75 10 18.75ZM14.7813 7.03125C15.1328 6.65625 15.1328 6.09375 14.7813 5.71875C14.4375 5.33125 13.8438 5.33125 13.5 5.71875L8.75 10.5938L6.5 8.33125C6.15625 7.9375 5.5625 7.9375 5.21875 8.33125C4.875 8.70625 4.875 9.26875 5.21875 9.64375L7.9375 12.375C8.28125 12.75 8.875 12.75 9.21875 12.375L14.7813 7.03125Z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-green-600 dark:text-green-500"><?php echo e(session('success')); ?></p>
                                    </div>
                                    <button type="button" @click="show = false" class="text-green-500 hover:text-green-700 dark:hover:text-green-300">&times;</button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if(session('error')): ?>
                            <div x-data="{show: true}" x-show="show" x-transition class="rounded-xl border border-red-500 bg-red-50 p-4 dark:border-red-500/30 dark:bg-red-500/15 shadow-lg ring-1 ring-red-500/20 pointer-events-auto">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        <div class="text-red-500">
                                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18.75C14.9706 18.75 19 14.7206 19 9.75C19 4.77944 14.9706 0.75 10 0.75C5.02944 0.75 1 4.77944 1 9.75C1 14.7206 5.02944 18.75 10 18.75ZM10.75 14.25C10.75 14.6642 10.4142 15 10 15C9.5858 15 9.25 14.6642 9.25 14.25C9.25 13.8358 9.5858 13.5 10 13.5C10.4142 13.5 10.75 13.8358 10.75 14.25ZM9.25 6.75C9.25 6.3358 9.5858 6 10 6C10.4142 6 10.75 6.3358 10.75 6.75V11.25C10.75 11.6642 10.4142 12 10 12C9.5858 12 9.25 11.6642 9.25 11.25V6.75Z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-red-600 dark:text-red-500"><?php echo e(session('error')); ?></p>
                                    </div>
                                    <button type="button" @click="show = false" class="text-red-500 hover:text-red-700 dark:hover:text-red-300">&times;</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>


                <?php echo e($slot); ?>

            </main>
        </div>
    </div>

    <!-- Alpine.js Theme Toggle (uncomment if needed) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/layouts/app.blade.php ENDPATH**/ ?>