<?php

use App\Http\Controllers\Career\CareerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'cms.access'])->group(function () {
    Route::get('/admin/careers', [CareerController::class, 'index'])
        ->name('careers.index');

    Route::get('/admin/careers/create', [CareerController::class, 'create'])
        ->name('careers.create');

    Route::post('/admin/careers', [CareerController::class, 'store'])
        ->name('careers.store');

    Route::get('/admin/careers/{career}/edit', [CareerController::class, 'edit'])
        ->name('careers.edit');

    Route::get('/admin/careers/{career}', [CareerController::class, 'show'])
        ->name('careers.show');

    Route::put('/admin/careers/{career}', [CareerController::class, 'update'])
        ->name('careers.update');

    Route::delete('/admin/careers/{career}', [CareerController::class, 'destroy'])
    ->name('careers.destroy');

    Route::put('/admin/careers/{career}/status', [CareerController::class, 'updateStatus'])
        ->name('careers.status.update');
});
