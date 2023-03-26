<?php

use App\Modules\Blog\Controllers\ArticleController;
use App\Modules\Common\Controllers\WelcomeController;
use App\Modules\Common\Middleware\MustBeAuthenticated;
use App\Modules\Users\Controllers\UserController;
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

Route::prefix('v1')->group(function () {

    Route::post('users/login', [UserController::class, 'login'])->name('users.login');

    Route::middleware([MustBeAuthenticated::class])->group(function () {
        Route::get('/', [WelcomeController::class, 'index'])->name('api.welcome');

        Route::get('articles/trashed', [ArticleController::class, 'trashed'])->name('articles.trashed');
        Route::patch('articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
        Route::patch('articles/{article}/draft', [ArticleController::class, 'draft'])->name('articles.draft');

        Route::apiResource('articles', ArticleController::class);

        Route::apiResource('users', UserController::class)->only('index', 'store', 'show', 'destroy');
    });

});
