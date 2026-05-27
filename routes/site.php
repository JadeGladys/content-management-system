<?php

use App\Http\Controllers\Site\SiteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/sites/create', [SiteController::class, 'create'])
        ->name('sites.create');

    Route::post('/admin/sites', [SiteController::class, 'store'])
        ->name('sites.store');
});