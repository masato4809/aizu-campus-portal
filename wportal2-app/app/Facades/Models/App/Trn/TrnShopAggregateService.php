<?php

declare(strict_types=1);

namespace App\Facades\Models\App\Trn;

use Illuminate\Support\Facades\Facade;

class TrnShopAggregateService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'models\app\trn\trn_shop_aggregate_service';
    }
}
