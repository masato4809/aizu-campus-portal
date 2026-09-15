<?php

declare(strict_types=1);

namespace App\Facades\Internal;

use Illuminate\Support\Facades\Facade;

class PipeService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'internal\pipe_service';
    }
}
