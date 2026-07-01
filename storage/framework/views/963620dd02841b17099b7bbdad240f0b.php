<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <!-- Flash Messages -->
    <?php if(session('status')): ?>
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700 dark:bg-green-500/10 dark:text-green-400">
            <?php echo e(session('status')); ?>

        </div>
    <?php endif; ?>

    <form id="loginForm" method="POST" action="<?php echo e(route('login')); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                <?php echo e(__('Email Address')); ?>

            </label>
            <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="username" placeholder="name@example.com" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20" />
            <?php if($errors->has('email')): ?>
                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($errors->first('email')); ?></p>
            <?php endif; ?>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                <?php echo e(__('Password')); ?>

            </label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20" />
            <?php if($errors->has('password')): ?>
                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($errors->first('password')); ?></p>
            <?php endif; ?>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-dark-800" />
            <label for="remember_me" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                <?php echo e(__('Remember me')); ?>

            </label>
        </div>

        <!-- Forgot Password & Login Button -->
        <div class="flex items-center justify-between">
            <?php if(Route::has('password.request')): ?>
                <a href="<?php echo e(url()->to(route('password.request'))); ?>" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                    <?php echo e(__('Forgot your password?')); ?>

                </a>
            <?php endif; ?>

            <button id="loginBtn" type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-6 py-2.5 text-center font-medium text-white hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                <span id="btnText"><?php echo e(__('Sign in')); ?></span>
                <span id="btnSpinner" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </div>

        <!-- SSO Login -->
        <div class="text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">atau masuk menggunakan</p>
            <a href="<?php echo e(url()->to(route('sso.redirect'))); ?>" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-2 text-sm font-medium hover:bg-gray-50">
                <img src="<?php echo e(asset('logo/bps.png')); ?>" alt="SSO" class="h-4 w-4 mr-2"> Masuk dengan SSO BPS
            </a>
        </div>

        <!-- Register Link (only when enabled) -->
        <?php if(Route::has('register')): ?>
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <?php echo e(__("Don't have an account?")); ?>

                    <a href="<?php echo e(url()->to(route('register'))); ?>" class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                        <?php echo e(__('Sign up')); ?>

                    </a>
                </p>
            </div>
        <?php endif; ?>
    </form>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            btn.disabled = true;
            btnText.textContent = '<?php echo e(__('Signing in...')); ?>';
            btnSpinner.classList.remove('hidden');
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php /**PATH D:\projects\timcare - Copy\resources\views/auth/login.blade.php ENDPATH**/ ?>