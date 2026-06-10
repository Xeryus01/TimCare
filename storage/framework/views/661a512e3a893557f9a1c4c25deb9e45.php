<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl"><?php echo e($ticket->title); ?></h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e($ticket->code); ?> • dibuat <?php echo e($ticket->created_at->format('d/m/Y H:i')); ?></p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(url()->to(route('tickets.index'))); ?>" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Kembali</a>
                <?php if(
                    auth()->user()->hasRole('Admin') ||
                    (auth()->user()->hasAnyRole(['Teknisi']) && ! in_array($ticket->status, [\App\Models\Ticket::STATUS_CANCELLED], true)) ||
                    (auth()->id() === $ticket->requester_id && ! in_array($ticket->status, [\App\Models\Ticket::STATUS_CANCELLED, \App\Models\Ticket::STATUS_SOLVED, \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES], true))
                ): ?>
                    <a href="<?php echo e(url()->to(route('tickets.edit', $ticket))); ?>" class="rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700">Ubah</a>
                <?php endif; ?>
                <?php if(auth()->id() === $ticket->requester_id && ! in_array($ticket->status, [\App\Models\Ticket::STATUS_CANCELLED, \App\Models\Ticket::STATUS_SOLVED, \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES, \App\Models\Ticket::STATUS_REJECTED])): ?>
                    <form method="POST" action="<?php echo e(route('tickets.update', $ticket)); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <input type="hidden" name="status" value="<?php echo e(\App\Models\Ticket::STATUS_CANCELLED); ?>" />
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Batalkan Tiket</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Keluhan / Permintaan</h2>
                    <p class="text-gray-700 dark:text-gray-300"><?php echo e($ticket->description); ?></p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-dark-800">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Jenis</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white"><?php echo e($ticket->category_label); ?></p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-dark-800">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pemohon</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white"><?php echo e(optional($ticket->requester)->name ?? '-'); ?></p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-dark-800">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Petugas</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white"><?php echo e(optional($ticket->assignee)->name ?? 'Belum ditentukan'); ?></p>
                    </div>
                </div>

                

                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Percakapan Tiket</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pemohon dan petugas bisa saling membalas perkembangan penanganan di sini.</p>
                        </div>
                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-300"><?php echo e($ticket->comments->count()); ?> pesan</span>
                    </div>

                    <div class="space-y-3">
                        <?php $initialAttachments = $ticket->attachments->where('comment_id', null); ?>
                        <?php if($initialAttachments->isNotEmpty()): ?>
                            <div class="flex justify-start">
                                <div class="w-full max-w-2xl rounded-2xl border p-4 border-brand-100 bg-brand-50/60 dark:border-brand-500/30 dark:bg-brand-500/10">
                                    <div class="mb-2 flex items-start justify-between gap-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-semibold text-gray-900 dark:text-white"><?php echo e(optional($ticket->requester)->name ?? 'Pemohon'); ?></p>
                                            <span class="rounded-full px-2.5 py-1 text-[11px] font-medium bg-white text-brand-700 dark:bg-brand-500/20 dark:text-brand-200">Pemohon</span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($ticket->created_at->format('d/m/Y H:i')); ?></p>
                                    </div>

                                    <p class="mb-3 text-sm text-gray-700 dark:text-gray-300">Lampiran awal tiket:</p>

                                    <div class="space-y-3">
                                        <?php $__currentLoopData = $initialAttachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $attachmentUrl = route('tickets.attachments.show', [$ticket, $att]);
                                                $isImage = filled($att->mime_type) && str_starts_with($att->mime_type, 'image/');
                                                $isPdf = $att->mime_type === 'application/pdf' || \Illuminate\Support\Str::endsWith(\Illuminate\Support\Str::lower($att->file_name), '.pdf');
                                            ?>
                                            <div class="rounded-xl border border-dashed border-gray-300 bg-white/80 p-3 dark:border-gray-600 dark:bg-dark-900/30">
                                                <div class="mb-2 flex items-center justify-between gap-2">
                                                    <a href="<?php echo e($attachmentUrl); ?>" target="_blank" class="text-sm font-medium text-brand-600 hover:underline"><?php echo e($att->file_name); ?></a>
                                                    <a href="<?php echo e($attachmentUrl); ?>" target="_blank" class="text-xs text-gray-500 hover:underline dark:text-gray-400">Lihat file</a>
                                                </div>

                                                <?php if($isImage): ?>
                                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-brand-700">Preview Gambar</p>
                                                    <img src="<?php echo e($attachmentUrl); ?>" alt="<?php echo e($att->file_name); ?>" class="max-h-64 w-full rounded-lg border border-gray-200 object-contain dark:border-gray-700">
                                                <?php elseif($isPdf): ?>
                                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-red-700">Preview PDF</p>
                                                    <iframe src="<?php echo e($attachmentUrl); ?>" title="<?php echo e($att->file_name); ?>" class="h-64 w-full rounded-lg border border-gray-200 dark:border-gray-700"></iframe>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php $__empty_1 = true; $__currentLoopData = $ticket->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $isRequesterMessage = $comment->user_id === $ticket->requester_id;
                                $isStaffMessage = $comment->user && $comment->user->hasAnyRole(['Admin', 'Teknisi']);
                                $roleLabel = $isRequesterMessage ? 'Pemohon' : ($isStaffMessage ? 'Petugas' : 'Pengguna');
                            ?>
                            <div class="flex <?php echo e($isRequesterMessage ? 'justify-start' : 'justify-end'); ?>">
                                <div class="w-full max-w-2xl rounded-2xl border p-4 <?php echo e($isRequesterMessage ? 'border-brand-100 bg-brand-50/60 dark:border-brand-500/30 dark:bg-brand-500/10' : 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/5'); ?>">
                                    <div class="mb-2 flex items-start justify-between gap-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-semibold text-gray-900 dark:text-white"><?php echo e($comment->user->name); ?></p>
                                            <span class="rounded-full px-2.5 py-1 text-[11px] font-medium <?php echo e($isRequesterMessage ? 'bg-white text-brand-700 dark:bg-brand-500/20 dark:text-brand-200' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200'); ?>"><?php echo e($roleLabel); ?></span>
                                            <?php if($comment->is_internal): ?>
                                                <span class="rounded-full bg-yellow-100 px-2.5 py-1 text-[11px] font-medium text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-200">Internal</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($comment->created_at->format('d/m/Y H:i')); ?></p>
                                    </div>

                                    <p class="whitespace-pre-line text-sm text-gray-700 dark:text-gray-300"><?php echo e($comment->message); ?></p>

                                    <?php if($comment->attachments->isNotEmpty()): ?>
                                        <div class="mt-3 space-y-3">
                                            <?php $__currentLoopData = $comment->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $attachmentUrl = route('tickets.attachments.show', [$ticket, $att]);
                                                    $isImage = filled($att->mime_type) && str_starts_with($att->mime_type, 'image/');
                                                    $isPdf = $att->mime_type === 'application/pdf' || \Illuminate\Support\Str::endsWith(\Illuminate\Support\Str::lower($att->file_name), '.pdf');
                                                ?>
                                                <div class="rounded-xl border border-dashed border-gray-300 bg-white/80 p-3 dark:border-gray-600 dark:bg-dark-900/30">
                                                    <div class="mb-2 flex items-center justify-between gap-2">
                                                        <a href="<?php echo e($attachmentUrl); ?>" target="_blank" class="text-sm font-medium text-brand-600 hover:underline"><?php echo e($att->file_name); ?></a>
                                                        <a href="<?php echo e($attachmentUrl); ?>" target="_blank" class="text-xs text-gray-500 hover:underline dark:text-gray-400">Lihat file</a>
                                                    </div>

                                                    <?php if($isImage): ?>
                                                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-brand-700">Preview Gambar</p>
                                                        <img src="<?php echo e($attachmentUrl); ?>" alt="<?php echo e($att->file_name); ?>" class="max-h-64 w-full rounded-lg border border-gray-200 object-contain dark:border-gray-700">
                                                    <?php elseif($isPdf): ?>
                                                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-red-700">Preview PDF</p>
                                                        <iframe src="<?php echo e($attachmentUrl); ?>" title="<?php echo e($att->file_name); ?>" class="h-64 w-full rounded-lg border border-gray-200 dark:border-gray-700"></iframe>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada percakapan. Silakan kirim pesan pertama untuk memulai tindak lanjut.</p>
                        <?php endif; ?>
                    </div>

                    <?php if(
                        auth()->user()->hasRole('Admin') ||
                        (auth()->id() === $ticket->requester_id && ! in_array($ticket->status, [\App\Models\Ticket::STATUS_SOLVED, \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES], true)) ||
                        (auth()->user()->hasRole('Teknisi') && $ticket->assignee_id === auth()->id())
                    ): ?>
                    <form method="POST" action="<?php echo e(route('tickets.comment', $ticket)); ?>" enctype="multipart/form-data" class="mt-5 space-y-4 rounded-xl bg-gray-50 p-4 dark:bg-white/5">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label for="message" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Balas percakapan</label>
                            <textarea id="message" name="message" rows="3" placeholder="Tulis pesan, update penanganan, atau permintaan tambahan..." class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('message')); ?></textarea>
                            <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="attachment" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Lampiran <span class="text-gray-400">(opsional)</span></label>
                            <input type="file" id="attachment" name="attachment" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt" class="w-full text-sm text-gray-700 dark:text-gray-300 <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <?php if(auth()->user()->hasAnyRole(['Admin','Teknisi'])): ?>
                            <div>
                                <label for="status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Ubah status saat ini</label>
                                <select id="status" name="status" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <?php $__currentLoopData = \App\Models\Ticket::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value); ?>" <?php echo e(old('status', $ticket->status) === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        <?php endif; ?>
                        <button type="submit" class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-700">Kirim Pesan</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h3 class="mb-3 text-sm font-bold uppercase text-gray-500 dark:text-gray-400">Status Penanganan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tahap penanganan saat ini.</p>

                    <div class="mt-4">
                        <span class="inline-flex rounded-lg px-3 py-1 text-sm font-semibold <?php echo e($ticket->status_badge_classes); ?>"><?php echo e($ticket->status_label); ?></span>
                    </div>

                    <?php if(auth()->user()->hasRole('Admin')): ?>
                        <form method="POST" action="<?php echo e(route('tickets.update', $ticket)); ?>" class="mt-4 space-y-4">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <div>
                                <label for="assignee_id" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Petugas</label>
                                <select id="assignee_id" name="assignee_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-dark-800 dark:text-white">
                                    <option value="">Belum ditentukan</option>
                                    <?php $__currentLoopData = $technicians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>" <?php echo e($ticket->assignee_id == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2 text-white">Perbarui Penanganan</button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h3 class="mb-3 text-sm font-bold uppercase text-gray-500 dark:text-gray-400">Timeline</h3>
                    <div class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Dibuat</p>
                            <p><?php echo e($ticket->created_at->format('d/m/Y H:i')); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Terakhir diperbarui</p>
                            <p><?php echo e($ticket->updated_at->format('d/m/Y H:i')); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Aset terkait</p>
                            <p><?php echo e(optional($ticket->asset)->name ?? 'Tidak ada'); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Selesai pada</p>
                            <p><?php echo e(optional($ticket->resolved_at)?->format('d/m/Y H:i') ?? 'Belum selesai'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/tickets/show.blade.php ENDPATH**/ ?>