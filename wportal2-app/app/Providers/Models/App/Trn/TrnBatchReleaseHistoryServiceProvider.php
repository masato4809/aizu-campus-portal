<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnBatchReleaseHistoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton(
            'models\scat\trn\trn_batch_release_history_service',
            'App\Services\Models\App\Trn\TrnBatchReleaseHistoryService'
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
