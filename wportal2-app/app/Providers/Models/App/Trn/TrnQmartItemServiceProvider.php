<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnQmartItemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_qmart_item_service',
            'App\Services\Models\App\Trn\TrnQmartItemService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
