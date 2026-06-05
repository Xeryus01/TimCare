<!-- Sidebar -->
<aside class="fixed top-0 left-0 z-50 h-screen w-64 overflow-y-auto overflow-x-hidden bg-white dark:bg-dark-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out" :style="{ transform: $store.sidebar.isOpen ? 'translateX(0)' : 'translateX(-100%)', width: '16rem' }">
    <!-- Logo -->
    <div class="flex items-center justify-between px-4 py-4 sm:px-5.5 sm:py-6.5 lg:py-7.5">
        <a href="<?php echo e(url()->to(route('dashboard'))); ?>" class="block" @click="$store.sidebar.closeOnMobile()">
            <img src="<?php echo e(asset(file_exists(public_path('logo/full-logo.png')) ? 'logo/full-logo.png' : 'logo/logo.png')); ?>" alt="<?php echo e(config('app.name', 'TimCare')); ?>" class="h-12 w-auto object-contain sm:h-14" />
        </a>
    </div>

    <!-- Navigation Menu -->
    <nav class="px-3 py-4 lg:px-5.5">
        <ul class="space-y-1.5">
            <!-- Dashboard -->
            <li>
                <a href="<?php echo e(url()->to(route('dashboard'))); ?>" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 <?php echo e(request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5'); ?>">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 4C2 2.89543 2.89543 2 4 2H8C9.10457 2 10 2.89543 10 4V8C10 9.10457 9.10457 10 8 10H4C2.89543 10 2 9.10457 2 8V4Z"></path>
                        <path d="M12 4C12 2.89543 12.8954 2 14 2H16C17.1046 2 18 2.89543 18 4V8C18 9.10457 17.1046 10 16 10H14C12.8954 10 12 9.10457 12 8V4Z"></path>
                        <path d="M2 12C2 10.8954 2.89543 10 4 10H8C9.10457 10 10 10.8954 10 12V16C10 17.1046 9.10457 18 8 18H4C2.89543 18 2 17.1046 2 16V12Z"></path>
                        <path d="M12 12C12 10.8954 12.8954 10 14 10H16C17.1046 10 18 10.8954 18 12V16C18 17.1046 17.1046 18 16 18H14C12.8954 18 12 17.1046 12 16V12Z"></path>
                    </svg>
                    <span>Beranda</span>
                </a>
            </li>

            <!-- Tickets -->
            <li>
                <a href="<?php echo e(url()->to(route('tickets.index'))); ?>" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 <?php echo e(request()->routeIs('tickets.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5'); ?>">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 2C2.89543 2 2 2.89543 2 4V16C2 17.1046 2.89543 18 4 18H16C17.1046 18 18 17.1046 18 16V4C18 2.89543 17.1046 2 16 2H4ZM4 4H16V16H4V4Z"></path>
                        <path d="M6 7C5.44772 7 5 7.44772 5 8C5 8.55228 5.44772 9 6 9H14C14.5523 9 15 8.55228 15 8C15 7.44772 14.5523 7 14 7H6Z"></path>
                        <path d="M6 11C5.44772 11 5 11.4477 5 12C5 12.5523 5.44772 13 6 13H14C14.5523 13 15 12.5523 15 12C15 11.4477 14.5523 11 14 11H6Z"></path>
                    </svg>
                    <span>Tiket Masalah TI</span>
                </a>
            </li>

            <!-- Reservations -->
            <li>
                <a href="<?php echo e(url()->to(route('reservations.index'))); ?>" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 <?php echo e(request()->routeIs('reservations.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5'); ?>">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 2C1.89543 2 1 2.89543 1 4V16C1 17.1046 1.89543 18 3 18H17C18.1046 18 19 17.1046 19 16V4C19 2.89543 18.1046 2 17 2H3ZM3 4H17V16H3V4Z"></path>
                        <path d="M2 8C1.44772 8 1 8.44772 1 9V9C1 9.55228 1.44772 10 2 10H18C18.5523 10 19 9.55228 19 9V9C19 8.44772 18.5523 8 18 8H2Z"></path>
                    </svg>
                    <span>Tiket ID Zoom</span>
                </a>
            </li>

            <!-- Assets -->
            <li>
                <a href="<?php echo e(url()->to(route('assets.index'))); ?>" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 <?php echo e(request()->routeIs('assets.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5'); ?>">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 4C1.89543 4 1 4.89543 1 6V14C1 15.1046 1.89543 16 3 16H17C18.1046 16 19 15.1046 19 14V6C19 4.89543 18.1046 4 17 4H3ZM3 6H17V14H3V6Z"></path>
                        <path d="M5 9C4.44772 9 4 9.44772 4 10C4 10.5523 4.44772 11 5 11H7C7.55228 11 8 10.5523 8 10C8 9.44772 7.55228 9 7 9H5Z"></path>
                    </svg>
                    <span>Data Aset</span>
                </a>
            </li>

            <!-- Jadwal Piket - Teknisi -->
            <?php if(auth()->user()->hasRole('Teknisi')): ?>
            <li>
                <a href="<?php echo e(url()->to(route('piket.view'))); ?>" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 <?php echo e(request()->routeIs('piket.view') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5'); ?>">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 2C4.89543 2 4 2.89543 4 4V6H2V8H4V10H2V12H4V14H2V16H4V18H6V16H8V18H10V16H12V18H14V16H16V18H18V16H16V14H18V12H16V10H18V8H16V6H14V4C14 2.89543 13.1046 2 12 2H6ZM6 4H12V6H6V4ZM6 8H8V10H6V8ZM10 8H12V10H10V8ZM6 12H8V14H6V12ZM10 12H12V14H10V12Z"></path>
                    </svg>
                    <span>Jadwal Piket</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- User Management - Admin Only -->
            <?php if(auth()->user()->hasRole('Admin')): ?>
            <li>
                <a href="<?php echo e(url()->to(route('users.index'))); ?>" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 <?php echo e(request()->routeIs('users.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5'); ?>">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM10 4C11.6569 4 13 5.34315 13 7C13 8.65685 11.6569 10 10 10C8.34315 10 7 8.65685 7 7C7 5.34315 8.34315 4 10 4ZM10 12C7.33579 12 4.25 13.2403 4.25 15.25C4.25 16.3856 7.21 17 10 17C12.79 17 15.75 16.3856 15.75 15.25C15.75 13.2403 12.6642 12 10 12Z"></path>
                    </svg>
                    <span>Manajemen User</span>
                </a>
            </li>

            <!-- Jadwal Piket - Admin Only -->
            <?php if(auth()->user()->hasRole('Admin')): ?>
            <li>
                <a href="<?php echo e(url()->to(route('piket.index'))); ?>" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 <?php echo e(request()->routeIs('piket.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5'); ?>">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 2C4.89543 2 4 2.89543 4 4V6H2V8H4V10H2V12H4V14H2V16H4V18H6V16H8V18H10V16H12V18H14V16H16V18H18V16H16V14H18V12H16V10H18V8H16V6H14V4C14 2.89543 13.1046 2 12 2H6ZM6 4H12V6H6V4ZM6 8H8V10H6V8ZM10 8H12V10H10V8ZM6 12H8V14H6V12ZM10 12H12V14H10V12Z"></path>
                    </svg>
                    <span>Jadwal Piket</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Divider -->
    <div class="my-4 border-t border-gray-200 dark:border-gray-700"></div>

    <!-- Settings Section -->
    <nav class="px-3 py-4 lg:px-5.5">
        <ul class="space-y-1.5">
            <!-- Profile -->
            <li>
                <a href="<?php echo e(url()->to(route('profile.edit'))); ?>" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 <?php echo e(request()->routeIs('profile.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5'); ?>">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM10 4C11.6569 4 13 5.34315 13 7C13 8.65685 11.6569 10 10 10C8.34315 10 7 8.65685 7 7C7 5.34315 8.34315 4 10 4ZM10 12C7.33579 12 4.25 13.2403 4.25 15.25C4.25 16.3856 7.21 17 10 17C12.79 17 15.75 16.3856 15.75 15.25C15.75 13.2403 12.6642 12 10 12Z"></path>
                    </svg>
                    <span>Profil</span>
                </a>
            </li>

            <!-- Logout -->
            <li>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-full text-left group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5">
                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.5 2.5C13.5 2.22386 13.7239 2 14 2H16C17.1046 2 18 2.89543 18 4V16C18 17.1046 17.1046 18 16 18H14C13.7239 18 13.5 17.7761 13.5 17.5C13.5 17.2239 13.7239 17 14 17H16V4H14C13.7239 4 13.5 3.77614 13.5 3.5V2.5Z"></path>
                            <path d="M10.3536 7.14645C10.5488 6.95118 10.5488 6.63858 10.3536 6.44332C10.1583 6.24805 9.84568 6.24805 9.65042 6.44332L5.79082 10.3029C5.2 10.8937 5.2 11.8437 5.79082 12.4345L9.65042 16.2941C9.84568 16.4893 10.1583 16.4893 10.3536 16.2941C10.5488 16.0988 10.5488 15.7862 10.3536 15.5909L6.98528 12.2224H14C14.2761 12.2224 14.5 11.9985 14.5 11.7224C14.5 11.4463 14.2761 11.2224 14 11.2224H6.98528L10.3536 7.85413C10.5488 7.65887 10.5488 7.34627 10.3536 7.15101V7.14645Z"></path>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</aside>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>