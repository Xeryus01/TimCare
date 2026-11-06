<!-- Sidebar -->
<aside class="fixed top-0 left-0 z-50 h-screen w-64 overflow-y-auto overflow-x-hidden bg-white dark:bg-dark-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out" :style="{ transform: $store.sidebar.isOpen ? 'translateX(0)' : 'translateX(-100%)', width: '16rem' }">
    <!-- Logo -->
    <div class="flex items-center justify-between px-4 py-4 sm:px-5.5 sm:py-6.5 lg:py-7.5">
        <a href="{{ url()->to(route('dashboard')) }}" class="block" @click="$store.sidebar.closeOnMobile()">
            <img src="{{ asset(file_exists(public_path('logo/full-logo.png')) ? 'logo/full-logo.png' : 'logo/logo.png') }}" alt="{{ config('app.name', 'TimCare') }}" class="h-12 w-auto object-contain sm:h-14" />
        </a>
    </div>

    <!-- Navigation Menu -->
    <nav class="px-3 py-4 lg:px-5.5">
        <ul class="space-y-1.5">
            <!-- Dashboard -->
            <li>
                <a href="{{ url()->to(route('dashboard')) }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="3" width="8" height="8" rx="1.5"></rect>
                        <rect x="13" y="3" width="8" height="5" rx="1.5"></rect>
                        <rect x="13" y="10" width="8" height="11" rx="1.5"></rect>
                        <rect x="3" y="13" width="8" height="8" rx="1.5"></rect>
                    </svg>
                    <span>Beranda</span>
                </a>
            </li>

            <!-- Tickets -->
            <li>
                <a href="{{ url()->to(route('tickets.index')) }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 {{ request()->routeIs('tickets.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                        <path d="M9 4.5V3h6v1.5M9 10h6M9 14h6M9 18h3"></path>
                        <path d="m7 10 .5.5L8.5 9"></path>
                    </svg>
                    <span>Tiket Masalah TI</span>
                </a>
            </li>

            <!-- Reservations -->
            <li>
                <a href="{{ url()->to(route('reservations.index')) }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 {{ request()->routeIs('reservations.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                        <path d="M16 3v4M8 3v4M3 10h13"></path>
                        <rect x="16" y="13" width="5" height="4" rx="1"></rect>
                        <path d="m16 14 2.5 1.5L21 14"></path>
                    </svg>
                    <span>Tiket ID Zoom</span>
                </a>
            </li>

            <!-- Assets -->
            <li>
                <a href="{{ url()->to(route('assets.index')) }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 {{ request()->routeIs('assets.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="13" rx="2"></rect>
                        <path d="M8 21h8M12 17v4M7 8h10M7 12h4"></path>
                        <circle cx="17" cy="12" r="1"></circle>
                    </svg>
                    <span>Data Aset</span>
                </a>
            </li>

            <!-- Pemeliharaan - Admin, Teknisi, ULP -->
            @if(auth()->user()->hasAnyRole(['Admin', 'Teknisi', 'ULP']))
            <li>
                <a href="{{ url()->to(route('maintenance.index')) }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 {{ request()->routeIs('maintenance.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14.7 6.3a5 5 0 0 0-6.4 6.4L3 18l3 3 5.3-5.3a5 5 0 0 0 6.4-6.4l-3 3-3-3 3-3Z"></path>
                        <path d="m4 4 3 3"></path>
                    </svg>
                    <span>Pemeliharaan</span>
                </a>
            </li>
            @endif

            <!-- Jadwal Piket - Teknisi -->
            @if(auth()->user()->hasRole('Teknisi'))
            <li>
                <a href="{{ url()->to(route('piket.view')) }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 {{ request()->routeIs('piket.view') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                        <path d="M16 3v4M8 3v4M3 10h18M8 14h3M8 17h3"></path>
                        <path d="m15 15 1.5 1.5L19 14"></path>
                    </svg>
                    <span>Jadwal Piket</span>
                </a>
            </li>
            @endif

            <li>
                <a href="{{ route('ti.reports.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 {{ request()->routeIs('ti.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="14" rx="2"></rect><path d="M8 21h8M12 17v4M6 12l4-4 4 3 4-5"></path></svg>
                    <span>Monitoring TI</span>
                </a>
            </li>

            <!-- User Management - Admin Only -->
            @if(auth()->user()->hasRole('Admin'))
            <li>
                <a href="{{ url()->to(route('users.index')) }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 {{ request()->routeIs('users.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="9" cy="8" r="3"></circle>
                        <path d="M3.5 20a5.5 5.5 0 0 1 11 0M16 5.5a3 3 0 0 1 0 5.8M17 14a5 5 0 0 1 3.5 4.8"></path>
                    </svg>
                    <span>Manajemen User</span>
                </a>
            </li>

            <!-- Jadwal Piket - Admin Only -->
            @if(auth()->user()->hasRole('Admin'))
            <li>
                <a href="{{ url()->to(route('piket.index')) }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 {{ request()->routeIs('piket.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                        <path d="M16 3v4M8 3v4M3 10h18M8 14h3M8 17h3M16 14v4M14 16h4"></path>
                    </svg>
                    <span>Jadwal Piket</span>
                </a>
            </li>

            <!-- Backup & File Manager - Admin Only -->
            <li>
                <a href="{{ url()->to(route('file-manager.index')) }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 {{ request()->routeIs('file-manager.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v10H3V7Z"></path>
                        <path d="M3 10h18M8 14h8M8 17h8"></path>
                    </svg>
                    <span>Backup &amp; Berkas</span>
                </a>
            </li>
            @endif
            @endif
        </ul>
    </nav>

    <!-- Divider -->
    <div class="my-4 border-t border-gray-200 dark:border-gray-700"></div>

    <!-- Settings Section -->
    <nav class="px-3 py-4 lg:px-5.5">
        <ul class="space-y-1.5">
            <!-- Profile -->
            <li>
                <a href="{{ url()->to(route('profile.edit')) }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 {{ request()->routeIs('profile.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="8" r="3.5"></circle>
                        <path d="M5 20a7 7 0 0 1 14 0"></path>
                    </svg>
                    <span>Profil</span>
                </a>
            </li>

            <!-- Logout -->
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left group relative flex items-center gap-3 rounded-lg px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M10 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h5"></path>
                            <path d="M14 8l4 4-4 4M18 12H9"></path>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</aside>
