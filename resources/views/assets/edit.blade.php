<x-app-layout>
<!-- Main Content -->
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Ubah Aset {{ $asset->asset_code }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Perbarui informasi aset</p>
        </div>

        <!-- Form Card -->
        <div class="max-w-4xl rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800 sm:p-8">
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

            <form method="POST" action="{{ route('assets.update', $asset) }}" class="space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                @if($canEditAll ?? true)
                <div class="rounded-2xl border border-gray-200 bg-gray-50/60 p-5 dark:border-gray-700 dark:bg-white/5 sm:p-6">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10m0-10L4 7v10l8 4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Informasi Aset</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Identitas dan spesifikasi perangkat</p>
                        </div>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                <!-- Asset Code -->
                <div class="sm:col-span-2">
                    <label for="asset_code" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        NO BMN
                    </label>
                    <input id="asset_code" type="text" name="asset_code" value="{{ old('asset_code', $asset->asset_code) }}" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('asset_code') border-red-500 @enderror" />
                    @error('asset_code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Nama
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name', $asset->name) }}" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('name') border-red-500 @enderror" />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Jenis Barang / Kategori
                    </label>
                    <input id="type" type="text" name="type" value="{{ old('type', $asset->type) }}" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('type') border-red-500 @enderror" />
                    @error('type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Brand -->
                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Merek
                    </label>
                    <input id="brand" type="text" name="brand" value="{{ old('brand', $asset->brand) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('brand') border-red-500 @enderror" />
                    @error('brand')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Model -->
                <div>
                    <label for="model" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Model
                    </label>
                    <input id="model" type="text" name="model" value="{{ old('model', $asset->model) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('model') border-red-500 @enderror" />
                    @error('model')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Serial Number -->
                <div>
                    <label for="serial_number" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Asset Tag
                    </label>
                    <input id="serial_number" type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('serial_number') border-red-500 @enderror" />
                    @error('serial_number')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                    </div>
                </div>

                @endif

                @php
                    $photoSerialUrlValue = old('photo_serial_url');
                    if ($photoSerialUrlValue === null && $asset->photo_serial) {
                        if (str_starts_with($asset->photo_serial, 'drive:')) {
                            $photoSerialUrlValue = \App\Models\Asset::googleDriveFileLink(substr($asset->photo_serial, 6));
                        } elseif (preg_match('/^https?:\/\//', $asset->photo_serial)) {
                            $photoSerialUrlValue = $asset->photo_serial;
                        }
                    }

                    $photoAssetUrlValue = old('photo_asset_url');
                    if ($photoAssetUrlValue === null && $asset->photo_asset) {
                        if (str_starts_with($asset->photo_asset, 'drive:')) {
                            $photoAssetUrlValue = \App\Models\Asset::googleDriveFileLink(substr($asset->photo_asset, 6));
                        } elseif (preg_match('/^https?:\/\//', $asset->photo_asset)) {
                            $photoAssetUrlValue = $asset->photo_asset;
                        }
                    }

                    $photoBmnUrlValue = old('photo_bmn_url');
                    if ($photoBmnUrlValue === null && $asset->photo_bmn) {
                        if (str_starts_with($asset->photo_bmn, 'drive:')) {
                            $photoBmnUrlValue = \App\Models\Asset::googleDriveFileLink(substr($asset->photo_bmn, 6));
                        } elseif (preg_match('/^https?:\/\//', $asset->photo_bmn)) {
                            $photoBmnUrlValue = $asset->photo_bmn;
                        }
                    }
                @endphp

                <div class="rounded-2xl border border-gray-200 bg-gray-50/60 p-5 dark:border-gray-700 dark:bg-white/5 sm:p-6">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 19.5h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Foto &amp; Dokumentasi</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Unggah file gambar atau tempel tautan Google Drive</p>
                        </div>
                    </div>
                <!-- Photos: Serial, Asset, Nomor BMN -->
                <div class="grid gap-6 sm:grid-cols-3">
                    <div>
                        <label for="photo_serial" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Foto Serial Number (Upload)</label>
                        <input id="photo_serial" type="file" name="photo_serial" accept="image/*" class="w-full" />
                        <p class="mt-1 text-sm text-gray-500">Atau masukkan link drive pada kolom berikut.</p>
                        <input id="photo_serial_url" type="url" name="photo_serial_url" value="{{ $photoSerialUrlValue }}" placeholder="https://drive.google.com/..." class="w-full mt-2 rounded-lg border border-gray-300 px-3 py-2" />
                        @error('photo_serial')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @error('photo_serial_url')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @if($asset->photo_serial)
                            <div class="mt-3">
                                <p class="text-sm text-gray-500">Preview saat ini:</p>
                                @if(str_starts_with($asset->photo_serial, 'drive:'))
                                    <iframe src="{{ \App\Models\Asset::googleDrivePreviewUrl(substr($asset->photo_serial, 6)) }}" class="mt-2 h-40 w-full rounded-lg border" frameborder="0" allowfullscreen></iframe>
                                @elseif(preg_match('/^https?:\/\//', $asset->photo_serial))
                                    <img src="{{ $asset->photo_serial }}" class="mt-2 max-h-40 rounded-lg border" alt="Foto Serial" />
                                @else
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($asset->photo_serial) }}" class="mt-2 max-h-40 rounded-lg border" alt="Foto Serial" />
                                @endif
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="photo_asset" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Foto Barang/Aset (Upload)</label>
                        <input id="photo_asset" type="file" name="photo_asset" accept="image/*" class="w-full" />
                        <p class="mt-1 text-sm text-gray-500">Atau masukkan link drive pada kolom berikut.</p>
                        <input id="photo_asset_url" type="url" name="photo_asset_url" value="{{ $photoAssetUrlValue }}" placeholder="https://drive.google.com/..." class="w-full mt-2 rounded-lg border border-gray-300 px-3 py-2" />
                        @error('photo_asset')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @error('photo_asset_url')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @if($asset->photo_asset)
                            <div class="mt-3">
                                <p class="text-sm text-gray-500">Preview saat ini:</p>
                                @if(str_starts_with($asset->photo_asset, 'drive:'))
                                    <iframe src="{{ \App\Models\Asset::googleDrivePreviewUrl(substr($asset->photo_asset, 6)) }}" class="mt-2 h-40 w-full rounded-lg border" frameborder="0" allowfullscreen></iframe>
                                @elseif(preg_match('/^https?:\/\//', $asset->photo_asset))
                                    <img src="{{ $asset->photo_asset }}" class="mt-2 max-h-40 rounded-lg border" alt="Foto Aset" />
                                @else
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($asset->photo_asset) }}" class="mt-2 max-h-40 rounded-lg border" alt="Foto Aset" />
                                @endif
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="photo_bmn" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Foto Nomor BMN (Upload)</label>
                        <input id="photo_bmn" type="file" name="photo_bmn" accept="image/*" class="w-full" />
                        <p class="mt-1 text-sm text-gray-500">Atau masukkan link drive pada kolom berikut.</p>
                        <input id="photo_bmn_url" type="url" name="photo_bmn_url" value="{{$photoBmnUrlValue}}" placeholder="https://drive.google.com/..." class="w-full mt-2 rounded-lg border border-gray-300 px-3 py-2" />
                        @error('photo_bmn')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{$message}}</p>
                        @enderror
                        @error('photo_bmn_url')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{$message}}</p>
                        @enderror
                        @if($asset->photo_bmn)
                            <div class="mt-3">
                                <p class="text-sm text-gray-500">Preview saat ini:</p>
                                @if(str_starts_with($asset->photo_bmn, 'drive:'))
                                    <iframe src="{{\App\Models\Asset::googleDrivePreviewUrl(substr($asset->photo_bmn, 6))}}" class="mt-2 h-40 w-full rounded-lg border" frameborder="0" allowfullscreen></iframe>
                                @elseif(preg_match('/^https?:\/\//', $asset->photo_bmn))
                                    <img src="{{$asset->photo_bmn}}" class="mt-2 max-h-40 rounded-lg border" alt="Foto Nomor BMN" />
                                @else
                                    <img src="{{\Illuminate\Support\Facades\Storage::url($asset->photo_bmn)}}" class="mt-2 max-h-40 rounded-lg border" alt="Foto Nomor BMN" />
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

                @if($canEditAll ?? true)
                <div class="rounded-2xl border border-gray-200 bg-gray-50/60 p-5 dark:border-gray-700 dark:bg-white/5 sm:p-6">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Penempatan, Perolehan &amp; Status</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Lokasi, pemegang, nilai, kondisi, dan status aset</p>
                        </div>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                <!-- Location -->
                <div class="sm:col-span-2">
                    <label for="location" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Lokasi Aset
                    </label>
                    <input id="location" type="text" name="location" value="{{ old('location', $asset->location) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('location') border-red-500 @enderror" />
                    @error('location')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purchased At -->
                <div>
                    <label for="purchased_at" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Tanggal Perolehan
                    </label>
                    <input id="purchased_at" type="date" name="purchased_at" value="{{ old('purchased_at', $asset->purchased_at ? $asset->purchased_at->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('purchased_at') border-red-500 @enderror" />
                    @error('purchased_at')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Holder -->
                <div>
                    <label for="holder" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Nama Pegawai
                    </label>
                    <input id="holder" type="text" name="holder" value="{{ old('holder', $asset->holder) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('holder') border-red-500 @enderror" />
                    @error('holder')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nilai Perolehan -->
                <div>
                    <label for="nilai_perolehan" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Nilai Perolehan
                    </label>
                    <input id="nilai_perolehan" type="number" step="0.01" name="nilai_perolehan" value="{{ old('nilai_perolehan', $asset->nilai_perolehan) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('nilai_perolehan') border-red-500 @enderror" />
                    @error('nilai_perolehan')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Satker -->
                <div>
                    <label for="kode_satker" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Kode Satker
                    </label>
                    <input id="kode_satker" type="text" name="kode_satker" value="{{ old('kode_satker', $asset->kode_satker) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('kode_satker') border-red-500 @enderror" />
                    @error('kode_satker')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NIP Pegawai -->
                <div>
                    <label for="nip_pegawai" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        NIP Pegawai
                    </label>
                    <input id="nip_pegawai" type="text" name="nip_pegawai" value="{{ old('nip_pegawai', $asset->nip_pegawai) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('nip_pegawai') border-red-500 @enderror" />
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
                            <option value="{{ $value }}" {{ old('condition', $asset->condition) === $value ? 'selected' : '' }}>{{ $label }}</option>
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
                            <option value="{{ $value }}" {{ old('status', $asset->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                    </div>
                </div>

                @endif

                <!-- Form Actions -->
                <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 dark:border-gray-700 sm:flex-row">
                    <a href="{{ url()->to(route('assets.index')) }}" class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 rounded-lg bg-brand-600 px-4 py-2.5 text-center font-medium text-white hover:bg-brand-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
