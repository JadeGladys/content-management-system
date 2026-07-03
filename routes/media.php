<?php

use App\Http\Controllers\Media\MediaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'cms.access'])->group(function () {
    Route::get('/admin/media', [MediaController::class, 'index'])
        ->name('media.index');

    Route::post('/admin/media', [MediaController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('media.store');
});
