<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'editor'])->get('/', function () {
    return view('dashboard');
})->name('dashboard');

require __DIR__.'/auth.php';

require __DIR__.'/user.php';

require __DIR__.'/site.php';
