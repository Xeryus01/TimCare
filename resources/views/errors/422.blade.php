<x-app-layout>
    @php $previousUrl = url()->previous() ?: route('dashboard'); @endphp

    @include('components.error.card', [
        'code' => '422',
        'title' => 'Permintaan Tidak Valid',
        'message' => $exception->getMessage() ?: 'Data yang dikirim tidak valid. Periksa input dan coba lagi.',
        'previousUrl' => $previousUrl,
    ])
</x-app-layout>
