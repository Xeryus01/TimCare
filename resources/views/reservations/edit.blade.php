<x-app-layout>
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Edit Pengajuan Zoom</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ubah detail pengajuan Zoom Anda.</p>
        </div>

        <div class="max-w-3xl mx-auto">
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800 sm:p-8">
                @if($errors->any())
                    <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-500/10 dark:text-red-400">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    $canEditZoomDetails = auth()->user()->hasAnyRole(['Admin', 'Teknisi']) || ! $reservation->zoom_link;
                @endphp

                <form method="POST" action="{{ route('reservations.update', $reservation) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    @unless($canEditZoomDetails)
                        <div class="rounded-xl border border-yellow-300 bg-yellow-50 p-4 text-sm text-yellow-800 dark:border-yellow-700 dark:bg-yellow-950/20 dark:text-yellow-200">
                            <p class="font-semibold">Perhatian:</p>
                            <p>Pengajuan Zoom ini sudah memiliki link. Sebagai pengguna, Anda hanya dapat memperbarui Nota Dinas atau membatalkan pengajuan.</p>
                        </div>
                    @endunless

                    @if($canEditZoomDetails)
                        <div>
                            <label for="room_name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Nama Kegiatan</label>
                            <input id="room_name" type="text" name="room_name" value="{{ old('room_name', $reservation->room_name) }}" placeholder="Contoh: Workshop Laravel 2026" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('room_name') border-red-500 @enderror" />
                            @error('room_name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="purpose" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Keperluan</label>
                            <textarea id="purpose" name="purpose" rows="3" placeholder="Jelaskan tujuan pengajuan zoom ini..." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('purpose') border-red-500 @enderror">{{ old('purpose', $reservation->purpose) }}</textarea>
                            @error('purpose')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="team_name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Nama Tim / Divisi</label>
                                <input id="team_name" type="text" name="team_name" value="{{ old('team_name', $reservation->team_name) }}" placeholder="Contoh: Tim TI" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('team_name') border-red-500 @enderror" />
                                @error('team_name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="participants_count" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Jumlah Peserta</label>
                                <input id="participants_count" type="number" name="participants_count" value="{{ old('participants_count', $reservation->participants_count ?: 1) }}" min="1" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('participants_count') border-red-500 @enderror" />
                                @error('participants_count')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-white/5">
                                <input type="hidden" name="operator_needed" value="0" />
                                <input id="operator_needed" type="checkbox" name="operator_needed" value="1" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-dark-800" {{ old('operator_needed', $reservation->operator_needed) ? 'checked' : '' }} />
                                <label for="operator_needed" class="text-sm text-gray-700 dark:text-gray-300">Butuh Operator Zoom</label>
                            </div>
                            <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-white/5">
                                <input type="hidden" name="breakroom_needed" value="0" />
                                <input id="breakroom_needed" type="checkbox" name="breakroom_needed" value="1" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-dark-800" {{ old('breakroom_needed', $reservation->breakroom_needed) ? 'checked' : '' }} />
                                <label for="breakroom_needed" class="text-sm text-gray-700 dark:text-gray-300">Butuh Ruang Istirahat</label>
                            </div>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="start_time_local" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Waktu Mulai</label>
                                <input id="start_time_local" type="datetime-local" name="start_time_local" value="{{ old('start_time_local', $reservation->start_time->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('start_time_local') border-red-500 @enderror" />
                                @error('start_time_local')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="end_time_local" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Waktu Selesai</label>
                                <input id="end_time_local" type="datetime-local" name="end_time_local" value="{{ old('end_time_local', $reservation->end_time->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('end_time_local') border-red-500 @enderror" />
                                @error('end_time_local')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-dark-800">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Nama Kegiatan</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $reservation->room_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Tim / Divisi</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $reservation->team_name }}</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Keperluan</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $reservation->purpose }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Waktu Mulai</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $reservation->start_time->format('d/m/Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Waktu Selesai</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $reservation->end_time->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label for="nota_dinas" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Nota Dinas</label>
                        @if($reservation->nota_dinas_path)
                            <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Nota dinas saat ini: <a href="{{ url()->to(route('reservations.nota-dinas', $reservation)) }}" target="_blank" class="text-brand-600 hover:text-brand-700">Lihat PDF</a></p>
                        @endif
                        <input id="nota_dinas" type="file" name="nota_dinas" accept=".pdf" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 dark:file:bg-brand-500/10 dark:file:text-brand-300 @error('nota_dinas') border-red-500 @enderror" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload nota dinas baru dalam format PDF (maksimal 1MB) - opsional, kosongkan jika tidak ingin mengubah</p>
                        @error('nota_dinas')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cancel Option for Regular Users -->
                    @if(!auth()->user()->hasPermissionTo('approve reservations') && $reservation->status !== \App\Models\Reservation::STATUS_CANCELLED && $reservation->status !== \App\Models\Reservation::STATUS_COMPLETED && $reservation->status !== \App\Models\Reservation::STATUS_REJECTED)
                        <div>
                            <label for="status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Aksi</label>
                            <select id="status" name="status" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('status') border-red-500 @enderror">
                                <option value="">-- Pilih Aksi --</option>
                                <option value="{{ \App\Models\Reservation::STATUS_CANCELLED }}">Batalkan Pengajuan</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="flex gap-3 pt-6">
                        <a href="{{ url()->to(route('reservations.show', $reservation)) }}" class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Batal</a>
                        <button type="submit" class="flex-1 rounded-lg bg-brand-600 px-4 py-2.5 text-center font-medium text-white hover:bg-brand-700">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
