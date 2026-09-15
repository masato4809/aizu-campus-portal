<?php

declare(strict_types=1);

namespace App\Facades\Models\App\Trn;

use App\Services\Models\App\Trn\TrnSeatService as BaseTrnSeatService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection list(array $with = [])
 * @method static \App\Models\App\Trn\TrnSeat findByIdOrFail(int $id, array $with = [])
 *
 * @see \App\Services\Models\App\Trn\TrnSeatService
 */
class TrnSeatService extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return BaseTrnSeatService::class;
    }
}
