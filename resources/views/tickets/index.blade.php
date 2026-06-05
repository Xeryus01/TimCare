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
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white">Terapkan</button>
        </form>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/5">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Kode</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Keluhan</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Pemohon</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Status</th>
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
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300">{{ optional($ticket->requester)->name ?? '-' }}</td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
                                        @if($ticket->status === \App\Models\Ticket::STATUS_OPEN) bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400
                                        @elseif($ticket->status === \App\Models\Ticket::STATUS_ASSIGNED_DETECT) bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400
                                        @elseif($ticket->status === \App\Models\Ticket::STATUS_WAITING_PARTS) bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400
                                        @elseif($ticket->status === \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES) bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-400
                                        @elseif($ticket->status === \App\Models\Ticket::STATUS_SOLVED) bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400
                                        @elseif($ticket->status === \App\Models\Ticket::STATUS_REJECTED) bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400
                                        @elseif($ticket->status === \App\Models\Ticket::STATUS_CANCELLED) bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400
                                        @else bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400
                                        @endif">
                                        {{ $ticket->status_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300">{{ optional($ticket->assignee)->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-right sm:px-6">
                                    <a href="{{ url()->to(route('tickets.show', $ticket)) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:px-6">Belum ada tiket yang sesuai filter.</td>
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
