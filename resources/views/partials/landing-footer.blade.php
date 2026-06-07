<footer class="bg-gray-900 text-white py-8 sm:py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 sm:gap-12 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <x-application-logo class="h-10 w-auto text-white" />
                </div>
                <p class="text-gray-400 text-sm sm:text-base max-w-md leading-relaxed">
                    Solusi helpdesk IT yang menyatukan tiket, pengajuan Zoom, dan pemantauan layanan dalam satu platform yang mudah digunakan.
                </p>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-semibold mb-4 text-white">Fitur</h3>
                <ul class="space-y-2 sm:space-y-3 text-gray-400">
                    <li><a href="{{ url('/tickets/create') }}" class="text-sm sm:text-base transition hover:text-white">Pengajuan Tiket</a></li>
                    <li><a href="{{ url('/reservations/create') }}" class="text-sm sm:text-base transition hover:text-white">Pengajuan Room Zoom</a></li>
                    <li><a href="{{ url('/dashboard') }}" class="text-sm sm:text-base transition hover:text-white">Dashboard</a></li>
                    <li><a href="{{ url('/notifications') }}" class="text-sm sm:text-base transition hover:text-white">Notifikasi</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-semibold mb-4 text-white">Dukungan</h3>
                <ul class="space-y-2 sm:space-y-3 text-gray-400">
                    <li><a href="{{ route('documentation') }}" class="text-sm sm:text-base transition hover:text-white">Dokumentasi</a></li>
                    <li><a href="{{ route('faq') }}" class="text-sm sm:text-base transition hover:text-white">FAQ</a></li>
                    <li><a href="{{ route('contact') }}" class="text-sm sm:text-base transition hover:text-white">Kontak</a></li>
                    <li><a href="{{ route('status') }}" class="text-sm sm:text-base transition hover:text-white">Status Sistem</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-8 sm:mt-12 lg:mt-16 border-t border-gray-800 pt-6 sm:pt-8 text-xs sm:text-sm text-gray-500">
            © {{ date('Y') }} TimCare. All rights reserved.
        </div>
    </div>
</footer>
