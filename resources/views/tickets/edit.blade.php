<x-app-layout>
<!-- Main Content -->
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Edit Tiket {{ $ticket->code }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Perbarui informasi tiket dalam bahasa Indonesia.</p>
        </div>

        <!-- Form Card -->
        <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800 sm:p-8">
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

<form method="POST" action="{{ route('tickets.update', $ticket) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <label for="submission_time" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Waktu Pengajuan Tiket
                    </label>
                    <input id="submission_time" type="text" value="{{ $ticket->created_at->format('d/m/Y H:i') }}" disabled class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300" />
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Waktu pengajuan dicatat saat tiket dibuat.</p>
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Jenis Permintaan
                    </label>
                    <select id="category" name="category" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('category') border-red-500 @enderror">
                        @foreach(\App\Models\Ticket::categoryLabels() as $value => $label)
                            <option value="{{ $value }}" {{ $ticket->category === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Judul Tiket
                    </label>
                    <input id="title" type="text" name="title" value="{{ old('title', $ticket->title) }}" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('title') border-red-500 @enderror" />
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Deskripsi
                    </label>
                    <textarea id="description" name="description" rows="4" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('description') border-red-500 @enderror">{{ old('description', $ticket->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="attachment" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Lampiran <span class="text-gray-400">(opsional)</span>
                    </label>
                    <input id="attachment" type="file" name="attachment" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('attachment') border-red-500 @enderror" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ukuran lampiran maksimal 1MB. Hanya gambar dan PDF disarankan.</p>
                    @error('attachment')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Asset Field -->
                <div>
                    <label for="asset_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Aset Terkait <span class="text-gray-400">(opsional)</span>
                    </label>
                    <select id="asset_id" name="asset_id" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('asset_id') border-red-500 @enderror">
                        <option value="">-- Pilih aset --</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ $ticket->asset_id == $asset->id ? 'selected' : '' }}>{{ $asset->name }} ({{ $asset->asset_code }})</option>
                        @endforeach
                    </select>
                    @error('asset_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Status Section -->
                @if(auth()->user()->hasAnyRole(['Admin', 'Teknisi']))
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Status Penanganan
                        </label>
                        <select id="status" name="status" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('status') border-red-500 @enderror">
                            @foreach(\App\Models\Ticket::statusLabels() as $value => $label)
                                <option value="{{ $value }}" {{ $ticket->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <!-- Regular User: Cancel Option Only -->
                    @if($ticket->status !== \App\Models\Ticket::STATUS_CANCELLED && $ticket->status !== \App\Models\Ticket::STATUS_SOLVED && $ticket->status !== \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES)
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Aksi
                            </label>
                            <select id="status" name="status" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:focus:border-brand-600 dark:focus:ring-brand-900/20 @error('status') border-red-500 @enderror">
                                <option value="">-- Pilih Aksi --</option>
                                <option value="{{ \App\Models\Ticket::STATUS_CANCELLED }}">Batalkan Tiket</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                @endif

                <!-- Form Actions -->
                <div class="flex gap-3 pt-6">
                    <a href="{{ url()->to(route('tickets.index')) }}" class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">
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
