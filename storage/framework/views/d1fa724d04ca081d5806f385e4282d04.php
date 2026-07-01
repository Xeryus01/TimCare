<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center gap-8">
                <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginal8892e718f3d0d7a916180885c6f012e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8892e718f3d0d7a916180885c6f012e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.application-logo','data' => ['class' => 'h-10 w-auto text-brand-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('application-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-10 w-auto text-brand-600']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $attributes = $__attributesOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $component = $__componentOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__componentOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
                </a>
                <div class="hidden md:flex items-center gap-2 sm:gap-4">
                    <a href="<?php echo e(route('documentation')); ?>" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Dokumentasi</a>
                    <a href="<?php echo e(route('faq')); ?>" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">FAQ</a>
                    <a href="<?php echo e(route('contact')); ?>" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Kontak</a>
                    <a href="<?php echo e(route('status')); ?>" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Status Sistem</a>
                </div>
            </div>
            <div class="flex items-center space-x-2 sm:space-x-4">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(url('/dashboard')); ?>" class="bg-brand-600 text-white px-4 sm:px-5 py-2 rounded-md text-sm font-medium hover:bg-brand-700 transition-colors">
                        Dashboard
                    </a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-gray-700 hover:text-gray-900 px-3 sm:px-4 py-2 rounded-md text-sm font-medium border border-gray-300 hover:border-gray-400 transition-colors">
                            Logout
                        </button>
                    </form>
                <?php else: ?>
                    <a href="<?php echo e(url()->to(route('login'))); ?>" class="text-gray-700 hover:text-gray-900 px-3 sm:px-4 py-2 rounded-md text-sm font-medium">
                        Masuk
                    </a>
                    <?php if(Route::has('register')): ?>
                        <a href="<?php echo e(url()->to(route('register'))); ?>" class="bg-brand-600 text-white px-3 sm:px-4 py-2 rounded-md text-sm font-medium hover:bg-brand-700">
                            Daftar
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<?php /**PATH D:\projects\timcare - Copy\resources\views/partials/landing-navigation.blade.php ENDPATH**/ ?>