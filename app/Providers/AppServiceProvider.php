<?php

namespace App\Providers;

use Domain\Blog\Services\Implementations\ArticleServiceImpl;
use Domain\Blog\Services\Interfaces\ArticleService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(ArticleService::class, ArticleServiceImpl::class);
    }
}
