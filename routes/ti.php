<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{TiTeamController,TiObjectController,TiReportController};
Route::middleware('auth')->prefix('monitoring-ti')->name('ti.')->group(function () {
    Route::get('/',[TiReportController::class,'index'])->name('reports.index');
    Route::get('/start',[TiReportController::class,'start'])->name('reports.start');
    Route::get('/export',[TiReportController::class,'export'])->name('reports.export');
    Route::get('/objects',[TiObjectController::class,'index'])->name('objects.index');
    Route::middleware('role:Admin')->group(function () {
        Route::get('/teams',[TiTeamController::class,'index'])->name('teams.index');
        Route::put('/teams',[TiTeamController::class,'bulkUpdate'])->name('teams.bulk-update');
        Route::get('/teams/{team}/members',[TiTeamController::class,'editMembers'])->name('teams.members.edit');
        Route::put('/teams/{team}/members',[TiTeamController::class,'updateMembers'])->name('teams.members.update');
        Route::post('/teams',[TiTeamController::class,'store'])->name('teams.store');
        Route::put('/teams/{team}',[TiTeamController::class,'update'])->name('teams.update');
        Route::get('/objects/create',[TiObjectController::class,'create'])->name('objects.create');
        Route::post('/objects',[TiObjectController::class,'store'])->name('objects.store');
        Route::get('/objects/{object}/edit',[TiObjectController::class,'edit'])->name('objects.edit');
        Route::put('/objects/{object}',[TiObjectController::class,'update'])->name('objects.update');
    });
    Route::get('/objects/{object}/{type}/create',[TiReportController::class,'create'])->name('reports.create');
    Route::post('/objects/{object}/{type}',[TiReportController::class,'store'])->name('reports.store');
    Route::get('/reports/{report}',[TiReportController::class,'show'])->name('reports.show');
    Route::get('/reports/{report}/edit',[TiReportController::class,'edit'])->name('reports.edit');
    Route::put('/reports/{report}',[TiReportController::class,'update'])->name('reports.update');
});
