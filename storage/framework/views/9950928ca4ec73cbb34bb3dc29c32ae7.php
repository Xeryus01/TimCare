<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([ 'code' => 'Error', 'title' => 'Terjadi Kesalahan', 'message' => null, 'previousUrl' => url()->previous() ?: route('dashboard') ]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([ 'code' => 'Error', 'title' => 'Terjadi Kesalahan', 'message' => null, 'previousUrl' => url()->previous() ?: route('dashboard') ]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="page-content">
    <div class="error-card">
        <div class="error-card-inner">
            <div class="error-visual">
                <div class="error-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7v6c0 5 3.58 9.74 10 13 6.42-3.26 10-8 10-13V7l-10-5z"/>
                        <path d="M12 9v4M12 17h.01"/>
                    </svg>
                </div>
                <div class="error-code">Error <?php echo e($code); ?></div>
            </div>

            <div class="error-body">
                <h2 class="error-title"><?php echo e($title); ?></h2>
                <div class="error-msg"><?php echo e($message ?? 'Maaf, terjadi kesalahan. Silakan coba lagi atau hubungi administrator.'); ?></div>
                <div class="error-actions">
                    <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M3 12h18M3 12l7-7M3 12l7 7"/></svg>
                        Kembali ke Dashboard
                    </a>
                    <a href="<?php echo e($previousUrl); ?>" class="btn btn-outline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 19l-7-7 7-7"/></svg>
                        Kembali
                    </a>
                    <button onclick="window.location.reload()" class="btn btn-ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M23 4v6h-6M1 20v-6h6"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
                        Muat Ulang
                    </button>
                </div>
                <p class="error-hint">Jika ini bukan kesalahan Anda, silakan <a href="#">hubungi administrator sistem</a>.</p>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/components/error/card.blade.php ENDPATH**/ ?>