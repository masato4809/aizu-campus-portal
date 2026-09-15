<?php

declare(strict_types=1);

namespace App\Facades\External;

use Illuminate\Support\Facades\Facade;

class GoogleCalendarService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'external\google_calendar_service';
    }
}
