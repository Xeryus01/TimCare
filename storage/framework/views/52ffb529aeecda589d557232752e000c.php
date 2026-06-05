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
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ubah Pemegang Aset</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aset: <?php echo e($asset->name); ?> (<?php echo e($asset->asset_code); ?>)</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="max-w-2xl">
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800 overflow-hidden">
                <!-- Current Info -->
                <div class="border-b border-gray-200 bg-gray-50 p-5 sm:p-7.5 dark:border-gray-700 dark:bg-white/5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Saat Ini</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pemegang Saat Ini</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white"><?php echo e($asset->holder ?? '-'); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Status Aset</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white"><?php echo e($asset->status_label); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form action="<?php echo e(route('assets.storeChangeHolder', $asset)); ?>" method="POST" class="p-5 sm:p-7.5">
                    <?php echo csrf_field(); ?>

                    <!-- New Holder -->
                    <div class="mb-6">
                        <label for="new_holder" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Pemegang Aset Baru
                        </label>
                        <input type="text" name="new_holder" id="new_holder" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 <?php $__errorArgs = ['new_holder'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Nama pemegang aset baru"
                            value="<?php echo e(old('new_holder')); ?>"
                            required>
                        <?php $__errorArgs = ['new_holder'];
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

                    <!-- Changed Date -->
                    <div class="mb-6">
                        <label for="changed_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Waktu Perubahan
                        </label>
                        <input type="datetime-local" name="changed_at" id="changed_at" 
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 <?php $__errorArgs = ['changed_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('changed_at', now()->format('Y-m-d\TH:i'))); ?>"
                            required>
                        <?php $__errorArgs = ['changed_at'];
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

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Catatan (Opsional)
                        </label>
                        <textarea name="notes" id="notes" rows="4"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Catatan tentang perubahan pemegang aset..."><?php echo e(old('notes')); ?></textarea>
                        <?php $__errorArgs = ['notes'];
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
                            Simpan Perubahan
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
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/assets/change-holder.blade.php ENDPATH**/ ?>