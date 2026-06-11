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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl"><?php echo e($reservation->room_name); ?></h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e($reservation->code); ?> • <?php echo e($reservation->status_label); ?></p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(url()->to(route('reservations.index'))); ?>" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Kembali</a>
                <?php if(
                    auth()->user()->hasRole('Admin') ||
                    (auth()->user()->hasAnyRole(['Teknisi']) && ! in_array($reservation->status, [\App\Models\Reservation::STATUS_CANCELLED], true)) ||
                    (auth()->id() === $reservation->requester_id && ! in_array($reservation->status, [\App\Models\Reservation::STATUS_CANCELLED, \App\Models\Reservation::STATUS_COMPLETED], true))
                ): ?>
                    <a href="<?php echo e(url()->to(route('reservations.edit', $reservation))); ?>" class="rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700">Ubah</a>
                <?php endif; ?>
                <?php if(auth()->id() === $reservation->requester_id && ! in_array($reservation->status, [\App\Models\Reservation::STATUS_CANCELLED, \App\Models\Reservation::STATUS_COMPLETED, \App\Models\Reservation::STATUS_REJECTED])): ?>
                    <form method="POST" action="<?php echo e(route('reservations.update', $reservation)); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <input type="hidden" name="status" value="<?php echo e(\App\Models\Reservation::STATUS_CANCELLED); ?>" />
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Batalkan Pengajuan</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3 min-w-0">
            <div class="space-y-6 lg:col-span-2 min-w-0">
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Detail Pengajuan</h2>
                    <p class="text-sm text-gray-700 dark:text-gray-300"><?php echo e($reservation->purpose); ?></p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Waktu mulai</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white"><?php echo e($reservation->start_time->format('d/m/Y H:i')); ?></p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Waktu selesai</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white"><?php echo e($reservation->end_time->format('d/m/Y H:i')); ?></p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tim / Divisi</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white"><?php echo e($reservation->team_name ?: 'Tidak disertakan'); ?></p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Jumlah peserta</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white"><?php echo e($reservation->participants_count ?: '1'); ?></p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Operator Zoom</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white"><?php echo e($reservation->operator_needed ? 'Ya' : 'Tidak'); ?></p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Breakout Room</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white"><?php echo e($reservation->breakroom_needed ? 'Ya' : 'Tidak'); ?></p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Nota Dinas</h2>
                    <?php if($reservation->nota_dinas_path): ?>
                        <div class="space-y-4">
                            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700 overflow-hidden">
                                <div class="mb-3 flex items-center justify-between gap-3 min-w-0">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Nota Dinas Pengajuan</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">File PDF yang diunggah saat pengajuan</p>
                                    </div>
                                    <a href="<?php echo e(url()->to(route('reservations.nota-dinas', $reservation))); ?>" target="_blank" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Buka PDF</a>
                                </div>

                                <iframe src="<?php echo e(url()->to(route('reservations.nota-dinas', $reservation))); ?>" title="Nota Dinas" class="h-96 w-full rounded-lg border border-gray-200 dark:border-gray-700"></iframe>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nota dinas belum diunggah.</p>
                    <?php endif; ?>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Hasil Follow Up</h2>
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Link Zoom</p>
                            <?php if($reservation->zoom_link): ?>
                                <a href="<?php echo e($reservation->zoom_link); ?>" target="_blank" class="font-medium text-brand-600 hover:underline"><?php echo e($reservation->zoom_link); ?></a>
                            <?php else: ?>
                                <p class="text-gray-700 dark:text-gray-300">Belum ditambahkan.</p>
                            <?php endif; ?>
                        </div>
                        <?php if($reservation->zoom_link): ?>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Link Record Zoom</p>
                                <?php if($reservation->zoom_record_link): ?>
                                    <a href="<?php echo e($reservation->zoom_record_link); ?>" target="_blank" class="font-medium text-brand-600 hover:underline"><?php echo e($reservation->zoom_record_link); ?></a>
                                <?php else: ?>
                                    <p class="text-gray-700 dark:text-gray-300">Belum ditambahkan.</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Catatan tindak lanjut</p>
                            <p class="text-gray-700 dark:text-gray-300"><?php echo e($reservation->notes ?: 'Belum ada catatan dari petugas.'); ?></p>
                        </div>
                    </div>
                </div>

                <?php if(auth()->user()->hasPermissionTo('approve reservations')): ?>
                    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Tindak Lanjut oleh Teknisi / Admin</h2>
                        
                        <?php if($errors->any()): ?>
                            <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-500/10 dark:text-red-400">
                                <ul class="list-inside list-disc space-y-1">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo e(route('reservations.update', $reservation)); ?>" class="space-y-6">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>

                            <div class="grid gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status</label>
                                    <select id="status" name="status" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <?php $__currentLoopData = \App\Models\Reservation::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value); ?>" <?php echo e($reservation->status === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div>
                                <label for="zoom_link" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                    Link Zoom 
                                    <?php if($reservation->status === \App\Models\Reservation::STATUS_APPROVED || request()->input('status') === \App\Models\Reservation::STATUS_APPROVED): ?>
                                        <span class="text-red-500">*</span>
                                    <?php endif; ?>
                                </label>
                                <input id="zoom_link" type="url" name="zoom_link" value="<?php echo e(old('zoom_link', $reservation->zoom_link)); ?>" placeholder="https://zoom.us/j/123456789/..." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['zoom_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       <?php if($reservation->status === \App\Models\Reservation::STATUS_APPROVED || request()->input('status') === \App\Models\Reservation::STATUS_APPROVED): ?> required <?php endif; ?> />
                                <?php $__errorArgs = ['zoom_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <?php if($reservation->zoom_link): ?>
                                <div>
                                    <label for="zoom_record_link" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Link Record Zoom <span class="text-gray-400">(Opsional)</span></label>
                                    <input id="zoom_record_link" type="url" name="zoom_record_link" value="<?php echo e(old('zoom_record_link', $reservation->zoom_record_link)); ?>" placeholder="https://zoom.us/rec/..." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['zoom_record_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hanya bisa ditambahkan setelah link zoom diberikan.</p>
                                    <?php $__errorArgs = ['zoom_record_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            <?php endif; ?>

                            <div>
                                <label for="notes" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Catatan Tindak Lanjut <span class="text-gray-400">(Opsional)</span></label>
                                <textarea id="notes" name="notes" rows="3" placeholder="Contoh: Link aktif 10 menit sebelum mulai." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('notes', $reservation->notes)); ?></textarea>
                                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <button type="submit" class="flex-1 rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-700">Simpan Follow Up</button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h3 class="mb-3 text-sm font-bold uppercase text-gray-500 dark:text-gray-400">Status Penanganan</h3>
                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium <?php echo e($reservation->status_badge_classes); ?>">
                        <?php echo e($reservation->status_label); ?>

                    </span>

                    <div class="mt-4 space-y-3 text-sm text-gray-700 dark:text-gray-300">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Dibuat</p>
                            <p><?php echo e($reservation->created_at->format('d/m/Y H:i')); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Terakhir diperbarui</p>
                            <p><?php echo e($reservation->updated_at->format('d/m/Y H:i')); ?></p>
                        </div>
                    </div>

                    <?php if(auth()->user()->hasRole('Admin')): ?>
                        <form method="POST" action="<?php echo e(route('reservations.update', $reservation)); ?>" class="mt-6 space-y-4">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>

                            <div>
                                <label for="approver_id_sidebar" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Petugas</label>
                                <select id="approver_id_sidebar" name="approver_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-dark-800 dark:text-white">
                                    <option value="">Belum ditentukan</option>
                                    <?php $__currentLoopData = $technicians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $technician): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($technician->id); ?>" <?php echo e($reservation->approver_id == $technician->id ? 'selected' : ''); ?>><?php echo e($technician->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['approver_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2 text-white">Perbarui Petugas</button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                    <h3 class="mb-3 text-sm font-bold uppercase text-gray-500 dark:text-gray-400">Ringkasan</h3>
                    <div class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Pemohon</p>
                            <p><?php echo e(optional($reservation->requester)->name ?? '-'); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Ditangani oleh</p>
                            <p><?php echo e(optional($reservation->approver)->name ?? 'Belum ada petugas'); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Dibuat pada</p>
                            <p><?php echo e($reservation->created_at->format('d/m/Y H:i')); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Terakhir diperbarui</p>
                            <p><?php echo e($reservation->updated_at->format('d/m/Y H:i')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Scroll to top if there's a success message
    <?php if(session('success')): ?>
        window.addEventListener('load', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    <?php endif; ?>

    // Highlight zoom link field if it was just updated
    <?php if(session('success') && strpos(session('success'), 'Zoom') !== false): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const zoomLinkInput = document.getElementById('zoom_link');
            if (zoomLinkInput) {
                zoomLinkInput.classList.add('ring-2', 'ring-green-500', 'border-green-500');
                setTimeout(() => {
                    zoomLinkInput.classList.remove('ring-2', 'ring-green-500', 'border-green-500');
                }, 3000);
            }
        });
    <?php endif; ?>

    // Handle zoom link requirement based on status
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        const zoomLinkInput = document.getElementById('zoom_link');
        const zoomLinkLabel = document.querySelector('label[for="zoom_link"]');

        function updateZoomLinkRequirement() {
            const approvedStatus = '<?php echo e(\App\Models\Reservation::STATUS_APPROVED); ?>';
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

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/reservations/show.blade.php ENDPATH**/ ?>