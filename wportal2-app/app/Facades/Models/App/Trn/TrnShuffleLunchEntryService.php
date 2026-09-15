<?php

declare(strict_types=1);

namespace App\Facades\Models\App\Trn;

use Illuminate\Support\Facades\Facade;

class TrnShuffleLunchEntryService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'models\app\trn\trn_shuffle_lunch_entry_service';
    }
}
