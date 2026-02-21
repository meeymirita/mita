<?php

namespace App\Providers;

use App\Contracts\User\AuthUserInterface;
use App\Contracts\User\PasswordResetUserInterface;
use App\Contracts\User\UpdateUserInterface;
use App\Contracts\User\UserCreateInterface;
use App\Services\User\AuthService;
use App\Services\User\PasswordResetService;
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
        /**
         *  User Interface
         */
        $this->app->bind(
            abstract: UserCreateInterface::class,
            concrete: UserCreateService::class
        );
        $this->app->bind(
            abstract: AuthUserInterface::class,
            concrete: AuthService::class
        );
        $this->app->bind(
            abstract: UpdateUserInterface::class,
            concrete: UpdateService::class
        );
        $this->app->bind(
            abstract: PasswordResetUserInterface::class,
            concrete: PasswordResetService::class
        );
    }

    public function boot(): void
    {
    }
}
