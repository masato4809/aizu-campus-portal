<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnUserProjectPriorityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_user_project_priority_service',
            'App\Services\Models\App\Trn\TrnUserProjectPriorityService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
