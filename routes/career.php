<?php

use App\Http\Controllers\Career\CareerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'cms.access'])->group(function () {
    Route::get('/admin/careers', [CareerController::class, 'index'])
        ->name('careers.index');
});
