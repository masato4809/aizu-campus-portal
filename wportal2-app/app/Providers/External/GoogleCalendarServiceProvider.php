<?php

declare(strict_types=1);

namespace App\Providers\External;

use Illuminate\Support\ServiceProvider;

class GoogleCalendarServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'external\google_calendar_service',
            'App\Services\External\GoogleCalendarService'
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
