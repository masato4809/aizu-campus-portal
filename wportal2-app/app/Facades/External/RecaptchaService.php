<?php

declare(strict_types=1);

namespace App\Facades\External;

use Illuminate\Support\Facades\Facade;

class RecaptchaService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'external\recaptcha_service';
    }
}
