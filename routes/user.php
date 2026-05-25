<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/users/create', [UserController::class, 'create'])
        ->name('users.create');

    Route::post('/admin/users', [UserController::class, 'store'])
        ->name('users.store');
});