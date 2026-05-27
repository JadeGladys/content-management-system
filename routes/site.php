<?php

use App\Http\Controllers\Site\SiteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/sites', [SiteController::class, 'index'])
        ->name('sites.index');
        
    Route::get('/admin/sites/create', function () {
        return redirect()->route('sites.index');
    })->name('sites.create');

    Route::post('/admin/sites', [SiteController::class, 'store'])
        ->name('sites.store');

    Route::patch('/admin/sites/{site}', [SiteController::class, 'update'])
        ->name('sites.update');

    Route::patch('/admin/sites/{site}/status', [SiteController::class, 'toggleStatus'])
        ->name('sites.status');
});
