<x-app-layout>
    <div class="mx-auto w-full max-w-[1440px] p-4 sm:p-6 lg:p-8">
        <div class="mb-6 overflow-hidden rounded-3xl border border-brand-100 bg-gradient-to-br from-white via-white to-brand-50 shadow-sm dark:border-gray-700 dark:from-dark-800 dark:via-dark-800 dark:to-dark-900">
            <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-7">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M4 21a8 8 0 0 1 16 0"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-300">Pengaturan akun</p>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">Profil Saya</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Kelola informasi pribadi dan keamanan akun Anda.</p>
                    </div>
                </div>
                <div class="inline-flex w-fit items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-500/10 dark:text-emerald-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Akun aktif
                </div>
            </div>
        </div>

        <div class="grid min-w-0 items-stretch gap-5 lg:grid-cols-[minmax(250px,320px)_minmax(0,1fr)] lg:grid-rows-[auto_auto]">
            <div class="grid min-w-0 lg:col-start-1 lg:row-span-2 lg:row-start-1 lg:[grid-template-rows:subgrid]">
            <aside class="min-w-0 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-dark-800">
                <div class="flex flex-col items-center border-b border-gray-100 bg-gradient-to-b from-gray-50 to-white px-5 py-7 text-center dark:border-gray-700 dark:from-dark-900/60 dark:to-dark-800">
                    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-indigo-600 text-3xl font-bold text-white shadow-lg shadow-brand-500/20 ring-4 ring-white dark:ring-dark-700">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h2 class="max-w-full break-words text-lg font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                    <p class="mt-1 max-w-full break-all text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    <span class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"></path>
                        </svg>
                        {{ match ($user->roles->first()->name ?? 'User') { 'Admin' => 'Administrator', 'User' => 'Pengguna', default => $user->roles->first()->name ?? 'Pengguna' } }}
                    </span>
                </div>

                <dl class="space-y-4 p-5">
                    <div class="flex min-w-0 items-start gap-3">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 3.1 5.2 2 2 0 0 1 5.1 3h3a2 2 0 0 1 2 1.7l.4 2.8a2 2 0 0 1-.6 1.7L8.6 10.5a16 16 0 0 0 4.9 4.9l1.3-1.3a2 2 0 0 1 1.7-.6l2.8.4a2 2 0 0 1 1.7 2Z"></path>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Nomor HP</dt>
                            <dd class="mt-0.5 break-words text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $user->phone_number ?: 'Belum diisi' }}</dd>
                        </div>
                    </div>
                    <div class="flex min-w-0 items-start gap-3">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 7v5l3 2"></path>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Login terakhir</dt>
                            <dd class="mt-0.5 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $lastLoginAt ? $lastLoginAt->locale('id')->translatedFormat('d F Y') : 'Belum tersedia' }}</dd>
                        </div>
                    </div>
                    <div class="flex min-w-0 items-start gap-3">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                <path d="M16 3v4M8 3v4M3 10h18"></path>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Bergabung sejak</dt>
                            <dd class="mt-0.5 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ optional($user->created_at)->locale('id')->translatedFormat('d F Y') ?? '-' }}</dd>
                        </div>
                    </div>
                </dl>
            </aside>
            <section class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-dark-800">
                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-700">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-500/15 dark:text-violet-300">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 12a9 9 0 1 0 2.6-6.4L3 8"></path>
                            <path d="M3 3v5h5m4-1v5l3 2"></path>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <h2 class="font-semibold text-gray-900 dark:text-white">Riwayat Aktivitas</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Aktivitas terbaru akun Anda.</p>
                    </div>
                </div>

                @if ($activityLogs->isEmpty())
                    <div class="flex flex-1 flex-col justify-center px-5 py-8 text-center">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Belum ada aktivitas yang tercatat.</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Aktivitas akun Anda akan muncul di sini.</p>
                    </div>
                @else
                    <ol class="flex-1 divide-y divide-gray-100 overflow-y-auto dark:divide-gray-700">
                        @foreach ($activityLogs as $activityLog)
                            <li class="flex gap-3 px-4 py-3.5">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 8v4l3 2"></path>
                                        <circle cx="12" cy="12" r="9"></circle>
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold leading-5 text-gray-900 dark:text-white">{{ $activityLogPresenter->action($activityLog) }}</p>
                                    <p class="mt-1 break-words text-xs text-gray-500 dark:text-gray-400">
                                        {{ $activityLogPresenter->entity($activityLog) }}
                                        <span class="px-1 text-gray-300 dark:text-gray-600">·</span>
                                        #{{ $activityLog->entity_id }}
                                    </p>
                                    <time class="mt-1.5 block text-[11px] text-gray-500 dark:text-gray-400" datetime="{{ $activityLog->created_at?->toIso8601String() }}">
                                        {{ $activityLog->created_at?->locale('id')->translatedFormat('d M Y') }}
                                        <span class="px-1 text-gray-300 dark:text-gray-600">·</span>
                                        {{ $activityLog->created_at?->format('H:i') }}
                                    </time>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                    @if ($activityLogs->hasPages())
                        <div class="space-y-3 border-t border-gray-100 px-4 py-3 dark:border-gray-700">
                            <p class="text-center text-xs text-gray-500 dark:text-gray-400">
                                {{ $activityLogs->firstItem() }}–{{ $activityLogs->lastItem() }} dari {{ $activityLogs->total() }} aktivitas
                            </p>
                            <nav class="flex items-center justify-between gap-2" aria-label="Navigasi riwayat aktivitas">
                                @if ($activityLogs->onFirstPage())
                                    <span class="cursor-not-allowed rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-400 dark:border-gray-700 dark:text-gray-500">Sebelumnya</span>
                                @else
                                    <a href="{{ $activityLogs->previousPageUrl() }}" class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-white/5">Sebelumnya</a>
                                @endif
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ $activityLogs->currentPage() }} / {{ $activityLogs->lastPage() }}</span>
                                @if ($activityLogs->hasMorePages())
                                    <a href="{{ $activityLogs->nextPageUrl() }}" class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-white/5">Berikutnya</a>
                                @else
                                    <span class="cursor-not-allowed rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-400 dark:border-gray-700 dark:text-gray-500">Berikutnya</span>
                                @endif
                            </nav>
                        </div>
                    @endif
                @endif
            </section>
            </div>

            <div class="grid min-w-0 lg:col-start-2 lg:row-span-2 lg:row-start-1 lg:[grid-template-rows:subgrid]">
                <section class="mt-5 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-dark-800">
                    <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-700 sm:px-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="8" r="4"></circle>
                                <path d="M4 21a8 8 0 0 1 16 0"></path>
                            </svg>
                        </span>
                        <div>
                            <h2 class="font-semibold text-gray-900 dark:text-white">Informasi Profil</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui nama, nomor HP, dan alamat email.</p>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                            @csrf
                        </form>

                        <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                            @csrf
                            @method('patch')

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Nama Lengkap</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-600 dark:bg-dark-900 dark:text-white dark:placeholder:text-gray-500">
                                    @if ($errors->has('name'))
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Nomor HP</label>
                                    <input type="text" id="phone" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" autocomplete="tel" placeholder="08xxxxxxxxxx" class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-600 dark:bg-dark-900 dark:text-white dark:placeholder:text-gray-500">
                                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Gunakan format 08xxxxxxxxxx atau 62xxxxxxxxxx.</p>
                                    @if ($errors->has('phone_number'))
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $errors->first('phone_number') }}</p>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Alamat Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email" class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-600 dark:bg-dark-900 dark:text-white dark:placeholder:text-gray-500">
                                @if ($errors->has('email'))
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $errors->first('email') }}</p>
                                @endif
                            </div>

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/50 dark:bg-amber-500/10">
                                    <p class="text-sm text-amber-800 dark:text-amber-300">
                                        {{ __('Your email address is unverified.') }}
                                        <button form="send-verification" type="submit" class="font-semibold underline underline-offset-2 hover:text-amber-900 dark:hover:text-amber-200">
                                            {{ __('Click here to re-send the verification email.') }}
                                        </button>
                                    </p>
                                    @if (session('status') === 'verification-link-sent')
                                        <p class="mt-2 text-sm font-medium text-emerald-700 dark:text-emerald-300">{{ __('A new verification link has been sent to your email address.') }}</p>
                                    @endif
                                </div>
                            @endif

                            <div class="flex flex-col gap-3 border-t border-gray-100 pt-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Terakhir diperbarui: {{ optional($user->updated_at)->locale('id')->translatedFormat('d F Y') ?? '-' }}</p>
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                    @if (session('status') === 'profile-updated')
                                        <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300" role="status">Profil berhasil diperbarui.</p>
                                    @endif
                                    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/20">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"></path>
                                            <path d="M17 21v-8H7v8M7 3v5h8"></path>
                                        </svg>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>

                <section class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-dark-800">
                    <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-700 sm:px-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </span>
                        <div>
                            <h2 class="font-semibold text-gray-900 dark:text-white">Keamanan Akun</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui password secara berkala.</p>
                        </div>
                    </div>
                    <div class="p-5 sm:p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
