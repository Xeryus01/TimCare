<x-app-layout>
    <div class="min-h-screen">
        <div class="p-5 sm:p-7.5 lg:p-9">
            <!-- Page Header -->
            <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">{{ $asset->name }}</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $asset->asset_code }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @can('manage assets')
                        <a href="{{ route('assets.changeHolder', $asset) }}" class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100 dark:border-blue-500/30 dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/20">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2z"/>
                            </svg>
                            Ubah Pemegang
                        </a>
                        <a href="{{ route('assets.addMaintenance', $asset) }}" class="inline-flex items-center gap-2 rounded-lg border border-green-300 bg-green-50 px-4 py-2 text-sm font-medium text-green-700 hover:bg-green-100 dark:border-green-500/30 dark:bg-green-500/15 dark:text-green-400 dark:hover:bg-green-500/20">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Catat Perawatan
                        </a>
                    @endcan
                    <a href="{{ route('assets.edit', $asset) }}" class="inline-flex items-center gap-2 rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 hover:bg-amber-100 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-400 dark:hover:bg-amber-500/20">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('assets.destroy', $asset) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Yakin ingin menghapus aset ini?')" class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/20">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Asset Details -->
                    <div class="rounded-xl border border-gray-200 bg-white p-5 sm:p-7.5 dark:border-gray-700 dark:bg-dark-800">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white">Informasi Aset</h2>
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">NO BMN</p>
                                <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $asset->asset_code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nama</p>
                                <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $asset->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Asset Tag</p>
                                <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $asset->serial_number ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Perolehan</p>
                                <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ optional($asset->purchased_at)->format('d/m/Y') ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nilai Perolehan</p>
                                <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $asset->nilai_perolehan ? 'Rp ' . number_format($asset->nilai_perolehan, 2, ',', '.') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Lokasi Aset</p>
                                <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $asset->location ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kode Satker</p>
                                <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $asset->kode_satker ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nama Pegawai</p>
                                <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $asset->holder ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">NIP Pegawai</p>
                                <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $asset->nip_pegawai ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Jenis Barang / Kategori</p>
                                <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $asset->type ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Merek</p>
                                <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $asset->brand ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kondisi</p>
                                <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $asset->condition_label }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $asset->status_label }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- History Pemegang -->
                    <div class="rounded-xl border border-gray-200 bg-white p-5 sm:p-7.5 dark:border-gray-700 dark:bg-dark-800">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Riwayat Pemegang Aset</h2>
                        @if($asset->holderHistory && $asset->holderHistory->count() > 0)
                            <div class="space-y-4">
                                @foreach($asset->holderHistory as $history)
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-white/5">
                                        <div class="mb-2 flex items-start justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($history->changed_at)->format('d/m/Y') }}</p>
                                                <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ $history->previous_holder ?? 'Awal' }} → {{ $history->new_holder }}
                                                </p>
                                            </div>
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $history->changedByUser->name ?? '-' }}</span>
                                        </div>
                                        @if($history->notes)
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $history->notes }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4 text-center dark:border-gray-600 dark:bg-white/5">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat perubahan pemegang</p>
                            </div>
                        @endif
                    </div>

                    <!-- History Perawatan -->
                    <div class="rounded-xl border border-gray-200 bg-white p-5 sm:p-7.5 dark:border-gray-700 dark:bg-dark-800">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Riwayat Perawatan</h2>
                        @if($asset->maintenances && $asset->maintenances->count() > 0)
                            <div class="space-y-4">
                                @foreach($asset->maintenances as $maintenance)
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-white/5">
                                        <div class="mb-3 flex items-start justify-between">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-500/15 dark:text-blue-400">
                                                        {{ $maintenance->getTypeLabel() }}
                                                    </span>
                                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($maintenance->maintenance_date)->format('d/m/Y') }}</p>
                                                </div>
                                                <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $maintenance->description }}</p>
                                            </div>
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $maintenance->performedByUser->name ?? '-' }}</span>
                                        </div>
                                        
                                        @if($maintenance->findings)
                                            <div class="mb-2">
                                                <p class="text-xs font-medium uppercase text-gray-600 dark:text-gray-400">Temuan</p>
                                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $maintenance->findings }}</p>
                                            </div>
                                        @endif

                                        @if($maintenance->actions_taken)
                                            <div class="mb-2">
                                                <p class="text-xs font-medium uppercase text-gray-600 dark:text-gray-400">Tindakan</p>
                                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $maintenance->actions_taken }}</p>
                                            </div>
                                        @endif

                                        <div class="mt-3 flex flex-wrap gap-3 border-t border-gray-200 pt-3 dark:border-gray-700">
                                            @if($maintenance->condition_before)
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Kondisi Sebelum</p>
                                                    <p class="font-medium text-gray-900 dark:text-white">{{ $maintenance->condition_before_label }}</p>
                                                </div>
                                            @endif
                                            @if($maintenance->condition_after)
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Kondisi Sesudah</p>
                                                    <p class="font-medium text-gray-900 dark:text-white">{{ $maintenance->condition_after_label }}</p>
                                                </div>
                                            @endif
                                            @if($maintenance->next_maintenance_date)
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Perawatan Berikutnya</p>
                                                    <p class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($maintenance->next_maintenance_date)->format('d/m/Y') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4 text-center dark:border-gray-600 dark:bg-white/5">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat perawatan</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="rounded-3xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-dark-800">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Foto Aset</h2>
                        <div class="grid gap-4">
                            <div class="rounded-3xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-white/5">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Foto Serial Number</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Foto label atau nomor seri</p>
                                    </div>
                                </div>
                                <div class="mt-4 overflow-hidden rounded-3xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800">
                                    @if($asset->photo_serial)
                                        @if(str_starts_with($asset->photo_serial, 'drive:'))
                                            <iframe src="{{ \App\Models\Asset::googleDrivePreviewUrl(substr($asset->photo_serial, 6)) }}" class="h-44 w-full" frameborder="0" allowfullscreen></iframe>
                                        @elseif(preg_match('/^https?:\/\//', $asset->photo_serial))
                                            <img src="{{ $asset->photo_serial }}" class="h-44 w-full object-contain" alt="Foto Serial" />
                                        @else
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($asset->photo_serial) }}" class="h-44 w-full object-contain" alt="Foto Serial" />
                                        @endif
                                    @else
                                        <div class="flex h-44 items-center justify-center text-center text-sm text-gray-500 dark:text-gray-400">Belum ada foto serial</div>
                                    @endif
                                </div>
                            </div>
                            <div class="rounded-3xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-white/5">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Foto Barang/Aset</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Foto tampilan fisik aset</p>
                                    </div>
                                </div>
                                <div class="mt-4 overflow-hidden rounded-3xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800">
                                    @if($asset->photo_asset)
                                        @if(str_starts_with($asset->photo_asset, 'drive:'))
                                            <iframe src="{{ \App\Models\Asset::googleDrivePreviewUrl(substr($asset->photo_asset, 6)) }}" class="h-44 w-full" frameborder="0" allowfullscreen></iframe>
                                        @elseif(preg_match('/^https?:\/\//', $asset->photo_asset))
                                            <img src="{{ $asset->photo_asset }}" class="h-44 w-full object-contain" alt="Foto Aset" />
                                        @else
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($asset->photo_asset) }}" class="h-44 w-full object-contain" alt="Foto Aset" />
                                        @endif
                                    @else
                                        <div class="flex h-44 items-center justify-center text-center text-sm text-gray-500 dark:text-gray-400">Belum ada foto aset</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-3xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-dark-800">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Status Aset</h2>
                        <div class="space-y-4">
                            <div class="rounded-3xl bg-gray-50 p-4 dark:bg-white/5">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                <p class="mt-2 inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800 dark:bg-green-500/15 dark:text-green-400">{{ $asset->status_label }}</p>
                            </div>
                            <div class="rounded-3xl bg-gray-50 p-4 dark:bg-white/5">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kondisi</p>
                                <p class="mt-2 inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-800 dark:bg-amber-500/15 dark:text-amber-400">{{ $asset->condition_label }}</p>
                            </div>
                            <div class="rounded-3xl bg-gray-50 p-4 dark:bg-white/5">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nama Pegawai</p>
                                <p class="mt-2 font-medium text-gray-900 dark:text-white">{{ $asset->holder ?? 'Belum Ditugaskan' }}</p>
                            </div>
                            <div class="rounded-3xl bg-gray-50 p-4 dark:bg-white/5">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Dibuat</p>
                                <p class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $asset->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="rounded-3xl bg-gray-50 p-4 dark:bg-white/5">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Terakhir Diperbarui</p>
                                <p class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $asset->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
