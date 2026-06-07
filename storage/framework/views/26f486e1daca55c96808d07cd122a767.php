<footer class="bg-gray-900 text-white py-8 sm:py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 sm:gap-12 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <?php if (isset($component)) { $__componentOriginal8892e718f3d0d7a916180885c6f012e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8892e718f3d0d7a916180885c6f012e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.application-logo','data' => ['class' => 'h-10 w-auto text-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('application-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-10 w-auto text-white']); ?>
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
                </div>
                <p class="text-gray-400 text-sm sm:text-base max-w-md leading-relaxed">
                    Solusi helpdesk IT yang menyatukan tiket, pengajuan Zoom, dan pemantauan layanan dalam satu platform yang mudah digunakan.
                </p>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-semibold mb-4 text-white">Fitur</h3>
                <ul class="space-y-2 sm:space-y-3 text-gray-400">
                    <li><a href="<?php echo e(url('/tickets/create')); ?>" class="text-sm sm:text-base transition hover:text-white">Pengajuan Tiket</a></li>
                    <li><a href="<?php echo e(url('/reservations/create')); ?>" class="text-sm sm:text-base transition hover:text-white">Pengajuan Room Zoom</a></li>
                    <li><a href="<?php echo e(url('/dashboard')); ?>" class="text-sm sm:text-base transition hover:text-white">Dashboard</a></li>
                    <li><a href="<?php echo e(url('/notifications')); ?>" class="text-sm sm:text-base transition hover:text-white">Notifikasi</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-semibold mb-4 text-white">Dukungan</h3>
                <ul class="space-y-2 sm:space-y-3 text-gray-400">
                    <li><a href="<?php echo e(route('documentation')); ?>" class="text-sm sm:text-base transition hover:text-white">Dokumentasi</a></li>
                    <li><a href="<?php echo e(route('faq')); ?>" class="text-sm sm:text-base transition hover:text-white">FAQ</a></li>
                    <li><a href="<?php echo e(route('contact')); ?>" class="text-sm sm:text-base transition hover:text-white">Kontak</a></li>
                    <li><a href="<?php echo e(route('status')); ?>" class="text-sm sm:text-base transition hover:text-white">Status Sistem</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-8 sm:mt-12 lg:mt-16 border-t border-gray-800 pt-6 sm:pt-8 text-xs sm:text-sm text-gray-500">
            © <?php echo e(date('Y')); ?> TimCare. All rights reserved.
        </div>
    </div>
</footer>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/partials/landing-footer.blade.php ENDPATH**/ ?>