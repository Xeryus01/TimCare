@props(['code' => 'Error', 'title' => 'Terjadi Kesalahan', 'message' => null, 'previousUrl' => url()->previous() ?: route('dashboard')])

<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-dark-900 px-4 py-10">
    <div class="w-full max-w-3xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center bg-white dark:bg-dark-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-8 shadow-lg">
            <div class="flex items-center justify-center">
                <div class="flex flex-col items-center text-center">
                    <div class="rounded-full bg-red-50 dark:bg-red-900/20 p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-600 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7v6c0 5 3.58 9.74 10 13 6.42-3.26 10-8 10-13V7l-10-5z" />
                            <path d="M8.5 11.5a3.5 3.5 0 0 1 7 0v1" />
                        </svg>
                    </div>
                    <p class="mt-4 text-sm font-semibold uppercase tracking-wide text-red-600 dark:text-red-400">{{ $code }} — {{ $title }}</p>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $title }}</h2>

                <p class="mt-4 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $message ?? 'Maaf, terjadi kesalahan. Silakan coba lagi atau hubungi administrator.' }}</p>

                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <a href="{{ $previousUrl }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-900 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        Kembali
                    </a>

                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                        Kembali ke Dashboard
                    </a>

                    <button onclick="window.location.reload()" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-transparent px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 dark:text-slate-300">
                        Muat ulang
                    </button>
                </div>

                <p class="mt-6 text-xs text-slate-400">Jika Anda percaya ini adalah kesalahan, hubungi administrator sistem.</p>
            </div>
        </div>
    </div>
</div>
