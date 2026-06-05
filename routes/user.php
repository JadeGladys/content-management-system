<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])
        ->name('users.index');

    Route::post('/admin/users', [UserController::class, 'store'])
        ->name('users.store');

    Route::post('/admin/users/{user}/resend-password-setup', [UserController::class, 'resendPasswordSetup'])
        ->name('users.password-setup.resend');
});
