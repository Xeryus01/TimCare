<x-app-layout>
<!-- Main Content -->
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <!-- Page Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Aset TI</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pantau dan kelola semua aset TI organisasi</p>
            </div>
            @can('manage assets')
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ url()->to(route('assets.export')) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7 2a1 1 0 00-1 1v10a1 1 0 102 0V5.414l6.293 6.293a1 1 0 001.414-1.414l-7.707-7.707A1 1 0 007 2z" clip-rule="evenodd" />
                            <path d="M3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" />
                        </svg>
                        Export Semua Aset
                    </a>
                    <a href="{{ url()->to(route('assets.create')) }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 font-medium text-white hover:bg-brand-700">
                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 3C10.5523 3 11 3.44772 11 4V9H16C16.5523 9 17 9.44772 17 10C17 10.5523 16.5523 11 16 11H11V16C11 16.5523 10.5523 17 10 17C9.44772 17 9 16.5523 9 16V11H4C3.44772 11 3 10.5523 3 10C3 9.44772 3.44772 9 4 9H9V4C9 3.44772 9.44772 3 10 3Z"></path>
                        </svg>
                        Tambah Aset
                    </a>
                </div>
            @endcan
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-700 dark:bg-green-900/20 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-700 dark:bg-red-900/20 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-dark-800">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Import Aset dari Excel</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Unduh template lalu unggah file Excel dengan kolom yang sesuai.</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ url()->to(route('assets.template')) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 2.75A.75.75 0 0 1 3.75 2h9.896a2 2 0 0 1 1.414.586l2.354 2.354A2 2 0 0 1 18 6.146V17.25a.75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 17.25V2.75zM12 3.5V6h2.5L12 3.5zM8.5 9a.75.75 0 0 1 .75.75v5.69l1.47-1.47a.75.75 0 0 1 1.06 1.06l-2.25 2.25a.75.75 0 0 1-1.06 0l-2.25-2.25a.75.75 0 1 1 1.06-1.06l1.47 1.47v-5.69A.75.75 0 0 1 8.5 9z" clip-rule="evenodd" />
                        </svg>
                        Download Template Excel
                    </a>
                </div>
            </div>
            <form action="{{ route('assets.import') }}" method="POST" enctype="multipart/form-data" class="mt-4 flex flex-wrap items-end gap-3">
                @csrf
                <div>
                    <label for="file" class="text-sm font-medium text-gray-900 dark:text-white">Pilih File (xlsx/csv)</label>
                    <input id="file" name="file" type="file" accept=".xlsx,.xls,.csv" required class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-white" />
                    @error('file')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Upload Excel</button>
            </form>
        </div>

        @can('manage assets')
        <!-- Export Assets -->
        <div class="mb-6 rounded-xl border-2 border-blue-500 bg-white p-4 shadow-sm dark:border-blue-400 dark:bg-dark-800">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Export Data Aset</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Export data aset berdasarkan periode waktu tertentu.</p>
                </div>
            </div>
            <form action="{{ route('assets.export') }}" method="GET" class="mt-4 flex flex-wrap items-end gap-3">
                <div>
                    <label for="period" class="text-sm font-medium text-gray-900 dark:text-white">Periode Preset</label>
                    <select id="period" name="period" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-white">
                        <option value="">Pilih Periode</option>
                        <option value="q1">Triwulan I (Jan-Mar)</option>
                        <option value="q2">Triwulan II (Apr-Jun)</option>
                        <option value="q3">Triwulan III (Jul-Sep)</option>
                        <option value="q4">Triwulan IV (Oct-Dec)</option>
                        <option value="year">Tahun Ini</option>
                    </select>
                </div>
                <div>
                    <label for="start_date" class="text-sm font-medium text-gray-900 dark:text-white">Tanggal Mulai</label>
                    <input id="start_date" name="start_date" type="date" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-white" />
                </div>
                <div>
                    <label for="end_date" class="text-sm font-medium text-gray-900 dark:text-white">Tanggal Akhir</label>
                    <input id="end_date" name="end_date" type="date" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-white" />
                </div>
                <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 inline h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Export Excel
                </button>
            </form>
        </div>
        @endcan

        <!-- Search and Filters -->
        <form id="asset-filters" method="GET" action="{{ route('assets.index') }}" class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-dark-800">
            <div class="grid gap-4 lg:grid-cols-4">
                <div class="col-span-full lg:col-span-2">
                    <label for="search" class="text-sm font-medium text-gray-900 dark:text-white">Cari Aset</label>
                    <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Kode, nama, serial, pegawai, lokasi..." class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-white" />
                </div>

                <div>
                    <label for="status" class="text-sm font-medium text-gray-900 dark:text-white">Status Aset</label>
                    <select id="status" name="status" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-white">
                        <option value="">Semua Status</option>
                        @foreach(\App\Models\Asset::statusOptions() as $value => $label)
                            <option value="{{ $value }}"{{ request('status') === $value ? ' selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="condition" class="text-sm font-medium text-gray-900 dark:text-white">Kondisi</label>
                    <select id="condition" name="condition" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-white">
                        <option value="">Semua Kondisi</option>
                        <option value="GOOD"{{ request('condition') === 'GOOD' ? ' selected' : '' }}>Baik</option>
                        <option value="LIGHT"{{ request('condition') === 'LIGHT' ? ' selected' : '' }}>Rusak Ringan</option>
                        <option value="HEAVY"{{ request('condition') === 'HEAVY' ? ' selected' : '' }}>Rusak Berat</option>
                    </select>
                </div>

                <div>
                    <label for="type" class="text-sm font-medium text-gray-900 dark:text-white">Jenis Aset</label>
                    <input id="type" name="type" type="text" value="{{ request('type') }}" placeholder="Contoh: Laptop" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-white" />
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Terapkan Filter</button>
                <a href="{{ route('assets.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10">Reset</a>
            </div>
        </form>

        <!-- Assets Table -->
        @php
            $sort = request('sort');
            $direction = request('direction', 'asc') === 'desc' ? 'desc' : 'asc';
            $baseQuery = request()->except('page');
        @endphp

        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800 overflow-hidden">
            <!-- Table Header -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/5">
                        <tr>
                            <th class="px-5 py-3.5 text-left sm:px-6">
                                @php $nextDir = ($sort === 'asset_code' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'asset_code', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    NO BMN
                                    @if($sort === 'asset_code') <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left sm:px-6">
                                @php $nextDir = ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'name', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Nama
                                    @if($sort === 'name') <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left sm:px-6">
                                @php $nextDir = ($sort === 'serial_number' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'serial_number', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Asset Tag
                                    @if($sort === 'serial_number') <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left sm:px-6">
                                @php $nextDir = ($sort === 'purchased_at' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'purchased_at', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Tanggal Perolehan
                                    @if($sort === 'purchased_at') <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left sm:px-6">
                                @php $nextDir = ($sort === 'nilai_perolehan' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'nilai_perolehan', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Nilai Perolehan
                                    @if($sort === 'nilai_perolehan') <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left sm:px-6">
                                @php $nextDir = ($sort === 'location' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'location', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Lokasi Aset
                                    @if($sort === 'location') <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left sm:px-6">
                                @php $nextDir = ($sort === 'kode_satker' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'kode_satker', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Kode Satker
                                    @if($sort === 'kode_satker') <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left sm:px-6">
                                @php $nextDir = ($sort === 'holder' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'holder', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Nama Pegawai
                                    @if($sort === 'holder') <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left sm:px-6">
                                @php $nextDir = ($sort === 'type' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'type', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Jenis Barang / Kategori
                                    @if($sort === 'type') <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left sm:px-6">
                                @php $nextDir = ($sort === 'brand' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'brand', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Merek
                                    @if($sort === 'brand') <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left sm:px-6">
                                @php $nextDir = ($sort === 'condition' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'condition', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Kondisi
                                    @if($sort === 'condition') <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left sm:px-6">
                                @php $nextDir = ($sort === 'status' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'status', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Status
                                    @if($sort === 'status') <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-right sm:px-6">
                                <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Aksi</span>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($assets as $asset)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $asset->asset_code }}</span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $asset->name }}</span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $asset->serial_number ?? '-' }}</span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $asset->purchased_at ? $asset->purchased_at->format('d/m/Y') : '-' }}</span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $asset->nilai_perolehan ? 'Rp ' . number_format($asset->nilai_perolehan, 2, ',', '.') : '-' }}</span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $asset->location ?? '-' }}</span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $asset->kode_satker ?? '-' }}</span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $asset->holder ?? '-' }}</span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $asset->type ?? '-' }}</span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $asset->brand ?? '-' }}</span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
                                        @if($asset->condition === 'GOOD') bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400
                                        @elseif($asset->condition === 'LIGHT') bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400
                                        @elseif($asset->condition === 'HEAVY') bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400
                                        @else bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400
                                        @endif">
                                        {{ $asset->condition_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
                                        @if($asset->status === 'ACTIVE') bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400
                                        @elseif($asset->status === 'INACTIVE') bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400
                                        @elseif($asset->status === 'PENDING') bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-400
                                        @elseif($asset->status === 'DECOMMISSIONED') bg-red-100 text-red-800 dark:bg-red-500/15 dark:text-red-400
                                        @else bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400
                                        @endif">
                                        {{ $asset->status_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right sm:px-6">
                                    <a href="{{ url()->to(route('assets.show', $asset)) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="px-5 py-8 text-center sm:px-6">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada aset</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($assets->hasPages())
                <x-pagination :paginator="$assets" />
            @endif
        </div>
    </div>
</div>

<script>
document.getElementById('period').addEventListener('change', function() {
    const period = this.value;
    const year = new Date().getFullYear();
    let startDate = '';
    let endDate = '';

    switch(period) {
        case 'q1':
            startDate = `${year}-01-01`;
            endDate = `${year}-03-31`;
            break;
        case 'q2':
            startDate = `${year}-04-01`;
            endDate = `${year}-06-30`;
            break;
        case 'q3':
            startDate = `${year}-07-01`;
            endDate = `${year}-09-30`;
            break;
        case 'q4':
            startDate = `${year}-10-01`;
            endDate = `${year}-12-31`;
            break;
        case 'year':
            startDate = `${year}-01-01`;
            endDate = `${year}-12-31`;
            break;
    }

    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
});
</script>
</x-app-layout>
