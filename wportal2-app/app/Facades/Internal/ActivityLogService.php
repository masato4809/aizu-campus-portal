<?php

declare(strict_types=1);

namespace App\Facades\Internal;

use Illuminate\Support\Facades\Facade;

class ActivityLogService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'internal\activity_log_service';
    }
}
