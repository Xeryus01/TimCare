<x-app-layout>
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Ajukan Ruang Zoom</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Isi jadwal dan keperluan meeting. Admin atau teknisi akan menindaklanjuti dan menambahkan link Zoom.</p>
        </div>

        <div class="max-w-2xl mx-auto rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800 sm:p-8">
            @if($errors->any())
                <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-500/10 dark:text-red-400">
                    <p class="font-medium">Mohon periksa kembali data berikut:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('reservations.store') }}" enctype="multipart/form-data" class="space-y-6" id="reservationForm">
                @csrf

                <div>
                    <label for="room_name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Nama Kegiatan</label>
                    <input id="room_name" type="text" name="room_name" value="{{ old('room_name') }}" required placeholder="Contoh: Rapat Koordinasi Mingguan" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('room_name') border-red-500 @enderror" />
                </div>

                <div>
                    <label for="purpose" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Keperluan</label>
                    <textarea id="purpose" name="purpose" rows="3" required placeholder="Contoh: Meeting internal divisi, presentasi, atau koordinasi proyek" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('purpose') border-red-500 @enderror">{{ old('purpose') }}</textarea>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="team_name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Nama Tim / Divisi</label>
                        <input id="team_name" type="text" name="team_name" value="{{ old('team_name') }}" placeholder="Contoh: Tim TI" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('team_name') border-red-500 @enderror" />
                        @error('team_name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="participants_count" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Jumlah Peserta</label>
                        <input id="participants_count" type="number" name="participants_count" value="{{ old('participants_count', 1) }}" min="1" placeholder="1" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('participants_count') border-red-500 @enderror" />
                        @error('participants_count')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="start_time" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Mulai</label>
                        <input id="start_time" type="datetime-local" name="start_time_local" value="{{ old('start_time_local') }}" required
                            @if(!auth()->user()->hasRole('Admin'))
                                min="{{ date('Y-m-d\TH:i') }}"
                            @endif
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('start_time_local') border-red-500 @enderror" />
                        @error('start_time_local')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_time" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Selesai</label>
                        <input id="end_time" type="datetime-local" name="end_time_local" value="{{ old('end_time_local') }}" required
                            @if(!auth()->user()->hasRole('Admin'))
                                min="{{ date('Y-m-d\TH:i') }}"
                            @endif
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('end_time_local') border-red-500 @enderror" />
                        @error('end_time_local')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-white/5">
                        <input id="operator_needed" type="checkbox" name="operator_needed" value="1" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-dark-800" {{ old('operator_needed') ? 'checked' : '' }}>
                        <label for="operator_needed" class="text-sm text-gray-700 dark:text-gray-300">Butuh Operator Zoom</label>
                    </div>
                    <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-white/5">
                        <input id="breakroom_needed" type="checkbox" name="breakroom_needed" value="1" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-dark-800" {{ old('breakroom_needed') ? 'checked' : '' }}>
                        <label for="breakroom_needed" class="text-sm text-gray-700 dark:text-gray-300">Butuh Breakout Room</label>
                    </div>
                </div>

                <div>
                    <label for="nota_dinas" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Nota Dinas <span class="text-red-500">*</span></label>
                    <input id="nota_dinas" type="file" name="nota_dinas" accept=".pdf" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 dark:file:bg-brand-500/10 dark:file:text-brand-300 @error('nota_dinas') border-red-500 @enderror" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload nota dinas dalam format PDF (maksimal 1MB)</p>
                </div>

                <div class="flex gap-3 pt-4">
                    <a href="{{ url()->to(route('reservations.index')) }}" class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Batal</a>
                    <button type="submit" class="flex-1 rounded-lg bg-brand-600 px-4 py-2.5 text-center font-medium text-white hover:bg-brand-700">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
