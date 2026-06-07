<x-app-layout>
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">{{ $ticket->title }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $ticket->code }} • dibuat {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ url()->to(route('tickets.index')) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Kembali</a>
                @if(
                    auth()->user()->hasRole('Admin') ||
                    (auth()->user()->hasAnyRole(['Teknisi']) && ! in_array($ticket->status, [\App\Models\Ticket::STATUS_CANCELLED], true)) ||
                    (auth()->id() === $ticket->requester_id && ! in_array($ticket->status, [\App\Models\Ticket::STATUS_CANCELLED, \App\Models\Ticket::STATUS_SOLVED, \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES], true))
                )
                    <a href="{{ url()->to(route('tickets.edit', $ticket)) }}" class="rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700">Ubah</a>
                @endif
                @if(auth()->id() === $ticket->requester_id && ! in_array($ticket->status, [\App\Models\Ticket::STATUS_CANCELLED, \App\Models\Ticket::STATUS_SOLVED, \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES, \App\Models\Ticket::STATUS_REJECTED]))
                    <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ \App\Models\Ticket::STATUS_CANCELLED }}" />
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Batalkan Tiket</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Keluhan / Permintaan</h2>
                    <p class="text-gray-700 dark:text-gray-300">{{ $ticket->description }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-dark-800">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Jenis</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $ticket->category_label }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-dark-800">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pemohon</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ optional($ticket->requester)->name ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-dark-800">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Petugas</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ optional($ticket->assignee)->name ?? 'Belum ditentukan' }}</p>
                    </div>
                </div>

                {{-- Lampiran Tiket dipindah ke percakapan; section ini dihapus --}}

                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Percakapan Tiket</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pemohon dan petugas bisa saling membalas perkembangan penanganan di sini.</p>
                        </div>
                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-300">{{ $ticket->comments->count() }} pesan</span>
                    </div>

                    <div class="space-y-3">
                        @php $initialAttachments = $ticket->attachments->where('comment_id', null); @endphp
                        @if($initialAttachments->isNotEmpty())
                            <div class="flex justify-start">
                                <div class="w-full max-w-2xl rounded-2xl border p-4 border-brand-100 bg-brand-50/60 dark:border-brand-500/30 dark:bg-brand-500/10">
                                    <div class="mb-2 flex items-start justify-between gap-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ optional($ticket->requester)->name ?? 'Pemohon' }}</p>
                                            <span class="rounded-full px-2.5 py-1 text-[11px] font-medium bg-white text-brand-700 dark:bg-brand-500/20 dark:text-brand-200">Pemohon</span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                                    </div>

                                    <p class="mb-3 text-sm text-gray-700 dark:text-gray-300">Lampiran awal tiket:</p>

                                    <div class="space-y-3">
                                        @foreach($initialAttachments as $att)
                                            @php
                                                $attachmentUrl = route('tickets.attachments.show', [$ticket, $att]);
                                                $isImage = filled($att->mime_type) && str_starts_with($att->mime_type, 'image/');
                                                $isPdf = $att->mime_type === 'application/pdf' || \Illuminate\Support\Str::endsWith(\Illuminate\Support\Str::lower($att->file_name), '.pdf');
                                            @endphp
                                            <div class="rounded-xl border border-dashed border-gray-300 bg-white/80 p-3 dark:border-gray-600 dark:bg-dark-900/30">
                                                <div class="mb-2 flex items-center justify-between gap-2">
                                                    <a href="{{ $attachmentUrl }}" target="_blank" class="text-sm font-medium text-brand-600 hover:underline">{{ $att->file_name }}</a>
                                                    <a href="{{ $attachmentUrl }}" target="_blank" class="text-xs text-gray-500 hover:underline dark:text-gray-400">Lihat file</a>
                                                </div>

                                                @if($isImage)
                                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-brand-700">Preview Gambar</p>
                                                    <img src="{{ $attachmentUrl }}" alt="{{ $att->file_name }}" class="max-h-64 w-full rounded-lg border border-gray-200 object-contain dark:border-gray-700">
                                                @elseif($isPdf)
                                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-red-700">Preview PDF</p>
                                                    <iframe src="{{ $attachmentUrl }}" title="{{ $att->file_name }}" class="h-64 w-full rounded-lg border border-gray-200 dark:border-gray-700"></iframe>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                        @forelse($ticket->comments as $comment)
                            @php
                                $isRequesterMessage = $comment->user_id === $ticket->requester_id;
                                $isStaffMessage = $comment->user && $comment->user->hasAnyRole(['Admin', 'Teknisi']);
                                $roleLabel = $isRequesterMessage ? 'Pemohon' : ($isStaffMessage ? 'Petugas' : 'Pengguna');
                            @endphp
                            <div class="flex {{ $isRequesterMessage ? 'justify-start' : 'justify-end' }}">
                                <div class="w-full max-w-2xl rounded-2xl border p-4 {{ $isRequesterMessage ? 'border-brand-100 bg-brand-50/60 dark:border-brand-500/30 dark:bg-brand-500/10' : 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/5' }}">
                                    <div class="mb-2 flex items-start justify-between gap-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ $comment->user->name }}</p>
                                            <span class="rounded-full px-2.5 py-1 text-[11px] font-medium {{ $isRequesterMessage ? 'bg-white text-brand-700 dark:bg-brand-500/20 dark:text-brand-200' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200' }}">{{ $roleLabel }}</span>
                                            @if($comment->is_internal)
                                                <span class="rounded-full bg-yellow-100 px-2.5 py-1 text-[11px] font-medium text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-200">Internal</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->format('d/m/Y H:i') }}</p>
                                    </div>

                                    <p class="whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">{{ $comment->message }}</p>

                                    @if($comment->attachments->isNotEmpty())
                                        <div class="mt-3 space-y-3">
                                            @foreach($comment->attachments as $att)
                                                @php
                                                    $attachmentUrl = route('tickets.attachments.show', [$ticket, $att]);
                                                    $isImage = filled($att->mime_type) && str_starts_with($att->mime_type, 'image/');
                                                    $isPdf = $att->mime_type === 'application/pdf' || \Illuminate\Support\Str::endsWith(\Illuminate\Support\Str::lower($att->file_name), '.pdf');
                                                @endphp
                                                <div class="rounded-xl border border-dashed border-gray-300 bg-white/80 p-3 dark:border-gray-600 dark:bg-dark-900/30">
                                                    <div class="mb-2 flex items-center justify-between gap-2">
                                                        <a href="{{ $attachmentUrl }}" target="_blank" class="text-sm font-medium text-brand-600 hover:underline">{{ $att->file_name }}</a>
                                                        <a href="{{ $attachmentUrl }}" target="_blank" class="text-xs text-gray-500 hover:underline dark:text-gray-400">Lihat file</a>
                                                    </div>

                                                    @if($isImage)
                                                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-brand-700">Preview Gambar</p>
                                                        <img src="{{ $attachmentUrl }}" alt="{{ $att->file_name }}" class="max-h-64 w-full rounded-lg border border-gray-200 object-contain dark:border-gray-700">
                                                    @elseif($isPdf)
                                                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-red-700">Preview PDF</p>
                                                        <iframe src="{{ $attachmentUrl }}" title="{{ $att->file_name }}" class="h-64 w-full rounded-lg border border-gray-200 dark:border-gray-700"></iframe>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada percakapan. Silakan kirim pesan pertama untuk memulai tindak lanjut.</p>
                        @endforelse
                    </div>

                    @if(
                        auth()->user()->hasAnyRole(['Admin','Teknisi']) ||
                        (auth()->id() === $ticket->requester_id && ! in_array($ticket->status, [\App\Models\Ticket::STATUS_SOLVED, \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES], true))
                    )
                    <form method="POST" action="{{ route('tickets.comment', $ticket) }}" enctype="multipart/form-data" class="mt-5 space-y-4 rounded-xl bg-gray-50 p-4 dark:bg-white/5">
                        @csrf
                        <div>
                            <label for="message" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Balas percakapan</label>
                            <textarea id="message" name="message" rows="3" placeholder="Tulis pesan, update penanganan, atau permintaan tambahan..." class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('message') border-red-500 @enderror" required>{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="attachment" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Lampiran <span class="text-gray-400">(opsional)</span></label>
                            <input type="file" id="attachment" name="attachment" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt" class="w-full text-sm text-gray-700 dark:text-gray-300 @error('attachment') border-red-500 @enderror">
                            @error('attachment')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        @if(auth()->user()->hasAnyRole(['Admin','Teknisi']))
                            <div>
                                <label for="status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Ubah status saat ini</label>
                                <select id="status" name="status" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white @error('status') border-red-500 @enderror">
                                    <option value="">Tetap seperti sekarang</option>
                                    @foreach(\App\Models\Ticket::statusLabels() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                        <button type="submit" class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-700">Kirim Pesan</button>
                    </form>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h3 class="mb-3 text-sm font-bold uppercase text-gray-500 dark:text-gray-400">Status Penanganan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tahap penanganan saat ini.</p>

                    <div class="mt-4">
                        <span class="inline-flex rounded-lg px-3 py-1 text-sm font-semibold {{ $ticket->status_badge_classes }}">{{ $ticket->status_label }}</span>
                    </div>

                    @if(auth()->user()->hasRole('Admin'))
                        <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="mt-4 space-y-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label for="assignee_id" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Petugas</label>
                                <select id="assignee_id" name="assignee_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-dark-800 dark:text-white">
                                    <option value="">Belum ditentukan</option>
                                    @foreach($technicians as $user)
                                        <option value="{{ $user->id }}" {{ $ticket->assignee_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2 text-white">Perbarui Penanganan</button>
                        </form>
                    @endif
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h3 class="mb-3 text-sm font-bold uppercase text-gray-500 dark:text-gray-400">Timeline</h3>
                    <div class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Dibuat</p>
                            <p>{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Terakhir diperbarui</p>
                            <p>{{ $ticket->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Aset terkait</p>
                            <p>{{ optional($ticket->asset)->name ?? 'Tidak ada' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Selesai pada</p>
                            <p>{{ optional($ticket->resolved_at)?->format('d/m/Y H:i') ?? 'Belum selesai' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
