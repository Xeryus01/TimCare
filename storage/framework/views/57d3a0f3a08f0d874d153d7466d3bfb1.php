<section>
    <div x-data="{ open: false }" class="space-y-6">
        <button @click="open = true" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-6 py-2.5 text-center font-medium text-white hover:bg-red-700">
            <?php echo e(__('Delete Account')); ?>

        </button>

        <!-- Delete Confirmation Modal -->
        <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="open = false">
            <div class="w-full max-w-md rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-dark-800" @click.stop>
                <!-- Modal Header -->
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        <?php echo e(__('Delete Account')); ?>

                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        <?php echo e(__('Once your account is deleted, all of its resources and data will be permanently deleted.')); ?>

                    </p>
                </div>

                <!-- Form -->
                <form method="post" action="<?php echo e(route('profile.destroy')); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('delete'); ?>

                    <!-- Warning Message -->
                    <div class="rounded-lg bg-red-50 p-4 dark:bg-red-500/10">
                        <p class="text-sm text-red-700 dark:text-red-400">
                            <?php echo e(__('Please enter your password to confirm you would like to permanently delete your account.')); ?>

                        </p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            <?php echo e(__('Password')); ?>

                        </label>
                        <input id="password" name="password" type="password" placeholder="<?php echo e(__('Enter your password')); ?>" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-red-600 focus:ring-2 focus:ring-red-100 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-red-600 dark:focus:ring-red-900/20" />
                        <?php if($errors->userDeletion->has('password')): ?>
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($errors->userDeletion->first('password')); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="open = false" class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">
                            <?php echo e(__('Cancel')); ?>

                        </button>
                        <button type="submit" class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-center font-medium text-white hover:bg-red-700">
                            <?php echo e(__('Delete Account')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/profile/partials/delete-user-form.blade.php ENDPATH**/ ?>