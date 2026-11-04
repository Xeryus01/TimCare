<x-app-layout>
@include('ti.partials.assets')
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <!-- Page Header -->
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Ubah Password User</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ubah password untuk {{ $user->name }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ url()->to(route('users.show', $user)) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-dark-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="mx-auto max-w-2xl">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800">
                <div class="border-b border-gray-200 p-5 sm:p-7.5 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi User</h2>
                </div>

                <!-- User Info -->
                <div class="space-y-4 border-b border-gray-200 p-5 sm:p-7.5 dark:border-gray-700">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Password Form -->
                <form data-tc-native action="{{ route('users.updatePassword', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-5 p-5 sm:p-7.5">
                        <!-- Password -->
                        <div>
                            <label for="password" class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Password Baru</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                value="{{ old('password') }}"
                                placeholder="Masukkan password baru (minimal 8 karakter)"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-700 dark:text-white dark:focus:border-brand-600 dark:focus:ring-brand-900/30"
                            />
                            @error('password')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Konfirmasi Password</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Konfirmasi password baru"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-brand-100 dark:border-gray-600 dark:bg-dark-700 dark:text-white dark:focus:border-brand-600 dark:focus:ring-brand-900/30"
                            />
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Note -->
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/30 dark:bg-amber-900/15">
                            <p class="text-sm text-amber-800 dark:text-amber-200">
                                <strong>Catatan:</strong> Password harus minimal 8 karakter dan harus sama di kedua field.
                            </p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex gap-3 border-t border-gray-200 bg-gray-50 p-5 sm:p-7.5 dark:border-gray-700 dark:bg-dark-700/50">
                        <a href="{{ url()->to(route('users.show', $user)) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-dark-700">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 font-medium text-white hover:bg-brand-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
