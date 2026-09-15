<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\External\GoogleCalendarService;
use App\Services\External\GoogleCalendarServiceInterface;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GoogleCalendarServiceInterface::class, GoogleCalendarService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        if (isPrd()) {
            URL::forceScheme('https');
        }
    }
}
