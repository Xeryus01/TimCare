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
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-4">
                <a href="<?php echo e(route('assets.show', $asset)); ?>" class="text-brand-600 hover:text-brand-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Catat Perawatan Aset</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aset: <?php echo e($asset->name); ?> (<?php echo e($asset->asset_code); ?>)</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="max-w-3xl">
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800 overflow-hidden">
                <!-- Current Info -->
                <div class="border-b border-gray-200 bg-gray-50 p-5 sm:p-7.5 dark:border-gray-700 dark:bg-white/5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Aset</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pemegang Aset</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white"><?php echo e($asset->holder ?? '-'); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kondisi Saat Ini</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white"><?php echo e($asset->condition_label); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form action="<?php echo e(route('assets.storeMaintenance', $asset)); ?>" method="POST" class="p-5 sm:p-7.5">
                    <?php echo csrf_field(); ?>

                    <!-- Type -->
                    <div class="mb-6">
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipe Perawatan
                        </label>
                        <select name="type" id="type" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            required>
                            <option value="">-- Pilih Tipe Perawatan --</option>
                            <?php $__currentLoopData = $maintenanceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(old('type') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Maintenance Date -->
                    <div class="mb-6">
                        <label for="maintenance_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tanggal Perawatan
                        </label>
                        <input type="date" name="maintenance_date" id="maintenance_date" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 <?php $__errorArgs = ['maintenance_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('maintenance_date', today()->toDateString())); ?>"
                            required>
                        <?php $__errorArgs = ['maintenance_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Deskripsi Perawatan
                        </label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Deskripsi perawatan yang dilakukan..."
                            required><?php echo e(old('description')); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Condition Before -->
                    <div class="mb-6">
                        <label for="condition_before" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kondisi Sebelum Perawatan (Opsional)
                        </label>
                        <select name="condition_before" id="condition_before" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 <?php $__errorArgs = ['condition_before'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">-- Pilih kondisi sebelum --</option>
                            <?php $__currentLoopData = \App\Models\Asset::conditionOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(old('condition_before', $asset->condition) === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['condition_before'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Findings -->
                    <div class="mb-6">
                        <label for="findings" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Temuan (Opsional)
                        </label>
                        <textarea name="findings" id="findings" rows="3"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 <?php $__errorArgs = ['findings'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Temuan selama inspeksi/perawatan..."><?php echo e(old('findings')); ?></textarea>
                        <?php $__errorArgs = ['findings'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Actions Taken -->
                    <div class="mb-6">
                        <label for="actions_taken" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tindakan yang Dilakukan (Opsional)
                        </label>
                        <textarea name="actions_taken" id="actions_taken" rows="3"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 <?php $__errorArgs = ['actions_taken'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Tindakan yang dilakukan untuk memperbaiki..."><?php echo e(old('actions_taken')); ?></textarea>
                        <?php $__errorArgs = ['actions_taken'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Condition After -->
                    <div class="mb-6">
                        <label for="condition_after" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kondisi Setelah Perawatan (Opsional)
                        </label>
                        <select name="condition_after" id="condition_after" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 <?php $__errorArgs = ['condition_after'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">-- Tidak ada perubahan --</option>
                            <?php $__currentLoopData = \App\Models\Asset::conditionOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(old('condition_after') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['condition_after'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Next Maintenance Date -->
                    <div class="mb-6">
                        <label for="next_maintenance_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Jadwal Perawatan Berikutnya (Opsional)
                        </label>
                        <input type="date" name="next_maintenance_date" id="next_maintenance_date" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 <?php $__errorArgs = ['next_maintenance_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('next_maintenance_date')); ?>">
                        <?php $__errorArgs = ['next_maintenance_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <a href="<?php echo e(route('assets.show', $asset)); ?>" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 font-medium text-white hover:bg-brand-700">
                            Simpan Perawatan
                        </button>
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
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/assets/add-maintenance.blade.php ENDPATH**/ ?>