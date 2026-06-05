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
<!-- Main Content -->
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <!-- Page Header -->
        <div class="mb-9">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl"><?php echo e(__('Profile Settings')); ?></h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your account information and preferences</p>
        </div>

        <!-- Settings Cards -->
        <div class="space-y-6">
            <!-- Profile Information Card -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                <div class="border-b border-gray-200 pb-6 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo e(__('Profile Information')); ?></h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update your personal information.</p>
                </div>
                <div class="mt-6">
                    <?php echo $__env->make('profile.partials.update-profile-information-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>

            <!-- Password Card -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800">
                <div class="border-b border-gray-200 pb-6 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo e(__('Update Password')); ?></h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ensure your account has a long, random password to stay secure.</p>
                </div>
                <div class="mt-6">
                    <?php echo $__env->make('profile.partials.update-password-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>

            <!-- Delete Account Card -->
            <div class="rounded-xl border border-red-200 bg-red-50 p-6 dark:border-red-500/20 dark:bg-red-500/10">
                <div class="border-b border-red-200 pb-6 dark:border-red-500/20">
                    <h2 class="text-lg font-bold text-red-900 dark:text-red-400"><?php echo e(__('Delete Account')); ?></h2>
                    <p class="mt-1 text-sm text-red-700 dark:text-red-400">Once you delete your account, there is no going back. Please be certain.</p>
                </div>
                <div class="mt-6">
                    <?php echo $__env->make('profile.partials.delete-user-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/profile/edit.blade.php ENDPATH**/ ?>