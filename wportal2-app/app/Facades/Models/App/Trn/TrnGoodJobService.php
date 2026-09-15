<?php

declare(strict_types=1);

namespace App\Facades\Models\App\Trn;

use Illuminate\Support\Facades\Facade;

class TrnGoodJobService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'models\app\trn\trn_good_job_service';
    }
}
