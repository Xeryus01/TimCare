<x-app-layout>
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">{{ $reservation->room_name }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $reservation->code }} • {{ $reservation->status_label }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ url()->to(route('reservations.index')) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Kembali</a>
                @if(auth()->user()->hasRole('Admin') || (! in_array($reservation->status, [\App\Models\Reservation::STATUS_CANCELLED]) && (auth()->user()->hasAnyRole(['Teknisi']) || auth()->id() === $reservation->requester_id)))
                    <a href="{{ url()->to(route('reservations.edit', $reservation)) }}" class="rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700">Ubah</a>
                @endif
                @if(auth()->id() === $reservation->requester_id && ! in_array($reservation->status, [\App\Models\Reservation::STATUS_CANCELLED, \App\Models\Reservation::STATUS_COMPLETED, \App\Models\Reservation::STATUS_REJECTED]))
                    <form method="POST" action="{{ route('reservations.update', $reservation) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ \App\Models\Reservation::STATUS_CANCELLED }}" />
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Batalkan Pengajuan</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3 min-w-0">
            <div class="space-y-6 lg:col-span-2 min-w-0">
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Detail Pengajuan</h2>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $reservation->purpose }}</p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Waktu mulai</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $reservation->start_time->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Waktu selesai</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $reservation->end_time->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tim / Divisi</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $reservation->team_name ?: 'Tidak disertakan' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Jumlah peserta</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $reservation->participants_count ?: '1' }}</p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Operator Zoom</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $reservation->operator_needed ? 'Ya' : 'Tidak' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Breakout Room</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $reservation->breakroom_needed ? 'Ya' : 'Tidak' }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Nota Dinas</h2>
                    @if($reservation->nota_dinas_path)
                        <div class="space-y-4">
                            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700 overflow-hidden">
                                <div class="mb-3 flex items-center justify-between gap-3 min-w-0">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Nota Dinas Pengajuan</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">File PDF yang diunggah saat pengajuan</p>
                                    </div>
                                    <a href="{{ url()->to(route('reservations.nota-dinas', $reservation)) }}" target="_blank" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Buka PDF</a>
                                </div>

                                <iframe src="{{ url()->to(route('reservations.nota-dinas', $reservation)) }}" title="Nota Dinas" class="h-96 w-full rounded-lg border border-gray-200 dark:border-gray-700"></iframe>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nota dinas belum diunggah.</p>
                    @endif
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Hasil Follow Up</h2>
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Link Zoom</p>
                            @if($reservation->zoom_link)
                                <a href="{{ $reservation->zoom_link }}" target="_blank" class="font-medium text-brand-600 hover:underline">{{ $reservation->zoom_link }}</a>
                            @else
                                <p class="text-gray-700 dark:text-gray-300">Belum ditambahkan.</p>
                            @endif
                        </div>
                        @if($reservation->zoom_link)
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Link Record Zoom</p>
                                @if($reservation->zoom_record_link)
                                    <a href="{{ $reservation->zoom_record_link }}" target="_blank" class="font-medium text-brand-600 hover:underline">{{ $reservation->zoom_record_link }}</a>
                                @else
                                    <p class="text-gray-700 dark:text-gray-300">Belum ditambahkan.</p>
                                @endif
                            </div>
                        @endif
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Catatan tindak lanjut</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $reservation->notes ?: 'Belum ada catatan dari petugas.' }}</p>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->hasPermissionTo('approve reservations'))
                    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Tindak Lanjut oleh Teknisi / Admin</h2>
                        
                        @if($errors->any())
                            <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-500/10 dark:text-red-400">
                                <ul class="list-inside list-disc space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('reservations.update', $reservation) }}" class="space-y-6">
                            @csrf
                            @method('PATCH')

                            <div class="grid gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status</label>
                                    <select id="status" name="status" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('status') border-red-500 @enderror">
                                        @foreach(\App\Models\Reservation::statusLabels() as $value => $label)
                                            <option value="{{ $value }}" {{ $reservation->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="zoom_link" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                    Link Zoom 
                                    @if($reservation->status === \App\Models\Reservation::STATUS_APPROVED || request()->input('status') === \App\Models\Reservation::STATUS_APPROVED)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                <input id="zoom_link" type="url" name="zoom_link" value="{{ old('zoom_link', $reservation->zoom_link) }}" placeholder="https://zoom.us/j/123456789/..." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('zoom_link') border-red-500 @enderror" 
                                       @if($reservation->status === \App\Models\Reservation::STATUS_APPROVED || request()->input('status') === \App\Models\Reservation::STATUS_APPROVED) required @endif />
                                @error('zoom_link')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            @if($reservation->zoom_link)
                                <div>
                                    <label for="zoom_record_link" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Link Record Zoom <span class="text-gray-400">(Opsional)</span></label>
                                    <input id="zoom_record_link" type="url" name="zoom_record_link" value="{{ old('zoom_record_link', $reservation->zoom_record_link) }}" placeholder="https://zoom.us/rec/..." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('zoom_record_link') border-red-500 @enderror" />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hanya bisa ditambahkan setelah link zoom diberikan.</p>
                                    @error('zoom_record_link')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <div>
                                <label for="notes" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Catatan Tindak Lanjut <span class="text-gray-400">(Opsional)</span></label>
                                <textarea id="notes" name="notes" rows="3" placeholder="Contoh: Link aktif 10 menit sebelum mulai." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('notes') border-red-500 @enderror">{{ old('notes', $reservation->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-3 pt-2">
                                <button type="submit" class="flex-1 rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-700">Simpan Follow Up</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h3 class="mb-3 text-sm font-bold uppercase text-gray-500 dark:text-gray-400">Status Penanganan</h3>
                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium
                        @if($reservation->status === \App\Models\Reservation::STATUS_PENDING) bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400
                        @elseif($reservation->status === \App\Models\Reservation::STATUS_APPROVED) bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400
                        @elseif($reservation->status === \App\Models\Reservation::STATUS_WAITING_MONITORING) bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400
                        @elseif($reservation->status === \App\Models\Reservation::STATUS_COMPLETED) bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400
                        @elseif($reservation->status === \App\Models\Reservation::STATUS_REJECTED || $reservation->status === \App\Models\Reservation::STATUS_CANCELLED) bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400
                        @else bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400
                        @endif">
                        {{ $reservation->status_label }}
                    </span>

                    <div class="mt-4 space-y-3 text-sm text-gray-700 dark:text-gray-300">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Dibuat</p>
                            <p>{{ $reservation->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Terakhir diperbarui</p>
                            <p>{{ $reservation->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if(auth()->user()->hasRole('Admin'))
                        <form method="POST" action="{{ route('reservations.update', $reservation) }}" class="mt-6 space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="approver_id_sidebar" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Petugas</label>
                                <select id="approver_id_sidebar" name="approver_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-dark-800 dark:text-white">
                                    <option value="">Belum ditentukan</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}" {{ $reservation->approver_id == $technician->id ? 'selected' : '' }}>{{ $technician->name }}</option>
                                    @endforeach
                                </select>
                                @error('approver_id')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2 text-white">Perbarui Petugas</button>
                        </form>
                    @endif
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h3 class="mb-3 text-sm font-bold uppercase text-gray-500 dark:text-gray-400">Ringkasan</h3>
                    <div class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Pemohon</p>
                            <p>{{ optional($reservation->requester)->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Ditangani oleh</p>
                            <p>{{ optional($reservation->approver)->name ?? 'Belum ada petugas' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Dibuat pada</p>
                            <p>{{ $reservation->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Terakhir diperbarui</p>
                            <p>{{ $reservation->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Scroll to top if there's a success message
    @if(session('success'))
        window.addEventListener('load', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    @endif

    // Highlight zoom link field if it was just updated
    @if(session('success') && strpos(session('success'), 'Zoom') !== false)
        document.addEventListener('DOMContentLoaded', function() {
            const zoomLinkInput = document.getElementById('zoom_link');
            if (zoomLinkInput) {
                zoomLinkInput.classList.add('ring-2', 'ring-green-500', 'border-green-500');
                setTimeout(() => {
                    zoomLinkInput.classList.remove('ring-2', 'ring-green-500', 'border-green-500');
                }, 3000);
            }
        });
    @endif

    // Handle zoom link requirement based on status
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        const zoomLinkInput = document.getElementById('zoom_link');
        const zoomLinkLabel = document.querySelector('label[for="zoom_link"]');

        function updateZoomLinkRequirement() {
            const approvedStatus = '{{ \App\Models\Reservation::STATUS_APPROVED }}';
            const isApproved = statusSelect.value === approvedStatus;
            const requiredSpan = zoomLinkLabel.querySelector('.text-red-500');

            if (isApproved) {
                zoomLinkInput.setAttribute('required', 'required');
                if (!requiredSpan) {
                    const span = document.createElement('span');
                    span.className = 'text-red-500';
                    span.textContent = '*';
                    zoomLinkLabel.appendChild(span);
                }
            } else {
                zoomLinkInput.removeAttribute('required');
                if (requiredSpan) {
                    requiredSpan.remove();
                }
            }
        }

        if (statusSelect && zoomLinkInput) {
            statusSelect.addEventListener('change', updateZoomLinkRequirement);
            // Initial check
            updateZoomLinkRequirement();
        }
    });
</script>

</x-app-layout>