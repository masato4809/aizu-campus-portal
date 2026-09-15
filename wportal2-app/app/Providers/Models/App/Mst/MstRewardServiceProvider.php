<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Mst;

use Illuminate\Support\ServiceProvider;

class MstRewardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\mst\mst_reward_service',
            'App\Services\Models\App\Mst\MstRewardService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
