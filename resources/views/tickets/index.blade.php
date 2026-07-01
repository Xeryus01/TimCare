<x-app-layout>
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Tiket Permasalahan</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">User mengajukan keluhan, lalu teknisi atau admin menindaklanjuti sampai selesai.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ url()->to(route('tickets.create')) }}" class="inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 font-medium text-white hover:bg-brand-700">Ajukan Tiket</a>
                <a href="{{ url()->to(route('exports.tickets', request()->query())) }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Ekspor CSV</a>
            </div>
        </div>

        <div class="mb-6 rounded-3xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-dark-800">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Alur Tiket Permasalahan</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-sky-600">1.</span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Ajukan tiket</p>
                    </div>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Isi keluhan, detail, dan kategori.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-sky-600">2.</span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Teknisi follow-up</p>
                    </div>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Assign, verifikasi, dan update status.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-sky-600">3.</span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Selesai</p>
                    </div>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Tiket ditutup saat masalah teratasi.</p>
                </div>
            </div>
        </div>

        <form method="GET" class="mb-4 flex flex-wrap gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-dark-800">
            <select name="status" class="rounded-lg border-gray-300 px-3 py-2 dark:bg-dark-800 dark:text-white">
                <option value="">Semua status</option>
                @foreach(\App\Models\Ticket::statusLabels() as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="requester_id" class="rounded-lg border-gray-300 px-3 py-2 dark:bg-dark-800 dark:text-white">
                <option value="">Semua pemohon</option>
                @foreach($requesters as $r)
                    <option value="{{ $r->id }}" {{ (string) request('requester_id') === (string) $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                @endforeach
            </select>
            <select name="assignee_id" class="rounded-lg border-gray-300 px-3 py-2 dark:bg-dark-800 dark:text-white">
                <option value="">Semua petugas</option>
                @foreach($assignees as $a)
                    <option value="{{ $a->id }}" {{ (string) request('assignee_id') === (string) $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
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
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">
                                @php $nextDir = ($sort === 'status' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'status', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1">
                                    Status
                                    @if($sort === 'status')
                                        <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">
                                @php $nextDir = ($sort === 'assignee' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['sort' => 'assignee', 'direction' => $nextDir])) }}" class="inline-flex items-center gap-1">
                                    Petugas
                                    @if($sort === 'assignee')
                                        <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
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
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $ticket->status_badge_classes }}">
                                        {{ $ticket->status_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300">{{ optional($ticket->assignee)->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-right sm:px-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ url()->to(route('tickets.show', $ticket)) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm hover:bg-blue-100 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400 dark:hover:bg-blue-500/20">
                                            <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </a>
                                        @if(auth()->user()->hasRole('Admin'))
                                            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" class="inline" onsubmit="return confirm('Hapus tiket {{ $ticket->code }}? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 shadow-sm hover:bg-red-100 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20">
                                                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:px-6">Belum ada tiket yang sesuai filter.</td>
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
