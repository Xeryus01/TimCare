<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Name Field -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                {{ __('Name') }}
            </label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20" />
            @if ($errors->has('name'))
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('name') }}</p>
            @endif
        </div>

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                {{ __('Email') }}
            </label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 disabled:cursor-default disabled:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20" />
            @if ($errors->has('email'))
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <div>
            <label for="phone_number" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                Nomor HP
            </label>
            <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number', $user->phone_number) }}" autocomplete="tel" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-900 placeholder-gray-500 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-brand-600 dark:focus:ring-brand-900/20" />
            @if ($errors->has('phone_number'))
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errors->first('phone_number') }}</p>
            @endif
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 rounded-lg bg-yellow-50 p-4 dark:bg-yellow-500/10">
                    <p class="text-sm text-yellow-800 dark:text-yellow-400">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" type="button" class="inline text-brand-600 underline hover:text-brand-700 dark:text-brand-400">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-6 py-2.5 text-center font-medium text-white hover:bg-brand-700">
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 dark:text-green-400">
                    {{ __('Saved successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>
