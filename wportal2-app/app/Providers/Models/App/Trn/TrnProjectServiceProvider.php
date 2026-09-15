<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnProjectServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_project_service',
            'App\Services\Models\App\Trn\TrnProjectService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
