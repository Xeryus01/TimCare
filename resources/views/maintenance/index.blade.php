<x-app-layout>
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Pemeliharaan</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Daftar tiket yang menunggu ketersediaan barang untuk diproses pemeliharaan.</p>
            </div>
        </div>

        <div class="mb-6 rounded-3xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-dark-800">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Alur Pemeliharaan</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-sky-600">1.</span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Menunggu Barang</p>
                    </div>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Tiket dalam status menunggu ketersediaan barang.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-sky-600">2.</span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Proses (ULP)</p>
                    </div>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Petugas ULP memproses pengadaan barang.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-sky-600">3.</span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Selesai (ULP)</p>
                    </div>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">ULP menyelesaikan pemeliharaan dan menutup tiket.</p>
                </div>
            </div>
        </div>

        <form method="GET" class="mb-4 flex flex-wrap gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-dark-800">
            <input type="text" name="code" value="{{ request('code') }}" placeholder="Cari kode tiket..." class="rounded-lg border-gray-300 px-3 py-2 dark:bg-dark-800 dark:text-white">
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white">Cari</button>
        </form>

        @php
            $sort = request('sort');
            $direction = request('direction', 'asc') === 'desc' ? 'desc' : 'asc';
            $baseQuery = request()->except('page');
        @endphp

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/5">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">
                                @php $nextDir = ($sort === 'code' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'code', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1">
                                    Kode
                                    @if($sort === 'code')
                                        <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">
                                @php $nextDir = ($sort === 'title' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'title', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1">
                                    Keluhan
                                    @if($sort === 'title')
                                        <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">
                                @php $nextDir = ($sort === 'created_at' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'created_at', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1">
                                    Waktu Pembuatan
                                    @if($sort === 'created_at')
                                        <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">
                                @php $nextDir = ($sort === 'requester' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'requester', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1">
                                    Pemohon
                                    @if($sort === 'requester')
                                        <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Pemeliharaan</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Status Tiket</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Petugas</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $ticket->code }}</span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $ticket->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->category_label }}</p>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300">{{ optional($ticket->requester)->name ?? '-' }}</td>
                                <td class="px-5 py-4 sm:px-6">
                                    @if($ticket->is_processed)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-500/15 dark:text-amber-400">
                                            <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Diproses
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-500/15 dark:text-gray-400">
                                            <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Belum Diproses
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $ticket->status_badge_classes }}">
                                        {{ $ticket->status_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300">{{ optional($ticket->assignee)->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-right sm:px-6">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Admin & Teknisi hanya lihat detail --}}
                                        @if(auth()->user()->hasAnyRole(['Admin', 'Teknisi']))
                                            <a href="{{ url()->to(route('tickets.show', $ticket)) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm hover:bg-blue-100 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400 dark:hover:bg-blue-500/20">
                                                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Detail
                                            </a>
                                        @endif

                                        {{-- ULP: tombol Proses (jika belum) dan Selesai --}}
                                        @if(auth()->user()->hasRole('ULP'))
                                            @if(!$ticket->is_processed)
                                                <form method="POST" action="{{ route('maintenance.process', $ticket) }}" class="inline" onsubmit="return confirm('Proses pemeliharaan tiket {{ $ticket->code }}?')">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 shadow-sm hover:bg-amber-100 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400 dark:hover:bg-amber-500/20">
                                                        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Proses
                                                    </button>
                                                </form>
                                            @endif

                                            <button type="button" onclick="document.getElementById('completeModal{{ $ticket->id }}').classList.remove('hidden')" class="inline-flex items-center gap-1.5 rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 shadow-sm hover:bg-green-100 dark:border-green-500/20 dark:bg-green-500/10 dark:text-green-400 dark:hover:bg-green-500/20">
                                                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                Selesai
                                            </button>

                                            {{-- Modal Selesai Pemeliharaan --}}
                                            <div id="completeModal{{ $ticket->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50" onclick="if(event.target === this) this.classList.add('hidden')">
                                                <div class="w-full max-w-lg mx-4 rounded-2xl bg-white p-6 shadow-xl dark:bg-dark-800 dark:border dark:border-gray-700" onclick="event.stopPropagation()">
                                                    <div class="flex items-center justify-between mb-5">
                                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Selesaikan Pemeliharaan</h3>
                                                        <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">&times;</button>
                                                    </div>
                                                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Tiket <strong>{{ $ticket->code }}</strong> — {{ $ticket->title }}</p>
                                                    <form method="POST" action="{{ route('maintenance.complete', $ticket) }}" enctype="multipart/form-data" class="space-y-4">
                                                        @csrf
                                                        <div>
                                                            <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status Penyelesaian <span class="text-red-500">*</span></label>
                                                            <select name="resolution_status" required class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:bg-dark-800 dark:text-white">
                                                                <option value="">Pilih status</option>
                                                                <option value="{{ \App\Models\Ticket::STATUS_SOLVED }}">Selesai</option>
                                                                <option value="{{ \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES }}">Selesai dengan Catatan</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Catatan (opsional)</label>
                                                            <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:bg-dark-800 dark:text-white" placeholder="Tambahkan catatan penyelesaian..."></textarea>
                                                        </div>
                                                        <div>
                                                            <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Lampiran Foto (opsional)</label>
                                                            <input type="file" name="attachment" accept="image/*,.pdf" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:bg-dark-800 dark:text-white">
                                                        </div>
                                                        <div class="flex justify-end gap-3 pt-2">
                                                            <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Batal</button>
                                                            <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">Selesaikan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:px-6">Tidak ada tiket yang menunggu pemeliharaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tickets->hasPages())
                <x-pagination :paginator="$tickets" />
            @endif
        </div>
    </div>
</div>
</x-app-layout>