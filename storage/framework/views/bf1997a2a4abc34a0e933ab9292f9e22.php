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
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Edit Pengajuan Zoom</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ubah detail pengajuan Zoom Anda.</p>
        </div>

        <div class="max-w-3xl mx-auto">
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800 sm:p-8">
                <?php if($errors->any()): ?>
                    <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-500/10 dark:text-red-400">
                        <ul class="list-inside list-disc space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php
                    $canEditZoomDetails = auth()->user()->hasAnyRole(['Admin', 'Teknisi']) || ! $reservation->zoom_link;
                ?>

                <form method="POST" action="<?php echo e(route('reservations.update', $reservation)); ?>" enctype="multipart/form-data" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>

                    <?php if (! ($canEditZoomDetails)): ?>
                        <div class="rounded-xl border border-yellow-300 bg-yellow-50 p-4 text-sm text-yellow-800 dark:border-yellow-700 dark:bg-yellow-950/20 dark:text-yellow-200">
                            <p class="font-semibold">Perhatian:</p>
                            <p>Pengajuan Zoom ini sudah memiliki link. Sebagai pengguna, Anda hanya dapat memperbarui Nota Dinas atau membatalkan pengajuan.</p>
                        </div>
                    <?php endif; ?>

                    <?php if($canEditZoomDetails): ?>
                        <div>
                            <label for="room_name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Nama Kegiatan</label>
                            <input id="room_name" type="text" name="room_name" value="<?php echo e(old('room_name', $reservation->room_name)); ?>" placeholder="Contoh: Workshop Laravel 2026" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['room_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" />
                            <?php $__errorArgs = ['room_name'];
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

                        <div>
                            <label for="purpose" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Keperluan</label>
                            <textarea id="purpose" name="purpose" rows="3" placeholder="Jelaskan tujuan pengajuan zoom ini..." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['purpose'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('purpose', $reservation->purpose)); ?></textarea>
                            <?php $__errorArgs = ['purpose'];
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

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="team_name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Nama Tim / Divisi</label>
                                <input id="team_name" type="text" name="team_name" value="<?php echo e(old('team_name', $reservation->team_name)); ?>" placeholder="Contoh: Tim TI" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['team_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" />
                                <?php $__errorArgs = ['team_name'];
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
                            <div>
                                <label for="participants_count" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Jumlah Peserta</label>
                                <input id="participants_count" type="number" name="participants_count" value="<?php echo e(old('participants_count', $reservation->participants_count ?: 1)); ?>" min="1" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['participants_count'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" />
                                <?php $__errorArgs = ['participants_count'];
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

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-white/5">
                                <input id="operator_needed" type="checkbox" name="operator_needed" value="1" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-dark-800" <?php echo e(old('operator_needed', $reservation->operator_needed) ? 'checked' : ''); ?> />
                                <label for="operator_needed" class="text-sm text-gray-700 dark:text-gray-300">Butuh Operator Zoom</label>
                            </div>
                            <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-white/5">
                                <input id="breakroom_needed" type="checkbox" name="breakroom_needed" value="1" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-dark-800" <?php echo e(old('breakroom_needed', $reservation->breakroom_needed) ? 'checked' : ''); ?> />
                                <label for="breakroom_needed" class="text-sm text-gray-700 dark:text-gray-300">Butuh Ruang Istirahat</label>
                            </div>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="start_time_local" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Waktu Mulai</label>
                                <input id="start_time_local" type="datetime-local" name="start_time_local" value="<?php echo e(old('start_time_local', $reservation->start_time->format('Y-m-d\TH:i'))); ?>" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['start_time_local'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" />
                                <?php $__errorArgs = ['start_time_local'];
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
                            <div>
                                <label for="end_time_local" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Waktu Selesai</label>
                                <input id="end_time_local" type="datetime-local" name="end_time_local" value="<?php echo e(old('end_time_local', $reservation->end_time->format('Y-m-d\TH:i'))); ?>" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['end_time_local'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" />
                                <?php $__errorArgs = ['end_time_local'];
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
                    <?php else: ?>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-dark-800">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Nama Kegiatan</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e($reservation->room_name); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Tim / Divisi</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e($reservation->team_name); ?></p>
                                </div>
                                <div class="sm:col-span-2">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Keperluan</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e($reservation->purpose); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Waktu Mulai</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e($reservation->start_time->format('d/m/Y H:i')); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Waktu Selesai</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e($reservation->end_time->format('d/m/Y H:i')); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label for="nota_dinas" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Nota Dinas</label>
                        <?php if($reservation->nota_dinas_path): ?>
                            <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Nota dinas saat ini: <a href="<?php echo e(url()->to(route('reservations.nota-dinas', $reservation))); ?>" target="_blank" class="text-brand-600 hover:text-brand-700">Lihat PDF</a></p>
                        <?php endif; ?>
                        <input id="nota_dinas" type="file" name="nota_dinas" accept=".pdf" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 dark:file:bg-brand-500/10 dark:file:text-brand-300 <?php $__errorArgs = ['nota_dinas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload nota dinas baru dalam format PDF (maksimal 1MB) - opsional, kosongkan jika tidak ingin mengubah</p>
                        <?php $__errorArgs = ['nota_dinas'];
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

                    <!-- Cancel Option for Regular Users -->
                    <?php if(!auth()->user()->hasPermissionTo('approve reservations') && $reservation->status !== \App\Models\Reservation::STATUS_CANCELLED && $reservation->status !== \App\Models\Reservation::STATUS_COMPLETED && $reservation->status !== \App\Models\Reservation::STATUS_REJECTED): ?>
                        <div>
                            <label for="status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Aksi</label>
                            <select id="status" name="status" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">-- Pilih Aksi --</option>
                                <option value="<?php echo e(\App\Models\Reservation::STATUS_CANCELLED); ?>">Batalkan Pengajuan</option>
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
                    <?php endif; ?>

                    <div class="flex gap-3 pt-6">
                        <a href="<?php echo e(url()->to(route('reservations.show', $reservation))); ?>" class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Batal</a>
                        <button type="submit" class="flex-1 rounded-lg bg-brand-600 px-4 py-2.5 text-center font-medium text-white hover:bg-brand-700">Simpan Perubahan</button>
                    </div>
                </form>
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
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/reservations/edit.blade.php ENDPATH**/ ?>