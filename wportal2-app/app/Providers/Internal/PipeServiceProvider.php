<?php

declare(strict_types=1);

namespace App\Providers\Internal;

use Illuminate\Support\ServiceProvider;

class PipeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'internal\pipe_service',
            'App\Services\Internal\PipeService'
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
