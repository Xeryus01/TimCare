<?php if (isset($component)) { $__componentOriginal66080d40165dc237152ede315c1d0309 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal66080d40165dc237152ede315c1d0309 = $attributes; } ?>
<?php $component = App\View\Components\ErrorLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('error-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\ErrorLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $previousUrl = url()->previous() ?: route('dashboard'); ?>

    <?php echo $__env->make('components.error.card', [
        'code' => '403',
        'title' => 'Akses Ditolak',
        'message' => $exception->getMessage() ?: 'Anda tidak memiliki izin untuk melihat halaman ini. Pastikan Anda menggunakan peran atau izin yang sesuai.',
        'previousUrl' => $previousUrl,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal66080d40165dc237152ede315c1d0309)): ?>
<?php $attributes = $__attributesOriginal66080d40165dc237152ede315c1d0309; ?>
<?php unset($__attributesOriginal66080d40165dc237152ede315c1d0309); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal66080d40165dc237152ede315c1d0309)): ?>
<?php $component = $__componentOriginal66080d40165dc237152ede315c1d0309; ?>
<?php unset($__componentOriginal66080d40165dc237152ede315c1d0309); ?>
<?php endif; ?>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/errors/403.blade.php ENDPATH**/ ?>