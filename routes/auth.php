<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;

//login and forgot password
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');

    Route::get('/admin/forgot-password', [PasswordResetController::class, 'create'])
    ->name('password.request');

    Route::post('/admin/forgot-password', [PasswordResetController::class, 'store'])
        ->name('password.email');
});

//setup password
Route::get('/admin/set-password', [SetPasswordController::class, 'edit'])
    ->name('password.setup');

Route::post('/admin/set-password', [SetPasswordController::class, 'update'])
    ->name('password.setup.update');

//reset password
Route::get('/admin/reset-password', [PasswordResetController::class, 'edit'])
    ->name('password.reset');

Route::post('/admin/reset-password', [PasswordResetController::class, 'update'])
    ->name('password.update');

//logout
Route::middleware('auth')->group(function () {
    Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
