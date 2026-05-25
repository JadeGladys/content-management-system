<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

Route::post('/admin/users', [UserController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('users.store');