<?php

declare(strict_types=1);

namespace App\Facades\Models\App\Trn;

use Illuminate\Support\Facades\Facade;

class TrnBatchReleaseHistoryService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'models\scat\trn\trn_batch_release_history_service';
    }
}
