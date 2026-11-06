<nav class="landing-nav sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
    <div class="mx-auto flex h-[68px] max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="{{ url('/') }}" class="flex shrink-0 items-center gap-2.5" aria-label="TimCare, beranda">
            <x-application-logo class="h-10 w-auto" />
            <span class="text-lg font-extrabold tracking-tight text-slate-900">TimCare</span>
        </a>

        <div class="hidden items-center gap-1 md:flex">
            <a href="{{ route('documentation') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-950">Dokumentasi</a>
            <a href="{{ route('faq') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-950">FAQ</a>
            <a href="{{ route('contact') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-950">Kontak</a>
            <a href="{{ route('status') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-950">Status Sistem</a>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            @auth
                <a href="{{ url('/dashboard') }}" class="inline-flex min-h-10 items-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-700">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                    @csrf
                    <button type="submit" class="inline-flex min-h-10 items-center rounded-xl border border-slate-200 px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="inline-flex min-h-10 items-center rounded-xl px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Masuk</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="hidden min-h-10 items-center rounded-xl bg-blue-700 px-4 text-sm font-semibold text-white transition hover:bg-blue-800 sm:inline-flex">Daftar</a>
                @endif
            @endauth

            <details class="landing-menu relative md:hidden">
                <summary class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-xl border border-slate-200 text-slate-700 transition hover:bg-slate-50 [&::-webkit-details-marker]:hidden" aria-label="Buka menu navigasi">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </summary>
                <div class="absolute right-0 top-12 z-50 w-64 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-900/10">
                    <a href="{{ route('documentation') }}" class="block rounded-xl px-3 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">Dokumentasi</a>
                    <a href="{{ route('faq') }}" class="block rounded-xl px-3 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">FAQ</a>
                    <a href="{{ route('contact') }}" class="block rounded-xl px-3 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">Kontak</a>
                    <a href="{{ route('status') }}" class="block rounded-xl px-3 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">Status Sistem</a>
                    @if (Route::has('register') && !auth()->check())
                        <a href="{{ route('register') }}" class="mt-1 block rounded-xl bg-blue-50 px-3 py-3 text-sm font-semibold text-blue-800 hover:bg-blue-100">Buat akun</a>
                    @endif
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="sm:hidden">
                            @csrf
                            <button type="submit" class="block w-full rounded-xl px-3 py-3 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50">Keluar</button>
                        </form>
                    @endauth
                </div>
            </details>
        </div>
    </div>
</nav>
