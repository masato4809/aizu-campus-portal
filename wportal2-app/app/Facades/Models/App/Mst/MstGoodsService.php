<?php

declare(strict_types=1);

namespace App\Facades\Models\App\Mst;

use Illuminate\Support\Facades\Facade;

class MstGoodsService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'models\app\mst\mst_goods_service';
    }
}
