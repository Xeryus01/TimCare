<x-app-layout>
<!-- Main Content -->
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Tambah Aset Baru</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Daftarkan aset TI baru ke dalam sistem</p>
        </div>

        <!-- Form Card -->
        <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800 sm:p-8">
            <!-- Error Messages -->
            @if($errors->any())
                <div class="mb-6 rounded-lg bg-red-50 p-4 dark:bg-red-500/10">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-red-600 dark:text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="font-medium text-red-800 dark:text-red-400">Silakan perbaiki kesalahan berikut:</h3>
                            <ul class="mt-2 list-inside space-y-1 text-sm text-red-700 dark:text-red-400">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('assets.store') }}" class="space-y-6">
                @csrf

                <!-- Asset Code -->
                <div>
                    <label for="asset_code" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        NO BMN
                    </label>
                    <input id="asset_code" type="text" name="asset_code" value="{{ old('asset_code') }}" required placeholder="e.g., ASSET-001" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('asset_code') border-red-500 @enderror" />
                    @error('asset_code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Nama
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Dell Desktop Computer" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('name') border-red-500 @enderror" />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Jenis Barang / Kategori
                    </label>
                    <input id="type" type="text" name="type" value="{{ old('type') }}" required placeholder="e.g., Desktop, Laptop, Monitor" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('type') border-red-500 @enderror" />
                    @error('type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Brand -->
                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Merek
                    </label>
                    <input id="brand" type="text" name="brand" value="{{ old('brand') }}" placeholder="e.g., Dell, HP, Lenovo" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('brand') border-red-500 @enderror" />
                    @error('brand')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Model -->
                <div>
                    <label for="model" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Model
                    </label>
                    <input id="model" type="text" name="model" value="{{ old('model') }}" placeholder="e.g., Vostro 3510, ProBook 450" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('model') border-red-500 @enderror" />
                    @error('model')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Serial Number -->
                <div>
                    <label for="serial_number" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Asset Tag
                    </label>
                    <input id="serial_number" type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="e.g., SN123456789" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('serial_number') border-red-500 @enderror" />
                    @error('serial_number')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Specs -->
                <div>
                    <label for="specs" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Spesifikasi
                    </label>
                    <textarea id="specs" name="specs" rows="3" placeholder='e.g., {"cpu":"i5","ram":"16GB","storage":"512GB SSD"} atau CPU: i5, RAM: 16GB, Storage: 512GB SSD' class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('specs') border-red-500 @enderror">{{ old('specs') }}</textarea>
                    @error('specs')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Lokasi Aset
                    </label>
                    <input id="location" type="text" name="location" value="{{ old('location') }}" placeholder="e.g., Ruang Server, Lantai 2" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('location') border-red-500 @enderror" />
                    @error('location')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purchased At -->
                <div>
                    <label for="purchased_at" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Tanggal Perolehan
                    </label>
                    <input id="purchased_at" type="date" name="purchased_at" value="{{ old('purchased_at') }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('purchased_at') border-red-500 @enderror" />
                    @error('purchased_at')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Holder -->
                <div>
                    <label for="holder" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Nama Pegawai
                    </label>
                    <input id="holder" type="text" name="holder" value="{{ old('holder') }}" placeholder="e.g., John Doe / IT Department" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('holder') border-red-500 @enderror" />
                    @error('holder')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nilai Perolehan -->
                <div>
                    <label for="nilai_perolehan" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Nilai Perolehan
                    </label>
                    <input id="nilai_perolehan" type="number" step="0.01" name="nilai_perolehan" value="{{ old('nilai_perolehan') }}" placeholder="e.g., 30500000" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('nilai_perolehan') border-red-500 @enderror" />
                    @error('nilai_perolehan')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Satker -->
                <div>
                    <label for="kode_satker" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Kode Satker
                    </label>
                    <input id="kode_satker" type="text" name="kode_satker" value="{{ old('kode_satker') }}" placeholder="e.g., 1900" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('kode_satker') border-red-500 @enderror" />
                    @error('kode_satker')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NIP Pegawai -->
                <div>
                    <label for="nip_pegawai" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        NIP Pegawai
                    </label>
                    <input id="nip_pegawai" type="text" name="nip_pegawai" value="{{ old('nip_pegawai') }}" placeholder="e.g., 197905132009022006" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('nip_pegawai') border-red-500 @enderror" />
                    @error('nip_pegawai')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Condition -->
                <div>
                    <label for="condition" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Kondisi
                    </label>
                    <select id="condition" name="condition" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('condition') border-red-500 @enderror">
                        @foreach(\App\Models\Asset::conditionOptions() as $value => $label)
                            <option value="{{ $value }}" {{ old('condition') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('condition')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Status
                    </label>
                    <select id="status" name="status" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('status') border-red-500 @enderror">
                        @foreach(\App\Models\Asset::statusOptions() as $value => $label)
                            <option value="{{ $value }}" {{ old('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex gap-3 pt-6">
                    <a href="{{ url()->to(route('assets.index')) }}" class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 rounded-lg bg-brand-600 px-4 py-2.5 text-center font-medium text-white hover:bg-brand-700">
                        Simpan Aset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
