<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnDivisionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_division_service',
            'App\Services\Models\App\Trn\TrnDivisionService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
