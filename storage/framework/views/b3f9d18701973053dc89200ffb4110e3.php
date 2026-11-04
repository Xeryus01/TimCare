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
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Backup &amp; File Manager</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Buat backup PDF per pilihan atau sekaligus untuk seluruh tiket dalam rentang tanggal.</p>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-4 rounded-lg border border-green-400 bg-green-100 p-4 text-sm text-green-700 dark:border-green-500/30 dark:bg-green-500/15 dark:text-green-400"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="mb-4 rounded-lg border border-red-400 bg-red-100 p-4 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-400"><?php echo e(session('error')); ?></div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="mb-4 rounded-lg border border-red-400 bg-red-100 p-4 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-400"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
            <p class="font-semibold">Cara pakai</p>
            <ol class="mt-1 list-decimal space-y-0.5 pl-5">
                <li>Tentukan rentang tanggal pembuatan untuk menampilkan data, lalu klik <strong>Backup seluruh periode</strong> untuk membuat PDF sekaligus (diunduh sebagai ZIP). Maksimal 500 data per proses.</li>
                <li>Atau centang beberapa baris dan klik <strong>Backup PDF Terpilih</strong>. Salinan PDF juga tersimpan di server.</li>
                <li>Setelah yakin sudah dibackup, centang tiket lalu klik <strong>Hapus Berkas Terpilih</strong> untuk menghapus foto/PDF lampiran tiket tersebut dari penyimpanan.</li>
            </ol>
        </div>

        <!-- ================= TIKET MASALAH ================= -->
        <div class="mb-10 overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800">
            <div class="flex flex-col gap-3 border-b border-gray-200 p-5 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tiket Masalah</h2>
            </div>

            <form method="GET" action="<?php echo e(route('file-manager.index')); ?>" class="flex flex-wrap items-end gap-3 border-b border-gray-200 p-4 dark:border-gray-700">
                <div>
                    <label for="ticket-date-from" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Dibuat dari</label>
                    <input id="ticket-date-from" type="date" name="ticket_from" value="<?php echo e(request('ticket_from')); ?>" class="rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-dark-800 dark:text-white">
                </div>
                <div>
                    <label for="ticket-date-to" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Sampai</label>
                    <input id="ticket-date-to" type="date" name="ticket_to" value="<?php echo e(request('ticket_to')); ?>" class="rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-dark-800 dark:text-white">
                </div>
                <input type="hidden" name="reservation_from" value="<?php echo e(request('reservation_from')); ?>">
                <input type="hidden" name="reservation_to" value="<?php echo e(request('reservation_to')); ?>">
                <input type="hidden" name="per_page" value="<?php echo e(request('per_page', 10)); ?>">
                <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500">Tampilkan periode</button>
                <a href="<?php echo e(route('file-manager.index', array_filter(['reservation_from' => request('reservation_from'), 'reservation_to' => request('reservation_to')]))); ?>" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Reset</a>
                <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($tickets->total()); ?> tiket dalam hasil filter</span>
            </form>

            <form method="POST" id="ticketForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="date_from" value="<?php echo e(request('ticket_from')); ?>">
                <input type="hidden" name="date_to" value="<?php echo e(request('ticket_to')); ?>">
                <div class="flex flex-wrap gap-2 border-b border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-white/5">
                    <button type="submit" formaction="<?php echo e(route('file-manager.backup.tickets')); ?>" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Backup PDF Terpilih</button>
                    <button type="submit" name="scope" value="period" formaction="<?php echo e(route('file-manager.backup.tickets')); ?>" <?php echo e(request('ticket_from') && request('ticket_to') ? '' : 'disabled'); ?> class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50">Backup seluruh periode</button>
                    <button type="submit" formaction="<?php echo e(route('file-manager.tickets.files.delete')); ?>" onclick="return tcConfirmDelete('Hapus semua berkas foto/PDF dari tiket masalah yang dipilih? Pastikan sudah dibackup. Tindakan ini tidak dapat dibatalkan.')" class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">Hapus Berkas Terpilih</button>
                    <?php if(request('ticket_from') && request('ticket_to')): ?>
                        <span class="self-center text-xs text-gray-500 dark:text-gray-400">Tanggal dibuat: <?php echo e(request('ticket_from')); ?> s/d <?php echo e(request('ticket_to')); ?></span>
                    <?php endif; ?>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3.5 text-left sm:px-6"><input type="checkbox" onclick="tcToggleAll(this, 'js-ticket-check')" class="rounded border-gray-300"></th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Kode</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Judul</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Dibuat</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Pemohon</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Berkas</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Ukuran</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Backup</th>
                                <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php $info = $ticketInfo[$ticket->id]; ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="px-4 py-4 sm:px-6"><input type="checkbox" name="ids[]" value="<?php echo e($ticket->id); ?>" class="js-ticket-check rounded border-gray-300"></td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-white sm:px-6"><?php echo e($ticket->code); ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 sm:px-6"><?php echo e(Str::limit($ticket->title, 40)); ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 sm:px-6"><?php echo e($ticket->created_at->format('d/m/Y')); ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 sm:px-6"><?php echo e(optional($ticket->requester)->name ?? '-'); ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 sm:px-6"><?php echo e($info['existing']); ?> / <?php echo e($info['count']); ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 sm:px-6"><?php echo e(number_format($info['totalBytes'] / 1024, 1)); ?> KB</td>
                                    <td class="px-4 py-4 sm:px-6">
                                        <?php if($info['hasBackup']): ?>
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-500/15 dark:text-green-400">Sudah</span>
                                        <?php else: ?>
                                            <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500 dark:bg-white/10 dark:text-gray-400">Belum</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4 text-right text-sm sm:px-6">
                                        <div class="flex items-center justify-end gap-2">
                                            <?php if($info['hasBackup']): ?>
                                                <a href="<?php echo e(route('file-manager.ticket.backup.download', $ticket)); ?>" class="font-medium text-green-600 hover:text-green-700 dark:text-green-400">Unduh PDF</a>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('tickets.show', $ticket)); ?>" class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">Lihat</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:px-6">Belum ada tiket masalah.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
            <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['paginator' => $tickets]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tickets)]); ?>
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

        <!-- ================= TIKET ZOOM ================= -->
        <div class="mb-10 overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800">
            <div class="flex flex-col gap-3 border-b border-gray-200 p-5 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tiket Zoom</h2>
            </div>

            <form method="GET" action="<?php echo e(route('file-manager.index')); ?>" class="flex flex-wrap items-end gap-3 border-b border-gray-200 p-4 dark:border-gray-700">
                <div>
                    <label for="reservation-date-from" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Dibuat dari</label>
                    <input id="reservation-date-from" type="date" name="reservation_from" value="<?php echo e(request('reservation_from')); ?>" class="rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-dark-800 dark:text-white">
                </div>
                <div>
                    <label for="reservation-date-to" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Sampai</label>
                    <input id="reservation-date-to" type="date" name="reservation_to" value="<?php echo e(request('reservation_to')); ?>" class="rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-dark-800 dark:text-white">
                </div>
                <input type="hidden" name="ticket_from" value="<?php echo e(request('ticket_from')); ?>">
                <input type="hidden" name="ticket_to" value="<?php echo e(request('ticket_to')); ?>">
                <input type="hidden" name="per_page" value="<?php echo e(request('per_page', 10)); ?>">
                <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500">Tampilkan periode</button>
                <a href="<?php echo e(route('file-manager.index', array_filter(['ticket_from' => request('ticket_from'), 'ticket_to' => request('ticket_to')]))); ?>" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-white/5">Reset</a>
                <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($reservations->total()); ?> pengajuan dalam hasil filter</span>
            </form>

            <form method="POST" id="reservationForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="date_from" value="<?php echo e(request('reservation_from')); ?>">
                <input type="hidden" name="date_to" value="<?php echo e(request('reservation_to')); ?>">
                <div class="flex flex-wrap gap-2 border-b border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-white/5">
                    <button type="submit" formaction="<?php echo e(route('file-manager.backup.reservations')); ?>" class="inline-flex items-center gap-1.5 rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">Backup PDF Terpilih</button>
                    <button type="submit" name="scope" value="period" formaction="<?php echo e(route('file-manager.backup.reservations')); ?>" <?php echo e(request('reservation_from') && request('reservation_to') ? '' : 'disabled'); ?> class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50">Backup seluruh periode</button>
                    <button type="submit" formaction="<?php echo e(route('file-manager.reservations.files.delete')); ?>" onclick="return tcConfirmDelete('Hapus berkas nota dinas (PDF) dari tiket zoom yang dipilih? Pastikan sudah dibackup. Tindakan ini tidak dapat dibatalkan.')" class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">Hapus Berkas Terpilih</button>
                    <?php if(request('reservation_from') && request('reservation_to')): ?>
                        <span class="self-center text-xs text-gray-500 dark:text-gray-400">Tanggal dibuat: <?php echo e(request('reservation_from')); ?> s/d <?php echo e(request('reservation_to')); ?></span>
                    <?php endif; ?>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3.5 text-left sm:px-6"><input type="checkbox" onclick="tcToggleAll(this, 'js-reservation-check')" class="rounded border-gray-300"></th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Kode</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Ruangan</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Dibuat</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Pemohon</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Nota Dinas</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Backup</th>
                                <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 sm:px-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php $rinfo = $reservationInfo[$reservation->id]; ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="px-4 py-4 sm:px-6"><input type="checkbox" name="ids[]" value="<?php echo e($reservation->id); ?>" class="js-reservation-check rounded border-gray-300"></td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-white sm:px-6"><?php echo e($reservation->code); ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 sm:px-6"><?php echo e($reservation->room_name ?? '-'); ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 sm:px-6"><?php echo e($reservation->created_at->format('d/m/Y')); ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 sm:px-6"><?php echo e(optional($reservation->requester)->name ?? '-'); ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 sm:px-6">
                                        <?php if($rinfo['hasFile'] && $rinfo['exists']): ?>
                                            <?php echo e(number_format($rinfo['totalBytes'] / 1024, 1)); ?> KB
                                        <?php elseif($rinfo['hasFile']): ?>
                                            <span class="text-amber-600 dark:text-amber-400">berkas hilang</span>
                                        <?php else: ?>
                                            <span class="text-gray-400">tidak ada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4 sm:px-6">
                                        <?php if($rinfo['hasBackup']): ?>
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-500/15 dark:text-green-400">Sudah</span>
                                        <?php else: ?>
                                            <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500 dark:bg-white/10 dark:text-gray-400">Belum</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4 text-right text-sm sm:px-6">
                                        <div class="flex items-center justify-end gap-2">
                                            <?php if($rinfo['hasBackup']): ?>
                                                <a href="<?php echo e(route('file-manager.reservation.backup.download', $reservation)); ?>" class="font-medium text-green-600 hover:text-green-700 dark:text-green-400">Unduh PDF</a>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('reservations.show', $reservation)); ?>" class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">Lihat</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:px-6">Belum ada tiket zoom.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
            <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['paginator' => $reservations]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reservations)]); ?>
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
    </div>
</div>

<script>
    function tcToggleAll(source, cls) {
        var boxes = document.querySelectorAll('.' + cls);
        boxes.forEach(function (b) {
            b.checked = source.checked;
        });
    }
    function tcConfirmDelete(msg) {
        return window.confirm(msg);
    }
</script>
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
<?php /**PATH C:\Users\BPS\Documents\timcare-dev\resources\views/admin/file-manager/index.blade.php ENDPATH**/ ?>