<x-app-layout>
    @php $previousUrl = url()->previous() ?: route('dashboard'); @endphp

    @include('components.error.card', [
        'code' => '419',
        'title' => 'Halaman Kadaluarsa',
        'message' => $exception->getMessage() ?: 'Halaman ini telah kadaluarsa. Silakan coba lagi.',
        'previousUrl' => $previousUrl,
    ])
</x-app-layout>
