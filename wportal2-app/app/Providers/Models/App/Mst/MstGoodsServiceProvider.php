<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Mst;

use Illuminate\Support\ServiceProvider;

class MstGoodsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\mst\mst_goods_service',
            'App\Services\Models\App\Mst\MstGoodsService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
