<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnShopAggregateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_shop_aggregate_service',
            'App\Services\Models\App\Trn\TrnShopAggregateService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
