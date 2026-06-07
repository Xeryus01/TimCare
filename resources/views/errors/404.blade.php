<x-error-layout>
    @php $previousUrl = url()->previous() ?: route('dashboard'); @endphp

    @include('components.error.card', [
        'code' => '404',
        'title' => 'Halaman Tidak Ditemukan',
        'message' => $exception->getMessage() ?: 'Halaman yang Anda cari tidak ditemukan. Periksa kembali alamat URL atau kembali ke dashboard.',
        'previousUrl' => $previousUrl,
    ])
</x-error-layout>
