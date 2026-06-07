<x-error-layout>
    @php $previousUrl = url()->previous() ?: route('dashboard'); @endphp

    @include('components.error.card', [
        'code' => '401',
        'title' => 'Belum Diautentikasi',
        'message' => $exception->getMessage() ?: 'Silakan masuk untuk melanjutkan.',
        'previousUrl' => $previousUrl,
    ])
</x-error-layout>
