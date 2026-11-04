<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware([\App\Http\Middleware\ContentSecurityPolicy::class])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/documentation', function () {
        return view('pages.documentation');
    })->name('documentation');

    Route::get('/faq', function () {
        return view('pages.faq');
    })->name('faq');

    Route::get('/contact', function () {
        return view('pages.contact');
    })->name('contact');

    Route::get('/status-sistem', function () {
        return view('pages.status');
    })->name('status');
});

Route::get('/dashboard', function () {
    $activePiketSchedules = collect();
    
    try {
        $currentWeek = \App\Models\PiketSchedule::getCurrentWeek();
        
        if ($currentWeek) {
            $activePiketSchedules->push([
                'week_start_date' => $currentWeek->week_start_date->format('d/m/Y'),
                'week_end_date' => $currentWeek->week_end_date->format('d/m/Y'),
                'technicians' => $currentWeek->scheduledUserNames(),
            ]);
        }
    } catch (\Exception $e) {
        // Fallback jika ada error
        $activePiketSchedules = collect();
    }
    
    return view('dashboard', compact('activePiketSchedules'));
})->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/api/dashboard/charts', [\App\Http\Controllers\Api\DashboardController::class, 'charts'])->name('api.dashboard.charts');

    Route::get('/exports/tickets', [ExportController::class, 'tickets'])->name('exports.tickets');
    Route::get('/exports/reservations', [ExportController::class, 'reservations'])->name('exports.reservations');

    // simple blade views for our features
    Route::middleware('role:Admin')->group(function () {
        Route::post('tickets/bulk-assign', [\App\Http\Controllers\TicketViewController::class, 'bulkAssign'])->name('tickets.bulk-assign');
        Route::post('reservations/bulk-assign', [\App\Http\Controllers\ReservationViewController::class, 'bulkAssign'])->name('reservations.bulk-assign');
    });
    Route::resource('tickets', \App\Http\Controllers\TicketViewController::class);
    Route::post('tickets/{ticket}/comments', [\App\Http\Controllers\TicketViewController::class, 'comment'])->name('tickets.comment');
    Route::post('tickets/{ticket}/attachments', [\App\Http\Controllers\TicketViewController::class, 'uploadAttachment'])->name('tickets.attachments.store');
    Route::get('tickets/{ticket}/attachments/{attachment}', [\App\Http\Controllers\TicketViewController::class, 'showAttachment'])->name('tickets.attachments.show');
    Route::post('tickets/{ticket}/rating', [\App\Http\Controllers\ServiceRatingController::class, 'rateTicket'])->name('tickets.rating.store');

    // Asset CSV/XLS download/import routes must be registered before the resource route to avoid conflict with assets/{asset}
    Route::get('assets/template', [\App\Http\Controllers\AssetViewController::class, 'downloadTemplate'])->name('assets.template');
    Route::post('assets/import', [\App\Http\Controllers\AssetViewController::class, 'import'])->name('assets.import');
    Route::get('assets/export', [\App\Http\Controllers\AssetViewController::class, 'export'])->name('assets.export');
    Route::resource('assets', \App\Http\Controllers\AssetViewController::class);

    // Asset management routes (Admin only)
    Route::middleware('role:Admin')->group(function () {
        Route::get('assets/{asset}/change-holder', [\App\Http\Controllers\AssetViewController::class, 'changeHolder'])->name('assets.changeHolder');
        Route::post('assets/{asset}/change-holder', [\App\Http\Controllers\AssetViewController::class, 'storeChangeHolder'])->name('assets.storeChangeHolder');
        Route::get('assets/{asset}/add-maintenance', [\App\Http\Controllers\AssetViewController::class, 'addMaintenance'])->name('assets.addMaintenance');
        Route::post('assets/{asset}/add-maintenance', [\App\Http\Controllers\AssetViewController::class, 'storeMaintenance'])->name('assets.storeMaintenance');
    });

    Route::resource('reservations', \App\Http\Controllers\ReservationViewController::class);
    Route::get('reservations/{reservation}/nota-dinas', [\App\Http\Controllers\ReservationViewController::class, 'showNotaDinas'])->name('reservations.nota-dinas');
    Route::post('reservations/{reservation}/rating', [\App\Http\Controllers\ServiceRatingController::class, 'rateReservation'])->name('reservations.rating.store');

    // notifications UI
    Route::get('/notifications', [\App\Http\Controllers\NotificationViewController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [\App\Http\Controllers\NotificationViewController::class, 'show'])->name('notifications.show');

    // notifications API endpoint for header dropdown (same-session web auth)
    Route::middleware('auth')->prefix('api')->group(function () {
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('api.notifications.index');
        Route::get('/notifications/latest-unread', [\App\Http\Controllers\NotificationController::class, 'latestUnread'])->name('api.notifications.latestUnread');
        Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('api.notifications.unreadCount');
        Route::get('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'show'])->name('api.notifications.show');
        Route::patch('/notifications/{notification}/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('api.notifications.markAsRead');
        Route::patch('/notifications/mark-visible-as-read', [\App\Http\Controllers\NotificationController::class, 'markVisibleAsRead'])->name('api.notifications.markVisibleAsRead');
        Route::patch('/notifications/mark-all-as-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('api.notifications.markAllAsRead');
        Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('api.notifications.destroy');
    });

    // User management - Admin only
    Route::middleware('role:Admin')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::get('users/{user}/change-password', [\App\Http\Controllers\UserController::class, 'editPassword'])->name('users.editPassword');
        Route::patch('users/{user}/change-password', [\App\Http\Controllers\UserController::class, 'updatePassword'])->name('users.updatePassword');
        // File Manager & Backup PDF (Admin)
        Route::get('admin/file-manager', [\App\Http\Controllers\FileManagerController::class, 'index'])->name('file-manager.index');
        Route::post('admin/file-manager/backup/tickets', [\App\Http\Controllers\FileManagerController::class, 'backupTickets'])->name('file-manager.backup.tickets');
        Route::post('admin/file-manager/backup/reservations', [\App\Http\Controllers\FileManagerController::class, 'backupReservations'])->name('file-manager.backup.reservations');
        Route::get('admin/file-manager/tickets/{ticket}/backup', [\App\Http\Controllers\FileManagerController::class, 'downloadTicketBackup'])->name('file-manager.ticket.backup.download');
        Route::get('admin/file-manager/reservations/{reservation}/backup', [\App\Http\Controllers\FileManagerController::class, 'downloadReservationBackup'])->name('file-manager.reservation.backup.download');
        Route::post('admin/file-manager/tickets/files/delete', [\App\Http\Controllers\FileManagerController::class, 'deleteTicketFiles'])->name('file-manager.tickets.files.delete');
        Route::post('admin/file-manager/reservations/files/delete', [\App\Http\Controllers\FileManagerController::class, 'deleteReservationFiles'])->name('file-manager.reservations.files.delete');

        // Piket schedule management - Admin only
        Route::get('admin/piket', [\App\Http\Controllers\PiketScheduleController::class, 'index'])->name('piket.index');
        Route::get('admin/piket/create', [\App\Http\Controllers\PiketScheduleController::class, 'create'])->name('piket.create');
        Route::post('admin/piket', [\App\Http\Controllers\PiketScheduleController::class, 'store'])->name('piket.store');
        Route::get('admin/piket/{schedule}/edit', [\App\Http\Controllers\PiketScheduleController::class, 'edit'])->name('piket.edit');
        Route::put('admin/piket/{schedule}', [\App\Http\Controllers\PiketScheduleController::class, 'update'])->name('piket.update');
        Route::delete('admin/piket/{schedule}', [\App\Http\Controllers\PiketScheduleController::class, 'destroy'])->name('piket.destroy');
    });

    // Get current piket schedule (public API)
    Route::get('api/piket/current', [\App\Http\Controllers\PiketScheduleController::class, 'show'])->name('api.piket.show');

    // Piket schedule view for technicians (read-only)
    Route::get('piket', [\App\Http\Controllers\PiketScheduleController::class, 'view'])->name('piket.view')->middleware('role:Teknisi');

    // Maintenance - Admin, Teknisi, ULP access
    Route::middleware(['auth', 'role:Admin|Teknisi|ULP'])->group(function () {
        Route::get('maintenance', [\App\Http\Controllers\MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('maintenance/{ticket}/process', [\App\Http\Controllers\MaintenanceController::class, 'process'])->name('maintenance.process');
        Route::post('maintenance/{ticket}/complete', [\App\Http\Controllers\MaintenanceController::class, 'complete'])->name('maintenance.complete');
    });
});

    require __DIR__.'/auth.php';

// Timcare Monitoring TI
require __DIR__.'/ti.php';
