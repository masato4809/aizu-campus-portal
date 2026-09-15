<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnShuffleLunchEntryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_shuffle_lunch_entry_service',
            'App\Services\Models\App\Trn\TrnShuffleLunchEntryService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
