<?php

namespace App\Providers;

use Domain\Blog\Services\Implementations\ArticleServiceImpl;
use Domain\Blog\Services\Interfaces\ArticleService;
use Domain\Common\Services\Implementations\AuthenticationServiceImpl;
use Domain\Common\Services\Interfaces\AuthenticationService;
use Domain\Users\Services\Implementations\UserServiceImpl;
use Domain\Users\Services\Interfaces\UserService;
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
        $this->app->bind(UserService::class, UserServiceImpl::class);

        $this->app->singleton(AuthenticationService::class, function ($app) {
            return new AuthenticationServiceImpl($app->make(UserService::class));
        });
    }
}
