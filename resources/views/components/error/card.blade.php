@props([ 'code' => 'Error', 'title' => 'Terjadi Kesalahan', 'message' => null, 'previousUrl' => url()->previous() ?: route('dashboard') ])

<div class="page-content">
    <div class="error-card">
        <div class="error-card-inner">
            <div class="error-visual">
                <div class="error-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7v6c0 5 3.58 9.74 10 13 6.42-3.26 10-8 10-13V7l-10-5z"/>
                        <path d="M12 9v4M12 17h.01"/>
                    </svg>
                </div>
                <div class="error-code">Error {{ $code }}</div>
            </div>

            <div class="error-body">
                <h2 class="error-title">{{ $title }}</h2>
                <div class="error-msg">{{ $message ?? 'Maaf, terjadi kesalahan. Silakan coba lagi atau hubungi administrator.' }}</div>
                <div class="error-actions">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M3 12h18M3 12l7-7M3 12l7 7"/></svg>
                        Kembali ke Dashboard
                    </a>
                    <a href="{{ $previousUrl }}" class="btn btn-outline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 19l-7-7 7-7"/></svg>
                        Kembali
                    </a>
                    <button onclick="window.location.reload()" class="btn btn-ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M23 4v6h-6M1 20v-6h6"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
                        Muat Ulang
                    </button>
                </div>
                <p class="error-hint">Jika ini bukan kesalahan Anda, silakan <a href="#">hubungi administrator sistem</a>.</p>
            </div>
        </div>
    </div>
</div>
