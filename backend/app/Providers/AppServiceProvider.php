<?php

namespace App\Providers;

use App\Contracts\User\AuthUserInterface;
use App\Contracts\User\UpdateUserInterface;
use App\Contracts\User\UserCreateInterface;
use App\Services\User\AuthService;
use App\Services\User\UpdateService;
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
            AuthUserInterface::class,
            AuthService::class
        );
        $this->app->bind(
            UpdateUserInterface::class,
            UpdateService::class
        );
    }

    public function boot(): void
    {
    }
}
