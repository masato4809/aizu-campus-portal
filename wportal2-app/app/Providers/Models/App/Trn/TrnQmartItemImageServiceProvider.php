<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnQmartItemImageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_qmart_item_image_service',
            'App\Services\Models\App\Trn\TrnQmartItemImageService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
