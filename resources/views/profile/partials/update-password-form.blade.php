<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Current Password Field -->
        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                Kata Sandi Saat Ini
            </label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20" />
            @if ($errors->updatePassword->has('current_password'))
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <!-- New Password Field -->
        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                Kata Sandi Baru
            </label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20" />
            @if ($errors->updatePassword->has('password'))
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <!-- Confirm Password Field -->
        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                Konfirmasi Kata Sandi
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20" />
            @if ($errors->updatePassword->has('password_confirmation'))
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-6 py-2.5 text-center font-medium text-white hover:bg-brand-700">
                Simpan Kata Sandi
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 dark:text-green-400">
                    Password berhasil diubah.
                </p>
            @endif
        </div>
    </form>
</section>
