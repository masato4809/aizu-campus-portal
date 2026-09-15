<?php

declare(strict_types=1);

namespace App\Enum\App;

/**
 * 曜日の定義.
 */
enum EWeekDay: int
{
    case SUNDAY    = 0; // 日曜日.
    case MONDAY    = 1; // 月曜日.
    case TUESDAY   = 2; // 火曜日.
    case WEDNESDAY = 3; // 水曜日.
    case THURSDAY  = 4; // 木曜日.
    case FRIDAY    = 5; // 金曜日.
    case SATURDAY  = 6; // 土曜日.
}
