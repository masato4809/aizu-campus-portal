<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnUserRewardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_user_reward_service',
            'App\Services\Models\App\Trn\TrnUserRewardService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
