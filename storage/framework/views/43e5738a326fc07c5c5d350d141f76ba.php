<?php if($paginator->hasPages()): ?>
    <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
        <div class="flex flex-col gap-4 sm:gap-6 sm:flex-row sm:items-center sm:justify-between">
            <!-- Info Text -->
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Menampilkan <span class="font-medium"><?php echo e($paginator->firstItem() ?? 0); ?></span> hingga 
                <span class="font-medium"><?php echo e($paginator->lastItem() ?? 0); ?></span> dari 
                <span class="font-medium"><?php echo e($paginator->total()); ?></span> hasil
            </div>

            <!-- Per Page Selector -->
            <form method="GET" class="flex items-center gap-2">
                <?php $__currentLoopData = request()->query(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($key !== 'per_page' && $key !== 'page'): ?>
                        <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <label for="per_page" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Per Halaman:
                </label>
                <select name="per_page" id="per_page" 
                    class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
                    onchange="this.form.submit()">
                    <option value="10" <?php echo e(request('per_page') == 10 ? 'selected' : ''); ?>>10</option>
                    <option value="20" <?php echo e(request('per_page') == 20 ? 'selected' : ''); ?>>20</option>
                    <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50</option>
                </select>
            </form>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 flex items-center justify-center gap-1 flex-wrap">
            
            <?php if($paginator->onFirstPage()): ?>
                <span class="relative inline-flex items-center rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-500 cursor-not-allowed dark:border-gray-600 dark:bg-white/5 dark:text-gray-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            <?php else: ?>
                <a href="<?php echo e($paginator->previousPageUrl()); ?>&per_page=<?php echo e(request('per_page', 10)); ?>" 
                    class="relative inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            <?php endif; ?>

            
            <?php
                $start = max(1, $paginator->currentPage() - 2);
                $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
            ?>

            <?php if($start > 1): ?>
                <a href="<?php echo e($paginator->url(1)); ?>&per_page=<?php echo e(request('per_page', 10)); ?>" 
                    class="relative inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10 transition-colors">
                    1
                </a>
                <?php if($start > 2): ?>
                    <span class="px-1 text-gray-500">...</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php for($i = $start; $i <= $end; $i++): ?>
                <?php if($i == $paginator->currentPage()): ?>
                    <span class="relative inline-flex items-center rounded-lg border border-brand-300 bg-brand-50 px-3 py-2 text-sm font-medium text-brand-600 dark:border-brand-500/30 dark:bg-brand-500/15 dark:text-brand-400">
                        <?php echo e($i); ?>

                    </span>
                <?php else: ?>
                    <a href="<?php echo e($paginator->url($i)); ?>&per_page=<?php echo e(request('per_page', 10)); ?>" 
                        class="relative inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10 transition-colors">
                        <?php echo e($i); ?>

                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if($end < $paginator->lastPage()): ?>
                <?php if($end < $paginator->lastPage() - 1): ?>
                    <span class="px-1 text-gray-500">...</span>
                <?php endif; ?>
                <a href="<?php echo e($paginator->url($paginator->lastPage())); ?>&per_page=<?php echo e(request('per_page', 10)); ?>" 
                    class="relative inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10 transition-colors">
                    <?php echo e($paginator->lastPage()); ?>

                </a>
            <?php endif; ?>

            
            <?php if($paginator->hasMorePages()): ?>
                <a href="<?php echo e($paginator->nextPageUrl()); ?>&per_page=<?php echo e(request('per_page', 10)); ?>" 
                    class="relative inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-white/10 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            <?php else: ?>
                <span class="relative inline-flex items-center rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-500 cursor-not-allowed dark:border-gray-600 dark:bg-white/5 dark:text-gray-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/components/pagination.blade.php ENDPATH**/ ?>