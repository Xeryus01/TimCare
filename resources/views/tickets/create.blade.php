<x-app-layout>
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Ajukan Tiket Permasalahan</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Sampaikan kendala secara singkat, lalu teknisi atau admin akan menanganinya.</p>
        </div>

        <div class="max-w-2xl mx-auto rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800 sm:p-8">
            @if($errors->any())
                <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-500/10 dark:text-red-400">
                    <p class="font-medium">Mohon periksa kembali data berikut:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label for="category" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Jenis Permintaan</label>
                    <select id="category" name="category" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('category') border-red-500 @enderror">
                        @foreach(\App\Models\Ticket::categoryLabels() as $value => $label)
                            <option value="{{ $value }}" {{ old('category', 'DATA_PROCESSING') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="submission_time" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Waktu Pengajuan Tiket</label>
                    <input id="submission_time" type="text" value="{{ now()->format('d/m/Y H:i') }}" disabled class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300" />
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Waktu pengajuan akan dicatat otomatis saat tiket dikirim.</p>
                </div>

                <div>
                    <label for="title" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Judul Keluhan</label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Printer tidak bisa digunakan" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('title') border-red-500 @enderror" />
                </div>

                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Detail Kendala</label>
                    <textarea id="description" name="description" rows="4" required placeholder="Jelaskan masalah yang dialami agar petugas lebih mudah menindaklanjuti." class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="attachment" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Lampiran Awal <span class="text-red-500">(wajib)</span></label>
                    <input id="attachment" type="file" name="attachment" required accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('attachment') border-red-500 @enderror" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ukuran lampiran maksimal 1MB. Hanya gambar dan PDF disarankan.</p>
                    @error('attachment')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="asset_id" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Aset Terkait <span class="text-gray-400">(opsional)</span></label>
                    <select id="asset_id" name="asset_id" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('asset_id') border-red-500 @enderror">
                        <option value="">Pilih aset jika ada</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>{{ $asset->name }} ({{ $asset->asset_code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3 pt-4">
                    <a href="{{ url()->to(route('tickets.index')) }}" class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Batal</a>
                    <button type="submit" class="flex-1 rounded-lg bg-brand-600 px-4 py-2.5 text-center font-medium text-white hover:bg-brand-700">Kirim Tiket</button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
