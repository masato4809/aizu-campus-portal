<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnUserAuthorityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_user_authority_service',
            'App\Services\Models\App\Trn\TrnUserAuthorityService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
