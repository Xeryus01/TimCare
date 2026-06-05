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
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Notifikasi</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lihat semua notifikasi dan tindakan yang diperlukan pada layanan IT.</p>
                </div>
                <?php if(auth()->user()->notifications()->where('is_read', false)->exists()): ?>
                    <button type="button" onclick="fetch('<?php echo e(url('/api/notifications/mark-visible-as-read')); ?>', {method: 'PATCH', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ids: <?php echo json_encode($notifications->pluck('id'), 15, 512) ?>})}).then(r => location.reload())" class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-4 py-2 text-center font-medium text-white hover:bg-brand-700">
                        Tandai Semua Dibaca
                    </button>
                <?php endif; ?>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-dark-800">
                <div class="mb-5 text-sm text-gray-600 dark:text-gray-400">
                    Anda memiliki <span class="font-medium"><?php echo e(auth()->user()->notifications()->where('is_read', false)->count()); ?></span> notifikasi belum dibaca.
                </div>

                <!-- Notifications List -->
        <div class="overflow-x-auto">
            <?php if($notifications->count() > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-white/5 transition min-w-0 <?php echo e(!$notification->is_read ? 'bg-blue-50/50 dark:bg-blue-500/10' : ''); ?>">
                            <!-- Unread Indicator -->
                            <div class="mt-1">
                                <?php if(!$notification->is_read): ?>
                                    <div class="h-3 w-3 rounded-full bg-brand-500"></div>
                                <?php else: ?>
                                    <div class="h-3 w-3 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                <?php endif; ?>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <a href="<?php echo e(url()->to(route('notifications.show', $notification))); ?>" class="block">
                                    <h4 class="font-medium text-gray-900 dark:text-white hover:text-brand-600 dark:hover:text-brand-400 transition break-words">
                                        <?php echo e($notification->title); ?>

                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 break-words">
                                        <?php echo e($notification->message); ?>

                                    </p>
                                </a>

                                <!-- Meta Info -->
                                <div class="flex items-center gap-4 mt-3">
                                    <span class="text-xs text-gray-500 dark:text-gray-500">
                                        <?php echo e($notification->created_at->diffForHumans()); ?>

                                    </span>
                                    <?php if($notification->action_type && $notification->action_id): ?>
                                        <?php switch($notification->action_type):
                                            case ('ticket'): ?>
                                                <a href="<?php echo e(url()->to(route('tickets.show', $notification->action_id))); ?>" class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                                    View Ticket
                                                </a>
                                                <?php break; ?>
                                            <?php case ('reservation'): ?>
                                                <a href="<?php echo e(url()->to(route('reservations.show', $notification->action_id))); ?>" class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                                    View Reservation
                                                </a>
                                                <?php break; ?>
                                            <?php case ('asset'): ?>
                                                <a href="<?php echo e(url()->to(route('assets.show', $notification->action_id))); ?>" class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                                    View Asset
                                                </a>
                                                <?php break; ?>
                                        <?php endswitch; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <?php if(!$notification->is_read): ?>
                                    <button onclick="fetch('<?php echo e(url('/api/notifications/' . $notification->id . '/mark-as-read')); ?>', {method: 'PATCH'}).then(r => location.reload())" class="inline-flex h-8 w-8 items-center justify-center rounded text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5" title="Mark as read">
                                        <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.8333 4.16667L6 12L2.16667 8.16667" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                <?php endif; ?>
                                <button onclick="if(confirm('Delete this notification?')) fetch('<?php echo e(url('/api/notifications/' . $notification->id)); ?>', {method: 'DELETE'}).then(r => location.reload())" class="inline-flex h-8 w-8 items-center justify-center rounded text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-red-600 dark:hover:text-red-400" title="Delete">
                                    <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.5 3.5L3.5 12.5M3.5 3.5L12.5 12.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Pagination -->
                <?php if($notifications->hasPages()): ?>
                    <div class="mt-6">
                        <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['paginator' => $notifications]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notifications)]); ?>
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
                <?php endif; ?>
            <?php else: ?>
                <div class="flex flex-col items-center justify-center py-12">
                    <svg class="h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 14.5V17c0 .55-.45 1-1 1H3c-.55 0-1-.45-1-1V6c0-.55.45-1 1-1h10c.55 0 1 .45 1 1v2.5M9 3v3M9 21v-2M3 9h8M3 13h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="text-base font-medium text-gray-900 dark:text-white">No notifications yet</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">You're all caught up!</p>
                </div>
            <?php endif; ?>
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
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/notifications/index.blade.php ENDPATH**/ ?>