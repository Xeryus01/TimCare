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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Ajukan Tiket Permasalahan</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Sampaikan kendala secara singkat, lalu teknisi atau admin akan menanganinya.</p>
        </div>

        <div class="max-w-2xl mx-auto rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800 sm:p-8">
            <?php if($errors->any()): ?>
                <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-500/10 dark:text-red-400">
                    <p class="font-medium">Mohon periksa kembali data berikut:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('tickets.store')); ?>" enctype="multipart/form-data" class="space-y-6">
                <?php echo csrf_field(); ?>

                <div>
                    <label for="category" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Jenis Permintaan</label>
                    <select id="category" name="category" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__currentLoopData = \App\Models\Ticket::categoryLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e(old('category', 'DATA_PROCESSING') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label for="submission_time" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Waktu Pengajuan Tiket</label>
                    <input id="submission_time" type="text" value="<?php echo e(now()->format('d/m/Y H:i')); ?>" disabled class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300" />
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Waktu pengajuan akan dicatat otomatis saat tiket dikirim.</p>
                </div>

                <div>
                    <label for="title" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Judul Keluhan</label>
                    <input id="title" type="text" name="title" value="<?php echo e(old('title')); ?>" required placeholder="Contoh: Printer tidak bisa digunakan" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" />
                </div>

                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Detail Kendala</label>
                    <textarea id="description" name="description" rows="4" required placeholder="Jelaskan masalah yang dialami agar petugas lebih mudah menindaklanjuti." class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('description')); ?></textarea>
                </div>

                <div>
                    <label for="attachment" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Lampiran Awal <span class="text-red-500">(wajib)</span></label>
                    <input id="attachment" type="file" name="attachment" required accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ukuran lampiran maksimal 1MB. Hanya gambar dan PDF disarankan.</p>
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

                <div>
                    <label for="asset_search" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Aset Terkait <span class="text-gray-400">(opsional)</span></label>
                    <?php
                        $oldAsset = $assets->firstWhere('id', old('asset_id'));
                        $oldAssetLabel = $oldAsset ? $oldAsset->asset_code . ' - ' . $oldAsset->name : '';
                    ?>
                    <input id="asset_search" type="text" autocomplete="off" value="<?php echo e(old('asset_id') ? $oldAssetLabel : ''); ?>" placeholder="Cari aset berdasarkan kode atau nama" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-dark-800 dark:text-white <?php $__errorArgs = ['asset_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" list="asset_list" />
                    <input id="asset_id" type="hidden" name="asset_id" value="<?php echo e(old('asset_id', '')); ?>" />
                    <datalist id="asset_list">
                        <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($asset->asset_code); ?> - <?php echo e($asset->name); ?>"><?php echo e($asset->asset_code); ?> - <?php echo e($asset->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </datalist>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Ketik untuk mencari aset, lalu pilih dari daftar dropdown.</p>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const assetInput = document.getElementById('asset_search');
                        const assetHidden = document.getElementById('asset_id');
                        const assetMap = <?php echo json_encode($assets->mapWithKeys(fn($asset) => [$asset->asset_code . ' - ' . $asset->name => $asset->id]), 15, 512) ?>;

                        const syncAssetId = () => {
                            const value = assetInput.value.trim();
                            assetHidden.value = assetMap[value] ?? '';
                        };

                        assetInput.addEventListener('input', syncAssetId);
                        assetInput.addEventListener('change', syncAssetId);
                    });
                </script>

                <div class="flex gap-3 pt-4">
                    <a href="<?php echo e(url()->to(route('tickets.index'))); ?>" class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Batal</a>
                    <button type="submit" class="flex-1 rounded-lg bg-brand-600 px-4 py-2.5 text-center font-medium text-white hover:bg-brand-700">Kirim Tiket</button>
                </div>
            </form>
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
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/tickets/create.blade.php ENDPATH**/ ?>