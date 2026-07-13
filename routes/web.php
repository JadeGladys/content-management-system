<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth', 'cms.access'])->get('/admin', [DashboardController::class, 'index'])
    ->name('dashboard');

require __DIR__.'/auth.php';

require __DIR__.'/user.php';

require __DIR__.'/career.php';

require __DIR__.'/article.php';

require __DIR__.'/media.php';

require __DIR__.'/audit.php';
