<?php

declare(strict_types=1);

namespace App\Enum\App;

/**
 * 勤務場所
 */
enum EWorkingPlace: string
{
    case INVALID = 'invalid'; // 無効.
    case OFFICE  = 'office'; // オフィス.
    case HOME    = 'home'; // 在宅.
}
