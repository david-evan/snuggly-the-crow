<?php

use App\Modules\Blog\Controllers\ArticleController;
use App\Modules\Common\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/', [WelcomeController::class, 'index'])->name('api.welcome');

Route::get('articles/trashed', [ArticleController::class, 'trashed'])->name('articles.trashed');
Route::patch('articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
Route::patch('articles/{article}/draft', [ArticleController::class, 'draft'])->name('articles.draft');

Route::apiResource('articles', ArticleController::class);
