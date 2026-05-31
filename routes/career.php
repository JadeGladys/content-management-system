<?php

use App\Http\Controllers\Career\CareerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'cms.access'])->group(function () {
    Route::get('/admin/careers', [CareerController::class, 'index'])
        ->name('careers.index');

    Route::post('/admin/careers', [CareerController::class, 'store'])
        ->name('careers.store');

    Route::get('/admin/careers/{career}/edit', [CareerController::class, 'edit'])
        ->name('careers.edit');

    Route::put('/admin/careers/{career}', [CareerController::class, 'update'])
        ->name('careers.update');
});