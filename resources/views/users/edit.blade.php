<x-app-layout>
<main class="p-6">
    <div class="max-w-2xl mx-auto">
                <!-- Page Header -->
                <div class="mb-8">
                    <div class="flex items-center">
                        <a href="{{ url()->to(route('users.index')) }}" class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit User</h1>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Perbarui informasi user {{ $user->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="bg-white dark:bg-dark-800 overflow-hidden shadow-sm rounded-lg">
                    <form data-tc-native method="POST" action="{{ route('users.update', $user) }}" class="p-6">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-brand-500 focus:border-brand-500 dark:bg-dark-700 dark:text-white">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-brand-500 focus:border-brand-500 dark:bg-dark-700 dark:text-white">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div class="mb-4">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">No. Handphone</label>
                            <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-brand-500 focus:border-brand-500 dark:bg-dark-700 dark:text-white">
                            @error('phone_number')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="mb-6">
                            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                            <select id="role" name="role" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 dark:bg-dark-700 dark:text-white">
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ (old('role', $user->roles->first()?->name) === $role->name) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        @include('users.partials.teams')

                        <!-- Actions -->
                        <div class="flex items-center justify-end">
                            <a href="{{ url()->to(route('users.index')) }}" class="mr-4 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-700 focus:bg-brand-700 active:bg-brand-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Perbarui User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
</x-app-layout>