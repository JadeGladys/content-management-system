<?php

use App\Http\Controllers\Article\ArticleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'cms.access'])->group(function () {
    Route::get('/admin/articles', [ArticleController::class, 'index'])
        ->name('articles.index');

    Route::post('/admin/articles', [ArticleController::class, 'store'])
        ->name('articles.store');

    Route::get('/admin/articles/{article}/edit', [ArticleController::class, 'edit'])
        ->name('articles.edit');

    Route::get('/admin/articles/{article}', [ArticleController::class, 'show'])
        ->name('articles.show');

    Route::put('/admin/articles/{article}', [ArticleController::class, 'update'])
        ->name('articles.update');

    Route::put('/admin/articles/{article}/status', [ArticleController::class, 'updateStatus'])
        ->name('articles.status.update');
});
