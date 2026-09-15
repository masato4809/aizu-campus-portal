<?php

declare(strict_types=1);

namespace App\Enum\App;

/**
 * 勤務状態
 */
enum EWorkingState: string
{
    case INVALID = 'invalid'; // 無効.
    case NONE    = 'none'; // 出勤前 or 休日.
    case WORKING = 'working'; // 勤務中.
    case REST    = 'rest'; // 休憩中.
    case LEAVING = 'leaving'; // 退勤後.
}
