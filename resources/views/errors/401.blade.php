<x-app-layout>
    @php $previousUrl = url()->previous() ?: route('dashboard'); @endphp

    @include('components.error.card', [
        'code' => '401',
        'title' => 'Tidak Terotentikasi',
        'message' => $exception->getMessage() ?: 'Anda harus masuk untuk mengakses halaman ini.',
        'previousUrl' => $previousUrl,
    ])
</x-app-layout>
