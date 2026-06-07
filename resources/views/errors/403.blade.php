<x-error-layout>
    @php $previousUrl = url()->previous() ?: route('dashboard'); @endphp

    @include('components.error.card', [
        'code' => '403',
        'title' => 'Akses Ditolak',
        'message' => $exception->getMessage() ?: 'Anda tidak memiliki izin untuk melihat halaman ini. Pastikan Anda menggunakan peran atau izin yang sesuai.',
        'previousUrl' => $previousUrl,
    ])
</x-error-layout>
