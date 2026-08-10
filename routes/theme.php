<?php

use App\Http\Controllers\Setting\ThemeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/theme', [ThemeController::class, 'index'])
        ->name('theme.index');
    
    Route::post('/admin/theme', [ThemeController::class, 'update'])
        ->name('theme.update');
});
