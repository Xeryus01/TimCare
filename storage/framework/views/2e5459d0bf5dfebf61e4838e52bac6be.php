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
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Pengajuan Zoom</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">User mengajukan kebutuhan Zoom, lalu teknisi atau admin melakukan follow up dan menambahkan link meeting.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(url()->to(route('reservations.create'))); ?>" class="rounded-lg bg-brand-600 px-4 py-2 font-medium text-white hover:bg-brand-700">Ajukan Zoom</a>
                <a href="<?php echo e(url()->to(route('exports.reservations', request()->query()))); ?>" class="rounded-lg border border-gray-300 px-4 py-2 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Ekspor CSV</a>
            </div>
        </div>

        <div class="mb-4 rounded-3xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-dark-800">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Alur Pengajuan Zoom</h2>
            <div class="mt-3 grid gap-2 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-2 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-sky-600">1.</span>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Ajukan</p>
                    </div>
                    <p class="mt-1 text-[11px] leading-4 text-gray-600 dark:text-gray-400">Isi kebutuhan Zoom dan upload nota dinas.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-2 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-sky-600">2.</span>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Assign</p>
                    </div>
                    <p class="mt-1 text-[11px] leading-4 text-gray-600 dark:text-gray-400">Admin/teknisi menindaklanjuti permohonan.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-2 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-sky-600">3.</span>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Cek ID Meeting</p>
                    </div>
                    <p class="mt-1 text-[11px] leading-4 text-gray-600 dark:text-gray-400">Jika ruang id meeting zoom masih tersedia, lanjutkan. Jika tidak, konfirmasi.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-2 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-sky-600">4.</span>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Selesai</p>
                    </div>
                    <p class="mt-1 text-[11px] leading-4 text-gray-600 dark:text-gray-400">Link Zoom ditambahkan jika disetujui.</p>
                </div>
            </div>
        </div>

        <form method="GET" class="mb-4 flex flex-wrap gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-dark-800">
            <select name="status" class="rounded-lg border-gray-300 px-3 py-2 dark:bg-dark-800 dark:text-white">
                <option value="">Semua status</option>
                <?php $__currentLoopData = \App\Models\Reservation::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php echo e(request('status') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white">Terapkan</button>
        </form>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/5">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Kode</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Kegiatan / Ruang</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Jadwal</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Pemohon</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Status</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Link Zoom</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Petugas</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-5 py-4 sm:px-6 text-sm font-semibold text-brand-600 dark:text-brand-400"><?php echo e($r->code); ?></td>
                                <td class="px-5 py-4 sm:px-6">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($r->room_name); ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e(\Illuminate\Support\Str::limit($r->purpose, 45)); ?></p>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300">
                                    <?php echo e($r->start_time->format('d/m/Y H:i')); ?><br>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">s/d <?php echo e($r->end_time->format('d/m/Y H:i')); ?></span>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300"><?php echo e(optional($r->requester)->name ?? '-'); ?></td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
                                        <?php if($r->status === \App\Models\Reservation::STATUS_PENDING): ?> bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400
                                        <?php elseif($r->status === \App\Models\Reservation::STATUS_APPROVED): ?> bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400
                                        <?php elseif($r->status === \App\Models\Reservation::STATUS_WAITING_MONITORING): ?> bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400
                                        <?php elseif($r->status === \App\Models\Reservation::STATUS_REJECTED): ?> bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400
                                        <?php elseif($r->status === \App\Models\Reservation::STATUS_COMPLETED): ?> bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400
                                        <?php elseif($r->status === \App\Models\Reservation::STATUS_CANCELLED): ?> bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400
                                        <?php else: ?> bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400
                                        <?php endif; ?>">
                                        <?php echo e($r->status_label); ?>

                                    </span>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300">
                                    <?php if($r->zoom_link): ?>
                                        <a href="<?php echo e($r->zoom_link); ?>" target="_blank" class="text-brand-600 hover:underline">Buka link</a>
                                    <?php else: ?>
                                        <span class="text-gray-400">Belum ada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-700 dark:text-gray-300"><?php echo e(optional($r->approver)->name ?? '-'); ?></td>
                                <td class="px-5 py-4 text-right sm:px-6">
                                    <a href="<?php echo e(url()->to(route('reservations.show', $r))); ?>" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:px-6">Belum ada pengajuan Zoom.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($reservations->hasPages()): ?>
                <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['paginator' => $reservations]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reservations)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $attributes = $__attributesOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $component = $__componentOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__componentOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
            <?php endif; ?>
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
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/reservations/index.blade.php ENDPATH**/ ?>