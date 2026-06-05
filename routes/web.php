<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware([\App\Http\Middleware\ContentSecurityPolicy::class])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
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
    Route::resource('tickets', \App\Http\Controllers\TicketViewController::class);
    Route::post('tickets/{ticket}/comments', [\App\Http\Controllers\TicketViewController::class, 'comment'])->name('tickets.comment');
    Route::post('tickets/{ticket}/attachments', [\App\Http\Controllers\TicketViewController::class, 'uploadAttachment'])->name('tickets.attachments.store');
    Route::get('tickets/{ticket}/attachments/{attachment}', [\App\Http\Controllers\TicketViewController::class, 'showAttachment'])->name('tickets.attachments.show');

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
        Route::patch('/notifications/mark-all-as-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('api.notifications.markAllAsRead');
        Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('api.notifications.destroy');
    });

    // User management - Admin only
    Route::middleware('role:Admin')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::get('users/{user}/change-password', [\App\Http\Controllers\UserController::class, 'editPassword'])->name('users.editPassword');
        Route::patch('users/{user}/change-password', [\App\Http\Controllers\UserController::class, 'updatePassword'])->name('users.updatePassword');

        // Piket schedule management - Admin only
        Route::get('admin/piket', [\App\Http\Controllers\PiketScheduleController::class, 'index'])->name('piket.index');
        Route::get('admin/piket/create', [\App\Http\Controllers\PiketScheduleController::class, 'create'])->name('piket.create');
        Route::post('admin/piket', [\App\Http\Controllers\PiketScheduleController::class, 'store'])->name('piket.store');
        Route::get('admin/piket/{weekStart}/edit', [\App\Http\Controllers\PiketScheduleController::class, 'edit'])->name('piket.edit');
        Route::put('admin/piket/{weekStart}', [\App\Http\Controllers\PiketScheduleController::class, 'update'])->name('piket.update');
        Route::delete('admin/piket/{weekStart}', [\App\Http\Controllers\PiketScheduleController::class, 'destroy'])->name('piket.destroy');
    });

    // Get current piket schedule (public API)
    Route::get('api/piket/current', [\App\Http\Controllers\PiketScheduleController::class, 'show'])->name('api.piket.show');

    // Piket schedule view for technicians (read-only)
    Route::get('piket', [\App\Http\Controllers\PiketScheduleController::class, 'view'])->name('piket.view')->middleware('role:Teknisi');
});

    require __DIR__.'/auth.php';
