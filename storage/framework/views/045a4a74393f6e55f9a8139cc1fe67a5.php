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
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Manajemen User</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola pengguna sistem helpdesk IT</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(url()->to(route('users.create'))); ?>" class="inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 font-medium text-white hover:bg-brand-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah User
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if(session('success')): ?>
            <div class="mb-4 rounded-lg border border-green-400 bg-green-100 p-3 text-sm text-green-700 dark:border-green-500/30 dark:bg-green-500/15 dark:text-green-400">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="mb-4 rounded-lg border border-red-400 bg-red-100 p-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-400">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <!-- Users Table -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/5">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Nama</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Email</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Role</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">No. HP</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Dibuat</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($user->name); ?></span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="text-sm text-gray-600 dark:text-gray-400"><?php echo e($user->email); ?></span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <?php if($user->hasRole('Admin')): ?>
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">Admin</span>
                                    <?php elseif($user->hasRole('Teknisi')): ?>
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">Teknisi</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-900 dark:text-gray-200">User</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-600 dark:text-gray-400"><?php echo e($user->phone_number ?? '-'); ?></td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-600 dark:text-gray-400"><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                                <td class="px-5 py-4 sm:px-6 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-3">
                                        <a href="<?php echo e(url()->to(route('users.show', $user))); ?>" class="text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">Lihat</a>
                                        <a href="<?php echo e(url()->to(route('users.editPassword', $user))); ?>" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">Password</a>
                                        <a href="<?php echo e(url()->to(route('users.edit', $user))); ?>" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</a>
                                        <?php if($user->id !== auth()->id()): ?>
                                            <form method="POST" action="<?php echo e(route('users.destroy', $user)); ?>" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:px-6">
                                    Tidak ada user ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['paginator' => $users]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users)]); ?>
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
<?php endif; ?><?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/users/index.blade.php ENDPATH**/ ?>