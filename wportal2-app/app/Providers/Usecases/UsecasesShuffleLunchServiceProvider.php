<?php

declare(strict_types=1);

namespace App\Providers\Usecases;

use Illuminate\Support\ServiceProvider;

class UsecasesShuffleLunchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'usecases\usecases_shuffle_lunch_service',
            'App\Services\Usecases\UsecasesShuffleLunchService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
