<?php

use App\Http\Controllers\Article\PublicArticleController;
use App\Http\Controllers\Career\PublicCareerController;
use Illuminate\Support\Facades\Route;

// Article routes
Route::get('/articles', [PublicArticleController::class, 'index']);
Route::get('/articles/{slug}', [PublicArticleController::class, 'show']);

// Career routes
Route::get('/careers', [PublicCareerController::class, 'index']);
Route::get('/careers/filters', [PublicCareerController::class, 'filters']);
Route::get('/careers/{slug}', [PublicCareerController::class, 'show']);