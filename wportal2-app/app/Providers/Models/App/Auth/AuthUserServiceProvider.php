<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Auth;

use Illuminate\Support\ServiceProvider;

class AuthUserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\auth\auth_user_service',
            'App\Services\Models\App\Auth\AuthUserService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
