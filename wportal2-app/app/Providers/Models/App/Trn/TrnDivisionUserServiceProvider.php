<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnDivisionUserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_division_user_service',
            'App\Services\Models\App\Trn\TrnDivisionUserService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
