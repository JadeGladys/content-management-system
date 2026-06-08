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

    Route::get('/admin/articles/{article}/edit', [ArticleController::class, 'edit'])
        ->name('articles.edit');

    Route::get('/admin/articles/{article}', [ArticleController::class, 'show'])
        ->name('articles.show');

    Route::put('/admin/articles/{article}', [ArticleController::class, 'update'])
        ->name('articles.update');

    Route::delete('/admin/articles/{article}', [ArticleController::class, 'destroy'])
    ->name('articles.destroy');

    Route::put('/admin/articles/{article}/status', [ArticleController::class, 'updateStatus'])
        ->name('articles.status.update');
});
