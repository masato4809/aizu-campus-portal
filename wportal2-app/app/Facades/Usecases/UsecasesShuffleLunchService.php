<?php

declare(strict_types=1);

namespace App\Facades\Usecases;

use Illuminate\Support\Facades\Facade;

class UsecasesShuffleLunchService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'usecases\usecases_shuffle_lunch_service';
    }
}
