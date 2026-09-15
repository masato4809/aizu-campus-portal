<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnGoodJobServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_good_job_service',
            'App\Services\Models\App\Trn\TrnGoodJobService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
