<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnSeatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_seat_service',
            'App\Services\Models\App\Trn\TrnSeatService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
