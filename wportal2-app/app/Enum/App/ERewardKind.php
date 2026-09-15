<?php

declare(strict_types=1);

namespace App\Enum\App;

/**
 * 報酬種別.
 */
enum ERewardKind: int
{
    case INVALID = 0; // 指定なし.
    case DAILY   = 1; // デイリー報酬.
    case ACHIEVE = 2; // 達成報酬.
}
