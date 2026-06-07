<?php
    $logoPath = file_exists(public_path('logo/logo.png'))
        ? 'logo/logo.png'
        : (file_exists(public_path('logo/full-logo.png')) ? 'logo/full-logo.png' : 'logo/logo.png');
?>

<img src="<?php echo e(asset($logoPath)); ?>" alt="<?php echo e(config('app.name', 'TimCare')); ?>" <?php echo e($attributes->merge(['class' => 'block h-9 w-auto object-contain'])); ?> />
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/components/application-logo.blade.php ENDPATH**/ ?>