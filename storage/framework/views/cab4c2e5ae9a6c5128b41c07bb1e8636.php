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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Edit Jadwal Piket</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e(\Carbon\Carbon::parse($schedule->week_start_date)->format('d M Y')); ?> - <?php echo e($schedule->week_end_date ? \Carbon\Carbon::parse($schedule->week_end_date)->format('d M Y') : \Carbon\Carbon::parse($schedule->week_start_date)->endOfWeek()->format('d M Y')); ?></p>
            </div>
            <a href="<?php echo e(url()->to(route('piket.index'))); ?>" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">
                ← Kembali
            </a>
        </div>

        <?php if($errors->any()): ?>
            <div class="mb-4 rounded-lg border border-red-400 bg-red-100 p-4 text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-400">
                <ul class="list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="text-sm"><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-dark-800">
            <form action="<?php echo e(route('piket.update', $schedule->week_start_date)); ?>" method="POST" class="p-5 sm:p-7.5 lg:p-9">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="space-y-6">
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div>
                            <label for="week_start_date" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Tanggal Mulai</label>
                            <input id="week_start_date" name="week_start_date" type="date" required value="<?php echo e(old('week_start_date', $schedule->week_start_date->toDateString())); ?>" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20">
                        </div>
                        <div>
                            <label for="week_end_date" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Tanggal Selesai</label>
                            <input id="week_end_date" name="week_end_date" type="date" required value="<?php echo e(old('week_end_date', $schedule->week_end_date ? $schedule->week_end_date->toDateString() : $schedule->week_start_date->endOfWeek()->toDateString())); ?>" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20">
                        </div>
                    </div>

                    <!-- Technician 1 -->
                    <div>
                        <label for="technician_1" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Petugas 1</label>
                        <select id="technician_1" name="technician_1" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20">
                            <option value="">-- Pilih Teknisi --</option>
                            <?php $__currentLoopData = $technicians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($name); ?>" <?php echo e($schedule->technician_1 === $name ? 'selected' : ''); ?>>
                                    <?php echo e($name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Technician 2 -->
                    <div>
                        <label for="technician_2" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Petugas 2</label>
                        <select id="technician_2" name="technician_2" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20">
                            <option value="">-- Pilih Teknisi --</option>
                            <?php $__currentLoopData = $technicians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($name); ?>" <?php echo e($schedule->technician_2 === $name ? 'selected' : ''); ?>>
                                    <?php echo e($name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Technician 3 -->
                    <div>
                        <label for="technician_3" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Petugas 3</label>
                        <select id="technician_3" name="technician_3" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20">
                            <option value="">-- Pilih Teknisi --</option>
                            <?php $__currentLoopData = $technicians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($name); ?>" <?php echo e($schedule->technician_3 === $name ? 'selected' : ''); ?>>
                                    <?php echo e($name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Current Schedule Preview -->
                    <div class="rounded-lg border border-brand-200 bg-brand-50 p-4 dark:border-brand-500/30 dark:bg-brand-500/15">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Preview</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-300">Petugas 1:</span>
                                <span class="font-semibold text-gray-900 dark:text-white" id="preview_technician_1"><?php echo e($schedule->technician_1); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-300">Petugas 2:</span>
                                <span class="font-semibold text-gray-900 dark:text-white" id="preview_technician_2"><?php echo e($schedule->technician_2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-300">Petugas 3:</span>
                                <span class="font-semibold text-gray-900 dark:text-white" id="preview_technician_3"><?php echo e($schedule->technician_3); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="grid gap-2 pt-3 sm:grid-cols-3">
                        <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 transition" id="submitBtn">
                            Simpan
                        </button>
                        <a href="<?php echo e(url()->to(route('piket.index'))); ?>" class="w-full inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5 transition">
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('submitBtn').addEventListener('click', function(e) {
        const startDate = document.getElementById('week_start_date').value;
        const endDate = document.getElementById('week_end_date').value;
        const tech1 = document.getElementById('technician_1').value;
        const tech2 = document.getElementById('technician_2').value;
        const tech3 = document.getElementById('technician_3').value;

        if (!startDate || !endDate) {
            alert('Tanggal mulai dan selesai harus diisi.');
            e.preventDefault();
            return;
        }

        if (endDate < startDate) {
            alert('Tanggal selesai harus sama atau setelah tanggal mulai.');
            e.preventDefault();
            return;
        }

        if (!tech1 || !tech2 || !tech3) {
            alert('Semua petugas harus dipilih!');
            e.preventDefault();
            return;
        }

        if (tech1 === tech2 || tech1 === tech3 || tech2 === tech3) {
            alert('Petugas tidak boleh sama! Setiap petugas harus berbeda dalam satu minggu.');
            e.preventDefault();
            return;
        }
    });

    // Disable teknisi yang sudah dipilih di select lainnya
    const tech1 = document.getElementById('technician_1');
    const tech2 = document.getElementById('technician_2');
    const tech3 = document.getElementById('technician_3');

    const updateSelects = () => {
        Array.from(tech2.options).forEach(opt => {
            opt.disabled = opt.value && (opt.value === tech1.value);
        });
        Array.from(tech3.options).forEach(opt => {
            opt.disabled = opt.value && (opt.value === tech1.value || opt.value === tech2.value);
        });
        Array.from(tech1.options).forEach(opt => {
            opt.disabled = opt.value && (opt.value === tech2.value || opt.value === tech3.value);
        });
    };

    tech1.addEventListener('change', updateSelects);
    tech2.addEventListener('change', updateSelects);
    tech3.addEventListener('change', updateSelects);
    updateSelects();

    // Update preview in real-time
    document.getElementById('technician_1').addEventListener('change', function() {
        document.getElementById('preview_technician_1').textContent = this.value || '-';
    });
    document.getElementById('technician_2').addEventListener('change', function() {
        document.getElementById('preview_technician_2').textContent = this.value || '-';
    });
    document.getElementById('technician_3').addEventListener('change', function() {
        document.getElementById('preview_technician_3').textContent = this.value || '-';
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
<?php endif; ?>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/admin/piket/edit.blade.php ENDPATH**/ ?>