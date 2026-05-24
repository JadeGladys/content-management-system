<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/admin/users', [UserController::class, 'store'])
    ->middleware('auth', 'admin')
    ->name('users.store');
