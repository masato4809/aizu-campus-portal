<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnUserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_user_service',
            'App\Services\Models\App\Trn\TrnUserService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
