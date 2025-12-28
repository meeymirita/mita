<?php

namespace App\Providers;

use App\Contracts\PostInterface;
use App\Contracts\UserCreateInterface;
use App\Services\Post\PostService;
use App\Services\User\UserCreateService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            UserCreateInterface::class,
            UserCreateService::class
        );
        $this->app->bind(
            PostInterface::class,
            PostService::class
        );
    }

    public function boot(): void
    {
    }
}
