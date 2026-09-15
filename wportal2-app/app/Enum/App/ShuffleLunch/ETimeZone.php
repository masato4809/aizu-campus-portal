<?php

declare(strict_types=1);

namespace App\Enum\App\ShuffleLunch;

enum ETimeZone: int
{
    case INVALID  = 0; // 無効値.

    case STANDBY  = 1; // 準備時間.
    case MATCHED  = 2; // マッチング済時間.
}
