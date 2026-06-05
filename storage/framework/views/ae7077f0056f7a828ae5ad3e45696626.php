<!-- Header -->
    <header class="sticky top-0 z-40 flex items-center justify-between border-b border-gray-200 bg-white px-4 py-3 sm:px-5.5 sm:py-4 lg:px-9 dark:border-gray-700 dark:bg-dark-800">
    <!-- Left Section -->
    <div class="flex items-center gap-3 sm:gap-4">
        <!-- Sidebar Toggle Button -->
        <button 
            @click="$store.sidebar.toggle()" 
            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/5 transition-colors lg:h-9 lg:w-9"
            title="Toggle sidebar"
        >
            <!-- Hamburger Icon -->
            <svg x-show="!$store.sidebar.isOpen" class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 6H21V8H3V6ZM3 11H21V13H3V11ZM3 16H21V18H3V16Z" fill="currentColor"/>
            </svg>
            <!-- Close Icon -->
            <svg x-show="$store.sidebar.isOpen" class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            </svg>
        </button>
    </div>

    <!-- Right Section -->
    <div class="flex items-center gap-2 sm:gap-3">
        <!-- Theme Toggle Button -->
        <button @click="$store.theme.toggle()" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/5 transition-colors lg:h-9 lg:w-9" title="Toggle dark mode">
            <!-- Moon Icon (shown in light mode - click to enable dark mode) -->
            <svg x-show="!$store.theme.isDark" class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.64 15.89C21.38 16.54 20.84 17.09 20.16 17.54C22.83 14.63 22.17 9.82 19.22 7.29C16.27 4.76 11.63 5.55 9.36 8.26C7.09 10.97 7.75 15.78 10.7 18.31C13.65 20.84 18.29 20.05 20.56 17.34C19.84 18.15 18.59 18.87 17.5 19.09L17.15 19.15C18.5 20.3 20.78 20.28 22.25 18.78C23.35 17.71 23.9 16.34 21.64 15.89Z" fill="currentColor"/>
            </svg>
            <!-- Sun Icon (shown in dark mode - click to enable light mode) -->
            <svg x-show="$store.theme.isDark" class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 18C15.3137 18 18 15.3137 18 12C18 8.68629 15.3137 6 12 6C8.68629 6 6 8.68629 6 12C6 15.3137 8.68629 18 12 18Z" fill="currentColor"/>
                <path d="M12 1V3M12 21V23M23 12H21M3 12H1M20.485 3.515L19.071 4.929M4.929 19.071L3.515 20.485M20.485 20.485L19.071 19.071M4.929 4.929L3.515 3.515" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <!-- Notifications Bell -->
        <div x-data="{
            notifOpen: false,
            notifications: [],
            unreadCount: 0,
            loading: false,
            error: null,
            csrfToken: '<?php echo e(csrf_token()); ?>',
            init() {
                this.requestNotificationPermission();
                this.fetchUnreadCount();
                setInterval(() => this.fetchUnreadCount(), 30000);
            },
            requestNotificationPermission() {
                if ('Notification' in window && Notification.permission === 'default') {
                    Notification.requestPermission();
                }
            },
            fetchUnreadCount() {
                const previousCount = this.unreadCount;
                fetch(`${window.apiBaseUrl}/notifications/unread-count`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        const newCount = data.unread_count || 0;
                        if (newCount > previousCount && previousCount > 0) {
                            this.showNewNotificationAlert();
                        }
                        this.unreadCount = newCount;
                        this.error = null;
                        if (this.notifOpen) this.fetchLatestUnread();
                    })
                    .catch(error => {
                        console.error('Error fetching unread count:', error);
                        this.error = 'Failed to load notifications';
                    });
            },
            fetchLatestUnread() {
                this.loading = true;
                this.error = null;
                fetch(`${window.apiBaseUrl}/notifications/latest-unread`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        this.notifications = data.data || [];
                        this.loading = false;
                    })
                    .catch(error => {
                        console.error('Error fetching latest unread:', error);
                        this.loading = false;
                        this.error = 'Failed to load notifications';
                    });
            },
            markAsRead(notification) {
                fetch(`${window.apiBaseUrl}/notifications/${notification.id}/mark-as-read`, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrfToken
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(() => {
                        this.fetchUnreadCount();
                    })
                    .catch(error => {
                        console.error('Error marking as read:', error);
                    });
            },
            showNewNotificationAlert() {
                const bell = this.$el.querySelector('button');
                bell.classList.add('animate-pulse');
                setTimeout(() => {
                    bell.classList.remove('animate-pulse');
                }, 2000);

                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification('TimCare - Notifikasi Baru', {
                        body: 'Anda memiliki notifikasi baru',
                        icon: '<?php echo e(asset('logo/logo.png')); ?>'
                    });
                }
            }
        }" class="relative">
            <button @click="notifOpen = !notifOpen; notifOpen && fetchLatestUnread()" class="relative inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/5">
                <!-- Bell Icon -->
                <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.1999 14.9C15.9199 14.7 15.7 14.4 15.7 14V10C15.7 7.9 14.5 6.2 12.9 5.5V5C12.9 3.6 11.8 2.5 10.4 2.5C9 2.5 7.9 3.6 7.9 5V5.5C6.3 6.2 5.1 7.9 5.1 10V14C5.1 14.4 4.9 14.7 4.6 14.9C3.5 15.7 2.8 16.8 2.8 18H16.1C16.1 16.8 15.3 15.7 16.1999 14.9Z"></path>
                    <path d="M11.7 18C11.7 18.6 11.2 19.1 10.5 19.1C9.8 19.1 9.3 18.6 9.3 18H11.7Z"></path>
                </svg>
                <!-- Unread Badge -->
                <span x-show="unreadCount > 0" x-text="unreadCount" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-75" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-75" class="absolute -top-1 -right-1 flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-white text-xs font-bold"></span>
            </button>

            <!-- Notifications Dropdown -->
            <div x-show="notifOpen" @click.outside="notifOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="absolute right-0 mt-2 w-80 sm:w-80 md:w-96 rounded-lg border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-dark-800 z-60 max-h-[80vh] overflow-hidden">
                <!-- Header -->
                <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi</p>
                </div>

                <!-- Notifications List -->
                <div class="max-h-96 overflow-y-auto">
                    <!-- Loading State -->
                    <template x-if="loading">
                        <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-brand-500 mx-auto mb-2"></div>
                            Memuat notifikasi...
                        </div>
                    </template>

                    <!-- Error State -->
                    <template x-if="error && !loading">
                        <div class="px-4 py-8 text-center text-sm text-red-500 dark:text-red-400">
                            <svg class="w-6 h-6 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span x-text="error"></span>
                            <br>
                            <button @click="fetchLatestUnread()" class="text-xs underline hover:no-underline mt-1">
                                Coba lagi
                            </button>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!loading && !error && notifications.length === 0">
                        <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            Belum ada notifikasi baru
                        </div>
                    </template>

                    <!-- Notifications -->
                    <template x-for="notification in notifications" :key="notification.id">
                        <div class="border-b border-gray-100 px-4 py-3 hover:bg-gray-50 dark:border-gray-700/50 dark:hover:bg-white/5 cursor-pointer transition duration-150 ease-in-out" @click="markAsRead(notification)">
                            <div class="flex gap-3">
                                <div class="mt-0.5 flex-shrink-0">
                                    <!-- Type-based Icon -->
                                    <div x-show="notification.type === 'ticket_comment'" class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="notification.type === 'ticket_status'" class="h-8 w-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="notification.type !== 'ticket_comment' && notification.type !== 'ticket_status'" class="h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="notification.title"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2 break-words" x-text="notification.message"></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2" x-text="new Date(notification.created_at).toLocaleString('id-ID', {
                                        day: 'numeric',
                                        month: 'short',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="border-t border-gray-200 px-4 py-2 dark:border-gray-700">
                    <a href="<?php echo e(url()->to(route('notifications.index'))); ?>" class="block text-center text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300 py-2">
                        Lihat semua notifikasi
                    </a>
                </div>
            </div>
        </div>

        <!-- User Profile Dropdown -->
        <div x-data="{ userOpen: false }" class="relative">
            <button @click="userOpen = !userOpen" class="flex items-center gap-3 rounded-lg px-3.5 py-2 hover:bg-gray-100 dark:hover:bg-white/5">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-500 text-white font-medium">
                    <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                </div>
                <div class="hidden text-left sm:block">
                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e(auth()->user()->email); ?></p>
                </div>
                <svg class="fill-current text-gray-500 dark:text-gray-400" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 11.75C7.8125 11.75 7.625 11.6875 7.47187 11.5469L2.34375 6.53906C2.03125 6.25 2.03125 5.78906 2.34375 5.5C2.65625 5.21094 3.15625 5.21094 3.46875 5.5L8 9.75L12.5312 5.5C12.8438 5.21094 13.3438 5.21094 13.6562 5.5C13.9688 5.78906 13.9688 6.25 13.6562 6.53906L8.52812 11.5469C8.375 11.6875 8.1875 11.75 8 11.75Z"></path>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="userOpen" @click.outside="userOpen = false" class="absolute right-0 mt-2 w-48 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-dark-800">
                <a href="<?php echo e(url()->to(route('profile.edit'))); ?>" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5">Profil Saya</a>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 border-t border-gray-200 dark:border-gray-700">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/layouts/header.blade.php ENDPATH**/ ?>