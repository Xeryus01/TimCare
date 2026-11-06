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
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Catat Perawatan Aset</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aset: {{ $asset->name }} ({{ $asset->asset_code }})</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="max-w-3xl">
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800 overflow-hidden">
                <!-- Current Info -->
                <div class="border-b border-gray-200 bg-gray-50 p-5 sm:p-7.5 dark:border-gray-700 dark:bg-white/5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Aset</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pemegang Aset</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $asset->holder ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kondisi Saat Ini</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $asset->condition_label }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('assets.storeMaintenance', $asset) }}" method="POST" class="p-5 sm:p-7.5">
                    @csrf

                    <!-- Type -->
                    <div class="mb-6">
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipe Perawatan
                        </label>
                        <select name="type" id="type" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('type') border-red-500 @enderror"
                            required>
                            <option value="">-- Pilih Tipe Perawatan --</option>
                            @foreach($maintenanceTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Maintenance Date -->
                    <div class="mb-6">
                        <label for="maintenance_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tanggal Perawatan
                        </label>
                        <input type="date" name="maintenance_date" id="maintenance_date" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('maintenance_date') border-red-500 @enderror"
                            value="{{ old('maintenance_date', today()->toDateString()) }}"
                            required>
                        @error('maintenance_date')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Deskripsi Perawatan
                        </label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('description') border-red-500 @enderror"
                            placeholder="Deskripsi perawatan yang dilakukan..."
                            required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Condition Before -->
                    <div class="mb-6">
                        <label for="condition_before" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kondisi Sebelum Perawatan (Opsional)
                        </label>
                        <select name="condition_before" id="condition_before" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('condition_before') border-red-500 @enderror">
                            <option value="">-- Pilih kondisi sebelum --</option>
                            @foreach(\App\Models\Asset::conditionOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('condition_before', $asset->condition_label) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('condition_before')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Findings -->
                    <div class="mb-6">
                        <label for="findings" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Temuan (Opsional)
                        </label>
                        <textarea name="findings" id="findings" rows="3"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('findings') border-red-500 @enderror"
                            placeholder="Temuan selama inspeksi/perawatan...">{{ old('findings') }}</textarea>
                        @error('findings')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions Taken -->
                    <div class="mb-6">
                        <label for="actions_taken" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tindakan yang Dilakukan (Opsional)
                        </label>
                        <textarea name="actions_taken" id="actions_taken" rows="3"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('actions_taken') border-red-500 @enderror"
                            placeholder="Tindakan yang dilakukan untuk memperbaiki...">{{ old('actions_taken') }}</textarea>
                        @error('actions_taken')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Condition After -->
                    <div class="mb-6">
                        <label for="condition_after" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kondisi Setelah Perawatan (Opsional)
                        </label>
                        <select name="condition_after" id="condition_after" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('condition_after') border-red-500 @enderror">
                            <option value="">-- Tidak ada perubahan --</option>
                            @foreach(\App\Models\Asset::conditionOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('condition_after') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('condition_after')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Next Maintenance Date -->
                    <div class="mb-6">
                        <label for="next_maintenance_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Jadwal Perawatan Berikutnya (Opsional)
                        </label>
                        <input type="date" name="next_maintenance_date" id="next_maintenance_date" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('next_maintenance_date') border-red-500 @enderror"
                            value="{{ old('next_maintenance_date') }}">
                        @error('next_maintenance_date')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <a href="{{ route('assets.show', $asset) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 font-medium text-white hover:bg-brand-700">
                            Simpan Perawatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
