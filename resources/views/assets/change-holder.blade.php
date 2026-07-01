<x-app-layout>
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('assets.show', $asset) }}" class="text-brand-600 hover:text-brand-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ubah Pemegang Aset</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aset: {{ $asset->name }} ({{ $asset->asset_code }})</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="max-w-2xl">
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800 overflow-hidden">
                <!-- Current Info -->
                <div class="border-b border-gray-200 bg-gray-50 p-5 sm:p-7.5 dark:border-gray-700 dark:bg-white/5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Saat Ini</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pemegang Saat Ini</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $asset->holder ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Status Aset</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $asset->status_label }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('assets.storeChangeHolder', $asset) }}" method="POST" class="p-5 sm:p-7.5">
                    @csrf

                    <!-- New Holder -->
                    <div class="mb-6">
                        <label for="new_holder" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Pemegang Aset Baru
                        </label>
                        <p class="-mt-1 mb-3 text-xs text-gray-500 dark:text-gray-400">Pilih dari daftar user yang tersedia, atau ketik nama lain secara manual di luar daftar.</p>
                        <input type="text" name="new_holder" id="new_holder" list="holder-user-list" autocomplete="off" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('new_holder') border-red-500 @enderror"
                            placeholder="Nama pemegang aset baru"
                            value="{{ old('new_holder') }}"
                            required>
                        <datalist id="holder-user-list">
                            @foreach(($users ?? []) as $userName)
                                <option value="{{ $userName }}"></option>
                            @endforeach
                        </datalist>
                        @error('new_holder')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Changed Date -->
                    <div class="mb-6">
                        <label for="changed_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Waktu Perubahan
                        </label>
                        <input type="datetime-local" name="changed_at" id="changed_at" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('changed_at') border-red-500 @enderror"
                            value="{{ old('changed_at', now()->format('Y-m-d\TH:i')) }}"
                            required>
                        @error('changed_at')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Catatan (Opsional)
                        </label>
                        <textarea name="notes" id="notes" rows="4"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('notes') border-red-500 @enderror"
                            placeholder="Catatan tentang perubahan pemegang aset...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <a href="{{ route('assets.show', $asset) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 font-medium text-white hover:bg-brand-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
