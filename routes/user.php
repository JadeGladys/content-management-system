<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])
        ->name('users.index');

    Route::get('/admin/users/create', function () {
        return redirect()->route('users.index');
    })
        ->name('users.create');

    Route::post('/admin/users', [UserController::class, 'store'])
        ->name('users.store');
});
