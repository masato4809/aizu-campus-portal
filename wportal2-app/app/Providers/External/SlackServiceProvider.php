<?php

declare(strict_types=1);

namespace App\Providers\External;

use Illuminate\Support\ServiceProvider;

class SlackServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'external\slack_service',
            'App\Services\External\SlackService'
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
