<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center gap-8">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <x-application-logo class="h-10 w-auto text-brand-600" />
                </a>
                <div class="hidden md:flex items-center gap-2 sm:gap-4">
                    <a href="{{ route('documentation') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Dokumentasi</a>
                    <a href="{{ route('faq') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">FAQ</a>
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Kontak</a>
                    <a href="{{ route('status') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Status Sistem</a>
                </div>
            </div>
            <div class="flex items-center space-x-2 sm:space-x-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-brand-600 text-white px-4 sm:px-5 py-2 rounded-md text-sm font-medium hover:bg-brand-700 transition-colors">
                        Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-gray-900 px-3 sm:px-4 py-2 rounded-md text-sm font-medium border border-gray-300 hover:border-gray-400 transition-colors">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ url()->to(route('login')) }}" class="text-gray-700 hover:text-gray-900 px-3 sm:px-4 py-2 rounded-md text-sm font-medium">
                        Masuk
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ url()->to(route('register')) }}" class="bg-brand-600 text-white px-3 sm:px-4 py-2 rounded-md text-sm font-medium hover:bg-brand-700">
                            Daftar
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>
