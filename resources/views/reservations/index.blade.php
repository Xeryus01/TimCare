<x-app-layout>
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Pengajuan Zoom</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">User mengajukan kebutuhan Zoom, lalu teknisi atau admin melakukan follow up dan menambahkan link meeting.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ url()->to(route('reservations.create')) }}" class="rounded-lg bg-brand-600 px-4 py-2 font-medium text-white hover:bg-brand-700">Ajukan Zoom</a>
                <a href="{{ url()->to(route('exports.reservations', request()->query())) }}" class="rounded-lg border border-gray-300 px-4 py-2 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Ekspor CSV</a>
            </div>
        </div>

        <div class="mb-4 rounded-3xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-dark-800">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Alur Pengajuan Zoom</h2>
            <div class="mt-3 grid gap-2 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-2 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-sky-600">1.</span>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Ajukan</p>
                    </div>
                    <p class="mt-1 text-[11px] leading-4 text-gray-600 dark:text-gray-400">Isi kebutuhan Zoom dan upload nota dinas.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-2 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-sky-600">2.</span>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Assign</p>
                    </div>
                    <p class="mt-1 text-[11px] leading-4 text-gray-600 dark:text-gray-400">Admin/teknisi menindaklanjuti permohonan.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-2 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-sky-600">3.</span>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Cek ID Meeting</p>
                    </div>
                    <p class="mt-1 text-[11px] leading-4 text-gray-600 dark:text-gray-400">Jika ruang id meeting zoom masih tersedia, lanjutkan. Jika tidak, konfirmasi.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-2 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-sky-600">4.</span>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Selesai</p>
                    </div>
                    <p class="mt-1 text-[11px] leading-4 text-gray-600 dark:text-gray-400">Link Zoom ditambahkan jika disetujui.</p>
                </div>
            </div>
        </div>

        <form method="GET" class="mb-4 flex flex-wrap gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-dark-800">
            <select name="status" class="rounded-lg border-gray-300 px-3 py-2 dark:bg-dark-800 dark:text-white">
                <option value="">Semua status</option>
                @foreach(\App\Models\Reservation::statusLabels() as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white">Terapkan</button>
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
                                @php $nextDir = ($sort === 'room_name' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'room_name', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1">
                                    Kegiatan / Ruang
                                    @if($sort === 'room_name')
                                        <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">
                                @php $nextDir = ($sort === 'start_time' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'start_time', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1">
                                    Jadwal
                                    @if($sort === 'start_time')
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
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">
                                @php $nextDir = ($sort === 'status' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'status', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1">
                                    Status
                                    @if($sort === 'status')
                                        <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Link Zoom</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">
                                @php $nextDir = ($sort === 'approver' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'approver', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1">
                                    Petugas
                                    @if($sort === 'approver')
                                        <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($reservations as $r)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-5 py-4 sm:px-6 text-sm font-semibold text-gray-900 dark:text-white">{{ $r->code }}</td>
                                <td class="px-5 py-4 sm:px-6">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $r->room_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($r->purpose, 45) }}</p>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $r->start_time->format('d/m/Y H:i') }}<br>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">s/d {{ $r->end_time->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300">{{ optional($r->requester)->name ?? '-' }}</td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $r->status_badge_classes }}">
                                        {{ $r->status_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300">
                                    @if($r->zoom_link)
                                        <a href="{{ $r->zoom_link }}" target="_blank" class="text-brand-600 hover:underline">Buka link</a>
                                    @else
                                        <span class="text-gray-400">Belum ada</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300">{{ optional($r->approver)->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-right sm:px-6">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ url()->to(route('reservations.show', $r)) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">Detail</a>
                                        @if(auth()->user()->hasRole('Admin'))
                                            <form method="POST" action="{{ route('reservations.destroy', $r) }}" class="inline" onsubmit="return confirm('Hapus pengajuan Zoom {{ $r->code }}? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:px-6">Belum ada pengajuan Zoom.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reservations->hasPages())
                <x-pagination :paginator="$reservations" />
            @endif
        </div>
    </div>
</div>
</x-app-layout>
