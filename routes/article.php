<?php

use App\Http\Controllers\Article\ArticleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'cms.access'])->group(function () {
    Route::get('/admin/articles', [ArticleController::class, 'index'])
        ->name('articles.index');
});
