<?php

declare(strict_types=1);

namespace App\Providers\External;

use Illuminate\Support\ServiceProvider;

class SesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'external\ses_service',
            'App\Services\External\SesService'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
