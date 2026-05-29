<?php

use App\Http\Controllers\Article\ArticleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'cms.access'])->group(function () {
    Route::get('/admin/articles', [ArticleController::class, 'index'])
        ->name('articles.index');
    
    Route::get('/admin/articles/create', [ArticleController::class, 'create'])
        ->name('articles.create');

    Route::post('/admin/articles', [ArticleController::class, 'store'])
        ->name('articles.store');
});
