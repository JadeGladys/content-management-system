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
});
