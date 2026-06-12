<?php

use App\Http\Controllers\Article\PublicArticleController;
use Illuminate\Support\Facades\Route;

Route::get('/articles', [PublicArticleController::class, 'index']);
Route::get('/articles/{slug}', [PublicArticleController::class, 'show']);