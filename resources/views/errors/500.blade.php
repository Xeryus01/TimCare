<x-app-layout>
    @php $previousUrl = url()->previous() ?: route('dashboard'); @endphp

    @include('components.error.card', [
        'code' => '500',
        'title' => 'Kesalahan Server',
        'message' => $exception->getMessage() ?: 'Terjadi kesalahan pada server. Silakan coba lagi nanti atau hubungi administrator.',
        'previousUrl' => $previousUrl,
    ])
</x-app-layout>
