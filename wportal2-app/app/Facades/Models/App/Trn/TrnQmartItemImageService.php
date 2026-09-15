<?php

declare(strict_types=1);

namespace App\Facades\Models\App\Trn;

use Illuminate\Support\Facades\Facade;

class TrnQmartItemImageService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'models\app\trn\trn_qmart_item_image_service';
    }
}
