<?php

declare(strict_types=1);

namespace App\Facades\Models\App\Auth;

use Illuminate\Support\Facades\Facade;

class AuthUserService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'models\app\auth\auth_user_service';
    }
}
